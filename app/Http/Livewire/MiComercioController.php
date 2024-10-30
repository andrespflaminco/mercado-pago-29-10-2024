<?php

namespace App\Http\Livewire;
use App\Models\Sale;
use App\Models\provincias;
use App\Models\datos_facturacion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Darryldecode\Cart\Facades\CartFacade as Cart;


class MiComercioController extends Component
{

    use WithPagination;
    use WithFileUploads;

    public $name,$phone,$email, $detalle_facturacion,$relacion_precio_iva, $pto_venta, $iva_defecto, $ciudad, $provincia, $cuit, $domicilio_comercial, $selected_id_facturacion, $iibb, $condicion_iva, $razon_social, $status,$image,$password,$selected_id,$id_provincia, $fileLoaded,$profile, $comercio_id, $usuario_id, $fecha_inicio_actividades;
    public $pageTitle, $componentName, $search, $localidad;
    private $pagination = 10;

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function mount()
    {
        $this->detalle_facturacion = [];
        $this->pageTitle ='Listado';
        $this->componentName ='general';
        $this->status ='Elegir';

        if(Auth::user()->comercio_id != 1)
        $comercio_id = Auth::user()->comercio_id;
        else
        $comercio_id = Auth::user()->id;

        $user =  User::select('users.*','datos_facturacions.*','provincias.provincia','provincias.id as id_provincia')
        ->leftjoin('datos_facturacions','datos_facturacions.comercio_id','users.id')
        ->leftjoin('provincias','provincias.id','datos_facturacions.provincia')
        ->where('users.id', $comercio_id)->first();

        $this->name = $user->name;
        $this->phone = $user->phone;
        $this->profile = $this->profile;
        $this->status = $user->status;
        $this->password = '';
        $this->email = $user->email;
        $this->domicilio_comercial = $user->domicilio_fiscal;
        $this->id_provincia = $user->id_provincia;
        $this->ciudad = $user->localidad;
        $this->image = $user->image;


        ////// DATOS FACTURACION ///////////
        $this->cuit = $user->cuit;
        $this->iibb = $user->iibb;
        $this->pto_venta = $user->pto_venta;
        $this->condicion_iva = $user->condicion_iva;
        $fechaFormateada = Carbon::parse($user->fecha_inicio_actividades)->format('Y-m-d');
        $this->fecha_inicio_actividades = $fechaFormateada;
        $this->razon_social = $user->razon_social;
        $this->iva_defecto = $user->iva_defecto;
        $this->relacion_precio_iva = $user->relacion_precio_iva ?? 0;

    }


    public function render()
    {
      if(Auth::user()->comercio_id != 1)
      $comercio_id = Auth::user()->comercio_id;
      else
      $comercio_id = Auth::user()->id;



       return view('livewire.mi-comercio.component',[
        'provincias' => provincias::select('*')->get(),
        'roles' => Role::orderBy('name','asc')->get(),
        'data' => User::select('users.*')->where('users.id', $comercio_id)->get()

    ])
       ->extends('layouts.theme-pos.app')
       ->section('content');
   }

