<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Http\V2_StandardApiResponse;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    //

    public function index(Request $request)
    {
        try {
            $member = Member::with('user', 'vikoba'); 
            $messages =[
                "Successfully retrived data!"
            ];
          return V2_StandardApiResponse::generate($member,$messages, 200);
        } catch (\Throwable $th) {
            $messages =[
                $th
            ];
          return V2_StandardApiResponse::generate(null, $messages, 500);
        }
    }

    public function show(Request $request)
    {
        try {
            $member = Member::with('user', 'vikoba'); 
            $messages =[
                "Successfully retrived data!"
            ];
          return V2_StandardApiResponse::generate($member,$messages, 200);
        } catch (\Throwable $th) {
            $messages =[
                $th
            ];
          return V2_StandardApiResponse::generate(null, $messages, 500);
        }
    }
}
