<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;

class BlockUserController extends Controller
{
    /**
     * Invoke method
     *
     * @param User $user
     * @return JsonResponse
     * @throws Exception
     */
    public function __invoke(User $user)
    {
        $user->delete();
        return response()->json();
    }
}
