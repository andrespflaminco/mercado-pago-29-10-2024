<?php

namespace App\Services;

use App\Models\ModulosSuscripcion;
use App\Models\planes_suscripcion;
use App\Models\User;
use App\Models\Suscripcion;
use AziendeGlobal\LaravelMercadoPago\MP;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Traits\ZohoCRMTrait;

class MPService
{

    use ZohoCRMTrait;

    protected  $suscripcionControlService;
    protected  $suscripcionCobroService;
    protected  $usersService;

    public function __construct()
    {
        $this->suscripcionControlService = new SuscripcionControlService();
        $this->suscripcionCobroService = new SuscripcionesCobrosService();
        $this->usersService = new UsersService();
    }

    public function getPresapprovalPlanMP($params)
    {

        Log::info('MPService - getPresapprovalPlanMP');

        try {
            $planSuscripcion = planes_suscripcion::find($params['plan_suscripcion']);

            Log::alert(json_encode($planSuscripcion));

            $user = $params['user'];
            //$free_days = $params['free_days'];
            $free_days = 14;

            $urlBase = config('app.APP_URL');

            $sysdate = date("Y-m-d H:i:s");
            $sydateFormat = strtotime($sysdate);
            $sydateAddMonth = date("Y-m-d H:i:s", strtotime("+1 month", $sydateFormat));
            //$sydateAddMonth = date("Y-m-d H:i:s", strtotime("+1 month", $sydateFormat));
            $sydateAddMonthFormat = strtotime($sydateAddMonth);
            $monto_plan = $planSuscripcion->monto;
            $monto = $planSuscripcion->monto;
            $monto_format = number_format($planSuscripcion->monto, 0, ',', '.');
            $descripcion = 'FLA-' . $user->id . ' | Plan ' . $planSuscripcion->id . ': ' . $planSuscripcion->nombre . ' ($' . $monto_format . ')';
            $users_amount = 0;

            if ($params['quantity'] > 0) {
                $users_amount = $params['quantity'] * floatval(config('app.USER_AMOUNT_VALUE'));
                $users_amount_format = number_format($users_amount, 0, ',', '.');
                $monto =  $monto + $users_amount;
                $descripcion = $descripcion . ' + ' . $params['quantity'] . ' usuarios/s ($' . $users_amount_format . ')';
            }

            $modulos_amount = 0;
            if (isset($params['modulos_id']) && $params['modulos_id']) {
                $modulos_seleccionados = explode(',', $params['modulos_id']);

                foreach ($modulos_seleccionados as $modulo_id) {
                    $modulo = ModulosSuscripcion::find($modulo_id);
                    $modulo_amount_format = number_format($modulo->monto, 0, ',', '.');
                    $monto =  $monto + $modulo->monto;
                    $modulos_amount += $modulo->monto;
                    $descripcion = $descripcion . ' + Módulo: ' . $modulo->nombre . ' ($' . $modulo_amount_format . ')';
                }
            }

            $data = [
                'descripcion' => $descripcion,
                'url_logo' => $urlBase . '/assets/img/livewire_logo.png',
                'monto' => $monto,
                'url_success' => $urlBase . '/mp-success',
                'users_amount' => $users_amount,
                'modulos_amount' => $modulos_amount,
                'free_days' => $free_days,
                /* nuevos campos */
                'frequency' => $planSuscripcion->frequency,
                'frequency_type' => $planSuscripcion->frequency_type,
                'trial_frequency' => $planSuscripcion->trial_frequency,
                'trial_frequency_type' => $planSuscripcion->trial_frequency_type,
                'billing_day' => $planSuscripcion->billing_day,
                'billing_day_proportional' => $planSuscripcion->billing_day_proportional,
            ];

            $dataFormat = $this->formatgeneratePreapprovalPlanData($data);
            $data['data_format'] = $dataFormat;

            $dataPreference = $this->generatePreapprovalPlan($dataFormat);
            Log::alert('MP resultado');




            if (isset($dataPreference['status'])) {
                if ($dataPreference['status'] == 201) {
                    $data['status'] = $dataPreference['status'];
                    $data['preference_status'] = $dataPreference['status'];
                    $data['preference'] = $dataPreference['response'];
                    $data['user_id'] = $user->id;
                    $data['user_name'] = $user->name;
                    $data['user_telefono'] = $user->telefono;
                    $data['monto'] = $user->monto;
                    $data['monto_mensual'] = $monto;
                    $data['monto_plan'] =  $monto_plan;
                    //$data['users_amount'] = $users_amount;
                    $data['quantity'] = $params['quantity'];
                    $data['modulos_id'] = (isset($params['modulos_id']) ? $params['modulos_id'] : '');
                    $data['plan_suscripcion_id'] = $planSuscripcion->id;
                    $data['plan_suscripcion_monto'] = $planSuscripcion->monto;
                    $data['proximo_cobro'] = $sydateAddMonth;

                    /* nuevos campos planes_suscripcion */
                    if (isset($dataPreference['response']['auto_recurring']['free_trial'])) {

                        $data['frequency'] = $dataPreference['response']['auto_recurring']['frequency'];
                        $data['frequency_type'] = $dataPreference['response']['auto_recurring']['frequency_type'];

                        $data['billing_day'] = $dataPreference['response']['auto_recurring']['billing_day'] ?? null;
                        $data['billing_day_proportional'] = $dataPreference['response']['auto_recurring']['billing_day_proportional'] ?? false;

                        $data['trial_frequency'] = $dataPreference['response']['auto_recurring']['free_trial']['frequency'];
                        $data['trial_frequency_type'] = $dataPreference['response']['auto_recurring']['free_trial']['frequency_type'];
                    }
                    Log::alert('DATA');
                    //Log::alert($data);

                    return $data;
                } else {
                    $data['preference_status'] = $dataPreference['status'];
                }
            } else {
                $data['preference_status'] = 999;
            }

            return $data;
        } catch (Exception $e) {
            Log::info('MPService - getPresapprovalPlanMP - error -- ' . $e->getMessage());
        }
    }

