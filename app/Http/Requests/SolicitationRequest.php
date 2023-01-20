<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class SolicitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [
            'client_id' => 'required',
            'type_card' => 'required',
        ];

        return $rules;
    }

    public function messages()
    {

        $messages = [
            'client_id.required' => 'Codigo do cliente é obrigatorio!',
            'type_card.required' => 'Tipo do cartão é obrigatorio!',
        ];

        return $messages;

    }
}
