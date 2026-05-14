<?php

namespace App\Http\Controllers\API\Direktur;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AkunController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json(['success' => true, 'data' => new UserResource($request->user()->load('roles'))]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $request->user()->update($request->validated());
        return response()->json(['success' => true, 'message' => 'Profil diperbarui.', 'data' => new UserResource($request->user()->fresh()->load('roles'))]);
    }
}
