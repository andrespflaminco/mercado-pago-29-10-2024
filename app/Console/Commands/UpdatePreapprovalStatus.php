<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;

use App\Exports\EtiquetasExport;
use App\Http\Livewire\SuscripcionController;
use Livewire\Component;
use Notification;
use App\Notifications\NotificarCambios;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Category;
use App\Models\descargas;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\MPService;
use App\Services\SuscripcionesService;
use Illuminate\Support\Facades\Log;

class UpdatePreapprovalStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:UpdatePreapprovalStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recorre la tabla suscripcions y consulta a la API de MP para luego actualizar los estados';
    protected $suscripcionesService;
    protected $mPService;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SuscripcionesService $suscripcionesService, MPService $mPService)
    {
        parent::__construct();
        $this->suscripcionesService = $suscripcionesService;
        $this->mPService = $mPService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        set_time_limit(0);

        /* plan_suscripcion 
quantity */


        /* getPresapprovalPlanMP */


        /* $data['suscripcion_id'];
        $data['plan_suscripcion']
         */

        $params['suscripcion_id'] = '4a9c9913fd8943f08e20c1cd0645f082';
        $params['plan_suscripcion'] = 1;
        $params['quantity'] = 0;
        $params['user'] = User::find(1);

        //$this->mPService->getPresapprovalPlanMP($params);
        $this->suscripcionesService->actualizarSuscripcion($params);
        exit;

        $this->suscripcionesService->updateAllSubscription();
        Log::alert('Fin del proceso');
    }
}