   public function resetUI()
   {
    $this->resetValidation();
    $this->resetPage();
}



public function editfacturacion()
{
  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $datos_facturacion = datos_facturacion::where('comercio_id',$comercio_id)->first();

  if($datos_facturacion != null) {

    $this->selected_id_facturacion = $datos_facturacion->id;
    $this->razon_social = $datos_facturacion->razon_social;
    $this->domicilio_comercial = $datos_facturacion->domicilio_fiscal;
    $this->condicion_iva = $datos_facturacion->condicion_iva;
    $this->iibb = $datos_facturacion->iibb;
    $this->cuit = $datos_facturacion->cuit;
  }

    $this->emit('show-modal-facturacion','open!');
}


protected $listeners =[
    'deleteRow' => 'destroy',
    'resetUI' => 'resetUI'

];


public function Store()
{

  
  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;
  $this->comercio_id = $comercio_id;


  // dd($this->condicion_iva); ---> si es monotributista no se debe dejar poner IVA.
  // dd($this->condicion_iva); ---> si es otro que no sea monotributista no se debe dejar poner sin iva.

  $user = User::find($comercio_id);
  $user->update([
      'name' => $this->name,
      'email' => $this->email,
      'phone' => $this->phone,
      'profile' => 'Comercio',
      'password' => strlen($this->password) > 0 ? bcrypt($this->password) : $user->password
  ]);
  
  // Obtén el usuario autenticado
  $user = Auth::user();
    
  // Actualiza el nombre de usuario
  $user->name = $this->name;

  $user->syncRoles('Comercio');

  //dd($this->image);
  
  if($this->image != $user->image)
  {
    $customFileName = uniqid() . '_.' . $this->image->extension();
    $this->image->storeAs('public/users', $customFileName);
    $imageTemp = $user->image; // imagen temporal
    $user->image = $customFileName;
    $user->save();

    
    if($imageTemp !=null)
    {
      if(file_exists('storage/users/' . $imageTemp )) {
        unlink('storage/users/' . $imageTemp);
      }
    }
    
  }


    $mensaje = "Datos Actualizados correctamente";

    
    return redirect('mi-comercio')->with('status',$mensaje); 
}

public function DatosFacturacionOld(){
  
  
  $datos_facturacion = datos_facturacion::where('comercio_id',$comercio_id)->first();
  
  if($datos_facturacion != null) {
  
  if($datos_facturacion->iva_defecto != $this->iva_defecto) {
      
      $itemsQuantity = Cart::getTotalQuantity();
      if(0 < $itemsQuantity){
      $this->emit("msg-error","Es imposible actualizar el IVA por defecto porque existen productos en su carrito de ventas, porfavor finalice la venta o limpie el carrito.");
      return;
      }
  }
    if($datos_facturacion->relacion_precio_iva != $this->relacion_precio_iva) {
 
      $itemsQuantity = Cart::getTotalQuantity();
      if(0 < $itemsQuantity){
      $this->emit("msg-error","Es imposible actualizar la relacion precio -  IVA, porque existen productos en su carrito de ventas, porfavor finalice la venta o limpie el carrito.");
      return;
      }
  }
    
  }
    

//////////         DATOS DE FACTURACION           /////////////

if(datos_facturacion::where('comercio_id',$comercio_id)->exists())
  {

    if(Auth::user()->comercio_id != 1)
    $comercio_id = Auth::user()->comercio_id;
    else
    $comercio_id = Auth::user()->id;

    $user = datos_facturacion::where('comercio_id',$comercio_id);

    $user->update([
       'razon_social' => $this->razon_social,
       'domicilio_fiscal' => $this->domicilio_comercial,
       'fecha_inicio_actividades' => $this->fecha_inicio_actividades,
       'condicion_iva' => $this->condicion_iva,
       'iibb' => $this->iibb,
       'relacion_precio_iva' => $this->relacion_precio_iva,
       'iva_defecto' => $this->iva_defecto,
       'pto_venta' => $this->pto_venta,
       'provincia' => $this->id_provincia,
       'localidad' => $this->ciudad,
       'cuit' => $this->cuit,
       'comercio_id' => $comercio_id
    ]);

//    $this->resetUI();
//    $this->emit('user-updated','Datos actualizados');



$mensaje = "Datos Actualizados correctamente";

} else {


  $datos_facturacion = datos_facturacion::create([
     'razon_social' => $this->razon_social,
     'domicilio_fiscal' => $this->domicilio_comercial,
     'localidad' => $this->ciudad,
     'iva_defecto' => $this->iva_defecto,
     'pto_venta' => $this->pto_venta,
     'relacion_precio_iva' => $this->relacion_precio_iva,
     'provincia' => $this->provincia,
     'fecha_inicio_actividades' => $this->fecha_inicio_actividades,
     'condicion_iva' => $this->condicion_iva,
     'iibb' => $this->iibb,
     'cuit' => $this->cuit,
     'comercio_id' => $comercio_id
  ]);

$mensaje = "Datos Registrados correctamente";
//$this->resetUI();
//$this->emit('user-added','Datos Registrados');
}    
}


}
