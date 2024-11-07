<?php


use App\Http\Livewire\RegistroEspecialController;
use App\Http\Livewire\RegistroYSuscripcionEspecialController;

use App\Http\Livewire\SuscripcionEspecialController;
use App\Http\Livewire\SuscripcionDirectaController;

use App\Http\Livewire\RetiroPorSucursales;

use App\Http\Livewire\SubcategoriasController;

use App\Http\Controllers\ExportController;
use App\Http\Livewire\AsignarController;
use App\Http\Livewire\CashoutController;
use App\Http\Livewire\CategoriesController;
use App\Http\Livewire\CoinsController;
use App\Http\Livewire\SucursalesController;
use App\Http\Livewire\PromosController;
use App\Http\Livewire\PuntosVentaController;
use App\Http\Livewire\MovimientoDineroCuentasController;


use App\Http\Livewire\ComisionesController;
use App\Http\Livewire\ComisionesResumenController;


use App\Http\Livewire\ConfiguracionImpresionController;
use App\Http\Livewire\ConfiguracionProductosController;
use App\Http\Livewire\ConfiguracionCtaCteController;
use App\Http\Livewire\ConfiguracionCajaController;


use App\Http\Livewire\MovimientoInsumosStockController;
use App\Http\Livewire\MovimientoInsumosStockResumenController;

use App\Http\Livewire\FacturacionController;
use App\Http\Livewire\FacturacionComprasController;

use App\Http\Livewire\ActualizacionMasivaController;

use App\Http\Livewire\EtiquetasController;
use App\Http\Livewire\EtiquetasMarcadoresController;
use App\Http\Livewire\ControladorStockController;
use App\Http\Livewire\PagosController;


use App\Http\Livewire\EtiquetasProductosController;
use App\Http\Livewire\PresupuestoController;
use App\Http\Livewire\ImportComprasController;
use App\Http\Livewire\MovimientoStockController;
use App\Http\Livewire\DescargasController;
use App\Http\Livewire\MostrarRecetaController;
use App\Http\Livewire\ImportProductsTestController;
use App\Http\Livewire\ImportCategoriasMonotributoController;

use App\Http\Livewire\MarcasController;
use App\Http\Livewire\ConsolidadoController;
use App\Http\Livewire\FormController;
use App\Http\Livewire\MostrarRecetaProduccionController;
use App\Http\Livewire\ChequesController;
use App\Http\Livewire\MovimientoStockResumenController;
use App\Http\Livewire\RecordatorioController;
use App\Http\Livewire\AsistenteProduccionController;
use App\Http\Livewire\UploadImageController;
use App\Http\Livewire\ReadNotificacionesController;
use App\Http\Livewire\ComprasCasaCentralController;


use App\Http\Livewire\PedidosSucursalResumenController;
use App\Http\Livewire\PedidosSucursalController;



use App\Http\Livewire\PresupuestoResumenController;


/* SUSCRIPCIONES */
use App\Http\Livewire\SuscripcionController;
use App\Http\Livewire\PlanesSuscripcionController;
use App\Http\Livewire\SuscripcionesAdminController;
use App\Http\Livewire\SuscripcionesConfiguracionController;
use App\Http\Livewire\SuscripcionesControlController;

/*-----------------*/

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
use App\Http\Livewire\EcommercethankswsController;

/* PARA QUE LOS CLIENTES PUEDAN VER SUS PEDIDOS */

use App\Http\Livewire\MisOrdenesClientesLoginController;
use App\Http\Livewire\MisOrdenesController;
use App\Http\Livewire\MisOrdenesVerController;

/* */
use App\Http\Livewire\CobrosMPController;
use App\Http\Livewire\AyudaController;
use App\Http\Livewire\MiComercioController;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Livewire\FacturaController;


use App\Http\Livewire\RecetasController;
use App\Http\Livewire\RecetasDetalleController;
use App\Http\Livewire\EditarRecetasController;
use App\Http\Livewire\ProduccionController;
use App\Http\Livewire\ProduccionNuevaController;


use App\Http\Livewire\AtributosController;
use App\Http\Livewire\MetodoPagoController;
use App\Http\Livewire\HistoricoStockController;
use App\Http\Livewire\HistoricoPreciosController;
use App\Http\Livewire\HistorialClienteController;
use App\Http\Livewire\HistoricoStockInsumosController;
use App\Http\Livewire\CajasController;
use App\Http\Livewire\CajasDetalleController;
use App\Http\Livewire\IngresosRetirosController;