    public function formatgeneratePreapprovalPlanData($params)
    {
        Log::info('MPService - formatgeneratePreapprovalPlanData');
        $free_days = $params['free_days'];

        $preferenceData = [
            "reason" => $params['descripcion'],
            'auto_recurring' => array(
                'frequency' => $params['frequency'],
                'frequency_type' => $params['frequency_type'],
                //'repetitions' => 12,                
                'billing_day_proportional' => $params['billing_day_proportional'],
                'transaction_amount' => floatval($params['monto']),
                'currency_id' => 'ARS',
            ),

            "back_url" => $params['url_success'],
        ];

        if ($params['billing_day']) {
            $preferenceData['auto_recurring']['billing_day'] = $params['billing_day'];
        }

        // Verificar si $free_days es mayor a 0
        if ($params['trial_frequency'] > 0) {
            // Agregar la configuracion de 'free_trial' al array 'auto_recurring'
            $preferenceData['auto_recurring']['free_trial'] = [
                'frequency' => $params['trial_frequency'],
                'frequency_type' => $params['trial_frequency_type'],
            ];
        }


        //Log::info(json_encode($preferenceData));
        Log::info($preferenceData);

        return $preferenceData;
    }


    public function getPresapprovalMP($params)
    {
        Log::info('MPService - getPresapprovalMP');

        $planSuscripcion = $params['plan_suscripcion'];
        $user = $params['user'];
        $urlBase = config('app.APP_URL');

        $sysdate = date("Y-m-d H:i:s");
        $sydateFormat = strtotime($sysdate);
        $sydateAddMonth = date("Y-m-d H:i:s", strtotime("+1 day", $sydateFormat));
        //$sydateAddMonth = date("Y-m-d H:i:s", strtotime("+1 month", $sydateFormat));
        $sydateAddMonthFormat = strtotime($sydateAddMonth);
        $monto = $planSuscripcion->monto;
        $monto_format = number_format($planSuscripcion->monto, 0, ',', '.');
        $descripcion = 'FLA-' . $user->id . ' | Plan ' . $planSuscripcion->id . ': ' . $planSuscripcion->nombre . ' ($' . $monto_format . ')';
        $users_amount = 0;

        if ($params['quantity'] > 0) {
            $users_amount = $params['quantity'] * floatval(config('app.USER_AMOUNT_VALUE'));
            $users_amount_format = number_format($users_amount, 0, ',', '.');
            $monto =  $monto + $users_amount;
            $descripcion = $descripcion . ' + ' . $params['quantity'] . ' usuarios/s ($' . $users_amount_format . ')';
        }

        $data = [
            'referencia_externa' => $descripcion,
            'descripcion' => $descripcion,
            'url_logo' => $urlBase . '/assets/img/livewire_logo.png',
            'monto' => $monto,
            'email' => $user->email,
            //'email' => 'user.'.$user->id.'@flaminco.com.ar',
            'url_success' => $urlBase . '/mp-success',
            'start_suscripcion' => str_replace('+00:00', '.000Z', gmdate('c', $sydateFormat)),
            'end_suscripcion' => str_replace('+00:00', '.000Z', gmdate('c', $sydateAddMonthFormat)),
            //'card_token' => 'xxx',   
            'users_amount' => $users_amount,
        ];

        $dataFormat = $this->formatgeneratePreapprovalData($data);
        $data['data_format'] = $dataFormat;

        $dataPreference = $this->generatePreapproval($dataFormat);

        if (isset($dataPreference['status'])) {
            if ($dataPreference['status'] == 201) {
                $data['preference_status'] = $dataPreference['status'];
                $data['preference'] = $dataPreference['response'];

                $suscripcionData = [
                    'user_id' => $user->id,
                    'suscripcion_id' => $data['preference']['id'],
                    'payer_id' => $data['preference']['payer_id'],
                    'payer_email' => $data['preference']['payer_email'],
                    'nombre_comercio' => $user->name,
                    'telefono' => $user->telefono,
                    'init_point' => $data['preference']['init_point'],
                    'fecha' => $sysdate,
                    'monto_mensual' => $planSuscripcion->monto,
                    'suscripcion_status' => "pending",
                    'plan_id_flaminco'  => $planSuscripcion->id,
                    'external_reference' => $data['preference']['external_reference'],
                    'proximo_cobro' => $sydateAddMonth,
                    'cobro_status' => "pending",
                ];

                $suscripcion = $this->createOrUpdateSuscripcion($suscripcionData);
            } else {
                $data['preference_status'] = $dataPreference['status'];
            }
        } else {
            $data['preference_status'] = 999;
        }

        return $data;
    }


