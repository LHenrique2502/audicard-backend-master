<?php

if (!function_exists('DummyFunction')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function DummyFunction()
    {

    }
}


if (!function_exists('trans_fb')) {

    /**
     * Makes translation fall back to specified value if definition does not exist
     *
     * @param string $key
     * @param null|string $fallback
     * @param null|string $locale
     * @param array|null $replace
     *
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    function trans_fb(string $key, ?string $fallback = null, ?string $locale = null, ?array $replace = [])
    {
        if (app(Illuminate\Contracts\Translation\Translator::class)->has($key, $locale)) {
            return trans($key, $replace, $locale);
        }

        return $fallback;
    }

}


if (!function_exists('lang_required')) {

    function defined_lang(string $v){
        $lang['required'] = [
            'email' => 'O e-mail é obrigatório',
            'password' => 'A senha é obrigatória',
            'credential' => 'Credenciais inválidas',
            'name' => 'O nome é obrigatório',
            'id' => 'O id é obrigatório!',
            'type' => 'O tipo de usuário é obrigatório!',
            'type_card' => 'O tipo de cartão é obrigatório!',
            'last_name' => 'O último nome é obrigatório!',
            'photo' => 'Foto é obrigatório!',
            'department' => 'O departamento é obrigatório!',
            'registration' => 'A matricula é obrigatória!',
            'client_id' => 'Codigo do cliente é obrigatório',

        ];

        $lang['image'] = [
            'photo' => 'Formato da imagem incorreto!',
        ];

        $lang['fail'] = [
            'email' => '',
            'password' => '',
            'credential' => 'Credenciais inválidas',
            'duplicate'=> 'Registro já cadastrado no nosso sistema!',
            'error' => 'Ocorreu um erro na solicitação',
            'not-found' => 'Registro não localizado!',
            'duplicate_update' => 'Impossivel atualizar um registro já finalizado!',
        ];

        $lang['success'] = [
            'create' => 'Registro incluido com sucesso!',
            'delete' => 'Registro excluido com sucesso!',
            'edit' => 'Registro atualizado com sucesso!',
        ];


        return $lang[$v];

    }

    /**
     * @param string $key
     */

    function yellowMessage(string $key, ?string $typer = 'required')
    {
       return defined_lang($typer)[$key];
    }


}

if(!function_exists('json_return')){

    function jsonReturn(?array $data,$cod_page = 200){


        return response()->json($data,$cod_page);

    }
}

if(!function_exists('descLogSolicitation')){

    /**
     * @param string $type = insert|update|delete|read
     * @param null $statusAnt
     * @param null $statusNew
     * @param null $id
     */
    function descLogSolicitation($type = 'insert',$statusAnt = null,$statusNew = null)
    {
        $return = null;
        if($type == 'insert'){
            $return = "Criação de uma nova solicitação";
        }elseif($type == 'update'){
            $return = 'Atualização do status de('.$statusAnt.') para (' . $statusNew . ')';
        }elseif ($type == 'delete'){
            $return = 'Exclusão da solicitação';
        }else{
            $return = 'Leitura da solicitação';
        }

        return $return;
    }

}
