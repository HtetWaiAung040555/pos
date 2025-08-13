<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Status;

class StatusesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = Status::with('createdBy', 'updatedBy')->get();
        return response()->json($statuses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:statuses,name',
            'created_by' => 'nullable|integer|exists:users,id',
            'updated_by' => 'nullable|integer|exists:users,id',
        ]);

        $createdBy = $request->input('created_by');
        $updatedBy = $request->input('updated_by', $createdBy);

        if ($createdBy === null) {
            return response()->json([
                'message' => 'created_by or user_id is required',
            ], 422);
        }

        $status = Status::create([
            'name' => $request->input('name'),
            'created_by' => $createdBy,
            'updated_by' => $updatedBy
        ]);

        return response()->json($status, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $status = Status::with('createdBy', 'updatedBy')->find($id);
        return response()->json($status);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $status = Status::with('createdBy', 'updatedBy')->findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:statuses,name,' . $status->id,
            'updated_by' => 'nullable|integer|exists:users,id',
        ]);

        $updatedBy = $request->input('updated_by');

        $data = [];
        if ($request->has('name')) {
            $data['name'] = $request->input('name');
        }
        if ($updatedBy !== null) {
            $data['updated_by'] = $updatedBy;
        }

        if (!empty($data)) {
            $status->update($data);
        }

        return response()->json($status->fresh(['createdBy', 'updatedBy']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $status = Status::with('createdBy', 'updatedBy')->findOrFail($id);
        $status->delete();
        return response()->json(null, 204);
    }
}
