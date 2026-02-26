<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    /* ========== GET /api/prescriptions ========== */
    public function index()
    {
        // get all bookings across all doctors
        $bookings = DB::table('doctor_bookings')
            ->join('user', 'doctor_bookings.user_id', '=', 'user.id')
            ->join('doctors', 'doctor_bookings.doctor_id', '=', 'doctors.id')
            ->select(
                'doctor_bookings.id as booking_id',
                'doctor_bookings.appointment_date',
                'doctor_bookings.slice_start',
                'doctor_bookings.booking_token',

                'user.id as patient_id',
                'user.name as patient_name',
                'user.email as patient_email',
                'user.phone as patient_phone',
                'user.address as patient_address',

                'doctors.id as doctor_id',
                DB::raw("CONCAT(doctors.first_name, ' ', doctors.last_name) as doctor_name"),
                'doctors.email as doctor_email',
                'doctors.phone as doctor_phone',
                'doctors.specialty',
                'doctors.degree',
                'doctors.image_url'
            )
            ->orderByDesc('doctor_bookings.appointment_date')
            ->get();

        $response = [];

        foreach ($bookings as $booking) {
            $prescription = DB::table('prescriptions')
                ->where('prescriptions.booking_id', $booking->booking_id)
                ->select('prescriptions.*')
                ->first();

            $response[] = [
                'booking'      => $booking,
                'prescription' => $prescription ?: 'Prescription not assigned'
            ];
        }

        return response()->json([
            'status' => 'success',
            'data'   => $response
        ], 200);
    }

    /* ========== GET /api/prescriptions/{id} ========== */
    public function show($id)
    {
        $prescription = DB::table('prescriptions')
            ->where('prescriptions.id', $id)
            ->join('doctors', 'prescriptions.doctor_id', '=', 'doctors.id')
            ->join('doctor_bookings', 'prescriptions.booking_id', '=', 'doctor_bookings.id')
            ->join('user', 'prescriptions.user_id', '=', 'user.id')
            ->select(
                'prescriptions.*',

                DB::raw("CONCAT(doctors.first_name, ' ', doctors.last_name) as doctor_name"),
                'doctors.email as doctor_email',
                'doctors.phone as doctor_phone',
                'doctors.specialty',
                'doctors.degree',
                'doctors.image_url',

                'user.name as patient_name',
                'user.email as patient_email',
                'user.phone as patient_phone',
                'user.address as patient_address',

                'doctor_bookings.appointment_date',
                'doctor_bookings.slice_start',
                'doctor_bookings.booking_token'
            )
            ->first();

        if (!$prescription) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Prescription not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $prescription
        ], 200);
    }

    /* ========== POST /api/prescriptions ========== */
    public function store(Request $request)
    {
        $data = $request->validate([
            'booking_id'     => 'required|exists:doctor_bookings,id',
            'doctor_id'      => 'required|exists:doctors,id',
            'user_id'        => 'required|exists:user,id',
            'notes'          => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'medicines'      => 'nullable|json',
        ]);

        $id = DB::table('prescriptions')->insertGetId([
            'booking_id'        => $data['booking_id'],
            'doctor_id'         => $data['doctor_id'],
            'user_id'           => $data['user_id'],
            'notes'             => $data['notes'] ?? null,
            'follow_up_date'    => $data['follow_up_date'] ?? null,
            'medicines'         => $data['medicines'] ?? null,
            'prescription_date' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Prescription created successfully',
            'id'      => $id
        ], 201);
    }

    /* ========== PUT /api/prescriptions/{id} ========== */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'notes'          => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'medicines'      => 'nullable|json',
        ]);

        $updated = DB::table('prescriptions')
            ->where('id', $id)
            ->update([
                'notes'          => $data['notes'] ?? null,
                'follow_up_date' => $data['follow_up_date'] ?? null,
                'medicines'      => $data['medicines'] ?? null,
                'updated_at'     => now(),
            ]);

        if (!$updated) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Prescription not found or not updated'
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Prescription updated successfully'
        ], 200);
    }

    /* ========== DELETE /api/prescriptions/{id} ========== */
    public function destroy($id)
    {
        $deleted = DB::table('prescriptions')->where('id', $id)->delete();

        return response()->json([
            'status'  => $deleted ? 'success' : 'error',
            'message' => $deleted ? 'Prescription deleted successfully' : 'Prescription not found'
        ], $deleted ? 200 : 404);
    }
}
