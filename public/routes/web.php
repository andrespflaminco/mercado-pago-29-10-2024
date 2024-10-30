<?php

use App\Http\Controllers\ExportController;
use App\Http\Livewire\AsignarController;
use App\Http\Livewire\CashoutController;
use App\Http\Livewire\CategoriesController;
use App\Http\Livewire\CoinsController;
use App\Http\Livewire\MetodoPagoController;
use App\Http\Livewire\Component1;
use App\Http\Livewire\DashController;
use App\Http\Livewire\ImportController;
use App\Http\Livewire\PermisosController;
use App\Http\Livewire\PosController;
use App\Http\Livewire\ProductsController;
use App\Http\Livewire\ReportsController;
use App\Http\Livewire\RolesController;
use App\Http\Livewire\Select2;
use App\Http\Livewire\UsersController;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\SeccionalmacenController;
use App\Http\Livewire\ReportsdetalleController;
use App\Http\Livewire\ProduccionController;
use App\Http\Livewire\ClientesMostradorController;
use App\Http\Livewire\Select2AutoSearch;



Route::get('/', function () {
    return view('auth.login');
});

//Auth::routes();
Route::get('dashboard', function () {
  return view('livewire.dashboard.component')
  ->extends('layouts.theme.app')
  ->section('content');

});


Auth::routes(['register' => false]); // deshabilitamos el registro de nuevos users

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {

    Route::get('categories', CategoriesController::class)->middleware('role:Admin');
    Route::get('products', ProductsController::class);
    Route::get('coins', CoinsController::class);
    Route::get('pos', PosController::class);
    Route::get('dash', DashController::class);

    Route::group(['middleware' => ['role:Admin']], function () {
        Route::get('roles', RolesController::class);
        Route::get('permisos', PermisosController::class);
        Route::get('asignar', AsignarController::class);
    });

    Route::get('users', UsersController::class);

    Route::get('clientes', ClientesMostradorController::class);
    Route::get('metodo-pago', MetodoPagoController::class);

    Route::get('autocomplete', 'ClientesMostradorController@autocomplete')->name('autocomplete');



    Route::get('cashout', CashoutController::class);
    Route::get('reports', ReportsController::class);



    Route::get('import', ImportController::class);

    //reportes PDF
    Route::get('report/pdf/{user}/{type}/{f1}/{f2}', [ExportController::class, 'reportPDF']);
    Route::get('report/pdf/{user}/{type}', [ExportController::class, 'reportPDF']);





//reportes EXCEL
    Route::get('report/excel/{user}/{type}/{f1}/{f2}', [ExportController::class, 'reporteExcel']);
    Route::get('report/excel/{user}/{type}', [ExportController::class, 'reporteExcel']);

    //reportes EXCEL
    Route::get('report-producto/excel', [ExportController::class, 'reporteExcelProducto']);
    //reportes EXCEL
    Route::get('report-categoria/excel', [ExportController::class, 'reporteExcelCategorias']);



    //reportes excel detalle de productos vendidos
    Route::get('report-detalle/excel/{user}/{type}/{f1}/{f2}', [ExportController::class, 'reporteExcelDetalle']);
    Route::get('report-detalle/excel/{user}/{type}', [ExportController::class, 'reporteExcelDetalle']);

    //reportes excel detalle de produccion
    Route::get('report-produccion/excel/{EstadoId}/{type}/{f1}/{f2}', [ExportController::class, 'reporteExcelProduccion']);
    Route::get('report-produccion/excel/{EstadoId}/{type}', [ExportController::class, 'reporteExcelProduccion']);

    //Reporte detalle de productos vendidos
    Route::get('reportes-detalle', ReportsdetalleController::class);


    //Partes de los depositos
    Route::get('almacenes', SeccionalmacenController::class);
    //Detalle de las cosas a producir
    Route::get('produccion', ProduccionController::class);



});























Route::get('conte', Component1::class);
Route::get('conte2', function(){
    return view('contenedor');
});



//rutas utils
Route::get('select2', Select2::class);
