<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RevokePermissionController extends Controller
{
    /**
     * Revoke from role
     *
     * @param Permission $permission
     * @param Role $role
     * @return JsonResponse
     */
    public function role(Permission $permission, Role $role)
    {
        $role->permissions()->detach($permission->id);

        return response()->json(['message' => 'Success']);
    }

    /**
     * Revoke from user
     *
     * @param Permission $permission
     * @param User $user
     * @return JsonResponse
     */
    public function user(Permission $permission, User $user)
    {
        $user->permissions()->detach($permission->id);

        return response()->json(['message' => 'Success']);
    }
}
