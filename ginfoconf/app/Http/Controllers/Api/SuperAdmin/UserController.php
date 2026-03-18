<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    /**
     * List all platform users (super admin only).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = User::query()->latest();

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                    ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }

        return UserResource::collection($query->paginate(50));
    }

    /**
     * Show a single user.
     */
    public function show(User $user): JsonResponse
    {
        $user->load('conferenceRoles.conference:id,slug');

        return response()->json(new UserResource($user));
    }

    /**
     * Grant or revoke super admin privileges.
     */
    public function setSuperAdmin(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'is_super_admin' => ['required', 'boolean'],
        ]);

        // Prevent self-demotion
        abort_if(
            $user->id === $request->user()->id && !$request->boolean('is_super_admin'),
            422,
            'You cannot revoke your own super admin privileges.'
        );

        $user->update(['is_super_admin' => $request->boolean('is_super_admin')]);

        return response()->json(new UserResource($user->fresh()));
    }

    /**
     * Soft-delete / deactivate a user account.
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        abort_if($user->id === $request->user()->id, 422, 'You cannot delete your own account.');
        abort_if($user->isSuperAdmin(), 422, 'Cannot delete a super admin account.');

        // Revoke all tokens before deletion
        $user->tokens()->delete();
        $user->delete();

        return response()->json(null, 204);
    }

    /**
     * List all conference roles for a user.
     */
    public function roles(User $user): JsonResponse
    {
        $roles = $user->conferenceRoles()
            ->with('conference:id,slug')
            ->get(['id', 'conference_id', 'role', 'assigned_at']);

        return response()->json(['data' => $roles]);
    }
}