    public function formatgeneratePreapprovalData($params)
    {
        Log::info('MPService - formatgeneratePreapprovalData');

        $preferenceData = [
            //"preapproval_plan_id" => "2c9380848f81302d018f873a1ded01d7",

            "external_reference" => $params['referencia_externa'],
            "payer_email" => $params['email'],
            "reason" => $params['descripcion'],

            //"card_token_id" => 'e3ed6f098462036dd2cbabe314b9de2a',  

            'auto_recurring' => array(
                'frequency' => 1,
                //'frequency_type' => //'months',
                'frequency_type' => 'days',
                'start_date' => $params['start_suscripcion'],
                'end_date' => $params['end_suscripcion'],
                'transaction_amount' => floatval($params['monto']),
                'currency_id' => 'ARS',
            ),

            "back_url" => $params['url_success'],
            //"status" => "authorized",
            "status" => "pending",
        ];

        //Log::info(json_encode($preferenceData));
        Log::info($preferenceData);

        return $preferenceData;
    }



    public function generatePreapproval($preaprovalData)
    {
        Log::info('MPService - generatePreapproval');

        $mercadoPago = null;
        $preference = null;

        try {
            $MP_APP_ID = config('app.MP_APP_ID');
            $MP_APP_SECRET = config('app.MP_APP_SECRET');
            $MP_INTEGRATOR_ID = config('app.MP_INTEGRATOR_ID');

            $mercadoPago = new MP(null, $MP_APP_ID, $MP_APP_SECRET, $MP_INTEGRATOR_ID);
            $preference = $mercadoPago->create_preapproval_payment($preaprovalData);
        } catch (\Exception $e) {
            Log::info('MercadoPagoService - generatePreapproval - Exception');
            Log::info($e->getMessage());
            session(['error_mp' => $e->getMessage()]);
            if ($e->getMessage() == 'invalid_token') {
                Log::info('MercadoPagoService - generatePreapproval - invalid_token');
            }
        }

        //Log::info(json_encode($preference));
        Log::info($preference);

        return $preference;
    }



