<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class GrantPermissionController extends Controller
{
    /**
     * Grant to a role
     *
     * @param Permission $permission
     * @param Role $role
     * @return JsonResponse
     */
    public function role(Permission $permission, Role $role)
    {
        $role->permissions()->syncWithoutDetaching([$permission->id]);

        return response()->json(['message' => 'Success']);
    }

    /**
     * Grant to a user
     *
     * @param Permission $permission
     * @param User $user
     * @return JsonResponse
     */
    public function user(Permission $permission, User $user)
    {
        $user->permissions()->syncWithoutDetaching($permission->id);

        return response()->json(['message' => 'Success']);
    }
}
