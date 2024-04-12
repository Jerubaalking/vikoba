<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RevokeRoleController extends Controller
{
    /**
     * Invoke method
     *
     * @param Role $role
     * @param User $user
     * @return JsonResponse
     */
    public function __invoke(Role $role, User $user)
    {
        $user->roles()->detach($role->id);

        return response()->json(['message' => 'Success']);
    }
}
