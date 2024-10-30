<?php

namespace App\Services;

use App\Models\ModulosSuscripcion;
use App\Models\planes_suscripcion;
use App\Models\Suscripcion;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SuscripcionesService
{
    protected $MPService;
    protected $usersService;
    protected $suscripcionControlService;

    public function __construct()
    {
        $this->MPService = new MPService();
        $this->usersService = new UsersService();
        $this->suscripcionControlService = new SuscripcionControlService();
    }

    public function getSuscripcionesPorVencer()
    {
        Log::info('SuscripcionesService - getSuscripcionesPorVencer - ');

        $sydate = date('Y-m-d');
        Log::info($sydate);
        $suscripciones = Suscripcion::whereRaw('DATE(proximo_cobro) = "' . $sydate.'"')->get();

        return $suscripciones;
    }


    public function cancelarSuscripcion($suscripcion_id)
    {
        Log::info('SuscripcionesService - cancelarSuscripcion - ');

        try {
            $preferenciaMP = $this->MPService->cancelPreapprovalPlan($suscripcion_id);

            $this->cancelSuscripcionFlaminco($suscripcion_id);


            return  $preferenciaMP;
        } catch (Exception $e) {
            Log::info('SuscripcionesService - cancelarSuscripcion - ' . $e->getMessage());
        }

        return false;
    }

    public function cancelarSuscripcionStatus($suscripcion_id)
    {
        $suscripcion = Suscripcion::where('suscripcion_id', $suscripcion_id)->first();
        $suscripcion->suscripcion_status = 'cancelada';
        $suscripcion->save();
    }

    public function cancelSuscripcionFlaminco($preaprovalPlanId)
    {
        Log::info('SuscripcionesService - cancelSuscripcionFlaminco');

        $this->cancelarSuscripcionStatus($preaprovalPlanId);

        $suscripcion = Suscripcion::where('suscripcion_id', $preaprovalPlanId)->first();

        $user = $this->usersService->cancelUserSuscripcion($suscripcion->user_id);

        $suscripcionData = [
            'user_id' => $suscripcion->user_id,
            'plan_id' => $suscripcion->plan_id,
            'payer_id' => $suscripcion->payer_id,
            'nombre_comercio' => $suscripcion->nombre_comercio,
            'init_point' => $suscripcion->init_point,
            'fecha_suscripcion' => $suscripcion->fecha,
            'monto_mensual' =>  $suscripcion->monto_mensual,
            'users_count' =>  $suscripcion->users_count,
            'users_amount' =>  $suscripcion->users_amount,
            'suscripcion_status' => $suscripcion->suscripcion_status,
            'plan_id_flaminco'  => $suscripcion->plan_id_flaminco,
            'external_reference' => $suscripcion->external_reference,
            'cobro_status' => $suscripcion->cobro_status,
            'suscripcion_id' => $suscripcion->suscripcion_id,
            'monto_plan' => $suscripcion->monto_plan,
            'proximo_cobro' => $suscripcion->proximo_cobro,
            'pagado' => 1,
            'action' => 'CANCELADA',
            'suscripcion_status' => 'cancelada',
            'cobro_status' => 'cancelado',
        ];

        $suscripcionControl = $this->suscripcionControlService->insert($suscripcionData);
    }


    public function actualizarSuscripcion($data)
    {
        Log::info('SuscripcionesService - actualizarSuscripcion - ');
        Log::info($data);

        try {

            $suscripcion_id = $data['suscripcion_id'];
            $suscripcion = Suscripcion::where('suscripcion_id', $suscripcion_id)->first();
            $plan_id = $suscripcion->plan_id;

            $planSuscripcion = planes_suscripcion::find($data['plan_suscripcion']);
            $user = Auth::user();

            $monto_plan = $planSuscripcion->monto;
            $monto = $planSuscripcion->monto;
            $monto_format = number_format($planSuscripcion->monto, 0, ',', '.');
            $descripcion = 'FLA-' . $user->id . ' | Plan ' . $planSuscripcion->id . ': ' . $planSuscripcion->nombre . ' ($' . $monto_format . ')';

            $users_amount = 0;
            if ($data['quantity'] > 0) {
                $users_amount = $data['quantity'] * floatval(config('app.USER_AMOUNT_VALUE'));
                $users_amount_format = number_format($users_amount, 0, ',', '.');
                $monto =  $monto + $users_amount;
                $descripcion = $descripcion . ' + ' . $data['quantity'] . ' usuarios/s ($' . $users_amount_format . ')';
            }

            $modulos_amount = 0;
            $modulos_id='';
            if (isset($data['modulos_id']) && $data['modulos_id']) {
                $modulos_id=$data['modulos_id'];
                $modulos_seleccionados = explode(',', $data['modulos_id']);

                foreach ($modulos_seleccionados as $modulo_id) {
                    $modulo = ModulosSuscripcion::find($modulo_id);
                    $modulo_amount_format = number_format($modulo->monto, 0, ',', '.');
                    $monto =  $monto + $modulo->monto;
                    $modulos_amount += $modulo->monto;
                    $descripcion = $descripcion . ' + Módulo: ' . $modulo->nombre . ' ($' . $modulo_amount_format . ')';
                }
            }

            $urlBase = config('app.APP_URL');
            $urlSuccess = $urlBase . '/mp-success';

            $preferenceData = [
                "reason" => $descripcion,
                'auto_recurring' => array(
                    'frequency' => 1,
                    'frequency_type' => 'months',
                    //'frequency_type' => 'days',
                    //'repetitions' => 12,
                    //'billing_day' => 8,
                    //'billing_day_proportional' => true,
                    /*
                'free_trial' => array(
                    'frequency' => 1,
                    'frequency_type' => "months",
                ),
                */
                    'transaction_amount' => floatval($monto),
                    'currency_id' => 'ARS',
                ),
                "back_url" =>  $urlSuccess,
            ];

            //$preferenciaMP = $this->MPService->getPresapprovalPlanMP($data);            
            $preferenciaMP = $this->MPService->updatePreapprovalPlan($plan_id, $preferenceData);
            Log::info('$preferenciaMP');
            Log::info($preferenciaMP);

            $dataSuscripcion =  [
                'preference' => $preferenciaMP['response'],
                'response' => $preferenciaMP['response'],
                'user_id' => $user->id,
                'nombre_comercio' => $suscripcion->nombre_comercio,
                'telefono' => $suscripcion->telefono,
                'proximo_cobro' => $suscripcion->proximo_cobro,
                'monto_mensual' =>  $monto,
                'monto_plan' =>  $monto_plan,
                'users_count' =>  $data['quantity'],
                'users_amount' =>  $users_amount,
                'plan_id_flaminco'  => $data['plan_suscripcion'],
                'modulos_id' =>  $modulos_id,
                'modulos_amount' =>  $modulos_amount,
                
                'suscripcion_status'  => 'activa',
                'cobro_status'  => 'pagada',
            ];

            if (in_array($preferenciaMP['status'], [200, 201])) {
                $this->actualizarSuscripcionFlaminco($dataSuscripcion);
            }
            return  $preferenciaMP;
        } catch (Exception $e) {
            Log::info('SuscripcionesService - actualizarSuscripcion - ' . $e->getMessage());
        }

        return false;
    }

    public function actualizarSuscripcionFlaminco($data)
    {
        
        //dd($data['proximo_cobro']);
        Log::info('SuscripcionesService - actualizarSuscripcionFlaminco');
        $sysdate = date("Y-m-d H:i:s");

        $sydateFormat = strtotime($data['proximo_cobro']);
        $sydateAddMonth = date("Y-m-d H:i:s", strtotime("+0 month", $sydateFormat));
        //$sydateAddMonth = date("Y-m-d H:i:s", strtotime("+1 month", $sydateFormat));
        $sydateAddMonthFormat = strtotime($sydateAddMonth);

        $suscripcionData = [
            'user_id' => $data['user_id'],
            'nombre_comercio' => $data['nombre_comercio'],
            'telefono' => $data['telefono'],
            'monto_mensual' =>  $data['monto_mensual'],
            'monto_plan' =>  $data['monto_plan'],
            'users_count' =>  $data['users_count'],
            'users_amount' => $data['users_amount'],
            'plan_id_flaminco'  => $data['plan_id_flaminco'],
            'modulos_id' => $data['modulos_id'],
            'modulos_amount' => $data['modulos_amount'],

            'fecha' => $sysdate,
            'suscripcion_status' => $data['suscripcion_status'],
            'cobro_status' => $data['cobro_status'],
            'proximo_cobro' => $sydateAddMonth,

            'plan_id' => $data['preference']['id'],
            'init_point' => $data['preference']['init_point'],
            'external_reference' => (isset($data['preference']['external_reference']) ? $data['preference']['external_reference'] : '-'),
        ];

        $suscripcion = $this->createOrUpdateSuscripcion($suscripcionData);

        $suscripcionData['suscripcion_id'] = '-';
        $suscripcionData['fecha_suscripcion'] = $sysdate;
        $suscripcionData['monto_plan'] = $data['monto_plan'];
        $suscripcionData['action'] = 'GENERADA';

        $this->suscripcionControlService = new SuscripcionControlService();
        $suscripcionControl = $this->suscripcionControlService->insert($suscripcionData);
    }

    public function createOrUpdateSuscripcion($param)
    {
        Log::info('SuscripcionesService - createOrUpdateSuscripcion -  ');

        $suscripcion = Suscripcion::where('user_id', $param['user_id'])->first();

        try {
            if ($suscripcion) {
                Log::info('SuscripcionesService - createOrUpdateSuscripcion - update ');
                $suscripcion =  $suscripcion->update($param);
            } else {
                $param['suscripcion_id'] = date("YmdHis");
                Log::info('SuscripcionesService - createOrUpdateSuscripcion - create ');
                $suscripcion = Suscripcion::create($param);
            }
            Log::info(json_encode($suscripcion));

            return $suscripcion;
        } catch (Exception $e) {
            Log::info('SuscripcionesService - createOrUpdateSuscripcion - exception ' . $e->getMessage());
            return false;
        }
    }
}
