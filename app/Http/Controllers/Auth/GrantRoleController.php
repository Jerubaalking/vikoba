<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class GrantRoleController extends Controller
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
        $user->roles()->syncWithoutDetaching([$role->id]);

        return response()->json(['message' => 'Success']);
    }
}