use App\Http\Livewire\AsistenteStock;

use App\Http\Livewire\ListaPreciosController;
use App\Http\Livewire\ListaPreciosInsumosController;
use App\Http\Livewire\ReglaPreciosController;

use App\Http\Livewire\ComprasController;
use App\Http\Livewire\ComprasElegirController;

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
use App\Http\Livewire\ImportClientesController;
use App\Http\Livewire\ImportProveedoresController;
use App\Http\Livewire\ImportRecetasController;
use App\Http\Livewire\ImportVentasController;
use App\Http\Livewire\ImportInsumosController;
use App\Http\Livewire\ImportPreciosController;
use App\Http\Livewire\ImportStockController;
use App\Http\Livewire\ProveedoresController;
use App\Http\Livewire\PermisosController;
use App\Http\Livewire\PosController;
//use App\Http\Livewire\PosNuevoController;
use App\Http\Livewire\VentaRapidaController;
use App\Http\Livewire\ReportsVentaRapidaController;
use App\Http\Livewire\PosAltoController;

use App\Http\Livewire\ProductsController;
use App\Http\Livewire\ProductsNuevoController;


use App\Http\Livewire\ProductsStockController;
use App\Http\Livewire\ProductsPrecioController;
use App\Http\Livewire\StockController;
use App\Http\Livewire\ProductsPriceController;
use App\Http\Livewire\ProductsAddController;
use App\Http\Livewire\ProductsAddedController;



use App\Http\Livewire\ComprasResumenController;

/* COMPRAS INSUMOS */
use App\Http\Livewire\ComprasResumenInsumosController;
use App\Http\Livewire\ComprasInsumosController;


use App\Http\Livewire\ReportsController;
use App\Http\Livewire\ReportsNuevoController;
use App\Http\Livewire\Reports2Controller;
use App\Http\Livewire\ReportsEcommerceController;
use App\Http\Livewire\RolesController;
use App\Http\Livewire\HojaRutaController;
use App\Http\Livewire\HojaRutaPedidoController;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\DashboardNuevo;
use App\Http\Livewire\Dashboard2;
use App\Http\Livewire\Select2;
use App\Http\Livewire\UsersController;
use App\Http\Livewire\UsersAdminController;
use App\Http\Livewire\PuntosVentaAdminController;
use App\Http\Livewire\CRMAdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\SeccionalmacenController;
use App\Http\Livewire\ReportsdetalleController;
use App\Http\Livewire\PaymentController;
use App\Http\Livewire\InsumosController;
use App\Http\Livewire\PaymentPlansController;
use App\Http\Livewire\ClientesMostradorController;
use App\Http\Livewire\Select2AutoSearch;

use App\Http\Livewire\ComprarRegistroController;

Auth::routes(['verify' => false]); // deshabilitamos el registro de nuevos users
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




Route::get('form/{plan}', FormController::class);

Route::get('ticket', function () {
  return view('pdf.ticket');
});


///////// ECOMMERCE /////////////////

Route::get('tienda/{slug}', EcommerceController::class);

Route::post('tienda/{slug}', EcommerceController::class);

Route::get('ecart/{slug}', EcommerceCartController::class);

Route::get('ecommerce-billing/{slug}', EcommerceBillingController::class);


Route::post('save-sale-e', [EcommerceBillingController::class, 'SaveSale']);

Route::get('ecommerce-login/{slug}', EcommerceLoginController::class);

Route::get('ecommerce-gracias/{slug}', EcommerceThanksController::class);

Route::get('ecommerce-ws/{id}/{slug}', EcommercethankswsController::class);

Route::get('ecommerce-account/{slug}', EcommerceAccountController::class);

Route::get('ecommerce-orders/{slug}', EcommerceOrdersController::class);

Route::post('webhooks', EcommerceThanksController::class);

Route::get('webhooks/pay/{slug}', [EcommerceBillingController::class, 'pay'])->name('webhooks.pay');


Route::get('ecommerce-email/pdf/{id}/{email}/{slug}', [ExportController::class, 'emailPDFGracias']);

Route::get('pdf/caja/{caja_id}/{uid}', [ExportController::class, 'PDFCaja']);
Route::get('remito-movimiento-stock/pdf/{Id}', [ExportController::class, 'RemitoMovimientoStockPDF']);

