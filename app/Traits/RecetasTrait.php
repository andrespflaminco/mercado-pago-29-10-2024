<?php
namespace App\Traits;


// Trait


// Modelos


// services 

use App\Services\CartVariaciones;

// Otros

use Illuminate\Support\Facades\Storage;
use Notification;
use App\Notifications\NotificarCambios;
use Illuminate\Validation\Rule;
use DB;
use Intervention\Image\Facades\Image;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use Carbon\Carbon;

use App\Models\receta;
use App\Models\recetas_costos;
use App\Models\Product;
use App\Models\productos_variaciones_datos;
use App\Models\productos_lista_precios;

use App\Traits\ProduccionTrait;

trait RecetasTrait {

use ProduccionTrait;

public function ActualizarRecetaDeProductos($insumo_id, $referencia_variacion, $comercio_id)
{
    try {
        $recetas = $this->GetRecetasDelInsumo($insumo_id);

        if ($recetas != null) {
            foreach ($recetas as $receta) {
                $insumo = Product::find($insumo_id);
                if (!$insumo) {
                    \Log::error("Insumo no encontrado con ID: $insumo_id");
                }

                $r = receta::find($receta->id);
                if (!$r) {
                    \Log::error("Receta no encontrada con ID: $receta->id");
                }

                $relacion = $this->GetRelacionUnidadesMedida($r->unidad_medida, $insumo->unidad_medida);
                $costo_unitario = $insumo->cost / $insumo->cantidad;
                $relacion_cantidad = $receta->cantidad / $insumo->cantidad;
                $array_receta = ['costo_unitario' => $costo_unitario, 'relacion_medida' => $relacion, 'relacion_cantidades' => $relacion_cantidad];

                $receta->update($array_receta);

                $this->CreateOrUpdateCostosRecetas($receta->product_id, $receta->referencia_variacion, $comercio_id);
                $this->CreateOrUpdateCostosRecetasListas($receta->product_id, $receta->referencia_variacion, $comercio_id);
            }
        }
    } catch (\Exception $e) {
        // Manejo de errores
        \Log::error("Error al actualizar receta de productos: " . $e->getMessage());
        // O puedes retornar un mensaje de error o realizar otras acciones según tu necesidad
        throw $e; // opcionalmente volver a lanzar la excepción para que sea manejada por el controlador o middleware
    }
}

public function GetRecetasDelInsumo($insumo_id)
{
    try {
        return receta::where('recetas.insumo_id', $insumo_id)
            ->where('eliminado', 0)
            ->get();
    } catch (\Exception $e) {
        // Manejo de errores
        \Log::error("Error al obtener recetas del insumo: " . $e->getMessage());
        throw $e;
    }
}

public function CreateOrUpdateCostosRecetasDB($product_id, $referencia_variacion, $lista_id, $comercio_id, $cost, $rinde)
{
    try {
        $precio_lista = $cost / $rinde;

        $costo = recetas_costos::updateOrCreate(
            [
                'product_id' => $product_id,
                'referencia_variacion' => $referencia_variacion,
                'lista_id' => $lista_id,
                'comercio_id' => $comercio_id
            ],
            [
                'costo' => $precio_lista
            ]
        );

        return $costo;
    } catch (\Exception $e) {
        // Manejo de errores
        \Log::error("Error al crear o actualizar costos de recetas en DB: " . $e->getMessage());
        throw $e;
    }
}

public function CreateOrUpdateCostosRecetasListas($product_id, $referencia_variacion, $comercio_id)
{
    try {
        $costs = receta::where('recetas.product_id', $product_id)
            ->where('recetas.referencia_variacion', $referencia_variacion)
            ->where('lista_precios.tipo', 2)
            ->join('productos_lista_precios', 'recetas.insumo_id', '=', 'productos_lista_precios.product_id')
            ->join('lista_precios', 'lista_precios.id', 'productos_lista_precios.lista_id')
            ->select(
                'productos_lista_precios.lista_id',
                'recetas.product_id',
                'recetas.referencia_variacion',
                'recetas.rinde',
                receta::raw('
                    CASE 
                        WHEN recetas.eliminado = 0 THEN 
                            SUM(recetas.relacion_cantidades * recetas.relacion_medida * productos_lista_precios.precio_lista)
                        ELSE 0 
                    END AS cost
                ')
            )
            ->groupBy(
                'productos_lista_precios.lista_id',
                'recetas.product_id',
                'recetas.referencia_variacion',
                'recetas.rinde',
                'recetas.eliminado'
            )
            ->get();

        foreach ($costs as $cost) {
            $costo_receta_id = $this->CreateOrUpdateCostosRecetasDB($cost->product_id, $cost->referencia_variacion, $cost->lista_id, $comercio_id, $cost->cost, $cost->rinde);
            $costo_receta = recetas_costos::find($costo_receta_id->id);
            $productos_lista_precios = productos_lista_precios::where('product_id', $cost->product_id)
                ->where('referencia_variacion', $cost->referencia_variacion)
                ->where('lista_id', $cost->lista_id)
                ->first();

            if ($productos_lista_precios) {
                $costo = $costo_receta->costo ?? 0;
                $productos_lista_precios->update([
                    'precio_lista' => $costo
                ]);
            } else {
                \Log::error("Productos_lista_precios no encontrado para product_id: $cost->product_id, referencia_variacion: $cost->referencia_variacion, lista_id: $cost->lista_id");
            }
        }
    } catch (\Exception $e) {
        // Manejo de errores
        \Log::error("Error al crear o actualizar costos de recetas en listas: " . $e->getMessage());
        throw $e;
    }
}

public function CreateOrUpdateCostosRecetas($product_id, $referencia_variacion, $comercio_id)
{
    try {
        $cost = receta::where('product_id', $product_id)
            ->where('referencia_variacion', $referencia_variacion)
            ->select(
            //    'recetas.cantidad',
            //    'recetas.relacion_medida',
                'recetas.product_id',
                'recetas.referencia_variacion',
                'recetas.rinde',
                receta::raw('
                    (CASE 
                        WHEN recetas.eliminado = 0 THEN 
                            SUM(recetas.cantidad * recetas.costo_unitario * recetas.relacion_medida)
                        ELSE 0 
                    END) AS cost
                ')
            )
            ->groupBy(
                'recetas.product_id',
                'recetas.referencia_variacion',
                'recetas.rinde',
                'recetas.eliminado'
            //    'recetas.cantidad',
            //    'recetas.relacion_medida'
            )
            ->first();

        if ($cost) {
            $costo_receta_id = $this->CreateOrUpdateCostosRecetasDB($cost->product_id, $cost->referencia_variacion, 0, $comercio_id, $cost->cost, $cost->rinde);
            $costo_receta = recetas_costos::find($costo_receta_id->id);
            if ($referencia_variacion == 0) {
                $costo = $costo_receta->costo ?? 0;
                $product = Product::find($costo_receta->product_id);
                if ($product) {
                    $product->update([
                        "cost" => $costo_receta->costo
                    ]);
                } else {
                    \Log::error("Product not found with ID: $costo_receta->product_id");
                }
            } else {
                $productos_variaciones_datos = productos_variaciones_datos::where('product_id', $costo_receta->product_id)
                    ->where('referencia_variacion', $referencia_variacion)
                    ->first();
                if ($productos_variaciones_datos) {
                    $productos_variaciones_datos->update([
                        "cost" => $costo_receta->costo
                    ]);
                } else {
                    \Log::error("Productos_variaciones_datos no encontrado para product_id: $costo_receta->product_id, referencia_variacion: $referencia_variacion");
                }
            }
        }
    } catch (\Exception $e) {
        // Manejo de errores
        \Log::error("Error al crear o actualizar costos de recetas: " . $e->getMessage());
        throw $e;
    }
}


}
