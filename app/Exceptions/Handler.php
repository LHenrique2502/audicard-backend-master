<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {

        // Not found exception handler
        if($exception instanceof NotFoundHttpException) {
            return response()->json([
                'error' => [
                    'description' => 'Invalid URI',
                    'messages' => []
                ]
            ], 404);
        }

        // Method not allowed exception handler
        if($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'error' => [
                    'description' => 'Method Not Allowed',
                    'messages' => []
                ]
            ], 405);
        }



        if($exception instanceof AuthenticationException ){
            return response()->json([
                'error' => [
                    'cod' => 411,
                    'description' => 'Por favor realizar o login novamente!',
                    'messages' => []
                ]
            ], 401);
        }

        if($exception instanceof InvalidArgumentException)
        {
            return response()->json([
                'error' => [
                    'cod' => 411,
                    'description' => 'Por favor realizar o login novamente!',
                    'messages' => []
                ]
            ], 401);
        }

        if(env('APP_DEBUG') == false){
            if($exception instanceof QueryException){
                return response()->json([
                    'error' => [
                        'cod' => 411,
                        'description' => 'Por favor realizar o login novamente!',
                        'messages' => []
                    ]
                ], 401);
            }
        }





        return parent::render($request, $exception);


    }


    protected function unauthenticated($request, AuthenticationException $exception)
    {

        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login'); //<----- Change this
    }
}
