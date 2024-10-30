<?php

use App\Http\Controllers\ExportController;
use App\Http\Livewire\AsignarController;
use App\Http\Livewire\CashoutController;
use App\Http\Livewire\CategoriesController;
use App\Http\Livewire\CoinsController;
use App\Http\Livewire\SucursalesController;
use App\Http\Livewire\EtiquetasController;
use App\Http\Livewire\PresupuestoController;
use App\Http\Livewire\MovimientoStockController;
use App\Http\Livewire\MovimientoStockResumenController;
use App\Http\Livewire\RecordatorioController;
use App\Http\Livewire\AsistenteProduccionController;

use App\Http\Livewire\PresupuestoResumenController;

/* WOOCOMMERCE */
use App\Http\Livewire\WooCommerceController;
/* ECOMMERCE */
use App\Http\Livewire\EcommerceController;
use App\Http\Livewire\EcommerceAjustesController;
use App\Http\Livewire\EcommerceAccountController;
use App\Http\Livewire\EcommerceOrdersController;
use App\Http\Livewire\EcommerceCartController;
use App\Http\Livewire\EcommerceCuponesController;
use App\Http\Livewire\EcommerceBillingController;
use App\Http\Livewire\EcommerceConfigController;
use App\Http\Livewire\EcommerceEnviosController;
use App\Http\Livewire\EcommerceLoginController;
use App\Http\Livewire\EcommercethanksController;


use App\Http\Livewire\CobrosMPController;
use App\Http\Livewire\MiComercioController;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Livewire\FacturaController;


use App\Http\Livewire\ProduccionRecetasController;
use App\Http\Livewire\RecetasDetalleController;
use App\Http\Livewire\EditarRecetasController;
use App\Http\Livewire\ProduccionController;
use App\Http\Livewire\ProduccionNuevaController;


use App\Http\Livewire\AtributosController;
use App\Http\Livewire\MetodoPagoController;
use App\Http\Livewire\HistoricoStockController;
use App\Http\Livewire\HistoricoStockInsumosController;
use App\Http\Livewire\CajasController;
use App\Http\Livewire\CajasDetalleController;
use App\Http\Livewire\AsistenteStock;

use App\Http\Livewire\ListaPreciosController;


use App\Http\Livewire\ComprasController;

/* CUENTAS CORRIENTES */
use App\Http\Livewire\CtaCteProveedoresController;
use App\Http\Livewire\CtaCteProveedoresMovimientosController;
use App\Http\Livewire\CtaCteClientesController;
use App\Http\Livewire\CtaCteClientesMovimientosController;



use App\Http\Livewire\Component1;
use App\Http\Livewire\BancosController;
use App\Http\Livewire\DashController;
use App\Http\Livewire\GastosController;
use App\Http\Livewire\ImportController;
use App\Http\Livewire\ImportRecetasController;
use App\Http\Livewire\ImportVentasController;
use App\Http\Livewire\ImportInsumosController;
use App\Http\Livewire\ImportPreciosController;
use App\Http\Livewire\ImportStockController;
use App\Http\Livewire\ProveedoresController;
use App\Http\Livewire\PermisosController;
use App\Http\Livewire\PosController;
use App\Http\Livewire\VentaRapidaController;
use App\Http\Livewire\ReportsVentaRapidaController;
use App\Http\Livewire\PosAltoController;

use App\Http\Livewire\ProductsController;
use App\Http\Livewire\StockController;
use App\Http\Livewire\ProductsPriceController;
use App\Http\Livewire\ProductsAddController;
use App\Http\Livewire\ProductsAddedController;



use App\Http\Livewire\ComprasResumenController;

/* COMPRAS INSUMOS */
use App\Http\Livewire\ComprasResumenInsumosController;
use App\Http\Livewire\ComprasInsumosController;


use App\Http\Livewire\ReportsController;
use App\Http\Livewire\ReportsEcommerceController;
use App\Http\Livewire\RolesController;
use App\Http\Livewire\HojaRutaController;
use App\Http\Livewire\HojaRutaPedidoController;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\Dashboard2;
use App\Http\Livewire\Select2;
use App\Http\Livewire\UsersController;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\SeccionalmacenController;
use App\Http\Livewire\ReportsdetalleController;
use App\Http\Livewire\PaymentController;
use App\Http\Livewire\PaymentController2;
use App\Http\Livewire\InsumosController;
use App\Http\Livewire\PaymentPlansController;
use App\Http\Livewire\ClientesMostradorController;
use App\Http\Livewire\Select2AutoSearch;

use App\Http\Livewire\ComprarRegistroController;

Auth::routes(['verify' => true]); // deshabilitamos el registro de nuevos users
Auth::routes(['register' => true]);