    public function mercadoPagoSuccess($suscripcion_id)
    {
        Log::info('MPService - mercadoPagoSuccess');

        $MP_APP_ID = config('app.MP_APP_ID');
        $MP_APP_SECRET = config('app.MP_APP_SECRET');
        $MP_INTEGRATOR_ID = config('app.MP_INTEGRATOR_ID');

        try {

            $mercadoPago = new MP(null, $MP_APP_ID, $MP_APP_SECRET, $MP_INTEGRATOR_ID);
            $preference = $mercadoPago->get_preapproval_payment($suscripcion_id);

            if (!$preference || !isset($preference['status']) || !in_array($preference['status'], [200, 2001])) {
                Log::info('$preference - error ');

                $user = User::find(auth()->user()->id);
                $user->intento_pago = "RECHAZADO";
                $user->save();

                $this->updateLeadFromUser($user->id);

                return false;
            }

            $user = User::find(auth()->user()->id);
            $user->intento_pago = "COMPLETADO";
            if ($user->flujo == 1) {
                $user->autogestion = "COMPLETADO";
            }
            $user->save();

            $this->updateLeadFromUser($user->id);

            Log::info($preference);

            $plan_id = $preference['response']['preapproval_plan_id'];
            $collector_id = $preference['response']['collector_id'];
            $status = $preference['response']['status'];
            $status_detail = $preference['response']['reason'];
            $reason = $preference['response']['reason'];
            $payer_id = $preference['response']['payer_id'];
            $suscripcion_id = $preference['response']['id'];
            $date_created = $preference['response']['date_created'];
            $last_modified = $preference['response']['last_modified'];

            $suscripcion = Suscripcion::where('plan_id', $plan_id)->first();

            if ($suscripcion) {
                $sysdate = date("Y-m-d H:i:s");
                $sydateFormat = strtotime($sysdate);
                $sydateAddMonth = date("Y-m-d H:i:s", strtotime("+1 month", $sydateFormat));

                $suscripcion->proximo_cobro = $sydateAddMonth;
                $suscripcion->cobro_status = 'pagada';
                $suscripcion->suscripcion_status = 'activa';
                $suscripcion->suscripcion_id = $suscripcion_id;
                $suscripcion->payer_id = $payer_id;
                $suscripcion->external_reference = $reason;
                $suscripcion->save();

                $sysdate = date("Y-m-d H:i:s");

                if ($suscripcion->user) {
                    $user = $suscripcion->user;
                    $user->confirmed = true;
                    $user->confirmed_at = $sysdate;
                    $user->save();
                }

                $suscripcionData = [
                    'user_id' => $suscripcion->user_id,
                    'plan_id' => $suscripcion->plan_id,
                    'payer_id' => $suscripcion->payer_id,
                    'nombre_comercio' => $suscripcion->nombre_comercio,
                    'init_point' => $suscripcion->init_point,
                    'fecha_suscripcion' => $suscripcion->fecha,
                    'monto_mensual' =>  $suscripcion->monto_mensual,
                    'monto_plan' =>  $suscripcion->monto_plan,
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
                    'action' => 'PAGADA',
                    'collector_id' => $collector_id,
                    'status' => $status,
                    'status_detail' => $status_detail,
                    'description' => $reason,
                    'date_created' => $date_created,
                    'date_last_updated' => $last_modified,
                ];

                $suscripcionControl = $this->suscripcionControlService->insert($suscripcionData);
                $suscripcionCobro = $this->suscripcionCobroService->insert($suscripcionData);
            } else {
                return false;
            }
            Log::info(json_encode($suscripcion));

            return true;
        } catch (Exception $e) {
            Log::info('MPService - mercadoPagoSuccess - exception ' . $e->getMessage());
            return false;
        }
    }

