<?php

namespace App\Http\Livewire;

use App\Models\bancos;
use App\Models\cajas;
use App\Models\Category;
use App\Models\ClientesMostrador;
use App\Models\datos_facturacion;
use App\Models\metodo_pago;
use App\Models\presupuestos;
use App\Models\presupuestos_detalle;
use App\Models\Product;
use App\Models\productos_ivas;
use App\Models\productos_lista_precios;
use App\Models\productos_variaciones;
use App\Models\productos_variaciones_datos;
use App\Models\proveedores;
use App\Models\variaciones;
use App\Services\CartPresupuesto;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PresupuestoController extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $name,
        $barcode,
        $cost,
        $df,
        $price,
        $pago,
        $relacion_precio_iva,
        $metodos,
        $iva_producto,
        $proveedor_id,
        $orderby_id,
        $caja,
        $tipo_comprobante,
        $metodo_pago_nuevo,
        $stock, $alerts,
        $categoryid,
        $codigo,
        $monto_total,
        $search,
        $image,
        $product_id,
        $pageTitle,
        $componentName,
        $comercio_id,
        $data_Cart,
        $observaciones,
        $query_product,
        $products_s,
        $Cart,
        $deuda,
        $metodo_pago_elegido,
        $product,
        $total,
        $iva,
        $iva_general,
        $itemsQuantity,
        $cantidad,
        $carrito,
        $qty,
        $tipo_factura,
        $numero_factura,
        $query,
        $tipo_pago,
        $query_id,
        $vigencia,
        $tipo_presupuesto,
        $descuento;

    public $productos_variaciones_datos = [];
    protected $listeners = [
        'scan-code' => 'BuscarCode',
        'clearCart' => 'clearCart',
    ];
    private $pagination = 25;

    public function mount()
    {
        $this->pageTitle = 'Listado';
        $this->cantidad = 1;
        $this->pago = 0;
        $this->metodos = [];
        $this->tipo_pago = "Elegir";
        $this->iva_general = 0;
        $this->componentName = 'Productos';
        $this->categoryid = 'Elegir';
        $this->tipo_factura = 'Elegir';
        $this->iva_general = session('IvaGralPresupuesto');
        $this->metodo_pago_elegido = session('MetodoPagoPresupuesto');
        $this->tipo_pago_elegido = session('TipoPagoPresupuesto');


        if ($this->tipo_pago_elegido != null) {
            $this->tipo_pago = $this->tipo_pago_elegido;
        } else {
            $this->tipo_pago = "Elegir";
        }


        if ($this->metodo_pago_elegido != null) {
            $this->metodo_pago_elegido = $this->metodo_pago_elegido;
            $this->metodo_pago_nuevo = $this->metodo_pago_elegido;
        } else {
            $this->metodo_pago_elegido = 'Elegir';
        }


    }

    public function render()
    {

        if (!Auth::check()) {
            // Redirigir al inicio de sesión y retornar una vista vacía
            $this->redirectLogin();
            return view('auth.login');
        }

        if (Auth::user()->comercio_id != 1)
            $comercio_id = Auth::user()->comercio_id;
        else
            $comercio_id = Auth::user()->id;

        $this->df = $this->getDatosFacturacion($comercio_id); //ok
        $this->relacion_precio_iva = $this->df->relacion_precio_iva;

        $cart = new CartPresupuesto;
        $this->monto_total = $cart->totalAmount();
        $this->subtotal = $cart->subtotalAmount();
        $this->iva_total = $cart->totalIva();
        $this->descuento_total = $cart->totalDescuento();


        $products = Product::join('categories as c', 'c.id', 'products.category_id')
            ->join('seccionalmacens as a', 'a.id', 'products.seccionalmacen_id')
            ->join('proveedores as pr', 'pr.id', 'products.proveedor_id')
            ->select('products.*', 'c.name as category', 'a.nombre as almacen', 'pr.nombre as nombre_proveedor')
            ->where('products.comercio_id', 'like', $comercio_id)
            ->where('products.eliminado', 'like', 0)
            ->orderBy('products.name', 'asc')
            ->paginate($this->pagination);

        $metodo_pagos = bancos::where('bancos.comercio_id', 'like', $comercio_id)->get();
        $proveedores = proveedores::where('proveedores.comercio_id', 'like', $comercio_id)->where('eliminado', 0)->get();
        $this->metodos = metodo_pago::join('bancos', 'bancos.id', 'metodo_pagos.cuenta')->select('metodo_pagos.*', 'bancos.nombre as nombre_banco')->where('metodo_pagos.comercio_id', 'like', $comercio_id);

        if ($this->tipo_pago != 1 && $this->tipo_pago != 2 && $this->tipo_pago != null) {
            $this->metodos = $this->metodos->where('metodo_pagos.cuenta', 'like', $this->tipo_pago);
        }

        $this->metodos = $this->metodos->orderBy('metodo_pagos.nombre', 'asc')->get();

        return view('livewire.presupuesto.component', [
            'data' => $products,
            'proveedores' => $proveedores,
            'metodo_pago' => $metodo_pagos,
            'metodos' => $this->metodos,
            'categorias_fabrica' => Category::orderBy('name', 'asc')->get()
        ])
            ->extends('layouts.theme-pos.app')
            ->section('content');
    }

    protected function redirectLogin()
    {
        return \Redirect::to("login");
    }

    public function getDatosFacturacion($comercioId)
    {
        return datos_facturacion::where('comercio_id', $comercioId)
            ->first();
    }

    public function showCart()
    {
        return view('livewire.ventas-fabrica.cart')
            ->extends('layouts.theme.app')
            ->section('content');
    }

    public function ACash()
    {

        $this->pago = $this->monto_total;
    }

    public function Agregar()
    {

        //Aumentar id order by
        $this->orderby_id = $this->orderby_id + 1;


        if ($this->metodo_pago_elegido != 1 && $this->metodo_pago_elegido != null && $this->metodo_pago_elegido != "Elegir") {

            $metodo_pago = metodo_pago::find($this->metodo_pago_elegido);

            $this->recargo = $metodo_pago->recargo / 100;

        } else {

            $this->recargo = 0;

        }


        $cart = new CartPresupuesto;
        $items = $cart->getContent();


        if ($items->contains('id', $this->id_cart)) {

            $cart = new CartPresupuesto;
            $items = $cart->getContent();

            $product = Product::find($this->product_id);

            foreach ($items as $i) {
                if ($i['id'] === $product['id']) {

                    $cart->removeProduct($i['id']);

                    $product = array(
                        "id" => $i['id'],
                        "barcode" => $i['barcode'],
                        "product_id" => $i['product_id'],
                        "name" => $i['name'],
                        "iva" => $i['iva'],
                        "referencia_variacion" => $i['referencia_variacion'],
                        "descuento" => $i['descuento'],
                        "recargo" => $i['recargo'],
                        "price" => $i['price'],
                        "relacion_precio_iva" => $i['relacion_precio_iva'],
                        "qty" => $i['qty'] + 1,
                        "orderby_id" => $this->orderby_id,
                    );

                    $cart->addProduct($product);

                }
            }

            $this->resetUI();

            $this->emit('product-added', 'Producto agregado');

            $this->monto_total = $cart->totalAmount();
            $this->subtotal = $cart->subtotalAmount();
            $this->iva_total = $cart->totalIva();
            $this->recargo_total = $cart->totalRecargo();
            $this->descuento_total = $cart->totalDescuento();

            return back();

        } else {

            //dd($this->iva_agregar);

            $cart = new CartPresupuesto;

            $product = array(
                "id" => $this->id_cart,
                "product_id" => $this->product_id,
                "barcode" => $this->barcode,
                "name" => $this->name,
                "referencia_variacion" => $this->referencia_variacion,
                "price" => $this->price,
                "descuento" => 0,
                "recargo" => $this->recargo,
                "iva" => $this->iva_agregar,
                "qty" => $this->cantidad,
                "relacion_precio_iva" => $this->relacion_precio_iva,
                "orderby_id" => $this->orderby_id,
            );

            //dd($product);

            $cart->addProduct($product);

            $this->monto_total = $cart->totalAmount();
            $this->subtotal = $cart->subtotalAmount();
            $this->iva_total = $cart->totalIva();
            $this->recargo_total = $cart->totalRecargo();
            $this->descuento_total = $cart->totalDescuento();

            $this->resetUI();


            $this->emit('product-added', 'Producto agregado');

        }

        // dd($product);

    }

    public function resetUI()
    {
        $this->name = '';
        $this->cantidad = 1;
        $this->barcode = '';
        $this->cost = '';
        $this->price = '';
        $this->stock = '';
        $this->alerts = '';
        $this->search = '';
        $this->categoryid = 'Elegir';
        $this->image = null;
        $this->product_id = 0;

    }

    public function removeProductFromCart($product)
    {
        $cart = new CartPresupuesto;
        $cart->removeProduct($product);
        session()->flash("message", ["success", __("Curso eliminado del carrito correctamente")]);
        return back();
    }

    public function UpdateIva($product, $iva)
    {
        $cart = new CartPresupuesto;
        $items = $cart->getContent();


        foreach ($items as $i) {
            if ($i['id'] === $product) {

                $cart->removeProduct($product);

                $product = array(
                    "id" => $i['id'],
                    "product_id" => $i['product_id'],
                    "name" => $i['name'],
                    "barcode" => $i['barcode'],
                    "iva" => $iva,
                    "price" => $i['price'],
                    "referencia_variacion" => $i['referencia_variacion'],
                    "descuento" => $i['descuento'],
                    "recargo" => $i['recargo'],
                    "relacion_precio_iva" => $i['relacion_precio_iva'],
                    "qty" => $i['qty'],
                    "orderby_id" => $i['orderby_id'],
                );

                $cart->addProduct($product);

            }


        }

        $this->monto_total = $cart->totalAmount();
        $this->subtotal = $cart->subtotalAmount();
        $this->iva_total = $cart->totalIva();
        $this->recargo_total = $cart->totalRecargo();
        $this->descuento_total = $cart->totalDescuento();


        $this->emit('product-added', 'Iva modificado');
        return back();
    }

    public function UpdatePrice($product, $price)
    {
        $cart = new CartPresupuesto;
        $items = $cart->getContent();

        foreach ($items as $i) {
            if ($i['id'] === $product) {

                $cart->removeProduct($product);

                $product = array(
                    "id" => $i['id'],
                    "name" => $i['name'],
                    "barcode" => $i['barcode'],
                    "product_id" => $i['product_id'],
                    "iva" => $i['iva'],
                    "referencia_variacion" => $i['referencia_variacion'],
                    "relacion_precio_iva" => $i['relacion_precio_iva'],
                    "recargo" => $i['recargo'],
                    "descuento" => $i['descuento'],
                    "price" => $price,
                    "qty" => $i['qty'],
                    "orderby_id" => $i['orderby_id'],
                );

                $cart->addProduct($product);

            }


        }

        $this->monto_total = $cart->totalAmount();
        $this->subtotal = $cart->subtotalAmount();
        $this->iva_total = $cart->totalIva();
        $this->descuento_total = $cart->totalDescuento();


        $this->emit('product-added', 'Precio modificado');
        return back();
    }


    // escuchar eventos

    public function updateDescuento($descuento)
    {


        $cart = new CartPresupuesto;
        $items = $cart->getContent();

        foreach ($items as $i) {


            $cart->removeProduct($i['id']);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "barcode" => $i['barcode'],
                "iva" => $i['iva'],
                "product_id" => $i['product_id'],
                "referencia_variacion" => $i['referencia_variacion'],
                "recargo" => $i['recargo'],
                "relacion_precio_iva" => $i['relacion_precio_iva'],
                "descuento" => $descuento,
                "price" => $i['price'],
                "qty" => $i['qty'],
                "orderby_id" => $i['orderby_id'],
            );

            $cart->addProduct($product);


        }

        $this->monto_total = $cart->totalAmount();
        $this->subtotal = $cart->subtotalAmount();
        $this->iva_total = $cart->totalIva();
        $this->descuento_total = $cart->totalDescuento();


        $this->emit('product-added', 'Descuento modificado');
        return back();
    }

    public function updateDescuentoGral($descuento)
    {

        if (empty($descuento)) {
            $descuento = 0;
        }

        $descuento = str_replace(",", ".", $descuento);

        $descuento_gral_mostrar = $descuento;
        session(['DescuentoGral' => $descuento]);

        $this->updateDescuento($descuento);


    }

    public function UpdateIvaGral(Product $product)
    {

        if ($this->iva_general != "Elegir") {

            session(['IvaGralPresupuesto' => $this->iva_general]);

            $cart = new CartPresupuesto;
            $items = $cart->getContent();


            foreach ($items as $i) {
                $cart->removeProduct($i['id']);

                $product = array(
                    "id" => $i['id'],
                    "product_id" => $i['product_id'],
                    "name" => $i['name'],
                    "barcode" => $i['barcode'],
                    "recargo" => $i['recargo'],
                    "relacion_precio_iva" => $i['relacion_precio_iva'],
                    "referencia_variacion" => $i['referencia_variacion'],
                    "descuento" => $i['descuento'],
                    "iva" => $this->iva_general,
                    "price" => $i['price'],
                    "qty" => $i['qty'],
                    "orderby_id" => $i['orderby_id'],
                );

                $cart->addProduct($product);

            }

            $this->monto_total = $cart->totalAmount();
            $this->subtotal = $cart->subtotalAmount();
            $this->iva_total = $cart->totalIva();
            $this->recargo_total = $cart->totalRecargo();
            $this->descuento_total = $cart->totalDescuento();


            $this->emit('product-added', 'Iva modificado');
            return back();


        }


    }

    public function updateQty($product, $qty)
    {
        $cart = new CartPresupuesto;
        $items = $cart->getContent();

        foreach ($items as $i) {
            if ($i['id'] === $product) {

                $cart->removeProduct($product);

                $product = array(
                    "id" => $i['id'],
                    "product_id" => $i['product_id'],
                    "name" => $i['name'],
                    "barcode" => $i['barcode'],
                    "relacion_precio_iva" => $i['relacion_precio_iva'],
                    "recargo" => $i['recargo'],
                    "referencia_variacion" => $i['referencia_variacion'],
                    "descuento" => $i['descuento'],
                    "iva" => $i['iva'],
                    "price" => $i['price'],
                    "qty" => $qty,
                    "orderby_id" => $i['orderby_id'],
                );

                $cart->addProduct($product);

            }


        }

        $this->monto_total = $cart->totalAmount();
        $this->subtotal = $cart->subtotalAmount();
        $this->iva_total = $cart->totalIva();
        $this->recargo_total = $cart->totalRecargo();
        $this->descuento_total = $cart->totalDescuento();


        $this->emit('product-added', 'Cantidad modificada');
        return back();
    }

    public function BuscarCode($barcode)
    {

        if (Auth::user()->comercio_id != 1) {
            $comercio_id = Auth::user()->comercio_id;
        } else {
            $comercio_id = Auth::user()->id;
        }

        $record = Product::where('barcode', $barcode)
            ->where('comercio_id', $comercio_id)
            ->where('eliminado', 0)->orderBy('created_at', 'desc')->first();

        if ($record == null || empty($record)) {

            $this->emit('scan-notfound', 'El producto no está registrado');

            $this->codigo = '';

        } else {

            ////////// SI ES VARIACION //////////////////////


            if ($record->producto_tipo == "v") {

                $this->productos_variaciones_datos = productos_variaciones_datos::where('product_id', $record->id)->where('eliminado', 0)->get();

                $this->atributos = productos_variaciones::join('variaciones', 'variaciones.id', 'productos_variaciones.variacion_id')
                    ->select('variaciones.nombre', 'variaciones.id as atributo_id', 'productos_variaciones.referencia_id')
                    ->where('productos_variaciones.producto_id', $record->id)
                    ->get();

                $this->product_id = $record->id;
                $this->barcode = $record->barcode;

                $this->variaciones = variaciones::where('variaciones.comercio_id', $comercio_id)->get();

                $this->emit('variacion-elegir', $record->id);

                return $this->barcode;


            }


            $this->product_id = $record->id;
            $this->id_cart = $record->id . "-0";
            $this->name = $record->name;
            $this->referencia_variacion = 0;
            $this->barcode = $record->barcode;
            $this->cost = $record->cost;
            $this->stock = $record->stock;
            $this->categoryid = $record->categorias_fabrica_id;
            $this->image = null;

            // Setear IVA y precio

            $iva_producto = productos_ivas::where('product_id', $record->id)->where('sucursal_id', $comercio_id)->first();
            $this->iva_producto = $iva_producto->iva ?? 0;
            $variacion = 0;

            $this->GetPrecioProducto($barcode, $variacion, $comercio_id, $this->relacion_precio_iva, $this->iva_producto);

            //
            $this->emit('show-modal', 'Show modal!');

            $this->codigo = '';
        }

    }

    public function GetPrecioProducto($barcode, $variacion, $comercio_id, $relacion_precio_iva, $iva_producto)
    {

        $price = productos_lista_precios::join('products', 'products.id', 'productos_lista_precios.product_id')
            ->where('products.barcode', $barcode)
            ->where('products.comercio_id', $comercio_id)
            ->where('productos_lista_precios.referencia_variacion', $variacion)
            ->where('products.eliminado', 0)
            ->first();


        if ($relacion_precio_iva == 2) {
            $this->precio_original = $price->precio_lista;
            $this->iva_agregar = $iva_producto;
            $this->price = $price->precio_lista / (1 + $iva_producto);
            $this->relacion_precio_iva = $relacion_precio_iva;
        }
        if ($relacion_precio_iva == 1) {
            $this->precio_original = $price->precio_lista;
            $this->iva_agregar = $iva_producto;
            $this->price = $price->precio_lista;
            $this->relacion_precio_iva = $relacion_precio_iva;
        }

        if ($relacion_precio_iva == 0) {
            $this->precio_original = $price->precio_lista;
            $this->iva_agregar = $iva_producto ?? 0;
            $this->price = $price->precio_lista;
            $this->relacion_precio_iva = $relacion_precio_iva;
        }


    }

    public function BuscarCodeVariacion($barcode)
    {

        $this->product = explode('|-|', $barcode);

        $barcode = $this->product[0];
        $variacion = $this->product[1];

        if (Auth::user()->comercio_id != 1)
            $comercio_id = Auth::user()->comercio_id;
        else
            $comercio_id = Auth::user()->id;

        $record = Product::where('barcode', $barcode)->where('comercio_id', $comercio_id)->where('eliminado', 0)->first();

        $price = productos_lista_precios::join('products', 'products.id', 'productos_lista_precios.product_id')
            ->where('products.barcode', $barcode)
            ->where('products.comercio_id', $comercio_id)
            ->where('productos_lista_precios.referencia_variacion', $variacion)
            ->where('products.eliminado', 0)
            ->first();

        $this->product_id = $record->id;
        $this->referencia_variacion = $variacion;
        $this->name = $record->name;
        $this->barcode = $record->barcode;
        $this->cost = $record->cost;
        $this->price = $price->precio_lista;
        $this->stock = $record->stock;
        $this->categoryid = $record->categorias_fabrica_id;
        $this->image = null;

        $this->id_cart = $record->id . '-' . $variacion;


        $productos_variaciones_datos = productos_variaciones::join('variaciones', 'variaciones.id', 'productos_variaciones.variacion_id')
            ->select('variaciones.nombre')
            ->where('productos_variaciones.referencia_id', $variacion)
            ->get();

        $pvd = [];

        foreach ($productos_variaciones_datos as $pv) {

            array_push($pvd, $pv->nombre);

        }

        $var = implode(" ", $pvd);

        $this->name = $record->name . " - " . $var;

        $this->emit('variacion-elegir-hide', 'Show modal!');
        $this->emit('show-modal', 'Show modal!');

        $this->codigo = '';
    }

    public function AbrirModal($id)
    {
        $record = Product::find($id);

        $this->product_id = $record->id;
        $this->name = $record->name;
        $this->barcode = $record->barcode;
        $this->cost = $record->cost;
        $this->price = $record->price;
        $this->stock = $record->stock;
        $this->categoryid = $record->categorias_fabrica_id;
        $this->image = null;

        $this->emit('show-modal', 'Show modal!');
    }

    public function AgregarNroFactura()
    {
        $this->emit('show-modal2', 'Show modal!');
    }

    // reset values inputs

    public function Edit2($monto_total)
    {


        $this->monto_total = $monto_total;

        $this->emit('show-modal2', 'Show modal!');
    }

    // guardar venta

    public function orders()
    {
        $orders = auth()->user()->processedOrders();
        $suma = 0;
        return view('products.orders', compact('orders', 'suma'));
    }

    public function saveSale()
    {

        $cart = new CartPresupuesto;

        $this->iva_total = $cart->totalIva();

        if ($this->metodo_pago_elegido == "Elegir") {
            $this->emit('sale-error', 'DEBE ELEGIR LA FORMA DE PAGO');
            return;
        }

        if ($this->metodo_pago_elegido == null) {
            $this->emit('sale-error', 'DEBE ELEGIR LA FORMA DE PAGO');
            return;
        }

        if ($this->vigencia == "") {
            $this->emit('sale-error', 'DEBE ELEGIR EL PLAZO DE VIGENCIA DEL PRESUPUESTO');
            return;
        }

        if ($this->tipo_presupuesto == "Elegir" || $this->tipo_presupuesto == null) {
            $this->emit('sale-error', 'DEBE ELEGIR El TIPO DE PRESUPUESTO');
            return;
        }


        if ($this->query_id == null) {
            $this->emit('sale-error', 'DEBE ELEGIR UN CLIENTE');
            return;
        }

        if (Auth::user()->comercio_id != 1) {
            $comercio_id = Auth::user()->comercio_id;
        } else {
            $comercio_id = Auth::user()->id;
        }

        $this->caja = cajas::where('estado', 0)->where('comercio_id', $comercio_id)->max('id');

        $cart = new CartPresupuesto;

        if (Auth::user()->comercio_id != 1)
            $comercio_id = Auth::user()->comercio_id;
        else
            $comercio_id = Auth::user()->id;


        // Fecha inicial
        $fechaInicial = Carbon::parse($this->vigencia);

        // Fecha final
        $fechaFinal = Carbon::now();

        // Calcula la diferencia de días
        $diferenciaDias = $fechaInicial->diffInDays($fechaFinal);

        $diferenciaDias = $diferenciaDias + 1;

        DB::beginTransaction();

        try {

            $this->monto_total = $cart->totalAmount();
            $this->total = $cart->subtotalAmount();
            $this->iva_total = $cart->totalIva();
            $this->recargo_total = $cart->totalRecargo();
            $this->descuento_total = $cart->totalDescuento();

            $sale = presupuestos::create([
                'subtotal' => $cart->subtotalAmount(),
                'recargo' => $cart->totalRecargo(),
                'descuento' => $cart->totalDescuento(),
                'iva' => $cart->totalIva(),
                'total' => $cart->totalAmount(),
                'items' => $cart->totalCantidad(),
                'observaciones' => $this->observaciones,
                'cliente_id' => $this->query_id,
                'tipo_comprobante' => $this->tipo_comprobante,
                'recargo' => $this->recargo_total,
                'relacion_precio_iva' => $this->relacion_precio_iva,
                'descuento' => $this->descuento_total,
                'metodo_pago' => $this->metodo_pago_nuevo,
                'vigencia' => $diferenciaDias,
                'comercio_id' => $comercio_id,
                'tipo_presupuesto' => $this->tipo_presupuesto,
                'alicuota_descuento' => $this->descuento
            ]);

            if ($sale) {
                $items = $cart->getContent();

                foreach ($items as $item) {

                    $this->descuento_item = $item['price'] * ($item['descuento'] / 100);
                    $this->recargo_item = $item['price'] * $item['recargo'];

                    $this->iva_item = ($item['price'] + $this->recargo_item - $this->descuento_item) * $item['iva'];

                    $array_detalle = [
                        'producto_id' => $item['product_id'],
                        'precio' => $item['price'],
                        'nombre' => $item['name'],
                        'alicuota_recargo' => $item['recargo'],
                        'alicuota_descuento' => ($item['descuento'] / 100),
                        'descuento' => $this->descuento_item,
                        'recargo' => $this->recargo_item,
                        'barcode' => $item['barcode'],
                        'relacion_precio_iva' => $item['relacion_precio_iva'],
                        'referencia_variacion' => $item['referencia_variacion'],
                        'cantidad' => $item['qty'],
                        'iva' => $this->iva_item,
                        'alicuota_iva' => $item['iva'],
                        'presupuesto_id' => $sale->id,
                        'comercio_id' => $comercio_id,
                        'estado' => 0
                    ];

                    //dd($array_detalle);
                    presupuestos_detalle::create($array_detalle);
                }

            }


            DB::commit();

            $this->pago = 0;
            $this->deuda = 0;
            $this->query_id = null;
            $this->vigencia = '';
            $this->observaciones = '';
            $this->numero_factura = '';
            $this->tipo_factura = 'Elegir';
            session(['IvaGralPresupuesto' => 'Elegir']);
            $this->monto = 0;
            $this->metodo_pago_elegido = 'Elegir';
            $this->proveedor_id = 'Elegir';
            $this->tipo_presupuesto = 'Elegir';
            $cart->clear();
            $this->emit('sale-ok', 'presupuesto registrado con éxito');


        } catch (Exception $e) {
            DB::rollback();
            $this->emit('sale-error', $e->getMessage());
        }

    }

    public function clearCart()
    {
        //Reseteo id order by
        $this->orderby_id = 0;
        $cart = new CartPresupuesto;
        $cart->clear();
    }

    public function selectProduct()
    {
        $this->query_product = '';

        $this->resetProduct();
    }

    public function resetProduct()
    {
        $this->products_s = [];
    }

    public function updatedQueryProduct()
    {
        if (Auth::user()->comercio_id != 1) {
            $comercio_id = Auth::user()->comercio_id;
        } else {
            $comercio_id = Auth::user()->id;
        }


        $this->products_s = Product::where('comercio_id', 'like', $comercio_id)->where('eliminado', 0)->where(function ($query) {
            $query->where('name', 'like', '%' . $this->query_product . '%')->orWhere('barcode', 'like', $this->query_product . '%');
        })
            ->limit(5)
            ->get()
            ->toArray();


    }

    public function selectContact(ClientesMostrador $cliente)
    {

        $this->query = $cliente->nombre;
        $this->query_id = $cliente->id;

        session(['NombreCliente' => $this->query]);
        session(['IdCliente' => $this->query_id]);

        $this->cliente = ClientesMostrador::find($this->query_id);

        if ($this->cliente->lista_precio != 0) {

            $this->emit('update-cliente-modal', $this->query_id);

        }

        $this->resetCliente();
    }

    public function resetCliente()
    {
        $this->contacts = [];
    }

    public function updatedQuery()
    {
        if (Auth::user()->comercio_id != 1)
            $comercio_id = Auth::user()->comercio_id;
        else
            $comercio_id = Auth::user()->id;

        $this->contacts = ClientesMostrador::where('nombre', 'like', '%' . $this->query . '%')
            ->where('comercio_id', 'like', $comercio_id)
            ->orWhere('comercio_id', 'like', 1)
            ->limit(8)
            ->get()
            ->toArray();
    }

    public function TipoPago($value)
    {

        session(['TipoPagoPresupuesto' => $value]);

        if ($value == 1) {
            $this->metodo_pago_elegido = 1;
            $this->MetodoPago($value);
        } else {
            $this->metodo_pago_elegido = "Elegir";
        }

    }

    public function MetodoPago($value)
    {

        if ($value == 'OTRO') {
            $this->emit('metodo-pago-nuevo-show', 'Sales');

            return;
        }

        $metodo_pago = metodo_pago::find($value);

        $this->recargo = $metodo_pago->recargo / 100;

        session(['MetodoPagoPresupuesto' => $value]);

        $this->metodo_pago_nuevo = $metodo_pago->id;

        $cart = new CartPresupuesto;
        $items = $cart->getContent();


        foreach ($items as $i) {

            $this->recargo = $metodo_pago->recargo / 100;

            $cart->removeProduct($i['id']);

            $product = array(
                "id" => $i['id'],
                "name" => $i['name'],
                "product_id" => $i['product_id'],
                "referencia_variacion" => $i['referencia_variacion'],
                "barcode" => $i['barcode'],
                "relacion_precio_iva" => $i['relacion_precio_iva'],
                "iva" => $i['iva'],
                "descuento" => $i['descuento'],
                "recargo" => $this->recargo,
                "price" => $i['price'],
                "qty" => $i['qty'],
                "orderby_id" => $i['orderby_id'],
            );

            $cart->addProduct($product);

        }

        $this->monto_total = $cart->totalAmount();
        $this->subtotal = $cart->subtotalAmount();
        $this->recargo_total = $cart->totalRecargo();
        $this->iva_total = $cart->totalIva();
        $this->descuento_total = $cart->totalDescuento();


    }

}
