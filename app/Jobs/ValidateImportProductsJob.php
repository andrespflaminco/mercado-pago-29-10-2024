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


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\importaciones;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Traits\ValidarImportJob;

use App\Jobs\ImportJob;

class ValidateImportProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use ValidarImportJob;

    protected $import_id,$filePath, $comercio_id, $columna, $nombre_archivo;
    
    public $tries = 3; // Increase number of attempts
    public $timeout = 3600; // Set a reasonable timeout in seconds (e.g., 1 hour)

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($importId, $filePath, $comercio_id,$columna, $nombre_archivo)
    {
        $this->import_id = $importId;
        $this->comercio_id_import = $comercio_id;
        $this->filePath = $filePath;
        $this->comercio_id = $comercio_id;
        $this->columna = $columna;
        $this->nombre_archivo = $nombre_archivo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $comercio_id = $this->comercio_id;
        $filePath = $this->filePath;
        $importId = $this->import_id;
        $columna = $this->columna;
        $nombre_archivo = $this->nombre_archivo;
        
        $filasTotales = $this->getDataImportJob($filePath,$importId);
        
        $validacion_error = [];
        $codigosVistos = []; // Array para almacenar los c√≥digos ya vistos
        
        try {

            
            for ($i = 0; $i < count($filasTotales); $i++) {
                
                var_dump($filasTotales[$i][$columna['codigo']]);
                
                $codigo = strtolower($filasTotales[$i][$columna['codigo']]);
                  // Validar si el c√≥digo ya existe en el array
               /*
                    if (in_array($codigo, $codigosVistos)) {
                        $error = 'FILA NRO ' . $i . '-> El codigo esta duplicado.';
                        $fe = $i;
                        array_push($validacion_error, $error);
                    } else {
                        // Si no existe, agregarlo al array de c√≥digos vistos
                        array_push($codigosVistos, $codigo);
                    }
                */
                
                $validacion_error = $this->HacerValidaciones($filasTotales[$i], $columna, $i, $validacion_error,$codigosVistos,$comercio_id);
                
                    var_dump('Filas: ' . $i .'/'. count($filasTotales));
                    
                    if ($i % 50 === 0 || (count($filasTotales) == ($i + 1) ) ) {
                        importaciones::where('id', $this->import_id)->update([
                          'proceso_validacion' => ($i + 1) . "/" . count($filasTotales) . "/procesando",
                          'estado' => 1
                        ]);  
                    }
                    
                    

            }

            if (!empty($validacion_error)) {
                $errores = json_encode($validacion_error);
                \Log::error('Error al importar: ' . $errores);
                importaciones::where('id', $this->import_id)->update([
                    'errores' => $errores
                ]);
                
            //    session()->flash('import_id', $this->import_id);
            } else {
                //  $filasTotales = $this->getDataImportJob($filePath,$importId);
                  ImportJob::dispatch($importId, $filasTotales, $this->comercio_id, $this->filePath, $this->nombre_archivo, 1,null,$this->columna);
                  return; // Add this line to stop further execution
            }
            

        } catch (\Exception $e) {
            \Log::error('Error al procesar el trabajo: ' . $e);
            // 14-8-2024
            $this->EscribirErrores($this->import_id,$filasTotales,$e,$comercio_id);
        }
    }
    
  public function getDataImportJob($path,$importId)
  {
    $this->filePath = $path;

    var_dump($path);
    
    $file = array_slice(glob($path), 0, 2)[0];

    $currentLocation = $file;

    $file_array = explode("/", $file);

    $file_archive = $file_array[2];

    $file_name = explode(".", $file_archive);

    $comercio_id = $this->comercio_id;

    $headings = (new HeadingRowImport)->toArray($file);

    $import = new ProductsImport();

    $rows = (Excel::toArray($import, $file))[0];
    $this->fila_error = [];
    
    $indexesToDelete = array_map(function ($index) {
      return $index - 2;
    }, $this->fila_error);

    $rows = array_map(function ($key, $row) use ($indexesToDelete) {
      return in_array($key, $indexesToDelete) ? null : $row;
    }, array_keys($rows), $rows);

    $rows = array_values(array_filter($rows));

    $this->totalRows = count($rows);

    importaciones::where('id', $importId)->update([
      'proceso_validacion' => 0 . "/" . $this->totalRows . "/procesando",
      'estado' => 1
    ]);   
    
    
    return $rows;

  }
  
  // 14-8-2024
public function EscribirErrores($import_id, $filasTotales, $e, $comercio_id) {
    // Actualizar el estado de la importaci®Æn y registrar los errores
    importaciones::where('id', $import_id)->update([
        'estado' => 3,
        'errores_bug' => $e
    ]);

    // Actualizar el progreso de la importaci®Æn
    $progreso = count($filasTotales) . "/" . count($filasTotales) . "/procesando";
    importaciones::where('id', $import_id)->update([
        'proceso' => $progreso,
        'proceso_validacion' => $progreso,
    ]);
}
}
