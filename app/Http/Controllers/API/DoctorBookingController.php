<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingSuccessMail;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use Throwable;

class DoctorBookingController extends Controller
{
    private const TABLE = 'doctor_bookings';

    /* ─────────────────────────── CREATE BOOKING ────────────────────────────── */
     public function store(Request $request)
    {
        /* 1) Validate -------------------------------------------------------- */
        $v = Validator::make($request->all(), [
            'doctor_id'        => 'required|integer|exists:doctors,id',
            'slot_id'          => 'required|integer|exists:doctor_appointment_schedules,id',
            'appointment_date' => 'required|date',            // YYYY-MM-DD
            'slice_start'      => 'required|date_format:H:i', // HH:MM (24-h)

            'patient_name'     => 'required|string|max:150',
            'patient_address'  => 'nullable|string',
            'additional_note'  => 'nullable|string',
            'alternate_phone'  => 'nullable|string|max:20',
            'alternate_email'  => 'nullable|email|max:150',
        ]);
        if ($v->fails()) {
            return response()->json(['success' => false, 'errors' => $v->errors()], 422);
        }

        $userId = $request->attributes->get('auth_id');            // set by auth middleware

        /* 2) Check that the slot really belongs to the doctor --------------- */
        $slot = DB::table('doctor_appointment_schedules')->find($request->slot_id);
        if (!$slot || $slot->doctor_id != $request->doctor_id) {
            return response()->json(['success' => false, 'message' => 'Slot / doctor mismatch'], 409);
        }

        /* 3) Make sure the requested 30-min slice is inside the slot window -- */
        $sliceStart = Carbon::parse($request->slice_start);
        $slotStart  = Carbon::parse($slot->start_time);
        $slotEnd    = Carbon::parse($slot->end_time);

        if ($sliceStart->lt($slotStart) ||
            $sliceStart->copy()->addMinutes(30)->gt($slotEnd)) {
            return response()->json(['success' => false, 'message' => 'Slice is outside slot window'], 422);
        }

        /* 4) Prepare the row to insert -------------------------------------- */
        do {
            $bookingToken = Str::upper(Str::random(10));
        } while (DB::table(self::TABLE)->where('booking_token', $bookingToken)->exists());

        $row = [
            'doctor_id'        => $request->doctor_id,
            'slot_id'          => $slot->id,
            'user_id'          => $userId,

            'appointment_date' => $request->appointment_date,
            'slice_start'      => $request->slice_start,

            'patient_name'     => $request->patient_name,
            'patient_address'  => $request->patient_address,
            'alternate_phone'  => $request->alternate_phone,
            'alternate_email'  => $request->alternate_email,
            'booking_token'    => $bookingToken,
            'additional_note'  => $request->additional_note,
            'created_at'       => now(),
            'updated_at'       => now(),
        ];

        /* 5) Insert — let DB enforce uniqueness (see migration) ------------- */
        try {
            DB::transaction(function () use (&$id, $row) {
                $id = DB::table(self::TABLE)->insertGetId($row);
            }, 3);                                                  // automatic dead-lock retry
        } catch (QueryException $e) {
            // 23000 = MySQL, 23505 = PostgreSQL ⇒ duplicate-key on unique index
            if (in_array($e->getCode(), ['23000', '23505'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'That 30-minute slice has just been booked. Please pick another.',
                ], 409);
            }
            throw $e; // Something else went wrong → let Laravel handle it
        }

        /* 6) Send confirmation e-mails (unchanged) ------------------------- */
        $doctor = DB::table('doctors')
                    ->select('first_name', 'last_name', 'email')
                    ->where('id', $request->doctor_id)
                    ->first();

        $user = DB::table('user')
                  ->select('name','email')
                  ->where('id', $userId)
                  ->first();

        $slotDate = Carbon::parse($request->appointment_date)->format('d M Y');
        $slotTime = Carbon::parse($request->slice_start)->format('g:i A')
                  . ' – '
                  . Carbon::parse($request->slice_start)->addMinutes(30)->format('g:i A');

        // user mail
        if ($user && $user->email) {
            Mail::to($user->email)->send(new BookingSuccessMail(
                doctorName    : "{$doctor->first_name} {$doctor->last_name}",
                patientName   : $request->patient_name,
                recipientName : $user->name,
                bookingToken  : $bookingToken,
                slotDate      : $slotDate,
                slotTime      : $slotTime
            ));
        }

        // alternate mail
        if ($request->filled('alternate_email') &&
            $request->alternate_email !== optional($user)->email) {
            Mail::to($request->alternate_email)->send(new BookingSuccessMail(
                doctorName    : "{$doctor->first_name} {$doctor->last_name}",
                patientName   : $request->patient_name,
                recipientName : $request->patient_name,
                bookingToken  : $bookingToken,
                slotDate      : $slotDate,
                slotTime      : $slotTime
            ));
        }

        // doctor mail
        if ($doctor && $doctor->email) {
            Mail::to($doctor->email)->send(new BookingSuccessMail(
                doctorName    : "{$doctor->first_name} {$doctor->last_name}",
                patientName   : $request->patient_name,
                recipientName : "{$doctor->first_name} {$doctor->last_name}",
                bookingToken  : $bookingToken,
                slotDate      : $slotDate,
                slotTime      : $slotTime
            ));
        }

        /* 7) Response ------------------------------------------------------- */
        return response()->json([
            'success'       => true,
            'message'       => 'Booking confirmed & e-mails sent',
            'booking_token' => $bookingToken,
            'id'            => $id,
        ], 201);
    }

