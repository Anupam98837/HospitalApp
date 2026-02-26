<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class DoctorController extends Controller
{
    private const IMG_DIR = 'assets/images/doctor';   // relative to /public

    /* ===============================================================
     |  POST /api/doctors/signup                – register doctor
     * ===============================================================*/
    public function signup(Request $request)
    {
        $rules = [
            'department_id'        => 'required|integer|exists:departments,id',
            'first_name'           => 'required|string|max:100',
            'last_name'            => 'required|string|max:100',
            'phone'                => 'nullable|string|max:20',
            'email'                => 'required|email|unique:doctors,email',
            'password'             => 'required|string|min:8',
            'specialty'            => 'nullable|string|max:150',
            'degree'               => 'nullable|string|max:150',
            'sex'                  => 'nullable|in:male,female,other',
            'home_town'            => 'nullable|string|max:150',
            'address'              => 'nullable|string',
            'office_address'       => 'nullable|string',
            'visiting_charge'      => 'nullable|numeric|min:0',
            'consultation_charge'  => 'nullable|numeric|min:0',
            'image'                => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return response()->json(['success' => false,'errors' => $v->errors()], 422);
        }

        try {
            DB::beginTransaction();

            /* ---------- image upload ---------- */
            $imgPath = null;
            if ($request->hasFile('image')) {
                $file     = $request->file('image');
                $filename = 'doc_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path(self::IMG_DIR), $filename);
                $imgPath  = self::IMG_DIR . '/' . $filename;
            }

            $doctorId = DB::table('doctors')->insertGetId([
                'department_id'        => $request->department_id,
                'first_name'           => $request->first_name,
                'last_name'            => $request->last_name,
                'phone'                => $request->phone,
                'email'                => $request->email,
                'password'             => Hash::make($request->password),
                'specialty'            => $request->specialty,
                'degree'               => $request->degree,
                'sex'                  => $request->sex,
                'home_town'            => $request->home_town,
                'address'              => $request->address,
                'office_address'       => $request->office_address,
                'visiting_charge'      => $request->visiting_charge,
                'consultation_charge'  => $request->consultation_charge,
                'image_url'            => $imgPath,
                'is_active'            => true,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Doctor registered successfully.',
                'id'      => $doctorId,
            ], 201);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Doctor signup failed: '.$e->getMessage());
            return response()->json(['success'=>false,'message'=>'Signup failed'], 500);
        }
    }

    /* ===============================================================
     |  POST /api/doctors/login                 – issue token
     * ===============================================================*/
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $doctor = DB::table('doctors')->where('email', $request->email)->first();

        if (!$doctor || !Hash::check($request->password, $doctor->password)) {
            Log::warning('Doctor login failed', ['email' => $request->email]);
            return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
        }

        $plain = bin2hex(random_bytes(40));
        DB::table('personal_access_tokens')->insert([
            'tokenable_type' => 'doctor',
            'tokenable_id'   => $doctor->id,
            'name'           => 'auth_token',
            'token'          => hash('sha256', $plain),
            'abilities'      => json_encode(['*']),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return response()->json([
            'status'         => 'success',
            'message'        => 'Login successful',
            'access_token'   => $plain,
            'token_type'     => 'Bearer',
            'tokenable_type' => 'doctor',
        ]);
    }

    /* ===============================================================
     |  POST /api/doctors/logout                – revoke token
     * ===============================================================*/
    public function logout(Request $request)
    {
        $token = $request->bearerToken() ?: $request->header('token');
        if (!$token) {
            return response()->json(['status'=>'error','message'=>'Token not provided'], 401);
        }

        $deleted = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $token))
            ->where('tokenable_type', 'doctor')
            ->delete();

        return response()->json([
            'status'  => $deleted ? 'success' : 'error',
            'message' => $deleted ? 'Logged out successfully' : 'Invalid token',
        ], $deleted ? 200 : 401);
    }

    /* ===============================================================
     |  GET /api/doctors                      – list
     * ===============================================================*/
    public function index()
    {
        $docs = DB::table('doctors')
                  ->orderBy('created_at','desc')
                  ->get();

        return response()->json(['success'=>true,'doctors'=>$docs], 200);
    }

    /* ===============================================================
     |  GET /api/doctors/{id}                 – single
     * ===============================================================*/
    public function show(int $id)
    {
        $doc = DB::table('doctors')->find($id);
        return $doc
            ? response()->json(['success'=>true,'doctor'=>$doc],200)
            : response()->json(['success'=>false,'message'=>'Doctor not found'],404);
    }

    /* ===============================================================
     |  PUT /api/doctors/{id} / PATCH         – update
     * ===============================================================*/
    public function update(Request $request, int $id)
    {
        $rules = [
            'department_id'        => 'sometimes|integer|exists:departments,id',
            'first_name'           => 'sometimes|string|max:100',
            'last_name'            => 'sometimes|string|max:100',
            'phone'                => 'sometimes|nullable|string|max:20',
            'email'                => "sometimes|email|unique:doctors,email,$id",
            'password'             => 'sometimes|string|min:8',
            'specialty'            => 'sometimes|nullable|string|max:150',
            'degree'               => 'sometimes|nullable|string|max:150',
            'sex'                  => 'sometimes|nullable|in:male,female,other',
            'home_town'            => 'sometimes|nullable|string|max:150',
            'address'              => 'sometimes|nullable|string',
            'office_address'       => 'sometimes|nullable|string',
            'visiting_charge'      => 'sometimes|nullable|numeric|min:0',
            'consultation_charge'  => 'sometimes|nullable|numeric|min:0',
            'is_active'            => 'sometimes|boolean',
            'image'                => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return response()->json(['success'=>false,'errors'=>$v->errors()],422);
        }

        $doc = DB::table('doctors')->find($id);
        if (!$doc) {
            return response()->json(['success'=>false,'message'=>'Doctor not found'],404);
        }

        try {
            DB::beginTransaction();

            /* ---------- optional new image ---------- */
            $imgPath = $doc->image_url;
            if ($request->hasFile('image')) {
                if ($imgPath && file_exists(public_path($imgPath))) {
                    @unlink(public_path($imgPath));
                }
                $file     = $request->file('image');
                $filename = 'doc_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path(self::IMG_DIR), $filename);
                $imgPath  = self::IMG_DIR . '/' . $filename;
            }

            DB::table('doctors')->where('id',$id)->update([
                'department_id'        => $request->department_id        ?? $doc->department_id,
                'first_name'           => $request->first_name           ?? $doc->first_name,
                'last_name'            => $request->last_name            ?? $doc->last_name,
                'phone'                => $request->phone                ?? $doc->phone,
                'email'                => $request->email                ?? $doc->email,
                'password'             => $request->password ? Hash::make($request->password) : $doc->password,
                'specialty'            => $request->specialty            ?? $doc->specialty,
                'degree'               => $request->degree               ?? $doc->degree,
                'sex'                  => $request->sex                  ?? $doc->sex,
                'home_town'            => $request->home_town            ?? $doc->home_town,
                'address'              => $request->address              ?? $doc->address,
                'office_address'       => $request->office_address       ?? $doc->office_address,
                'visiting_charge'      => $request->visiting_charge      ?? $doc->visiting_charge,
                'consultation_charge'  => $request->consultation_charge  ?? $doc->consultation_charge,
                'is_active'            => $request->is_active            ?? $doc->is_active,
                'image_url'            => $imgPath,
                'updated_at'           => now(),
            ]);

            DB::commit();
            return response()->json(['success'=>true,'message'=>'Doctor updated'],200);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Doctor update failed: '.$e->getMessage());
            return response()->json(['success'=>false,'message'=>'Update failed'],500);
        }
    }

    /* ===============================================================
     |  DELETE /api/doctors/{id}              – delete
     * ===============================================================*/
    public function destroy(int $id)
    {
        $doc = DB::table('doctors')->find($id);
        if (!$doc) {
            return response()->json(['success'=>false,'message'=>'Doctor not found'],404);
        }

        if ($doc->image_url && file_exists(public_path($doc->image_url))) {
            @unlink(public_path($doc->image_url));
        }

        DB::table('doctors')->where('id',$id)->delete();
        return response()->json(['success'=>true,'message'=>'Doctor deleted'],200);
    }

    /* ===============================================================
     |  PATCH /api/doctors/{id}/toggle-active – on/off
     * ===============================================================*/
    public function toggleActive(int $id)
    {
        $doc = DB::table('doctors')->find($id);
        if (!$doc) {
            return response()->json(['success'=>false,'message'=>'Doctor not found'],404);
        }

        $newState = !$doc->is_active;
        DB::table('doctors')->where('id',$id)->update([
            'is_active'  => $newState,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success'=>true,
            'message'=>"Doctor is now ".($newState ? 'active' : 'inactive'),
            'is_active'=>$newState
        ],200);
    }

    /* ===============================================================
|  GET /api/departments/{id}/doctors  – doctors + schedules
|  Public route – no middleware
* ===============================================================*/
public function doctorsByDepartment(int $departmentId)
{
    // 1. Doctors in the department (active only)
    $doctors = DB::table('doctors')
        ->where('department_id', $departmentId)
        ->where('is_active', true)
        ->orderBy('created_at', 'desc')
        ->get();

    if ($doctors->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No doctors found for this department.'
        ], 404);
    }

    // 2. All schedules for those doctors
    $schedules = DB::table('doctor_appointment_schedules')
        ->whereIn('doctor_id', $doctors->pluck('id'))
        ->orderBy('day_of_week')   // tinyint 0-6 or 1-7
        ->orderBy('start_time')
        ->get()
        ->groupBy('doctor_id');

    // 3. Merge schedules into each doctor record
    $result = $doctors->map(function ($doc) use ($schedules) {
        $doc->schedules = $schedules->get($doc->id, collect())->values();
        return $doc;
    });

    return response()->json([
        'success' => true,
        'doctors' => $result
    ], 200);
}

}
