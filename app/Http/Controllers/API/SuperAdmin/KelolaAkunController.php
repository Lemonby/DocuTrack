<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KelolaAkunController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::with('roles')
            ->when($request->query('role'), fn ($q, $role) => $q->role($role))
            ->when($request->query('search'), fn ($q, $s) => $q->where('nama', 'LIKE', "%{$s}%"))
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
            'meta' => ['total' => $users->total()],
        ]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => $validated['password'], // auto-hashed by cast
            'nama_jurusan' => $validated['nama_jurusan'] ?? null,
        ]);

        $user->assignRole($validated['role']);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dibuat.',
            'data' => new UserResource($user->load('roles')),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::with('roles')->findOrFail($id);

        return response()->json(['success' => true, 'data' => new UserResource($user)]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['sometimes', 'string', 'max:100'],
            'email' => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($id, 'user_id')],
            'password' => ['sometimes', 'string', 'min:8'],
            'status' => ['sometimes', 'in:Aktif,Tidak Aktif'],
            'role' => ['sometimes', 'string', 'exists:roles,name'],
            'nama_jurusan' => ['sometimes', 'nullable', 'string', 'exists:jurusans,nama_jurusan'],
        ]);

        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
            unset($validated['role']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diperbarui.',
            'data' => new UserResource($user->fresh()->load('roles')),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete(); // soft delete

        return response()->json(['success' => true, 'message' => 'User berhasil dihapus (soft delete).']);
    }
}
