<?php

namespace App\Services;

use App\Models\ModulosSuscripcion;
use App\Models\planes_suscripcion;
use App\Models\Suscripcion;
use App\Models\SuscripcionControl;
use Carbon\Carbon;
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
        $suscripciones = Suscripcion::whereRaw('DATE(proximo_cobro) = "' . $sydate . '"')->get();

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
            $modulos_id = '';
            if (isset($data['modulos_id']) && $data['modulos_id']) {
                $modulos_id = $data['modulos_id'];
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
                    'frequency_type' => $planSuscripcion->frequency_type,
                    //'repetitions' => 12,
                    'billing_day_proportional' => $planSuscripcion->billing_day_proportional,
                    'transaction_amount' => floatval($monto),
                    'currency_id' => 'ARS',
                ),
                "back_url" =>  $urlSuccess,
            ];

            if ($planSuscripcion->billing_day) {
                $preferenceData['auto_recurring']['billing_day'] = $planSuscripcion->billing_day;
            }

            if ($planSuscripcion->trial_frequency > 0) {
                // Agregar la configuracion de 'free_trial' al array 'auto_recurring'
                $preferenceData['auto_recurring']['free_trial'] = [
                    'frequency' => $planSuscripcion->trial_frequency,
                    'frequency_type' => $planSuscripcion->trial_frequency_type,
                ];
            }

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

    function getPreapprovalBySuscriptionId($id)
    {
        $preapproval_suscripcion = $this->MPService->getPreapprovalBySuscriptionId($id);

        return $preapproval_suscripcion;
    }

    function updateAllSubscription($suscripcion_id = null)
    {
        Log::info('SuscripcionesService - updateAllSubscription');
        $date = Carbon::now()->subDays(60);

        $suscripcions = Suscripcion::where('updated_at', '>=', $date);

        if ($suscripcion_id) {
            $suscripcions = $suscripcions->where('suscripcion_id',  $suscripcion_id );
        }

        $suscripcions =  $suscripcions->get();

        $preapproval_suscripcion = null;

        foreach ($suscripcions as $key => $suscripcion) {
            Log::info('SuscripcionesService - updateAllSubscription - '.$suscripcion->suscripcion_id);

            $preapproval_suscripcion = $this->MPService->getPreapprovalBySuscriptionId($suscripcion->suscripcion_id);
            Log::info($preapproval_suscripcion);

            if ($preapproval_suscripcion) {
                $data_preapproval = $this->setDataPreapprovalSuscription($preapproval_suscripcion);
                $update_preapproval  = $this->updateSuscripcion($data_preapproval, $suscripcion);
                $insert_suscripcion_control = $this->insertSuscripcionControl($data_preapproval, $suscripcion);
            }
        }

        return $preapproval_suscripcion;
    }

    function setDataPreapprovalSuscription($preapproval_suscripcion)
    {
        $response = array();

        $auto_recurring = $preapproval_suscripcion['response']['auto_recurring'] ?? null;
        $sumarized = $preapproval_suscripcion['response']['summarized'] ?? null;

        if (!is_null($auto_recurring))
            unset($preapproval_suscripcion['response']['summarized']);
        if (!is_null($preapproval_suscripcion))
            unset($preapproval_suscripcion['response']['auto_recurring']);

        $general = $preapproval_suscripcion['response'] ?? null;

        if (!is_null($general)) {
            $response = array_merge($general, $auto_recurring, $sumarized);
        }


        return $response;
    }

    function updateSuscripcion($data, $suscripcion)
    {
        $data = $this->convertFechas($data);

        $data_update = [

            'collector_id_mp' => $data['collector_id'] ?? null,
            'application_id_mp' => $data['application_id'] ?? null,
            'reason_mp' => $data['reason'] ?? null,
            'date_created_mp' => $data['date_created'] ?? null,
            'last_modified_mp' => $data['last_modified'] ?? null,
            'frequency_mp' => $data['frequency'] ?? null,
            'frequency_type_mp' => $data['frequency_type'] ?? null,
            'transaction_amount_mp' => $data['transaction_amount'] ?? null,
            'currency_id_mp' => $data['currency_id'] ?? null,
            'start_date_mp' => $data['start_date'] ?? null,
            'end_date_mp' => $data['end_date'] ?? null,
            'free_trial_mp' => $data['free_trial'] ?? null,
            'quotas_mp' => $data['quotas'] ?? null,
            'charged_quantity_mp' => $data['charged_quantity'] ?? null,
            'pending_charge_quantity_mp' => $data['pending_charge_quantity'] ?? null,
            'charged_amount_mp' => $data['charged_amount'] ?? null,
            'pending_charge_amount_mp' => $data['pending_charge_amount'] ?? null,
            'semaphore_mp' => $data['semaphore'] ?? null,
            'last_charged_date_mp' => $data['last_charged_date'] ?? null,
            'last_charged_amount_mp' => $data['last_charged_amount'] ?? null,
            'next_payment_date_mp' => $data['next_payment_date'] ?? null,
            'payment_method_id_mp' => $data['payment_method_id'] ?? null,
            'payment_method_id_secondary_mp' => $data['payment_method_id_secondary'] ?? null,
            'first_invoice_offset_mp' => $data['first_invoice_offset'] ?? null,
            'billing_day_proportional_mp' => $data['billing_day_proportional'] ?? null,
            'has_billing_day_mp' => $data['has_billing_day'] ?? null,
            'back_url_mp' => $data['back_url'] ?? null,
            'status_mp' => $data['status'] ?? null,

        ];

        if(isset($data['status']) && $data['status'] == 'authorized'){
            $data_update['suscripcion_status'] = 'activ';
            $data_update['cobro_status'] = 'pagad';
        }

        try {
            $suscripcion = $suscripcion->update($data_update);
            Log::info('SuscripcionesService - updateSuscripcion - $suscripcion ');

            return $suscripcion;
        } catch (\Throwable $th) {
            Log::alert($th);
            return false;
        }
    }

    function insertSuscripcionControl($data, $suscripcion)
    {
        $data = $this->convertFechas($data);

        $SuscripcionControl = SuscripcionControl::where('suscripcion_id',$data['id'])->orderBy('id','DESC')->first();

        if($SuscripcionControl){

            if($SuscripcionControl->last_charged_date_mp == $data['last_charged_date']){
                return null;
            }
        }

        $data_update = [

            'collector_id_mp' => $data['collector_id'] ?? null,
            'application_id_mp' => $data['application_id'] ?? null,
            'reason_mp' => $data['reason'] ?? null,
            'date_created_mp' => $data['date_created'] ?? null,
            'last_modified_mp' => $data['last_modified'] ?? null,
            'frequency_mp' => $data['frequency'] ?? null,
            'frequency_type_mp' => $data['frequency_type'] ?? null,
            'transaction_amount_mp' => $data['transaction_amount'] ?? null,
            'currency_id_mp' => $data['currency_id'] ?? null,
            'start_date_mp' => $data['start_date'] ?? null,
            'end_date_mp' => $data['end_date'] ?? null,
            'free_trial_mp' => $data['free_trial'] ?? null,
            'quotas_mp' => $data['quotas'] ?? null,
            'charged_quantity_mp' => $data['charged_quantity'] ?? null,
            'pending_charge_quantity_mp' => $data['pending_charge_quantity'] ?? null,
            'charged_amount_mp' => $data['charged_amount'] ?? null,
            'pending_charge_amount_mp' => $data['pending_charge_amount'] ?? null,
            'semaphore_mp' => $data['semaphore'] ?? null,
            'last_charged_date_mp' => $data['last_charged_date'] ?? null,
            'last_charged_amount_mp' => $data['last_charged_amount'] ?? null,
            'next_payment_date_mp' => $data['next_payment_date'] ?? null,
            'payment_method_id_mp' => $data['payment_method_id'] ?? null,
            'payment_method_id_secondary_mp' => $data['payment_method_id_secondary'] ?? null,
            'first_invoice_offset_mp' => $data['first_invoice_offset'] ?? null,

            'plan_id' => $data['preapproval_plan_id'] ?? null,

            'billing_day_proportional_mp' => $data['billing_day_proportional'] ?? null,
            'has_billing_day_mp' => $data['has_billing_day'] ?? null,
            'back_url_mp' => $data['back_url'] ?? null,
            'status_mp' => $data['status'] ?? null,

        ];

        $data_suscripcion = $suscripcion->toArray();

        $data_merge = array_merge($data_suscripcion, $data_update);

        try {
            $suscripcion_control = $this->suscripcionControlService->insert($data_merge);
            Log::info('SuscripcionesService - insertSuscripcionControl - $suscripcion_control ');

            return $suscripcion_control;
        } catch (\Throwable $th) {
            Log::alert($th);
        }
    }

    public function convertFechas($data)
    {


        if (isset($data['date_created']))
            $data['date_created'] = Carbon::parse($data['date_created'])->format('Y-m-d H:i:s');


        if (isset($data['last_modified']))
            $data['last_modified'] = Carbon::parse($data['last_modified'])->format('Y-m-d H:i:s');

        if (isset($data['start_date']))
            $data['start_date'] = Carbon::parse($data['start_date'])->format('Y-m-d H:i:s');

        if (isset($data['end_date']))
            $data['end_date'] = Carbon::parse($data['end_date'])->format('Y-m-d H:i:s');

        if (isset($data['last_charged_date']))
            $data['last_charged_date'] = Carbon::parse($data['last_charged_date'])->format('Y-m-d H:i:s');

        if (isset($data['next_payment_date']))
            $data['next_payment_date'] = Carbon::parse($data['next_payment_date'])->format('Y-m-d H:i:s');


        return $data;
    }
}
