<?php

namespace App\Http\Controllers\Admin;

use App\Services\SolicitationService;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function solicitation(Request $request)
    {
        $result = SolicitationService::report($request);

        return response()->json([
            'data' => $result
        ], 200);
    }


    public function clients(){

        $result = UserService::clients();


        return response()->json([
            'data' => $result
        ], 200);


    }

}
