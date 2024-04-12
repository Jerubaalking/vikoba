<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Token;

class DestroyTokenController extends Controller
{
    // Default response data
    private const DATA = [
        'message' => 'Success',
        'description' => 'Logged out!',
    ];

    /**
     * Invoke method.
     *
     * @return JsonResponse
     */
    public function __invoke()
    {
        return rescue(function () {
            $token_id = request()->user()->token()->id;

            Token::destroy([$token_id]);

            DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $token_id)
                ->delete();

            return response()->json(self::DATA);
        }, fn () => response()->json(self::DATA));
    }
}
