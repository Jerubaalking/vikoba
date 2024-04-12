<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RestoreUserController extends Controller
{
    /**
     * Invoke method
     *
     * @param $id
     * @return JsonResponse
     */
    public function __invoke($id)
    {
        User::onlyTrashed()->findOrFail($id)->restore();

        return response()->json();
    }
}
