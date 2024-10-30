<?php

use App\Http\Controllers\ExportController;
use App\Http\Livewire\AsignarController;
use App\Http\Livewire\CashoutController;
use App\Http\Livewire\CategoriesController;
use App\Http\Livewire\CoinsController;
use App\Http\Livewire\CobrosMPController;
use App\Http\Livewire\MiComercioController;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Livewire\FacturaController;
use App\Http\Livewire\MetodoPagoController;
use App\Http\Livewire\HistoricoStockController;
use App\Http\Livewire\CajasController;
use App\Http\Livewire\CajasDetalleController;
use App\Http\Livewire\AsistenteStock;
use App\Http\Livewire\ComprasController;
use App\Http\Livewire\Component1;
use App\Http\Livewire\BancosController;
use App\Http\Livewire\DashController;
use App\Http\Livewire\GastosController;
use App\Http\Livewire\ImportController;
use App\Http\Livewire\ProveedoresController;
use App\Http\Livewire\PermisosController;
use App\Http\Livewire\PosController;
use App\Http\Livewire\ComprasResumenController;
use App\Http\Livewire\PosAltoController;
use App\Http\Livewire\ProductsController;
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
use App\Http\Livewire\ProduccionController;
use App\Http\Livewire\PaymentController;
use App\Http\Livewire\ClientesMostradorController;
use App\Http\Livewire\Select2AutoSearch;

use App\Http\Livewire\ComprarRegistroController;

Auth::routes(['verify' => true]); // deshabilitamos el registro de nuevos users
Auth::routes(['register' => true]); // deshabilitamos el registro de nuevos users


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

///////// PAGOS /////////////////

Route::get('regist', PaymentController::class);

Route::get('payments2', CobrosMPController::class);


Route::get('/regist/approval', [PaymentController::class, 'approval'])->name('approval');
Route::get('/regist/cancelled', [PaymentController::class, 'cancelled'])->name('cancelled');
Route::post('/regist/pay', [PaymentController::class, 'pay'])->name('pay');





Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->middleware('verified')->group(function () {

Route::get('categories', CategoriesController::class);
Route::get('products', ProductsController::class);
Route::get('coins', CoinsController::class);
Route::get('pos', PosController::class);
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

    Route::get('mi-comercio', MiComercioController::class);

    Route::get('reports-ecommerce', ReportsEcommerceController::class);







    Route::get('import', ImportController::class);

    //reportes PDF
    Route::get('report/pdf/{usuarioSeleccionado}/{clienteId}/{f1}/{f2}', [ExportController::class, 'reportPDF']);
    Route::get('report/pdf/{usuarioSeleccionado}/{clienteId}', [ExportController::class, 'reportPDF']);




Route::get('report-factura/pdf/{id}', [ExportController::class, 'reportPDFFactura']);

Route::get('report-email/pdf/{id}/{email}', [ExportController::class, 'emailPDFFactura']);



//reportes EXCEL
    Route::get('report/excel/{usuarioSeleccionado}/{cliente}/{estado_pago}/{estado}/{metodo_pago}/{uid}/{f1}/{f2}', [ExportController::class, 'reporteExcel']);
    Route::get('report/excel/{usuarioSeleccionado}/{cliente}/{estado_pago}/{estado}/{metodo_pago}/{uid}', [ExportController::class, 'reporteExcel']);


    Route::get('report/excel-cajas/{cajaid}/{uid}', [ExportController::class, 'reporteExcelCaja']);

    //reportes EXCEL
    Route::get('report-producto/excel', [ExportController::class, 'reporteExcelProducto']);
    //reportes EXCEL
    Route::get('report-categoria/excel', [ExportController::class, 'reporteExcelCategorias']);



    //reportes excel detalle de productos vendidos
    Route::get('report-detalle/excel/{usuarioSeleccionado}/{ClienteSeleccionado}/{metodopagoSeleccionado}/{productoSeleccionado}/{categoriaSeleccionado}/{almacenSeleccionado}/{uid}/{f1}/{f2}', [ExportController::class, 'reporteExcelDetalle']);

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


    //Partes de los depositos
    Route::get('almacenes', SeccionalmacenController::class);
    //Partes de los depositos
    Route::get('proveedores', ProveedoresController::class);
    //Detalle de las cosas a producir
    Route::get('produccion', ProduccionController::class);
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
