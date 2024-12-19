<?php

namespace App\Console\Commands;

use App\Notifications\SuscripcionPorVencer as NotificationsSuscripcionPorVencer;
use App\Services\SuscripcionControlService;
use App\Services\SuscripcionesService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SuscripcionPorVencer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suscripcion:vencimiento';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Busca las suscripciones proximas a vencer e informa al usuario';

    protected $suscripcionesService;
    protected $suscripcionControlService;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        SuscripcionesService $suscripcionesService,
        SuscripcionControlService $suscripcionControlService
    ) {
        $this->suscripcionesService = $suscripcionesService;
        $this->suscripcionControlService = $suscripcionControlService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('SuscripcionPorVencer - start');

        $suscripciones =  $this->suscripcionesService->getSuscripcionesPorVencer();

        Log::info($suscripciones->count()); 

        foreach ($suscripciones as $suscripcion) {
            if ($suscripcion->user) {
                if ($suscripcion->user->confirmed && $suscripcion->user->confirmed_at) {
                //    Notification::sendNow($suscripcion->user, new NotificationsSuscripcionPorVencer());

                    $suscripcionData = [
                        'user_id' => $suscripcion->user_id,
                        'plan_id' => $suscripcion->plan_id,
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
                        'action' => 'POR VENCER',
                        'proceso_asociado' => 'SuscripcionPorVencer',
                    ];
                    $suscripcionControl = $this->suscripcionControlService->insert($suscripcionData);
                }
            }
        }

        Log::info('SuscripcionPorVencer - end');

    }
}
