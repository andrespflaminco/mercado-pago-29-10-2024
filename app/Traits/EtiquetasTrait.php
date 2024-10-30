<?php
namespace App\Traits;


// Trait


// Modelos en donde aparece etiquetas

use App\Models\gastos;
use App\Models\Product;



// Modelos de etiquetas 
use App\Models\etiquetas;
use App\Models\etiquetas_relacion;
use App\Models\colores;

// Otros

use Illuminate\Support\Facades\Storage;
use Notification;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

use App\Models\bancos;

trait EtiquetasTrait {

public $etiqueta_json, $forma_pago_json,$etiqueta_nombre,$etiqueta_id; 
public $etiqueta_buscar_nombre,$etiqueta_buscar_id,$etiquetas_filtro_excel, $etiquetas_gastos, $etiqueta, $nombre_etiqueta,$etiqueta_form,$etiquetas_filtro;

public $etiquetas_seleccionadas2 = [];

public function GetEtiquetas($comercio_id,$origen) {
    
     $etiquetas = etiquetas::where('etiquetas.comercio_id', $comercio_id)->where('origen',$origen)->where('eliminado',0)->get();
    
     return $etiquetas;
     

}

public function LimpiarEtiquetas($comercio_id,$origen){
    $etiquetas = etiquetas::where('comercio_id', $comercio_id)
            ->where('origen', $origen)
            ->where('eliminado', 0)
            ->get()
            ->map(function ($etiqueta) {
                return [
                    'id' => $etiqueta->nombre,
                    'text' => $etiqueta->nombre,
                ];
            });
            
    
            
    $this->emit('LimpiarEtiquetas', $this->etiqueta->toArray());
    
    return $etiquetas;
}

