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
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SuscripcionesService $suscripcionesService)
    {
        parent::__construct();
        $this->suscripcionesService = $suscripcionesService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->suscripcionesService->updateAllSubscription();
        Log::alert('Fin del proceso');
    }
}