    public function formatData($params)
    {
        Log::info('MPService - formatData');

        $preferenceData = [
            'items' => array([
                'id' => 1,
                'category_id' => 'services',
                'title' => $params['referencia_externa'],
                'description' => $params['descripcion'],
                'picture_url' => $params['url_logo'],
                'quantity' => 1,
                'currency_id' => 'ARS',
                'unit_price' => floatval($params['monto'])
            ]),

            "payer" => array(
                "name" => $params['nombre'],
                "surname" => $params['apellido'],
                "email" => $params['email'],
                "date_created" => "",
                "phone" => array(
                    "area_code" => $params['telefono_area'],
                    "number" => $params['telefono_nro']
                ),
                "identification" => array(
                    "type" => "",
                    "number" => $params['dni']
                ),
                "address" => array(
                    "street_name" => $params['domicilio_calle'],
                    "street_number" => $params['domicilio_nro'],
                    "zip_code" => ""
                )
            ),

            "back_urls" => array(
                "success" => $params['url_success'],
                "failure" => $params['url_failure'],
                "pending" => $params['url_pending']
            ),

            "auto_return" => "approved",
            "notification_url" => $params['url_notification'],
            "external_reference" =>  $params['referencia_externa'],
        ];

        if (isset($params['fee']) && $params['fee'] > 0) {
            $preferenceData["marketplace_fee"] = floatval($params['fee']);
        }

        //Log::info(json_encode($preferenceData));

        return $preferenceData;
    }


    public function getPreferenciaMP($params)
    {
        Log::info('MPService - getPreferenciaMP');

        $planSuscripcion = $params['plan_suscripcion'];
        $user = $params['user'];
        $urlBase = config('app.APP_URL');
        $descripcion = 'FLA-' . $user->id . ' | Plan: ' . $planSuscripcion->id . ' ' . $planSuscripcion->id;

        $data = [
            'referencia_externa' => $descripcion,
            'descripcion' => $descripcion,
            'url_logo' => $urlBase . '/assets/img/livewire_logo.png',
            'monto' => $planSuscripcion->monto,
            'nombre' => $user->name,
            'apellido' => '',
            'email' => $user->email,
            'telefono_area' => '',
            'telefono_nro' => $user->phone,
            'dni' => '',
            'domicilio_calle' => '',
            'domicilio_nro' => '',
            'url_success' => $urlBase . '/mp-success',
            'url_failure' => $urlBase . '/mp-failure',
            'url_pending' => $urlBase . '/mp-pending',
            'url_notification' => $urlBase . '/mp-notification',
            'start_suscripcion' => str_replace('+00:00', '.000Z', gmdate('c', strtotime('2013-05-07 18:56:57'))),
            'end_suscripcion' => str_replace('+00:00', '.000Z', gmdate('c', strtotime('2013-05-07 18:56:57'))),
            'card_token' => str_replace('+00:00', '.000Z', gmdate('c', strtotime('2013-05-07 18:56:57'))),

        ];

        $dataFormat = $this->formatData($data);
        $data['data_format'] = $dataFormat;

        $dataPreference = $this->generatePreferenciaPago($dataFormat);

        if (isset($dataPreference['status'])) {
            if ($dataPreference['status'] == 201) {
                $data['preference_status'] = $dataPreference['status'];
                $data['preference'] = $dataPreference['response'];
            } else {
                $data['preference_status'] = $dataPreference['status'];
            }
        } else {
            $data['preference_status'] = 999;
        }


        return $data;
    }

    public function generatePreferenciaPago($preferenceData)
    {
        Log::info('MPService - generatePreferenciaPago');

        $mercadoPago = null;
        $preference = null;

        try {
            $MP_APP_ID = config('app.MP_APP_ID');
            $MP_APP_SECRET = config('app.MP_APP_SECRET');
            $MP_INTEGRATOR_ID = config('app.MP_INTEGRATOR_ID');

            $mercadoPago = new MP(null, $MP_APP_ID, $MP_APP_SECRET, $MP_INTEGRATOR_ID);
            $preference = $mercadoPago->create_preference($preferenceData);

            //$preference = MP::create_preference($preferenceData);
        } catch (\Exception $e) {
            Log::info('MercadoPagoService - generatePreferenciaPago - Exception');
            Log::info($e->getMessage());
            session(['error_mp' => $e->getMessage()]);
            if ($e->getMessage() == 'invalid_token') {
                Log::info('MercadoPagoService - generatePreferenciaPago - invalid_token');
            }
        }

        Log::info(json_encode($preference));

        return $preference;
    }

