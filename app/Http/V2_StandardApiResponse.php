<?php
namespace App\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

class V2_StandardApiResponse
{
    public static function generate($data = null, array $messages = [],int $statusCode = 200): JsonResponse
    {

        $response = [
            'data' => $data,
            'response_info' => [
                'messages' => $messages,
                'database' =>  config('database.connections.mysql.database'),
                'language' =>  App::getLocale(),
            ]
        ];



        return response()->json($response, $statusCode, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            ->header('X-Api-Status', $statusCode)
            ->header('X-Api-Language',App::getLocale() )
            ->header('X-Api-Database', config('database.connections.mysql.database'));
    }
}