Route::post('store-login', [EcommerceLoginController::class, 'customLogin']);


/// PARA QUE LOS CLIENTES PUEDAN VER SUS PEDIDOS //

Route::get('mis-ordenes-login/{slug}', MisOrdenesClientesLoginController::class);

Route::get('mis-ordenes/{slug}', MisOrdenesController::class);

Route::get('ver-orden/{id}', MisOrdenesVerController::class);

Route::post('store-login-mis-ordenes', [MisOrdenesClientesLoginController::class, 'customLogin']);


///////// PAGOS /////////////////



//Route::get('registers-suscripcion/{intencion_compra}', RegistroYSuscripcionEspecialController::class);


//* SUSCRIPCIONES *//


Route::get('suscribirse/{slug}/{quantity?}', SuscripcionEspecialController::class);

Route::get('registro-directo/{slug}/{quantity?}', SuscripcionDirectaController::class);
//Route::get('registers/{slug}/{quantity?}', SuscripcionDirectaController::class);
//Route::get('registers/{slug}/{quantity?}', RegistroEspecialController::class);


Route::post('comprobar-datos-registro', [SuscripcionDirectaController::class, 'comprobarDatosRegistro'])->name('comprobar-datos-registro');


Route::post('confirmarSuscripcion', [SuscripcionEspecialController::class, 'confirmarSuscripcion'])->name('confirmar.suscripcion');;
Route::get('cancelarSuscripcion/{suscripcion_id}', [SuscripcionEspecialController::class, 'cancelarSuscripcion']);
Route::get('actualizarSuscripcion/{suscripcion_id}/{plan_suscripcion_id}/{usuarios_count?}/{modulos_id?}', [SuscripcionEspecialController::class, 'actualizarSuscripcion']);
Route::get('mpCreateUserTest', [SuscripcionEspecialController::class, 'mercadoPagoCreateUserTest']);

Route::get('mp-success', [SuscripcionEspecialController::class, 'mercadoPagoSuccess']);
Route::get('mp-webhooks', [SuscripcionEspecialController::class, 'mercadoPagoWebhooks']);
Route::get('mp-notification', [SuscripcionEspecialController::class, 'mercadoPagoNotification']);

Route::get('mp-checkout-success', [SuscripcionEspecialController::class, 'CheckoutSuccess'])->name('checkout.success');
Route::get('mp-checkout-failure', [SuscripcionEspecialController::class, 'CheckoutFailure'])->name('checkout.failure');
Route::get('mp-checkout-pending', [SuscripcionEspecialController::class, 'CheckoutPending'])->name('checkout.pending');


//Route::get('suscribirse/{slug}', SuscripcionEspecialController::class);

Route::get('actualizacion-masiva', ActualizacionMasivaController::class);








///////////////////////////////////////////////////////////////////


/*
Route::get('payments2', CobrosMPController::class);
Route::get('/regist/approval', [PaymentController::class, 'approval'])->name('approval');
Route::get('/regist/cancelled', [PaymentController::class, 'cancelled'])->name('cancelled');
Route::post('/regist/pay', [PaymentController::class, 'pay'])->name('pay');
Route::get('planes/{plan}', PaymentPlansController::class);
Route::get('/regist/approval', [PaymentPlansController::class, 'approval'])->name('approval.plan');
Route::get('/regist/cancelled', [PaymentPlansController::class, 'cancelled'])->name('cancelled.plan');
Route::post('/planes/pay', [PaymentPlansController::class, 'pay'])->name('pay-plan');
*/

Route::get('regist/', SuscripcionController::class)->name('regist');
Route::get('suscripcion-configuracion', SuscripcionesConfiguracionController::class);

Route::get('getPreapprovalBySuscriptionId/{id}', [SuscripcionController::class, 'getPreapprovalBySuscriptionId'])->name('getPreapprovalBySuscriptionId');
Route::get('updateAllSubscription', [SuscripcionController::class, 'updateAllSubscription'])->name('updateAllSubscription');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// ->middleware('verified')