    public function generatePreapprovalPlan($preaprovalPlanData)
    {
        Log::info('MPService - generatePreapprovalPlan');

        $mercadoPago = null;
        $preference = null;

        try {
            $MP_APP_ID = config('app.MP_APP_ID');
            $MP_APP_SECRET = config('app.MP_APP_SECRET');
            $MP_INTEGRATOR_ID = config('app.MP_INTEGRATOR_ID');

            $mercadoPago = new MP(null, $MP_APP_ID, $MP_APP_SECRET, $MP_INTEGRATOR_ID);
            $preference = $mercadoPago->create_preapproval_plan_payment($preaprovalPlanData);
        } catch (\Exception $e) {
            Log::info('MercadoPagoService - generatePreapprovalPlan - Exception');
            Log::info($e->getMessage());
            session(['error_mp' => $e->getMessage()]);
            if ($e->getMessage() == 'invalid_token') {
                Log::info('MercadoPagoService - generatePreapprovalPlan - invalid_token');
            }
        }

        //Log::info(json_encode($preference));
        Log::info($preference);

        return $preference;
    }

    public function cancelPreapprovalPlan($preaprovalPlanId)
    {
        Log::info('MPService - cancelPreapprovalPlan');

        $mercadoPago = null;
        $preference = null;

        try {
            $MP_APP_ID = config('app.MP_APP_ID');
            $MP_APP_SECRET = config('app.MP_APP_SECRET');
            $MP_INTEGRATOR_ID = config('app.MP_INTEGRATOR_ID');

            $mercadoPago = new MP(null, $MP_APP_ID, $MP_APP_SECRET, $MP_INTEGRATOR_ID);
            $preference = $mercadoPago->cancel_preapproval_payment($preaprovalPlanId);

            return $preference;
        } catch (\Exception $e) {
            Log::info('MercadoPagoService - cancelPreapprovalPlan - Exception');
            Log::info($e->getMessage());
            session(['error_mp' => $e->getMessage()]);
            if ($e->getMessage() == 'invalid_token') {
                Log::info('MercadoPagoService - cancelPreapprovalPlan - invalid_token');
            }
        }

        //Log::info(json_encode($preference));
        Log::info($preference);

        return $preference;
    }


    public function updatePreapprovalPlan($suscripcion_id, $preaprovalPlanData)
    {
        Log::info('MPService - updatePreapprovalPlan');

        $mercadoPago = null;
        $preference = null;

        try {

            $MP_APP_ID = config('app.MP_APP_ID');
            $MP_APP_SECRET = config('app.MP_APP_SECRET');
            $MP_INTEGRATOR_ID = config('app.MP_INTEGRATOR_ID');

            $mercadoPago = new MP(null, $MP_APP_ID, $MP_APP_SECRET, $MP_INTEGRATOR_ID);
            $preference = $mercadoPago->update_preapproval_plan_payment($suscripcion_id, $preaprovalPlanData);
        } catch (\Exception $e) {
            Log::info('MercadoPagoService - updatePreapprovalPlan - Exception');
            Log::info($e->getMessage());
            session(['error_mp' => $e->getMessage()]);
            if ($e->getMessage() == 'invalid_token') {
                Log::info('MercadoPagoService - updatePreapprovalPlan - invalid_token');
            }
        }

        //Log::info(json_encode($preference));
        Log::info($preference);

        return $preference;
    }


    public function createUserTest($data)
    {
        Log::info('MPService - createUserTest');

        $mercadoPago = null;
        $preference = null;

        try {
            $MP_APP_ID = config('app.MP_APP_ID');
            $MP_APP_SECRET = config('app.MP_APP_SECRET');
            $MP_INTEGRATOR_ID = config('app.MP_INTEGRATOR_ID');

            $mercadoPago = new MP(null, $MP_APP_ID, $MP_APP_SECRET, $MP_INTEGRATOR_ID);
            $preference = $mercadoPago->create_user_test($data);

            Log::info($preference);
        } catch (\Exception $e) {
            Log::info('MercadoPagoService - createUserTest - Exception');
            Log::info($e->getMessage());
            session(['error_mp' => $e->getMessage()]);
            if ($e->getMessage() == 'invalid_token') {
                Log::info('MercadoPagoService - createUserTest - invalid_token');
            }
        }

        //Log::info(json_encode($preference));
        Log::info($preference);

        return $preference;
    }

