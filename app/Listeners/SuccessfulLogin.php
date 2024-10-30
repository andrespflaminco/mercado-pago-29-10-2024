<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Carbon\Carbon;
use App\Traits\ZohoCRMTrait;

class SuccessfulLogin
{
    use ZohoCRMTrait;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
     public function handle(Login $event)
     {
         if($event->user->email_verified_at != null){
         $event->user->last_login = Carbon::now();
         $event->user->cantidad_login = $event->user->cantidad_login + 1;
         $event->user->save();             
         }

         //
         
         if($event->user->lead_soho_id != null){
         $this->updateLeadFromUser($event->user->id);    
         }
         
     }
}