Route::middleware(['auth'])->group(function () {

  Route::get('ecommerce-config', EcommerceConfigController::class);

  Route::get('ecommerce-cupones', EcommerceCuponesController::class);
  Route::get('ecommerce-ajustes', EcommerceAjustesController::class);
  Route::get('ecommerce-envios', EcommerceEnviosController::class);

  Route::get('categories', CategoriesController::class);
  Route::get('subcategorias', SubcategoriasController::class);

  Route::get('marcas', MarcasController::class);

  Route::get('comisiones', ComisionesController::class);
  Route::get('comisiones-resumen', ComisionesResumenController::class);


  Route::get('consolidado', ConsolidadoController::class);

  //Route::get('products', ProductsController::class);

  Route::get('products', ProductsNuevoController::class);

  //Route::get('products-stock', ProductsStockController::class);
  //Route::get('products-precios', ProductsPrecioController::class);

  Route::get('productos', ProductsNuevoController::class);



  //Route::match(['get','post'],'/',\App\Http\Livewire\ProductsController::class)->name('products');


  Route::get('atributos', AtributosController::class);

  Route::get('stock', StockController::class);

  Route::get('asistente-produccion', AsistenteProduccionController::class);


  Route::get('presupuesto', PresupuestoController::class);

  Route::get('presupuesto-resumen', PresupuestoResumenController::class);

  Route::get('products-price', ProductsPriceController::class);

  // Agregar productos 
  Route::get('products-add', ProductsAddController::class);
  Route::post('store-producto', [ProductsAddController::class, 'Store']);


  Route::get('product-added/{id}', ProductsAddedController::class);

  //Route::get('insumos', InsumosController::class);

  // Ruta que redirige de /insumos a /products con el parámetro tipo=insumo
  Route::get('/insumos', function () {
    return redirect()->to('/products?tipo=insumo');
  });


  Route::get('coins', CoinsController::class);


  //Route::get('pos', PosNuevoController::class);
  //Route::post('pos', PosNuevoController::class);

  Route::get('pos', PosController::class);
  Route::post('pos', PosController::class);

  Route::get('promos', PromosController::class);
  Route::get('retiro-sucursal', RetiroPorSucursales::class);


  Route::get('configuracion-impresion', ConfiguracionImpresionController::class);
  Route::get('configuracion-productos', ConfiguracionProductosController::class);
  Route::get('configuracion-cta-cte', ConfiguracionCtaCteController::class);
  Route::get('configuracion-cajas', ConfiguracionCajaController::class);


  //Route::get('test/{product_id}/{variacion_id}/{comercio_id}', [PosController::class, 'Test']);

  Route::get('venta-rapida', VentaRapidaController::class);
  Route::get('reporte-venta-rapida', ReportsVentaRapidaController::class);

  Route::get('pos-alto', PosAltoController::class);
  Route::get('dash', DashController::class);
  Route::get('dashboard', Dashboard::class);
  Route::get('dashboard-nuevo', DashboardNuevo::class);

  Route::get('dashboard2', Dashboard2::class);


  Route::get('roles', RolesController::class);
  Route::get('asignar', AsignarController::class);

  Route::group(['middleware' => ['role:Admin']], function () {



    Route::get('planes-suscripcion', PlanesSuscripcionController::class);
    Route::get('permisos', PermisosController::class);
    Route::get('suscripciones-admin', SuscripcionesAdminController::class);


    Route::get('suscripciones-control', SuscripcionesControlController::class);


    Route::get('crm-admin', CRMAdminController::class);
    Route::get('users-admin', UsersAdminController::class);
    Route::get('puntos-venta-admin', PuntosVentaAdminController::class);
    Route::get('import-categorias-monotributo', ImportCategoriasMonotributoController::class);
  });

  Route::group(['middleware' => ['role:Comercio']], function () {});

  Route::get('users', UsersController::class);

  Route::get('clientes', ClientesMostradorController::class);
  Route::get('metodo-pago', MetodoPagoController::class);
  Route::get('hoja-ruta', HojaRutaController::class);
  Route::get('hoja-ruta-pedido', HojaRutaPedidoController::class);

  Route::get('autocomplete', 'ClientesMostradorController@autocomplete')->name('autocomplete');



  Route::get('pagos', PagosController::class);

  Route::get('cashout', CashoutController::class);

  Route::get('reports', ReportsNuevoController::class);
  Route::get('reports-nuevo', ReportsNuevoController::class);


  Route::get('puntos-venta', PuntosVentaController::class);
  Route::get('facturacion', FacturacionController::class);
  Route::get('facturacion-compras', FacturacionComprasController::class);


  Route::get('reports2', Reports2Controller::class);

  Route::get('woocommerce', WooCommerceController::class);


  Route::get('movimiento-stock', MovimientoStockController::class);


  Route::get('movimiento-stock-resumen', MovimientoStockResumenController::class);


  Route::get('mi-comercio', MiComercioController::class);
  Route::get('sucursales', SucursalesController::class);

  Route::get('reports-ecommerce', ReportsEcommerceController::class);


  //Route::post('import', [ImportController::class, 'parseImport']);
  //Route::get('import', ImportController::class);
  Route::any('import', ImportController::class);
  Route::any('livewire/message/import-controller', ImportController::class);



  Route::get('import-compra', ImportComprasController::class);



  Route::post('post-importar-clientes', [ImportClientesController::class, 'uploadClientes']);

  Route::get('import-clientes', ImportClientesController::class);

  Route::get('import-proveedores', ImportProveedoresController::class);

  Route::get('import-ventas', ImportVentasController::class);
  Route::get('import-products-test', ImportProductsTestController::class);

  Route::get('import-insumos', ImportInsumosController::class);
  Route::get('import-recetas', ImportRecetasController::class);

  Route::post('upload-products', [ImportController::class, 'uploadProducts']);


  Route::post('importar-precios', [ImportPreciosController::class, 'import']);
  Route::get('import-precios', ImportPreciosController::class);
  Route::get('import-stock', ImportStockController::class);

  //reportes PDF
  Route::get('report/pdf/{usuarioSeleccionado}/{clienteId}/{f1}/{f2}', [ExportController::class, 'reportPDF']);
  Route::get('report/pdf/{usuarioSeleccionado}/{clienteId}', [ExportController::class, 'reportPDF']);

  Route::get('report/crm/{uid}', [ExportController::class, 'reportCRM']);

  Route::get('report-cta-cte-proveedor/excel/{search}/{uid}', [ExportController::class, 'reporteExcelCtaCteProveedores']);
  Route::get('report-cta-cte-clientes/excel/{search}/{uid}', [ExportController::class, 'reporteExcelCtaCteClientes']);

  Route::get('movimientos-cta-cte-clientes/excel/{cliente_id}/{from}/{to}/{uid}', [ExportController::class, 'reporteExcelCtaCteClientesMovimiento']);
  Route::get('movimientos-cta-cte-clientes-producto/excel/{cliente_id}/{from}/{to}/{uid}', [ExportController::class, 'reporteExcelCtaCteClientesMovimientoPorProducto']);
  Route::get('movimientos-cta-cte-clientes/pdf/{cliente_id}/{from}/{to}/{uid}', [ExportController::class, 'PDFCtaCteClientesMovimiento']);


  Route::get('etiquetas', EtiquetasController::class);
  Route::get('etiquetas-marcadores', EtiquetasMarcadoresController::class);

  Route::get('movimiento-insumos-stock', MovimientoInsumosStockController::class);
  Route::get('movimiento-insumos-stock-resumen', MovimientoInsumosStockResumenController::class);

  // Todas las exportaciones 

  Route::get('etiquetas/pdf/{nombre_producto}/{precio}/{codigo}/{codigo_barra}/{fecha_impresion}/{size}/{producto_elegido}', [ExportController::class, 'Etiquetas']);

  Route::get('ticket/{saleId}', [ExportController::class, 'Ticket']);

  Route::get('ticket-factura/{factura_id}', [ExportController::class, 'TicketFactura']);

  Route::get('pdf-zeta/{Uid}/{dateFrom}/{dateTo}', [ExportController::class, 'PDFZeta']);

  Route::get('ticket-rapido/{saleId}', [ExportController::class, 'TicketRapido']);




  Route::get('report-factura-rapido/pdf/{id}', [ExportController::class, 'PDFFacturaRapido']);


  Route::get('report-email-rapido/pdf/{id}/{email}', [ExportController::class, 'emailPDFFacturaRapido']);

  Route::get('estado-email/pdf/{id}/{email}/{estado}', [ExportController::class, 'emailPDFEstado']);


  Route::get('imprimir-compra/pdf/{id}', [ExportController::class, 'reportPDFCompra']);

  Route::get('imprimir-compra-insumos/pdf/{id}', [ExportController::class, 'reportPDFCompraInsumos']);

  Route::get('report-remito/pdf/{id}', [ExportController::class, 'reportPDFRemito']);

  Route::get('report-presupuesto/pdf/{id}', [ExportController::class, 'reportPDFPresupuesto']);

  Route::get('receta-imprimir/pdf/{id}', [ExportController::class, 'reportPDFRecetaImprimir']);


  Route::get('report-email/pdf/{id}/{email}', [ExportController::class, 'emailPDFVenta']);

  Route::get('report-factura/pdf/{id}', [ExportController::class, 'reportPDFFactura']);

  Route::get('imprimir-factura/pdf/{id}', [ExportController::class, 'PDFFactura']);

  Route::get('enviar-factura/pdf/{id}/{email}', [ExportController::class, 'emailPDFFactura']);

  //

  Route::post('store-added', [ProductsAddedController::class, 'StorePrecios']);


  //reportes EXCEL
  Route::get('report/excel/{sucursal_id}/{usuarioSeleccionado}/{cliente}/{estado_pago}/{estado}/{metodo_pago}/{estado_facturacion}/{f1}/{f2}/{uid}', [ExportController::class, 'reporteExcel']);
  Route::get('report/excel/{sucursal_id}/{usuarioSeleccionado}/{cliente}/{estado_pago}/{estado}/{metodo_pago}/{estado_facturacion}/{uid}', [ExportController::class, 'reporteExcel']);

  Route::get('facturas/excel/{sucursal_id}/{tipo_comprobante_buscar}/{facturas_repetidas}/{cliente}/{estado_pago}/{f1}/{f2}/{uid}', [ExportController::class, 'FacturacionExcel']);
  Route::get('facturas/excel/{sucursal_id}/{tipo_comprobante_buscar}/{facturas_repetidas}/{cliente}/{estado_pago}/{uid}', [ExportController::class, 'FacturacionExcel']);


  Route::get('facturas-compras/excel/{sucursal_id}/{tipo_comprobante_buscar}/{proveedor}/{f1}/{f2}/{uid}', [ExportController::class, 'FacturacionComprasExcel']);
  Route::get('facturas-compras/excel/{sucursal_id}/{tipo_comprobante_buscar}/{proveedor}/{uid}', [ExportController::class, 'FacturacionComprasExcel']);


  Route::get('report-compras/excel/{id_compra}/{proveedor_id}/{estado_pago}/{f1}/{f2}/{uid}', [ExportController::class, 'reporteExcelCompras']);

  // AKA
  Route::get('report-gastos/excel/{search}/{categoria_filtro}/{etiquetas_filtro}/{metodo_pago_filtro}/{forma_pago_filtro}/{f1}/{f2}/{uid}', [ExportController::class, 'reporteExcelGastos']);

  //  Route::get('report-gastos/excel/{search}/{categoria_filtro}/{etiquetas_filtro}/{forma_pago_filtro}/{f1}/{f2}/{uid}', [ExportController::class, 'reporteExcelGastos']);


  // 18-4-2024
  Route::get('report-pagos/excel/{tipo_movimiento_filtro}/{estado_pago}/{operacion_filtro}/{banco_filtro}/{metodo_pago_filtro}/{sucursal_id}/{uid}', [ExportController::class, 'reporteExcelPagos']);

  Route::get('report-etiquetas/excel/{id}/{uid}', [ExportController::class, 'reporteExcelEtiquetas']);

  Route::get('report/excel-clientes/{sucursal_id}/{uid}', [ExportController::class, 'reporteExcelClientes']);

  Route::get('report/excel-proveedores/{uid}', [ExportController::class, 'reporteExcelProveedores']);

  Route::get('report/excel-cajas/{cajaid}/{uid}', [ExportController::class, 'reporteExcelCaja']);

  //reportes EXCEL
  Route::get('report-producto/excel/{fecha}/{id_reporte}/{comercio_id}/{reportName}', [ExportController::class, 'reporteExcelProducto']);

  Route::get('reporte-productos-ejemplo/excel/{comercio_id}/{nombre_reporte}', [ExportController::class, 'reporteExcelProductoEjemplo']);

  //reportes EXCEL
  Route::get('recetas/excel/{fecha}', [ExportController::class, 'reporteExcelRecetas']);

  //reportes LISTA DE PRECIOS
  Route::get('lista-precios/excel/{fecha}/{listaId}/{id_categoria}/{id_almacen}/{proveedor_elegido}', [ExportController::class, 'reporteExcelListaPrecios']);
  //reportes STOCK DE LA SUCURSAL
  Route::get('stock-sucursal/excel/{fecha}/{sucursalId}/{id_categoria}/{id_almacen}/{proveedor_elegido}', [ExportController::class, 'reporteExcelStock']);


  Route::get('controlador-stock', ControladorStockController::class);
  Route::get('movimiento-dinero-cuentas', MovimientoDineroCuentasController::class);


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

  Route::get('report-hoja-ruta-consolidado/pdf/{id_hoja_ruta}/{uid}', [ExportController::class, 'reportePDFHojaRutaConsolidado']);
  Route::get('report-hoja-ruta/pdf/{id_hoja_ruta}/{uid}', [ExportController::class, 'reportePDFHojaRuta']);


  //Reporte detalle de productos vendidos
  Route::get('reportes-detalle', ReportsdetalleController::class);



  Route::get('historial-cliente/{id_cliente}', HistorialClienteController::class);
  //Historico de stock
  Route::get('historico-stock', HistoricoStockController::class);
  Route::get('historico-precios', HistoricoPreciosController::class);
  Route::get('historico-stock-insumos', HistoricoStockInsumosController::class);


  // ALMACENES
  Route::get('almacenes', SeccionalmacenController::class);
  Route::get('recordatorio', RecordatorioController::class);
  Route::get('ayuda', AyudaController::class);

  Route::get('image', UploadImageController::class);

  Route::get('image', UploadImageController::class);

  Route::post('image', [UploadImageController::class, 'store']);
  Route::get('descargas', DescargasController::class);



  // PROVEEDORES
  Route::get('proveedores', ProveedoresController::class);

  // PROVEEDORES
  Route::get('lista-precios', ListaPreciosController::class);
  Route::get('lista-precios-insumos', ListaPreciosInsumosController::class);

  Route::get('regla-precios', ReglaPreciosController::class);


  // PRODUCCION
  Route::get('produccion', ProduccionController::class);
  Route::get('produccion-nueva', ProduccionNuevaController::class);


  Route::get('recetas', RecetasController::class);
  Route::get('recetas_detalle', RecetasDetalleController::class);
  //    Route::get('componentes_editar/{product_id}', EditarRecetasController::class);

  Route::get('mostrar_receta/{product_id}', MostrarRecetaController::class);

  Route::get('mostrar_receta_produccion/{id}', MostrarRecetaProduccionController::class);

  //Asistente de compras stock
  Route::get('asistente_stock', AsistenteStock::class);

  Route::get('bancos', BancosController::class);

  // Pedidos desde las sucursales 

  Route::get('pedidos-sucursales-resumen', PedidosSucursalResumenController::class);
  Route::get('pedidos-sucursales', PedidosSucursalController::class);


  //Gastos
  Route::get('gastos', GastosController::class);

  //Cheques
  Route::get('cheques', ChequesController::class);
  //Partes de los depositos
  Route::get('cajas', CajasController::class);

  Route::get('ingresos-retiros', IngresosRetirosController::class);


  Route::get('read-notificacion/{Id}', [ReadNotificacionesController::class, 'render']);

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


  // Compras //
  Route::get('compras', ComprasController::class);
  Route::get('compras-elegir', ComprasElegirController::class);

  Route::get('/remove-product-from-cart/{product}', [ComprasController::class, 'removeProductFromCart'])->name('remove_product_from_cart');
  Route::get('/cart', [ComprasController::class, 'showCart'])->name('cart');

  Route::get('compras-central', ComprasCasaCentralController::class);


  Route::get('/incrementar/{product}', [ComprasController::class, 'Incrementar'])->name('incrementar');
  Route::get('/decrecer/{product}', [ComprasController::class, 'Decrecer'])->name('decrecer');
});


Route::get('conte', Component1::class);
Route::get('conte2', function () {
  return view('contenedor');
});



//rutas utils
Route::get('select2', Select2::class);

// E-mail verification
Route::get('/register/verify/{code}', 'GuestController@verify');


// anexo endpoints para refrescar token
Route::get('/refresh-csrf', function () {
  return response()->json(['csrfToken' => csrf_token()]);
});
