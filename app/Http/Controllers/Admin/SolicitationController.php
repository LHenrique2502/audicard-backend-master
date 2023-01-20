<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Auth\AuthController;
use App\Services\SolicitationService;
use App\Validator\DetailSolicitationValidator;
use App\Validator\SolicitationValidator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SolicitationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function __construct()
    {
        $this->middleware('auth:api');

    }


    public function index()
    {
        $result = SolicitationService::all(Auth::user()->type == 2 ? Auth::id() : null);

        return response()->json([
            'data' => $result
        ], 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = $request->all();

        //print_r($data);return;

        $validator = SolicitationValidator::validate($data);


        if ($validator->fails()) {
            return response()->json([
                'message' => yellowMessage('error', 'fail'),
                'errors' => $validator->errors()->all()
            ], 422);
        }

        /** $validator = DetailSolicitationValidator::validate($request);


        if ($validator instanceof \Illuminate\Validation\Validator) {
            if ($validator->fails()) {
                return response()->json([
                    'message' => yellowMessage('error', 'fail'),
                    'errors' => $validator->errors()->all()
                ], 422);
            }
        } else {
            if (is_array($validator->errors())) {
                return response()->json([
                    'message' => yellowMessage('error', 'fail'),
                    'errors' => $validator->errors()
                ], 422);
            }

        }*/


        $result = SolicitationService::insert($request);

        if ($result != yellowMessage('create', 'success')) {
            return response()->json([
                'data' => $result
            ], 422);
        }

        return response()->json([
            'data' => $result
        ], 200);

        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        if (!is_numeric($id) && strlen($id) != 36) {
            if (Auth::user()->type == 2) {
                $result = SolicitationService::counts(Auth::id());
            } else {
                $result = SolicitationService::counts();
            }
        } else {
            $result = SolicitationService::show($id);
            if ($result == yellowMessage('not-found', 'fail')) {
                return response()->json([
                    'data' => $result
                ], 422);
            }

        }

        return response()->json([
            'data' => $result
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $data = json_decode($request->getContent(), true);

        $result = SolicitationService::update($data, $id);

        if ($result != yellowMessage('edit', 'success')) {
            return response()->json([
                'data' => $result
            ], 422);
        }

        return response()->json([
            'data' => $result
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = SolicitationService::destroy($id);

        if ($result != 1) {
            return response()->json([
                'data' => $result
            ], 422);
        }

        return response()->json([
            'data' => yellowMessage('delete', 'success')
        ], 200);


    }






}
