<?php
/**
 * Created by PhpStorm.
 * User: Otoniel Oliveira otonielloliveira@gmail.com
 * Empresa: Yellow Sistemas
 * Date: 04/04/2019
 * Time: 18:10
 */

namespace App\Validator;


use Illuminate\Support\Facades\Validator;

class UserValidator
{

    public static function rules(){
        return [
            'name' => 'required',
            'type' => 'required',
            'email' => 'required',
        ];
    }

    public static function message(){
        return [
            'name.required' => yellowMessage('name'),
            'type.required' => yellowMessage('type'),
            'email.required' => yellowMessage('email'),
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

    public static function destroy($id)
    {
        $validator = null;


            $validator = Validator::make(['id'=>$id],[
                'id' => 'required',
            ],[
                'required' => yellowMessage('id'),
            ]);

        return $validator;
    }

    public static function show($id)
    {
        $validator = null;


        $validator = Validator::make(['id'=>$id],[
            'id' => 'required',
        ],[
            'required' => yellowMessage('id'),
        ]);

        return $validator;
    }




}