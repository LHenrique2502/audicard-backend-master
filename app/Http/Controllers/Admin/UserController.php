<?php

namespace App\Http\Controllers\Admin;

use App\Services\UserService;
use App\Validator\UserValidator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $users = UserService::all();

        return response()->json(['data' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = $request->all();

        $validator = UserValidator::validate($data);

        if ($validator->fails()) {
            return response()->json([
                'message' => yellowMessage('error', 'fail'),
                'errors' => $validator->errors()->all()
            ], 422);
        }


        $result = UserService::insert($data);

        if ($result != yellowMessage('create', 'success')) {
            return response()->json([
                'data' => $result
            ], 422);
        }

        return response()->json([
            'data' => $result
        ], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $validator = UserValidator::show($id);

        if ($validator->fails()) {
            return response()->json([
                'message' => yellowMessage('error', 'fail'),
                'errors' => $validator->errors()->all()
            ], 422);
        }

        $result = UserService::show($id);

        if (!isset($result->id)) {
            return response()->json([
                'data' => $result
            ], 422);
        }

        return response()->json([
            'data' => $result
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = json_decode($request->getContent(),true);


        $validator = UserValidator::validate($data);

        if ($validator->fails()) {
            return response()->json([
                'message' => yellowMessage('error', 'fail'),
                'errors' => $validator->errors()->all()
            ], 422);
        }


        $result = UserService::update($data,$id);

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
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $validator = UserValidator::destroy($id);

        if ($validator->fails()) {
            return response()->json([
                'message' => yellowMessage('error', 'fail'),
                'errors' => $validator->errors()->all()
            ], 422);
        }

        $result = UserService::destroy($id);

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