    /* ─────────────────────────── UPDATE BOOKING ──────────────────────────── */
    public function update(Request $request, string $bookingToken)
    {
        /* Only cosmetics below — no change in logic, but added early-return if nothing to update */
        $v = Validator::make($request->all(), [
            'patient_name'     => 'sometimes|string|max:150',
            'patient_address'  => 'sometimes|nullable|string',
            'additional_note'  => 'sometimes|nullable|string',
            'alternate_phone'  => 'sometimes|nullable|string|max:20',
            'alternate_email'  => 'sometimes|nullable|email|max:150',
        ]);
        if ($v->fails()) {
            return response()->json(['success' => false, 'errors' => $v->errors()], 422);
        }

        $uid = $request->attributes->get('auth_id');

        $booking = DB::table(self::TABLE)
                     ->where('booking_token', $bookingToken)
                     ->where('user_id', $uid)
                     ->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }

        $updateData = array_filter($request->only([
            'patient_name',
            'patient_address',
            'additional_note',
            'alternate_phone',
            'alternate_email',
        ]), fn ($val) => $request->has($val));

        if (empty($updateData)) {
            return response()->json(['success' => false, 'message' => 'Nothing to update'], 400);
        }

        $updateData['updated_at'] = now();
        DB::table(self::TABLE)->where('id', $booking->id)->update($updateData);

        return response()->json(['success' => true, 'message' => 'Booking updated'], 200);
    }
    

    /* ─────────────────────────── LIST MY BOOKINGS ─────────────────────────── */
    public function index(Request $request)
    {
        $uid   = $request->attributes->get('auth_id');
        $query = DB::table(self::TABLE)->where('user_id', $uid);

        if ($request->filled('booking_token')) {
            $query->where('booking_token', $request->booking_token);
        }

        $rows = $query->orderBy('appointment_date', 'desc')->get();

        return $rows->isEmpty()
            ? response()->json(['success' => false, 'message' => 'No bookings found'], 404)
            : response()->json(['success' => true, 'bookings' => $rows], 200);
    }

    
    /* ─────────────────────────── CANCEL BOOKING ───────────────────────────── */
    public function destroy(Request $request, string $bookingToken)
    {
        $uid = $request->attributes->get('auth_id');

        $deleted = DB::table(self::TABLE)
                   ->where('booking_token', $bookingToken)
                   ->where('user_id', $uid)
                   ->delete();

        return $deleted
            ? response()->json(['success' => true, 'message' => 'Booking cancelled'], 200)
            : response()->json(['success' => false, 'message' => 'Booking not found'], 404);
    }
}
