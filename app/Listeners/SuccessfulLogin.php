<?php

namespace App\Listeners;

use App\Services\SuscripcionesService;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Carbon\Carbon;
use App\Traits\ZohoCRMTrait;
use Illuminate\Support\Facades\Log;

class SuccessfulLogin
{
    use ZohoCRMTrait;
    protected $suscripcionesService;


    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(SuscripcionesService $suscripcionesService)
    {
        $this->suscripcionesService = $suscripcionesService;
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        /* 

        Queda comentado, habría que hacer un JOB
        
        $this->suscripcionesService->updateAllSubscription(); 
        
        */


        if ($event->user->email_verified_at != null) {
            $event->user->last_login = Carbon::now();
            $event->user->cantidad_login = $event->user->cantidad_login + 1;
            $event->user->save();
        }



        if ($event->user->lead_soho_id != null) {
            $this->updateLeadFromUser($event->user->id);
        }
    }
}
