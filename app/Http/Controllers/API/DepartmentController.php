<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class DepartmentController extends Controller
{
    /** Where we keep department images (relative to public/) */
    private const IMG_DIR = 'assets/images/department';

    /* ===============================================================
     |  POST /api/departments           – create
     * ===============================================================*/
    public function store(Request $request)
    {
        Log::info('Creating department', ['payload' => $request->all()]);

        $rules = [
            'title'       => 'required|string|max:150',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            Log::warning('Department validation failed', ['errors' => $v->errors()]);
            return response()->json(['success' => false, 'errors' => $v->errors()], 422);
        }

        /* ---------- optional image upload ---------- */
        $imgPath = null;
        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $filename = 'dept_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path(self::IMG_DIR), $filename);
            $imgPath  = self::IMG_DIR . '/' . $filename;
        }

        $id = DB::table('departments')->insertGetId([
            'title'       => $request->title,
            'description' => $request->description,
            'image_url'   => $imgPath,
            'status'      => 'active',        // default
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        Log::info('Department created', ['id' => $id]);

        return response()->json([
            'success' => true,
            'message' => 'Department created successfully.',
            'id'      => $id,
        ], 201);
    }

    /* ===============================================================
     |  GET /api/departments            – list
     * ===============================================================*/
    public function index()
    {
        Log::info('Fetching all departments');

        $departments = DB::table('departments')
                         ->orderBy('created_at', 'desc')
                         ->get();

        return response()->json([
            'success'     => true,
            'departments' => $departments,
        ], 200);
    }

    /* ===============================================================
     |  GET /api/departments/{id}       – single record
     * ===============================================================*/
    public function show(int $id)
    {
        Log::info('Fetching department', ['id' => $id]);

        $department = DB::table('departments')->find($id);

        return $department
            ? response()->json(['success' => true, 'department' => $department], 200)
            : response()->json(['success' => false, 'message' => 'Department not found'], 404);
    }

    /* ===============================================================
     |  PUT /api/departments/{id}       – update
     * ===============================================================*/
    public function update(Request $request, int $id)
    {
        Log::info('Updating department', ['id' => $id, 'payload' => $request->all()]);

        $rules = [
            'title'       => 'sometimes|required|string|max:150',
            'description' => 'sometimes|nullable|string',
            'image'       => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'status'      => 'sometimes|in:active,inactive',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return response()->json(['success' => false, 'errors' => $v->errors()], 422);
        }

        $department = DB::table('departments')->find($id);
        if (!$department) {
            return response()->json(['success' => false, 'message' => 'Department not found'], 404);
        }

        /* ---------- handle (optional) new image ---------- */
        $imgPath = $department->image_url;
        if ($request->hasFile('image')) {
            // delete old file if it exists
            if ($imgPath && file_exists(public_path($imgPath))) {
                @unlink(public_path($imgPath));
            }

            $file     = $request->file('image');
            $filename = 'dept_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path(self::IMG_DIR), $filename);
            $imgPath  = self::IMG_DIR . '/' . $filename;
        }

        DB::table('departments')->where('id', $id)->update([
            'title'       => $request->has('title')       ? $request->title       : $department->title,
            'description' => $request->has('description') ? $request->description : $department->description,
            'image_url'   => $imgPath,
            'status'      => $request->status ?? $department->status,
            'updated_at'  => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Department updated'], 200);
    }

    /* ===============================================================
     |  DELETE /api/departments/{id}    – delete
     * ===============================================================*/
    public function destroy(int $id)
    {
        Log::info('Deleting department', ['id' => $id]);

        $department = DB::table('departments')->find($id);
        if (!$department) {
            return response()->json(['success' => false, 'message' => 'Department not found'], 404);
        }

        // delete image file if present
        if ($department->image_url && file_exists(public_path($department->image_url))) {
            @unlink(public_path($department->image_url));
        }

        DB::table('departments')->where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Department deleted'], 200);
    }

    /* ===============================================================
     |  PATCH /api/departments/{id}/toggle-status – active ↔ inactive
     * ===============================================================*/
    public function toggleStatus(int $id)
    {
        Log::info('Toggling department status', ['id' => $id]);

        $department = DB::table('departments')->find($id);
        if (!$department) {
            return response()->json(['success' => false, 'message' => 'Department not found'], 404);
        }

        $newStatus = $department->status === 'active' ? 'inactive' : 'active';

        DB::table('departments')
            ->where('id', $id)
            ->update([
                'status'     => $newStatus,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => "Status switched to {$newStatus}.",
            'status'  => $newStatus,
        ], 200);
    }
}
