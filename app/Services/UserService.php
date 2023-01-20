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
use App\Models\LogSolicitation;
use App\Models\Solicitation;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{

    protected static $table = 'users';


    public function __construct()
    {
    }


    public static function all()
    {
        $result = DB::table(self::$table)
            ->select(DB::raw('users.id
            ,users.uuid
            ,users.name
            ,users.type
            ,users.email
            ,DATE_FORMAT(users.created_at,"%d/%m/%Y %H:%i:%s") as created_at
            ,DATE_FORMAT(users.updated_at,"%d/%m/%Y %H:%i:%s") as updated_at
            ,users.active'))
            ->get()
            ->toArray();


        return $result;
    }

    public static function insert($data)
    {

        $result = DB::table(self::$table)
            ->where('email', '=', $data['email'])
            ->count();

        if ($result != 0) {
            return yellowMessage('duplicate', 'fail');
        }


        $user_data = [
            'name' => $data['name'],
            'type' => $data['type'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ];

        try {

            $user_id = User::create($user_data)->id;

            if (!is_numeric($user_id)) {
                return yellowMessage('error', 'fail');
            }

        } catch (\PDOException $exception) {
            echo $exception->getMessage();
        } catch (MassAssignmentException $exception) {
            echo $exception->getMessage();
        } finally {
            return yellowMessage('create', 'success');
        }


    }

    public static function destroy($id)
    {

        try {

            $result = DB::table(self::$table)->where('id', '=', $id)->first();

            if (empty($result)) {
                return yellowMessage('not-found', 'fail');
            }
            DB::beginTransaction();

            LogSolicitation::where('user_id', $result->id)->delete();


            $solicitations = Solicitation::where('client_id', $result->id)->get();


            if (count($solicitations) > 0) {
                foreach ($solicitations as $solicitation) {
                    $details = DetailSolicitation::where('solicitation_id', $solicitation->id)->get();
                    if (count($details) > 0) {
                        foreach ($details as $detail) {
                            Storage::disk('public')->delete($detail->photo);
                            DetailSolicitation::where('id', $detail->id)->delete();

                        }
                    }
                    Solicitation::where('id', $solicitation->id)->delete();
                }
            }

            User::where('id', '=', $result->id)->delete();

            DB::commit();

            return 1;

        } catch (\PDOException $PDOException) {
            DB::rollBack();
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
            'active' => $data['active'],
        ];

        if (isset($data['password']) && !empty($data['password'])) {
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

    public static function count()
    {
        $result = User::where('type', '=', '2')->count();
        return $result;
    }

    public static function clients()
    {
        $result = User::all();
        return $result;
    }

}