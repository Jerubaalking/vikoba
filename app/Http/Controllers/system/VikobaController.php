<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Http\V2_StandardApiResponse;
use App\Models\Vikoba;
use Illuminate\Http\Request;

class VikobaController extends Controller
{
    //

    public function index(Request $request)
    {
        try {
            $vikoba = Vikoba::all(); 
            $messages =[
                "Successfully retrived data!"
            ];
          return V2_StandardApiResponse::generate($vikoba,$messages, 200);
        } catch (\Throwable $th) {
            $messages =[
                $th
            ];
          return V2_StandardApiResponse::generate(null, $messages, 500);
        }
    }
}
