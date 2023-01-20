<?php
/**
 * Created by PhpStorm.
 * User: Otoniel Oliveira otonielloliveira@gmail.com
 * Empresa: Yellow Sistemas
 * Date: 04/04/2019
 * Time: 18:10
 */

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DetailSolicitationValidator
{

    public static function rules()
    {
        return [
            'name.*' => 'required',
            'last_name.*' => 'required',
            'department.*' => 'required',
            'registration.*' => 'required',
            'photo.*' => 'required|image',
        ];
    }

    public static function message()
    {
        return [
            'name.*.required' => yellowMessage('name'),
            'last_name.*.required' => yellowMessage('last_name'),
            'department.*.required' => yellowMessage('department'),
            'registration.*.required' => yellowMessage('registration'),
            'photo.*.required' => yellowMessage('photo'),
            'photo.*.image' => yellowMessage('photo', 'image'),
        ];
    }

    public static function validate(Request $request)
    {

        $validator = false;
        $rules = array();
        if (empty($request->get('name')) || count($request->get('name')) != $request->get('count')) {
            $validator = true;
            $rules[] = yellowMessage('name');
        }

        if (empty($request->get('last_name')) || count($request->get('last_name')) != $request->get('count')) {
            $validator = true;
            $rules[] = yellowMessage('last_name');
        }

        if (empty($request->get('department')) || count($request->get('department')) != $request->get('count')) {
            $validator = true;
            $rules[] = yellowMessage('department');
        }

        if (empty($request->get('registration')) || count($request->get('registration')) != $request->get('count')) {
            $validator = true;
            $rules[] = yellowMessage('registration');
        }



        if (empty($request->file('photo')) || count($request->file('photo')) != $request->get('count')) {
            $validator = true;
            $rules[] = yellowMessage('photo');
        }else{
            $_validator = Validator::make(
                $request->all(), [
                'photo.*' => 'required|mimes:jpg,jpeg,png,bmp|max:20000'
            ],[
                    'photo.*.required' => 'Por favor carregue uma imagem',
                    'photo.*.mimes' => 'Apenas imagens jpeg, png e bmp são permitidas',
                    'photo.*.max' => 'Desculpa! O tamanho máximo permitido para uma imagem é 20MB',
                ]
            );

            if($_validator->fails()){
                return $_validator;
            }

        }



        if ($validator) {
            $error = ValidationException::withMessages($rules);
            return $error;
        }


        if (is_array($request)) {
            $validator = Validator::make($request, self::rules(), self::message());
        };

        if ($request instanceof Request) {
            $validator = Validator::make($request->all(), self::rules(), self::message());
        }

        return $validator;

    }

    public static function make($data = array(), $rule = array(), $message = array())
    {
        return Validator::make($data, $rule, $message);
    }

    public static function destroy($id)
    {
        $validator = null;


        $validator = Validator::make(['id' => $id], [
            'id' => 'required',
        ], [
            'required' => yellowMessage('id'),
        ]);

        return $validator;
    }

    public static function show($id)
    {
        $validator = null;


        $validator = Validator::make(['id' => $id], [
            'id' => 'required',
        ], [
            'required' => yellowMessage('id'),
        ]);

        return $validator;
    }


}