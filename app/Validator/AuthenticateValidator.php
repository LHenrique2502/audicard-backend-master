<?php
/**
 * Created by PhpStorm.
 * User: Otoniel Oliveira otonielloliveira@gmail.com
 * Empresa: Yellow Sistemas
 * Date: 04/04/2019
 * Time: 09:32
 */

namespace App\Validator;


use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AuthenticateValidator extends Validator
{

    public static function rules(){
        return [
            'email' => 'required|email',
            'password' => 'required'
        ];
    }

    public static function message(){
        return [
            'email.required' => yellowMessage('email'),
            'password.required' => yellowMessage('password'),
        ];
    }

    public static function validate( $request){

        $validator = null;


        if(is_array($request)){
            $validator = Validator::make($request,self::rules(),self::message());
        };

        if( $request instanceof Request){
            $validator = Validator::make($request->all(),self::rules(),self::message());
        }

        return $validator;

    }

}