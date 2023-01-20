<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\AuthenticateRequest;
use App\Validator\AuthenticateValidator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{


    // Iremos chamar a autenticação da API em todas
    // as funções desse Controller exceto no Login.
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['authenticate']]);
    }

    public function authenticate(Request $request)
    {

        $credentials = $request->only(['email', 'password']);

        $validator = AuthenticateValidator::validate($request);

        if ($validator->fails()) {
            return response()->json([
                'message' => yellowMessage('credential', 'fail'),
                'errors' => $validator->errors()->all()
            ], 422);
        }

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => yellowMessage('credential', 'fail')], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $currentUser = Auth::user();
        if($currentUser->active != 1){
            return response()->json(['error' => "Usuário desativado no sistema, entre em contato com adm!"], 401);
        }

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);

    }

    // Renovação de Token
    public function refresh()
    {
        $token = JWTAuth::getToken();
        $newToken = JWTAuth::refresh($token);
        return response()->json([
            'token' => $newToken
        ]);
    }


    // Retorna as informações da sessão atual
    public function me()
    {
        return response()->json(Auth::user());
    }


    // Invalida a sessão atual
    public function logout()
    {
        $token = JWTAuth::getToken();
        JWTAuth::invalidate($token);
        return response()->json([
            'status' => 'success'
        ]);
    }
}
