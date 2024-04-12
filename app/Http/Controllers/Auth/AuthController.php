<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\V2_StandardApiResponse;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Accessing Protected Resources
     *
     * To access this route, the user must have a valid access token that is generated during the login process.
     *
     * This ensures that only authenticated users are able to access the endpoint
     *

     * @group Authentication v2
     *
     * @Error Message
     *
     * @urlParam lang The language. Example: en or sw
     *
     * @response 200 {
     * "data": true,
     * "response_info": {
     * "messages":  [
     * [
     *     "Mtumiaji amethibitishwa"
     * ]
     *   ] ,
     * "database" :"tunzaadb_test",
     * "language" : "sw"
     * }
     * }
     * @response 401 {
     * "success": false,
     * "data": null,
     * "response_info": {
     * "messages":  [
     * [
     *   "Authentication required"
     * ]
     *  ] ,
     * "database" :"tunzaadb_test",
     * "language" : "en"
     * }
     * }
     */
    public function validToken(){
        $messages = [trans('auth.user_verified')];
        return V2_StandardApiResponse::generate(true,$messages,200);

    }
}
