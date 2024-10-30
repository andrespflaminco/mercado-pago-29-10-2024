<?php
namespace App\Traits;

use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

use Carbon\Carbon;

// Modelos

use App\Models\productos_ivas;
use App\Models\User;
use App\Models\provincias;
use App\Models\paises;
use App\Models\promos;
use App\Models\promos_productos;
use App\Models\ColumnConfiguration;
use App\Models\historico_stock;
use App\Models\cajas;
use App\Models\hoja_ruta;
use App\Models\beneficios;
use App\Models\bancos;
use App\Models\nota_credito;
use App\Models\productos_variaciones_datos;
use App\Models\productos_variaciones;
use App\Models\productos_stock_sucursales;
use App\Models\productos_lista_precios;
use App\Models\variaciones;
use App\Models\detalle_compra_proveedores;
use App\Models\metodo_pago;
use App\Models\pagos_facturas;
use App\Models\datos_facturacion;
use App\Models\Sale;
use App\Models\ClientesMostrador;
use App\Models\Product;
use App\Models\sucursales;
use App\Models\lista_precios;
use App\Models\compras_proveedores;
use App\Models\wocommerce;
use App\Models\facturacion;
use App\Models\ecommerce_envio;
use App\Models\SaleDetail;


trait ComprasTrait {
    
    
}

