<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Carbon\Carbon;   

class AppointmentController extends Controller
{
    /** DB table used by this resource */
    private const TABLE = 'doctor_appointment_schedules';

    /* ════════════════════════════════════════════════════════════
       POST /api/appointments        → create slot
    ════════════════════════════════════════════════════════════ */
    public function add(Request $request)
    {
        $rules = [
            'doctor_id'        => 'required|integer|exists:doctors,id',
            'day_of_week'      => 'required|integer|between:0,6',          // 0=Sun … 6=Sat
            'start_time'       => 'required|date_format:H:i',
            'end_time'         => 'required|date_format:H:i|after:start_time',
            'appointment_type' => 'nullable|string|max:50',
            'location'         => 'nullable|string|max:100',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return response()->json(['success' => false, 'errors' => $v->errors()], 422);
        }

        /* ── fail if another slot on same day overlaps this one ── */
        $overlap = DB::table(self::TABLE)
            ->where('doctor_id',   $request->doctor_id)
            ->where('day_of_week', $request->day_of_week)
            ->where('start_time', '<', $request->end_time)   // existing starts before new ends
            ->where('end_time',   '>', $request->start_time) // existing ends   after new starts
            ->exists();

        if ($overlap) {
            return response()->json([
                'success' => false,
                'message' => 'This slot overlaps an existing one on that day.'
            ], 409);
        }

        try {
            $id = DB::table(self::TABLE)->insertGetId([
                'doctor_id'        => $request->doctor_id,
                'day_of_week'      => $request->day_of_week,
                'start_time'       => $request->start_time,   // 'HH:MM'
                'end_time'         => $request->end_time,
                'appointment_type' => $request->appointment_type,
                'location'         => $request->location,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Slot added',
                'id'      => $id
            ], 201);

        } catch (Throwable $e) {
            Log::error('Add appointment failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Insert failed'], 500);
        }
    }

    /* ════════════════════════════════════════════════════════════
       GET /api/appointments/doctor/{doctorId}  → list slots
    ════════════════════════════════════════════════════════════ */
    public function index(int $doctorId)
    {
        $slots = DB::table(self::TABLE)
            ->where('doctor_id', $doctorId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return response()->json(['success' => true, 'appointments' => $slots], 200);
    }

    /* ════════════════════════════════════════════════════════════
       GET /api/appointments/{id}               → show slot
    ════════════════════════════════════════════════════════════ */
    public function show(int $id)
    {
        $slot = DB::table(self::TABLE)->find($id);

        return $slot
            ? response()->json(['success' => true, 'appointment' => $slot], 200)
            : response()->json(['success' => false, 'message' => 'Appointment not found'], 404);
    }

    /* ════════════════════════════════════════════════════════════
       PUT / PATCH /api/appointments/{id}       → update slot
    ════════════════════════════════════════════════════════════ */
    public function update(Request $request, int $id)
    {
        $rules = [
            'day_of_week'      => 'sometimes|integer|between:0,6',
            'start_time'       => 'sometimes|date_format:H:i',
            'end_time'         => 'sometimes|date_format:H:i|after:start_time',
            'appointment_type' => 'sometimes|nullable|string|max:50',
            'location'         => 'sometimes|nullable|string|max:100',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return response()->json(['success' => false, 'errors' => $v->errors()], 422);
        }

        $slot = DB::table(self::TABLE)->find($id);
        if (!$slot) {
            return response()->json(['success' => false, 'message' => 'Appointment not found'], 404);
        }

        /* ---- determine prospective values ---- */
        $newDow   = $request->input('day_of_week', $slot->day_of_week);
        $newStart = $request->input('start_time',  $slot->start_time);
        $newEnd   = $request->input('end_time',    $slot->end_time);

        /* ── overlap check (exclude current record) ── */
        $overlap = DB::table(self::TABLE)
            ->where('doctor_id',   $slot->doctor_id)
            ->where('day_of_week', $newDow)
            ->where('id', '<>',    $id)
            ->where('start_time', '<', $newEnd)
            ->where('end_time',   '>', $newStart)
            ->exists();

        if ($overlap) {
            return response()->json([
                'success' => false,
                'message' => 'Updated time overlaps another slot.'
            ], 409);
        }

        /* ---- perform update ---- */
        DB::table(self::TABLE)->where('id', $id)->update([
            'day_of_week'      => $newDow,
            'start_time'       => $newStart,
            'end_time'         => $newEnd,
            'appointment_type' => $request->appointment_type ?? $slot->appointment_type,
            'location'         => $request->location        ?? $slot->location,
            'updated_at'       => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Appointment updated'], 200);
    }

    /* ════════════════════════════════════════════════════════════
       DELETE /api/appointments/{id}            → remove slot
    ════════════════════════════════════════════════════════════ */
    public function delete(int $id)
    {
        $deleted = DB::table(self::TABLE)->where('id', $id)->delete();

        return $deleted
            ? response()->json(['success' => true, 'message' => 'Appointment deleted'], 200)
            : response()->json(['success' => false, 'message' => 'Appointment not found'], 404);
    }

    public function slices(Request $request, int $slotId)
{
    // 1) Grab the weekly slot window
    $slot = DB::table(self::TABLE)->find($slotId);

    if (!$slot) {
        return response()->json([
            'success' => false,
            'message' => 'Slot not found'
        ], 404);
    }

    // 2) Work out which calendar date to use
    $date = $request->input('date');          // YYYY-MM-DD
    if ($date) {
        $date = Carbon::parse($date)->startOfDay();
    } else {
        // next occurrence of that day_of_week (0 = Sun)
        $dow  = ($slot->day_of_week == 0) ? Carbon::SUNDAY : $slot->day_of_week;
        $date = Carbon::now()->next($dow)->startOfDay();
    }

    // 3) Build the 30-minute slices within the window
    $start = Carbon::parse($date->toDateString().' '.$slot->start_time);
    $end   = Carbon::parse($date->toDateString().' '.$slot->end_time);

    $slices = [];
    while ($start->lt($end)) {
        $sliceEnd = $start->copy()->addMinutes( 15);

        // check if slice already booked
        $booked = DB::table('doctor_bookings')
            ->where([
                ['slot_id',          '=', $slotId],
                ['appointment_date', '=', $date->toDateString()],
                ['slice_start',      '=', $start->format('H:i:s')],
            ])->exists();

        $slices[] = [
            'start'     => $start->format('H:i'),
            'end'       => $sliceEnd->format('H:i'),
            'is_booked' => $booked,
        ];

        $start = $sliceEnd;
    }

    return response()->json([
        'success' => true,
        'slices'  => $slices,
        'date'    => $date->toDateString(),
    ], 200);
}
}
