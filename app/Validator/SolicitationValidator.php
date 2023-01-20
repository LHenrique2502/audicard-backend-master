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

class SolicitationValidator
{

    public static function rules()
    {
        return [
            'client_id' => 'required',
            'type_card' => 'required',
        ];
    }

    public static function message()
    {
        return [
            'client_id.required' => yellowMessage('client_id'),
            'type_card.required' => yellowMessage('type_card'),
        ];
    }

    public static function validate($request)
    {

        $validator = null;


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