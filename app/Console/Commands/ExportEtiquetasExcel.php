<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;

use App\Exports\EtiquetasExport;
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

class ExportEtiquetasExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:etiquetasexcel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
     public function handle()
     {

       $reportes_excels = descargas::where('estado',0)->where('tipo','exportar_etiquetas_excel')->get();
       
       foreach($reportes_excels as $re) {
        
       $report_excels = descargas::find($re->id);
       
        $report_excels->estado = 1;
        $report_excels->save();

       $reportName = $re->nombre . '.xlsx';
       $comercio_id = $re->comercio_id;
       $id_reporte = $re->id;

       $excel = Excel::store(new EtiquetasExport($id_reporte), 'etiquetas/'.$reportName);
       
       var_dump($excel);
       
       $report_excels->estado = 2;
        $report_excels->save();
       
       //   ENVIAR NOTIFICACION CUANDO ESTE LISTO PARA EXPORTAR    //
    
    	$esquema = User::find($re->user_id);
    
    	$notificacion = [
    	'titulo' => 'Excel de Etiquetas',
    	'contenido' => 'Listo para Descargar'
    	];
    
    	Notification::sendNow($esquema, new NotificarCambios($notificacion));


       
       }

    }
}