Route::get('google-login', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('google-callback', function () {
  $googleUser = Socialite::driver('google')->user();

    $user = User::where('email', $googleUser->email)->where('external_id', $googleUser->id)->where('external_auth', 'google')->first();

    if ($user) {

    Auth::login($user);

    return redirect('/pos');

    } else {
        $user = User::create([
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'external_id' => $googleUser->id,
            'external_auth' => 'google',
            'comercio_id' => '1',
            'profile' => 'Comercio',
            'email_verified_at' => Carbon::now(),
            'password' => bcrypt('gmail'),
        ]);

        $user->syncRoles('Comercio');

        Auth::login($user);

        return redirect('/mi-comercio');
    }


});




Route::get('/', function () {
    return view('auth.login');
});


Route::get('/form', function () {
    return view('auth.form.component');
});


Route::get('ticket', function () {
    return view('pdf.ticket');
});


///////// ECOMMERCE /////////////////

Route::get('tienda/{slug}', EcommerceController::class);

Route::get('ecart/{slug}', EcommerceCartController::class);

Route::get('ecommerce-billing/{slug}', EcommerceBillingController::class);

Route::get('ecommerce-login/{slug}', EcommerceLoginController::class);

Route::get('ecommerce-gracias/{slug}', EcommerceThanksController::class);

Route::get('ecommerce-account/{slug}', EcommerceAccountController::class);

Route::get('ecommerce-orders/{slug}', EcommerceOrdersController::class);

Route::post('webhooks', EcommerceThanksController::class);

Route::get('webhooks/pay/{slug}', [EcommerceBillingController::class, 'pay'])->name('webhooks.pay');

Route::get('webhooks/pay/{slug}', [PaymentController2::class, 'pay'])->name('webhooks.pay');

Route::get('ecommerce-email/pdf/{id}/{email}/{slug}', [ExportController::class, 'emailPDFGracias']);



Route::post('store-login', [EcommerceLoginController::class, 'customLogin']);

///////// PAGOS /////////////////

Route::get('regist', PaymentController2::class);



Route::post('planes', PaymentController2::class);

Route::get('planes/pay', [PaymentController2::class, 'pay'])->name('planes.pay');





///////////////////////////////////////////////////////////////////



Route::get('payments2', CobrosMPController::class);


Route::get('/regist/approval', [PaymentController::class, 'approval'])->name('approval');
Route::get('/regist/cancelled', [PaymentController::class, 'cancelled'])->name('cancelled');
Route::post('/regist/pay', [PaymentController::class, 'pay'])->name('pay');



///////// PAGOS /////////////////

Route::get('planes/{plan}', PaymentPlansController::class);

Route::get('/regist/approval', [PaymentPlansController::class, 'approval'])->name('approval.plan');
Route::get('/regist/cancelled', [PaymentPlansController::class, 'cancelled'])->name('cancelled.plan');
Route::post('/planes/pay', [PaymentPlansController::class, 'pay'])->name('pay-plan');





Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->middleware('verified')->group(function () {

Route::get('ecommerce-config', EcommerceConfigController::class);

Route::get('ecommerce-cupones', EcommerceCuponesController::class);
Route::get('ecommerce-ajustes', EcommerceAjustesController::class);
Route::get('ecommerce-envios', EcommerceEnviosController::class);

Route::get('categories', CategoriesController::class);
Route::get('products', ProductsController::class);
Route::get('atributos', AtributosController::class);

Route::get('stock', StockController::class);

Route::get('asistente-produccion', AsistenteProduccionController::class);


Route::get('presupuesto', PresupuestoController::class);

Route::get('presupuesto-resumen', PresupuestoResumenController::class);

Route::get('products-price', ProductsPriceController::class);
Route::get('products-add', ProductsAddController::class);

Route::get('product-added/{id}', ProductsAddedController::class);

Route::get('insumos', InsumosController::class);
Route::get('coins', CoinsController::class);
Route::get('pos', PosController::class);
Route::get('venta-rapida', VentaRapidaController::class);
Route::get('reporte-venta-rapida', ReportsVentaRapidaController::class);

Route::get('pos-alto', PosAltoController::class);
Route::get('dash', DashController::class);
Route::get('dashboard', Dashboard::class);
Route::get('dashboard2', Dashboard2::class);


Route::group(['middleware' => ['role:Admin']], function () {
    Route::get('roles', RolesController::class);
    Route::get('permisos', PermisosController::class);
    Route::get('asignar', AsignarController::class);
});

Route::group(['middleware' => ['role:Comercio']], function () {
    Route::get('roles', RolesController::class);
    Route::get('permisos', PermisosController::class);
    Route::get('asignar', AsignarController::class);
});

    Route::get('users', UsersController::class);

    Route::get('clientes', ClientesMostradorController::class);
    Route::get('metodo-pago', MetodoPagoController::class);
    Route::get('hoja-ruta', HojaRutaController::class);
    Route::get('hoja-ruta-pedido', HojaRutaPedidoController::class);

    Route::get('autocomplete', 'ClientesMostradorController@autocomplete')->name('autocomplete');



    Route::get('cashout', CashoutController::class);
    Route::get('reports', ReportsController::class);
    Route::get('woocommerce', WooCommerceController::class);


    Route::get('movimiento-stock', MovimientoStockController::class);


    Route::get('movimiento-stock-resumen', MovimientoStockResumenController::class);


    Route::get('mi-comercio', MiComercioController::class);
    Route::get('sucursales', SucursalesController::class);

    Route::get('reports-ecommerce', ReportsEcommerceController::class);







    Route::get('import', ImportController::class);
    Route::get('import-ventas', ImportVentasController::class);
    
    Route::get('import-insumos', ImportInsumosController::class);
    Route::get('import-recetas', ImportRecetasController::class);

    Route::post('upload-products', [ImportController::class, 'uploadProducts']);

  Route::post('importar-wc', [ImportPreciosController::class, 'WocommerceUpdateProducts']);
    Route::get('import-precios', ImportPreciosController::class);
    Route::get('import-stock', ImportStockController::class);

    //reportes PDF
    Route::get('report/pdf/{usuarioSeleccionado}/{clienteId}/{f1}/{f2}', [ExportController::class, 'reportPDF']);
    Route::get('report/pdf/{usuarioSeleccionado}/{clienteId}', [ExportController::class, 'reportPDF']);




Route::get('report-factura/pdf/{id}', [ExportController::class, 'reportPDFFactura']);

Route::get('report-presupuesto/pdf/{id}', [ExportController::class, 'reportPDFPresupuesto']);

Route::get('etiquetas', EtiquetasController::class);
Route::get('etiquetas/pdf/{nombre_producto}/{precio}/{codigo}/{codigo_barra}/{fecha_impresion}/{size}/{producto_elegido}', [ExportController::class, 'Etiquetas']);

Route::get('ticket/{saleId}', [ExportController::class, 'Ticket']);

Route::get('pdf-zeta/{Uid}/{dateFrom}/{dateTo}', [ExportController::class, 'PDFZeta']);

Route::get('ticket-rapido/{saleId}', [ExportController::class, 'TicketRapido']);


Route::get('report-email/pdf/{id}/{email}', [ExportController::class, 'emailPDFFactura']);


Route::get('report-factura-rapido/pdf/{id}', [ExportController::class, 'PDFFacturaRapido']);


Route::get('report-email-rapido/pdf/{id}/{email}', [ExportController::class, 'emailPDFFacturaRapido']);

Route::get('estado-email/pdf/{id}/{email}/{estado}', [ExportController::class, 'emailPDFEstado']);


  Route::post('store-added', [ProductsAddedController::class, 'StorePrecios']);


//reportes EXCEL
    Route::get('report/excel/{usuarioSeleccionado}/{cliente}/{estado_pago}/{estado}/{metodo_pago}/{f1}/{f2}/{uid}', [ExportController::class, 'reporteExcel']);
    Route::get('report/excel/{usuarioSeleccionado}/{cliente}/{estado_pago}/{estado}/{metodo_pago}/{uid}', [ExportController::class, 'reporteExcel']);

    Route::get('report/excel-clientes/{uid}', [ExportController::class, 'reporteExcelClientes']);

    Route::get('report/excel-cajas/{cajaid}/{uid}', [ExportController::class, 'reporteExcelCaja']);

    //reportes EXCEL
    Route::get('report-producto/excel/{fecha}', [ExportController::class, 'reporteExcelProducto']);
    
    //reportes EXCEL
    Route::get('recetas/excel/{fecha}', [ExportController::class, 'reporteExcelRecetas']);


    //reportes LISTA DE PRECIOS
    Route::get('lista-precios/excel/{fecha}/{listaId}/{id_categoria}/{id_almacen}/{proveedor_elegido}', [ExportController::class, 'reporteExcelListaPrecios']);
    //reportes STOCK DE LA SUCURSAL
    Route::get('stock-sucursal/excel/{fecha}/{sucursalId}/{id_categoria}/{id_almacen}/{proveedor_elegido}', [ExportController::class, 'reporteExcelStock']);

    //reportes INSUMOS
    Route::get('insumos/excel/{fecha}', [ExportController::class, 'reporteExcelInsumos']);


    //reportes EXCEL
    Route::get('report-categoria/excel', [ExportController::class, 'reporteExcelCategorias']);



  //reportes EXCEL
  Route::get('report-remito/excel/{$saleId}', [ExportController::class, 'reporteExcelRemito']);



     //reportes excel detalle de productos vendidos
    Route::get('report-detalle/excel/{usuarioSeleccionado}/{ClienteSeleccionado}/{metodopagoSeleccionado}/{productoSeleccionado}/{categoriaSeleccionado}/{almacenSeleccionado}/{f1}/{f2}/{sucursal_id}/{uid}', [ExportController::class, 'reporteExcelDetalle']);

    //reportes excel produccion de productos
    Route::get('report-produccion/excel/{estadoSeleccionado}/{ClienteSeleccionado}/{metodopagoSeleccionado}/{productoSeleccionado}/{categoriaSeleccionado}/{almacenSeleccionado}/{f1}/{f2}', [ExportController::class, 'reporteExcelProduccion']);




    //reportes excel detalle de produccion
    Route::get('report-asistente/excel/{id_proveedor}/{type}/{buscar}', [ExportController::class, 'reporteExcelAsistente']);

  //reportes excel hoja de ruta
  Route::get('report-hoja-ruta/excel/{id_hoja_ruta}/{uid}', [ExportController::class, 'reporteExcelHojaRuta']);

    //Reporte detalle de productos vendidos
    Route::get('reportes-detalle', ReportsdetalleController::class);

    //Historico de stock
    Route::get('historico-stock', HistoricoStockController::class);
    Route::get('historico-stock-insumos', HistoricoStockInsumosController::class);


    // ALMACENES
    Route::get('almacenes', SeccionalmacenController::class);
    Route::get('recordatorio', RecordatorioController::class);


    // PROVEEDORES
    Route::get('proveedores', ProveedoresController::class);

    // PROVEEDORES
    Route::get('lista-precios', ListaPreciosController::class);

    // PRODUCCION
    Route::get('produccion', ProduccionController::class);
    Route::get('produccion-nueva', ProduccionNuevaController::class);
    Route::get('componentes_detalle/{product_id}', RecetasDetalleController::class);
    Route::get('componentes_editar/{product_id}', EditarRecetasController::class);
    Route::get('produccion_recetas', ProduccionRecetasController::class);
    //Asistente de compras stock
    Route::get('asistente_stock', AsistenteStock::class);

    Route::get('bancos', BancosController::class);

    //Gastos
    Route::get('gastos', GastosController::class);
    //Partes de los depositos
    Route::get('cajas', CajasController::class);


  Route::get('cajas-detalle/{cajaId}', [CajasDetalleController::class, 'render']);

    //Factura

  Route::get('factura/{ventaId}', [FacturaController::class, 'render']);

  Route::get('factura/pago/{ventaId}/{monto}', [FacturaController::class, 'GuardarPago']);

  Route::get('cambio-estado/{estado}/{ventaId}', [FacturaController::class, 'GuardarCambioEstado']);

  Route::get('cambio-hoja-ruta/{hoja_ruta}/{ventaId}', [FacturaController::class, 'HojaRutaElegida']);

Route::post('store-form', [FacturaController::class, 'GuardarPago']);

Route::post('store-form-modal', [FacturaController::class, 'GuardarPagoModal']);

Route::post('guardar-form-hoja-ruta', [FacturaController::class, 'GuardarHojaDeRuta']);



Route::get('compras-resumen', ComprasResumenController::class);

/* COMPRAS INSUMOS */

Route::get('compras-resumen-insumos', ComprasResumenInsumosController::class);
Route::get('compras-insumos', ComprasInsumosController::class);

// Ccuenta corriente //

Route::get('ctacte-clientes', CtaCteClientesController::class);
Route::get('movimientos-clientes/{id}', CtaCteClientesMovimientosController::class);

Route::get('ctacte-proveedores', CtaCteProveedoresController::class);
Route::get('movimientos-proveedores/{id}', CtaCteProveedoresMovimientosController::class);


// Compras a la fabrica //
Route::get('compras', ComprasController::class);
Route::get('/remove-product-from-cart/{product}', [ComprasController::class,'removeProductFromCart'])->name('remove_product_from_cart');
Route::get('/cart', [ComprasController::class,'showCart'])->name('cart');

Route::get('/incrementar/{product}', [ComprasController::class,'Incrementar'])->name('incrementar');
Route::get('/decrecer/{product}', [ComprasController::class,'Decrecer'])->name('decrecer');




});


Route::get('conte', Component1::class);
Route::get('conte2', function(){
    return view('contenedor');
});



//rutas utils
Route::get('select2', Select2::class);

// E-mail verification
Route::get('/register/verify/{code}', 'GuestController@verify');