    public function createOrUpdateSuscripcion($param)
    {
        Log::info('MPService - createOrUpdateSuscripcion -  ');

        $suscripcion = Suscripcion::where('user_id', $param['user_id'])->first();

        try {
            if ($suscripcion) {
                Log::info('MPService - createOrUpdateSuscripcion - update ');
                $suscripcion =  $suscripcion->update($param);
            } else {
                $param['suscripcion_id'] = date("YmdHis");
                Log::info('MPService - createOrUpdateSuscripcion - create ');
                $suscripcion = Suscripcion::create($param);
            }
            Log::info(json_encode($suscripcion));

            return $suscripcion;
        } catch (Exception $e) {
            Log::info('MPService - createOrUpdateSuscripcion - exception ' . $e->getMessage());
            return false;
        }
    }


    public function CrearCheckoutPreference($checkoutData)
    {
        Log::info('MPService - CrearCheckout');

        $mercadoPago = null;
        $preference = null;

        try {
            $MP_APP_ID = config('app.MP_APP_ID');
            $MP_APP_SECRET = config('app.MP_APP_SECRET');
            $MP_INTEGRATOR_ID = config('app.MP_INTEGRATOR_ID');

            $mercadoPago = new MP(null, $MP_APP_ID, $MP_APP_SECRET, $MP_INTEGRATOR_ID);
            $preference = $mercadoPago->create_preference($checkoutData);
        } catch (\Exception $e) {
            Log::info('MercadoPagoService - CrearCheckout - Exception');
            Log::info($e->getMessage());
            session(['error_mp' => $e->getMessage()]);
            if ($e->getMessage() == 'invalid_token') {
                Log::info('MercadoPagoService - CrearCheckout - invalid_token');
            }
        }

        //Log::info(json_encode($preference));
        Log::info($preference);

        return $preference;
    }


    public function getPreapprovalBySuscriptionId($id)
    {
        /* get_preapproval_payment */
        Log::info('MPService - getPreapprovalBySuscriptionId');

        try {
            $MP_APP_ID = config('app.MP_APP_ID');
            $MP_APP_SECRET = config('app.MP_APP_SECRET');
            $MP_INTEGRATOR_ID = config('app.MP_INTEGRATOR_ID');

            $mercadoPago = new MP(null, $MP_APP_ID, $MP_APP_SECRET, $MP_INTEGRATOR_ID);
            $data_preapproval = $mercadoPago->get_preapproval_payment($id);
            return $data_preapproval;
        } catch (\Exception $e) {
            Log::info('MercadoPagoService - getPreapprovalBySuscriptionId - Exception');
            Log::info($e->getMessage());
            session(['error_mp' => $e->getMessage()]);
            if ($e->getMessage() == 'invalid_token') {
                Log::info('MercadoPagoService - getPreapprovalBySuscriptionId - invalid_token');
            }
            return false;
        }
    }

    function get_preapproval_payments_search($suscripcion)
    {

        $user = User::find($suscripcion->user_id);
        if ($user) {
            $preapproval_plan_id = $suscripcion->plan_id;
            $payer_email = $user->email;
        }

        Log::info('MPService - get_preapproval_payments_search - results');

        try {
            $MP_APP_ID = config('app.MP_APP_ID');
            $MP_APP_SECRET = config('app.MP_APP_SECRET');
            $MP_INTEGRATOR_ID = config('app.MP_INTEGRATOR_ID');

            $mercadoPago = new MP(null, $MP_APP_ID, $MP_APP_SECRET, $MP_INTEGRATOR_ID);

            $array_suscripcion = [
                'preapproval_plan_id' => $preapproval_plan_id,
                'payer_email' => $payer_email
            ];

            $data_preapproval = $mercadoPago->get_preapproval_payments_search($array_suscripcion);

            Log::alert($data_preapproval['response']['results']);


            return $data_preapproval['response']['results'];
        } catch (\Exception $e) {
            Log::info('MercadoPagoService - getPreapprovalBySuscriptionId - Exception');
            Log::info($e->getMessage());
            session(['error_mp' => $e->getMessage()]);
            if ($e->getMessage() == 'invalid_token') {
                Log::info('MercadoPagoService - getPreapprovalBySuscriptionId - invalid_token');
            }
            return false;
        }
    }
}
