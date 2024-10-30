<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\ImportProducts;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
     public function boot()
     {
         
        Validator::extend('date_not_after_current', function ($attribute, $value, $parameters, $validator) {
        // Comparar la fecha ingresada con la fecha actual
        return strtotime($value) <= strtotime(now());
        });
        
        Validator::extend('is_mail', function ($attribute, $value, $parameters, $validator) {
        // valida el mail
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
        });
        
        Validator::extend('not_negative', function ($attribute, $value, $parameters, $validator) {
        return $value >= 0;
        });
        
        

         $this->commands([
             \App\Console\Commands\ImportProducts::class
         ]);

         $this->app->booted(function () {
             $schedule = $this->app->make(Schedule::class);
             $schedule->command('export:products')->everyMinute();
             $schedule->command('export:etiquetas')->everyMinute();
             $schedule->command('sinc:wc')->everyFiveMinutes();
        
        //     $schedule->command('export:etiquetas-excel')->everyMinute();
        //   $schedule->command('import:products')->everyMinute();     

             
        });
     }

}