    public function GetEtiquetasJson($comercio_id, $origen)
    {
        return etiquetas::where('comercio_id', $comercio_id)
            ->where('origen', $origen)
            ->where('eliminado', 0)
            ->get()
            ->map(function ($etiqueta) {
                return [
                    'id' => $etiqueta->nombre,
                    'text' => $etiqueta->nombre,
                ];
            });
            
    $this->emit('etiquetasCargadas', $this->etiqueta->toArray());
    
    }

// Componente Livewire

/*
public function cargarEtiquetas()
{
    $this->etiqueta_gastos = $this->GetEtiquetasJson($comercio_id, "gastos");
    $this->emit('etiquetasCargadas', $this->etiqueta_gastos->toArray());
}
*/

public function BuscarEtiquetas ($value,$comercio_id,$origen) {
        
    // dd($value);
    $array = [];
    foreach ($value as $nombre) {
        $etiquetasSeleccionadas = etiquetas_relacion::where('nombre_etiqueta', $nombre)
            ->where('estado', 1)
            ->where('origen', $origen)
            ->where('comercio_id', $comercio_id)
            ->get();
    
        $array = array_merge($array, $etiquetasSeleccionadas->toArray());
    }
    
    // Eliminar duplicados por la clave 'relacion_id'
    $array = collect($array)->unique('relacion_id')->values()->all();    
    
    // Nuevo array para almacenar relacion_id únicos
    $relacionIdsArray = [];
    
    // Iterar sobre el array original
    foreach ($array as $item) {
        // Agregar el valor de relacion_id al nuevo array solo si no existe aún
        if (!in_array($item["relacion_id"], $relacionIdsArray)) {
            $relacionIdsArray[] = $item["relacion_id"];
        }
    }
    
    // Ahora $relacionIdsArray contiene los valores únicos de relacion_id
    //dd($relacionIdsArray);
    
    return $relacionIdsArray;    
        
}

public function GetEtiquetasEdit($relacion_id,$origen,$comercio_id){
        
        $this->GetEtiquetasJson($comercio_id, $origen);
        
        $etiquetasSeleccionadas = etiquetas_relacion::where('relacion_id', $relacion_id)
        ->where('estado', 1)
        ->where('origen',$origen)
        ->get();
        
        if($relacion_id != 0) {
        $a_pasar = $etiquetasSeleccionadas->pluck('nombre_etiqueta')
        ->toArray();
        } else {$a_pasar = [];}
        
        if($this->etiqueta_json != null) { $e_array = $this->etiqueta_json->toArray();} else {$e_array = [];}
        
        $this->emit('etiquetasCargadasEdit', $e_array,$a_pasar);
        
        if(isset($etiquetasSeleccionadas)) {
            
        foreach($etiquetasSeleccionadas as $e) {
            $array = [
                'id' => $e->etiqueta_id,
                'nombre' => $e->nombre_etiqueta 
                ];
                
            array_push($this->etiquetas_seleccionadas,$array);
        
        $comercio_id = $e->comercio_id;
        } 
        
        }
        if($relacion_id == 0) {$this->etiquetas_seleccionadas = [];}
        
        
}

public function UpdateEtiquetas($id_etiqueta,$nombre_etiqueta,$origen) {

    $etiqueta = etiquetas::find($id_etiqueta);

 	$etiqueta->update([
  	 'nombre' => $nombre_etiqueta
  	]);
  	
  	//dd($etiqueta);
          
    $etiquetasSeleccionadas = etiquetas_relacion::where('etiqueta_id', $etiqueta->id)
    ->where('estado', 1)
    ->where('origen',$origen)
    ->get();	
    
   // dd($etiquetasSeleccionadas);
    
    foreach($etiquetasSeleccionadas as $r) {
    $e = etiquetas_relacion::find($r->id);
    $e->update(['nombre_etiqueta' =>  $nombre_etiqueta]);
    $this->GetEtiquetasEdit($r->relacion_id,$origen,$etiqueta->comercio_id);
    $this->StoreUpdateEtiquetas($r->relacion_id,2,"gastos",$etiqueta->comercio_id);
    }
    
    
}

public function CreateEtiquetas($nombre,$comercio_id,$origen) {
        
$color_etiqueta = etiquetas::where('comercio_id',$comercio_id)->where('origen',$origen)->where('eliminado',0)->orderBy('id','desc')->first();
 
    if($color_etiqueta == null){ $color_etiqueta = "secondary";} else {$color_etiqueta = $color_etiqueta->color;}
//dd($color_etiqueta);   

    $color = colores::where('colores',$color_etiqueta)->first()->id; 
    $color_id = $color + 1;
    if(6 < $color_id) {
    $color_id = 1;    
    }
    
    $color_etiqueta = colores::find($color_id)->colores;
    
    $etiqueta_seleccionada = etiquetas::create([
           'nombre' => $nombre,
           'comercio_id' => $comercio_id,
           'origen' => $origen,
           'color' => $color_etiqueta
           ]); 
           
    return $etiqueta_seleccionada;
           
}


public function SetEtiquetasSeleccionadas($comercio_id,$Etiquetas,$origen)
{
        
    $this->etiquetas_seleccionadas = [];
        
    foreach($Etiquetas as $nombre) {
        
    $etiqueta_seleccionada = etiquetas::where('comercio_id',$comercio_id)
    ->where('nombre',$nombre)
    ->where('origen',$origen)
    ->where('eliminado',0)
    ->first();
        
    if($etiqueta_seleccionada == null) {
    $etiqueta_seleccionada = $this->CreateEtiquetas($nombre,$comercio_id,$origen);
    } 
    
    if (!$this->existeIdEtiqueta($etiqueta_seleccionada->id)) {
    array_push($this->etiquetas_seleccionadas, ['id' => $etiqueta_seleccionada->id, 'nombre' => $nombre] );
    }
        
    }
        
}
    
    
private function existeIdEtiqueta($id)
    {
        // Verificar si el ID ya existe en el array
        foreach ($this->etiquetas_seleccionadas as $etiqueta) {
            if ($etiqueta['id'] == $id) {
                return true;
            }
        }

        return false;
    }

    
    public function StoreUpdateEtiquetas($relacion_id,$accion,$origen,$comercio_id) {
        
        //dd($this->etiquetas_seleccionadas2);
             
        // Si $accion es 1 crea si es 2 actualiza
        
        if($accion == 1) {
        
        foreach($this->etiquetas_seleccionadas as $e){
    
        etiquetas_relacion::create([
           'relacion_id' => $relacion_id,
           'etiqueta_id' => $e['id'],
           'nombre_etiqueta' => $e['nombre'],
           'estado' => 1,
           'origen' => $origen,
           'comercio_id' => $comercio_id
            ]);
        }
        
            
        } 
        else {
        
        $etique = etiquetas_relacion::where('relacion_id', $relacion_id)->get();

        // Ids de las etiquetas encontradas en la consulta
        $idsEncontrados = $etique->pluck('etiqueta_id')->toArray();
        
        // Array de etiquetas
        $etiquetas = $this->etiquetas_seleccionadas;
        
        // Iterar sobre el resultado de la consulta
        foreach ($etique as $et) {
            // Verificar si la etiqueta_id está en el array de etiquetas
            $estado = in_array($et->etiqueta_id, array_column($etiquetas, 'id')) ? 1 : 0;
        
            // Actualizar el estado en la base de datos
            $et->update(['estado' => $estado]);
        }
        
        // Buscar ids en $etiquetas que no estén en $ep
        $nuevosIds = array_diff(array_column($etiquetas, 'id'), $idsEncontrados);
        
        //dd($nuevosIds);
        
        // Crear nuevas filas en la tabla etiquetas_productos
        foreach ($nuevosIds as $nuevoId) {
            $nuevaEtiqueta = null;
            foreach ($etiquetas as $etiqueta) {
                if ($etiqueta['id'] == $nuevoId) {
                    $nuevaEtiqueta = $etiqueta;
                    break;
                }
            }
        
            if ($nuevaEtiqueta) {
                etiquetas_relacion::create([
                    'relacion_id' => $relacion_id,
                    'comercio_id' => $comercio_id,
                    'origen' => $origen,
                    'etiqueta_id' => $nuevoId,
                    'nombre_etiqueta' => $nuevaEtiqueta['nombre'],
                    'estado' => 1 // O el valor que desees
                ]);
            }
        }
        }
        
           // Realizar la consulta para obtener las etiquetas
        $etiquetas = etiquetas_relacion::where('relacion_id', $relacion_id)->where('estado', 1)->get();
        
        // Obtener un array de los valores 'nombre_etiqueta'
        $nombres_etiquetas = $etiquetas->pluck('nombre_etiqueta')->toArray();
        
        // Realizar el implode
        $etiquetas_implode = implode(' | ', $nombres_etiquetas);
        
        // Imprimir o utilizar $etiquetas_implode según tus necesidades
        //dd($etiquetas_implode);
        
        if($origen == "gastos") {
        
        //dd($relacion_id);
        $gastos = gastos::find($relacion_id);
        
        $gastos->update([
            'etiquetas' => $etiquetas_implode
            ]);
            
        }
        
        if($origen == "productos") {
        
        //dd($relacion_id);
        $product = Product::find($relacion_id);
        
        $product->update([
            'etiquetas' => $etiquetas_implode
            ]);
            
        }
           
        $this->etiquetas_seleccionadas = [];    
        $this->emit("edit",[]);
            
    }
 
 
 
  public function DeleteEtiqueta($id_etiqueta)
	{
	    
	    $etiquetas = etiquetas::find($id_etiqueta);
	    
		$etiquetas->update([
			'eliminado' => 1
		]);

        return $etiquetas;
        
	}
	



}
