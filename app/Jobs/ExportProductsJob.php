<?php

namespace App\Jobs;

use App\Imports\ProductsImport;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;



use App\Models\User;
use Notification;
use App\Notifications\NotificarCambios;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\importaciones;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Traits\ValidarImportJob;

use App\Exports\ProductsExport;

use App\Models\descargas;

class ExportProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use ValidarImportJob;

    protected $id_reporte,$filePath, $comercio_id, $columna, $nombre_archivo,$casa_central_id;

    public $tries = 1;
    public $timeout = 6000000;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id_reporte, $comercio_id,$casa_central_id)
    {
        $this->id_reporte = $id_reporte;
        $this->comercio_id_import = $comercio_id;
        $this->casa_central_id = $casa_central_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $comercio_id = $this->comercio_id;
        $id_reporte = $this->id_reporte;
        $casa_central_id = $this->casa_central_id;
        
        try {
        $re = descargas::find($id_reporte);
        $reportName = $re->nombre . '.xlsx';
        Excel::store(new ProductsExport($casa_central_id, $re->id), 'catalogos/' . $reportName);
        
        \Log::error("Se hizo el excel de la exportacion");
        
        $re->update(['estado' => 2]);  
        
        $esquema = User::find($re->user_id);
        
        $notificacion = [
        'titulo' => 'Excel de Catalogo',
        'contenido' => 'Listo para Descargar'
        ];
        
        Notification::sendNow($esquema, new NotificarCambios($notificacion));
        
        } catch (\Exception $e) {
            \Log::error('Error al procesar el trabajo: ' . $e);
            // 14-8-2024
            //$this->EscribirErrores($this->import_id,$filasTotales,$e,$comercio_id);
        }
    }
    
}
