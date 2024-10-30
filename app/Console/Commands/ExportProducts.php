<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;

use App\Exports\ProductsExport;
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

class ExportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:products';

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
        $lockFile = storage_path('logs/reporte_handle.lock');
        $lockFileLifetime = 900;
        // Intentar obtener el bloqueo
        if ($this->acquireLock($lockFile, $lockFileLifetime)) {
            try {
               


           $reportes_excels = descargas::where('estado',0)->where('tipo','exportar_productos')->get();
           
           foreach($reportes_excels as $re) {
             $re->update(['estado' => 1]);  
           //$report_excels = descargas::find($re->id);
           
           //$report_excels->estado = 1;
           //$report_excels->save();    
           
           $reportName = $re->nombre . '.xlsx';
           $comercio_id = $re->comercio_id;
           $id_reporte = $re->id;
    
           $excel = Excel::store(new ProductsExport($comercio_id, $id_reporte), 'catalogos/'.$reportName);
           
           //   ENVIAR NOTIFICACION CUANDO ESTE LISTO PARA EXPORTAR    //
        
        	$esquema = User::find($re->user_id);
        
        	$notificacion = [
        	'titulo' => 'Excel de Catalogo',
        	'contenido' => 'Listo para Descargar'
        	];
        
        	Notification::sendNow($esquema, new NotificarCambios($notificacion));
           }
        } finally {
                    // Liberar el bloqueo
                    $this->releaseLock($lockFile);
                }
        } else {
            echo 'tarea en ejecucion';
        }
       
       

    }
    
    private function acquireLock($lockFile, $lifetime)
    {
        
        if (file_exists($lockFile)) {
            $fileModificationTime = filemtime($lockFile);
            if ((time() - $fileModificationTime) < $lifetime) {
                return false; // El archivo de bloqueo aún es válido
            } else {
                // El archivo de bloqueo ha expirado
                unlink($lockFile); // Eliminar el archivo de bloqueo
            }
        }
    
        // Intentar crear el archivo de bloqueo
        return touch($lockFile);
    }
    
    private function releaseLock($lockFile)
    {
        if (file_exists($lockFile)) {
            unlink($lockFile);
        }
    }
    
}
