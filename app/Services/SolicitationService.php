<?php
/**
 * Created by PhpStorm.
 * User: Otoniel Oliveira otonielloliveira@gmail.com
 * Empresa: Yellow Sistemas
 * Date: 04/04/2019
 * Time: 17:35
 */

namespace App\Services;


use App\Models\Solicitation;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SolicitationService
{

    protected static $table = 'solicitations';

    protected static $primary = 'solicitations.id';


    public function __construct()
    {
    }


    public static function all($clientId = null)
    {


        $result = DB::table(self::$table)
            ->select(
                DB::raw('solicitations.id
                    ,solicitations.uuid
                    ,users.name
                    ,solicitations.type_card
                    ,solicitations.freight
                    ,DATE_FORMAT(solicitations.created_at,"%d/%m/%Y %H:%i:%s") as created_at
                    ,DATE_FORMAT(solicitations.updated_at,"%d/%m/%Y %H:%i:%s") as updated_at
                    ,solicitations.protocol
                    ,solicitations.note
                    ,solicitations.status'
                )
            )
            ->join('users', 'solicitations.client_id', '=', 'users.id')
            ->when($clientId, function ($query, $clientId) {
                return $query->where('client_id', $clientId);
            })->orderBy('solicitations.id', 'desc')
            ->get()
            ->toArray();


        return $result;
    }

    public static function insert(Request $request)
    {

        $data = $request->all();

        $solicitation_data = [
            'client_id' => $data['client_id'],
            'type_card' => $data['type_card'],
            'freight' => $data['freight'],
            'note' => $data['note'] ?? "",
            'protocol' => $data['protocol'],
        ];

        try {

            DB::beginTransaction();
            $solicitation_id = Solicitation::create($solicitation_data)->id;


            DetailSolicitationService::insert($request, $solicitation_id);

            LogSolicitationService::insert([
                'solicitation_id' => $solicitation_id,
                'user_id' => $data['client_id'],
                'name' => 'Nova solicitação',
                'note' => descLogSolicitation('insert')
            ]);

            DB::commit();

        } catch (\PDOException $exception) {
            DB::rollBack();
            echo $exception->getMessage();
        } catch (MassAssignmentException $exception) {
            DB::rollBack();
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

            DetailSolicitationService::destroy($id);
            $result = Solicitation::where('id', '=', $id)->delete();

            LogSolicitationService::insert([
                'solicitation_id' => $id,
                'user_id' => Auth::id(),
                'name' => 'Exclusão solicitação',
                'note' => descLogSolicitation('delete')
            ]);

            return $result;

        } catch (\PDOException $PDOException) {
            return $PDOException->getMessage();
        }


    }

    public static function show($id)
    {
        try {
            DB::connection()->enableQueryLog();
            $result = DB::table(self::$table)
                ->select(
                    DB::raw('solicitations.id
                    ,solicitations.uuid
                    ,solicitations.type_card
                    ,solicitations.freight
                    ,DATE_FORMAT(solicitations.created_at,"%d/%m/%Y %H:%i:%s") as created_at
                    ,DATE_FORMAT(solicitations.updated_at,"%d/%m/%Y %H:%i:%s") as updated_at
                    ,solicitations.protocol
                    ,solicitations.note
                    ,solicitations.status
                    ,detail_solicitations.name as detail_solicitationsName
                    ,detail_solicitations.last_name
                    ,detail_solicitations.department
                    ,detail_solicitations.registration
                    ,detail_solicitations.photo
                    ,users.name'
                    )
                )
                ->join('users', 'solicitations.client_id', '=', 'users.id')
                ->join('detail_solicitations', self::$primary, '=', 'detail_solicitations.solicitation_id')
                ->where('solicitations.uuid', $id)
                ->get();



            if (!isset($result[0]->id)) {
                return yellowMessage('not-found', 'fail');
            }


            LogSolicitationService::insert([
                'solicitation_id' => $result[0]->id,
                'user_id' => Auth::id(),
                'name' => 'Solicitação',
                'note' => descLogSolicitation('read')
            ]);


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
            ->where('uuid', '=', $id)
            ->first();

        if (empty($result)) {
            return yellowMessage('not-found', 'fail');
        } else {
            if ($result->status != 0) {
                return yellowMessage('duplicate_update', 'fail');
            }
        }


        $_data = [
            'status' => $data['status'],
            'protocol' => $data['protocol'],
            'user_id' => $data['user_id'],
        ];


        try {

            $user_id = Solicitation::where('uuid', $id)->update($_data);


            LogSolicitationService::insert([
                'solicitation_id' => $result->id,
                'user_id' => Auth::id(),
                'name' => 'Ataulização solicitação',
                'note' => descLogSolicitation('update', $result->status, $data['status'])
            ]);


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

    public static function counts($clientId = null)
    {

        $pendente = DB::table(self::$table)
            ->select(DB::raw('count(*) as qtd'))
            ->where('status', '=', 0)
            ->when($clientId, function ($query, $clientId) {
                return $query->where('client_id', $clientId);
            })
            ->first();

        $finalizado = DB::table(self::$table)
            ->select(DB::raw('count(*) as qtd'))
            ->where('status', '=', 1)
            ->when($clientId, function ($query, $clientId) {
                return $query->where('client_id', $clientId);
            })
            ->first();

        $clientes = UserService::count();

        $data = ['pendente' => $pendente->qtd, "finalizado" => $finalizado->qtd, 'cliente' => $clientes];

        return $data;
    }

    public static function report(Request $request)
    {
        $result = DB::table(self::$table)
            ->select(
                DB::raw('detail_solicitations.*
                    ,DATE_FORMAT(detail_solicitations.created_at,"%d/%m/%Y") as created_at
                    ,DATE_FORMAT(detail_solicitations.updated_at,"%d/%m/%Y") as updated_at'
                )
            )
            ->join('detail_solicitations', 'solicitations.id', '=', 'detail_solicitations.solicitation_id')
            ->where('solicitations.client_id', $request->get('client_id'))
            ->where('solicitations.status', 1)
            ->whereRaw('DATE(detail_solicitations.created_at) >= ? AND  DATE(detail_solicitations.created_at) <= ?',
                [$request->get('dtInicial'),
                    $request->get('dtFinal')])
            ->orderBy('solicitations.id', 'asc')
            ->get()
            ->toArray();

        return $result;
    }


}
