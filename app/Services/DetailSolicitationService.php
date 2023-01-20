<?php
/**
 * Created by PhpStorm.
 * User: Otoniel Oliveira otonielloliveira@gmail.com
 * Empresa: Yellow Sistemas
 * Date: 04/04/2019
 * Time: 17:35
 */

namespace App\Services;


use App\Models\DetailSolicitation;
use App\Models\Solicitation;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DetailSolicitationService
{

    protected static $table = 'detail_solicitations';

    protected static $primary = 'detail_solicitations.id';


    public function __construct()
    {
    }


    public static function all($solicitation_id)
    {


        $result = DB::table(self::$table)
            ->select(DB::raw('solicitation_id
            ,name
            ,last_name
            ,department
            ,DATE_FORMAT(created_at,"%d/%m/%Y %H:%i:%s") as created_at
            ,DATE_FORMAT(updated_at,"%d/%m/%Y %H:%i:%s") as updated_at
            ,registration
            ,photo
            ,users.name as users_name'
            ))
            ->join('users', self::$primary, '=', 'users.id')
            //->join('solicitations', self::$primary, '=', 'solicitations.id') Desabilidado para teste de como ficaria a query para os detalhes
            ->where('solicitation_id', '=', $solicitation_id)
            ->get()
            ->toArray();


        return $result;
    }


    public static function insert(Request $request,$solicitation_id)
    {

        $data = $request->all();

        try {


            foreach ($data['name'] as $key => $val){
                $detail_data = [
                    'solicitation_id' => $solicitation_id,
                    'name' => $data['name'][$key],
                    'last_name' => $data['last_name'][$key],
                    'department' => $data['department'][$key],
                    'registration' => $data['registration'][$key],

                ];

                if(isset($request->file('photo')[$key])){
                    $detail_data['photo'] = $request->file('photo')[$key]->store('public');
                }else{
                    $detail_data['photo'] = null;
                }

                DetailSolicitation::create($detail_data);
            }




        } catch (\PDOException $exception) {
            Solicitation::find($solicitation_id)->delete();
            echo $exception->getMessage();
        } catch (MassAssignmentException $exception) {
            Solicitation::find($solicitation_id)->delete();
            echo $exception->getMessage();
        } finally {
            return yellowMessage('create', 'success');
        }


    }

    public static function destroy($id)
    {

        try {

            $result = DetailSolicitation::where('solicitation_id', '=', $id)->delete();

            return $result;

        } catch (\PDOException $PDOException) {
            return $PDOException->getMessage();
        }


    }

    public static function show($id)
    {
        try {

            $result = DB::table(self::$table)
                ->select(DB::raw('users.*
                                ,DATE_FORMAT(users.created_at,"%d/%m/%Y %H:%i:%s") as created_at
                                ,DATE_FORMAT(users.updated_at,"%d/%m/%Y %H:%i:%s") as updated_at
                                ,users.active')
                )->where('uuid', '=', $id)->first();

            if (empty($result)) {
                return yellowMessage('not-found', 'fail');
            }

            unset($result->password);

            return $result;

        } catch (\PDOException $PDOException) {
            return $PDOException->getMessage();
        }
    }

    public static function store($data)
    {


        $result = DB::table(self::$table)->where('id', '=', $data['id'])->count();

        if ($result == 0) {
            return yellowMessage('not-found', 'fail');
        }


        $result = DB::table(self::$table)->where('id', '=', $data['id'])->update([
            'name' => $data['name'],
            'active' => $data['active']
        ]);


        if (!is_numeric($result)) {
            return yellowMessage('error', 'fail');
        }

        return yellowMessage('create', 'success');


    }

    public static function update($data, $id)
    {
        $result = DB::table(self::$table)
            ->where('email', '=', $data['email'])
            ->where('uuid', '<>', $id)
            ->count();

        if ($result != 0) {
            return yellowMessage('duplicate', 'fail');
        }


        $user_data = [
            'name' => $data['name'],
            'type' => $data['type'],
            'email' => $data['email'],
        ];

        if (isset($data['password'])) {
            $user_data["password"] = bcrypt($data['password']);
        }

        try {

            $user_id = User::where('uuid', $id)->update($user_data);


            if (!is_numeric($user_id)) {
                return yellowMessage('error', 'fail');
            }

        } catch (\PDOException $exception) {
            echo $exception->getMessage();
        } catch (MassAssignmentException $exception) {
            echo $exception->getMessage();
        } finally {
            return yellowMessage('edit', 'success');
        }
    }


}