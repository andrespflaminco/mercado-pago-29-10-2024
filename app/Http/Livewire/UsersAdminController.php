<?php

namespace App\Http\Livewire;
use App\Models\Sale;
use App\Models\datos_facturacion;
use App\Models\sucursales;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use App\Traits\ZohoCRMTrait;
use Illuminate\Support\Facades\Crypt;

class UsersAdminController extends Component
{

    use WithPagination;
    use WithFileUploads;
    use ZohoCRMTrait;

    public $name,$phone,$email,$validar_email,$nombre_comercio_a_agregar,$casa_central_id,$validacion_email, $detalle_facturacion,$agregar, $sucursal_id,$buscar, $cuit, $domicilio_comercial, $selected_id_facturacion, $iibb, $condicion_iva, $razon_social, $status,$image,$password,$selected_id,$fileLoaded,$profile, $comercio_id, $usuario_id, $confirmed, $cantidad_usuarios;
    public $pageTitle, $componentName,$prueba_gratis,$lista_precios, $columnaOrden, $search, $categoria_profile, $plan_admin,$sucursales;
    private $pagination = 25;
    
    public $token_pass;
    
    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function mount()
    {
        $this->columnaOrden = "id";
        $this->buscar = '';
        $this->detalle_facturacion = [];
        $this->pageTitle ='Listado';
        $this->componentName ='Usuarios';
        $this->status ='Elegir';
        $this->buscar = '';
        $this->sucursal_id = Auth::user()->id;
        $this->nombre_comercio_a_agregar = null;
        $this->direccionOrden = "desc";
    }

  
  public function OrdenarColumna($columna)
{
    if ($this->columnaOrden == $columna) {
        // Cambiar la dirección de orden si la columna es la misma
        $this->columnaOrden = $columna;
        $this->direccionOrden = $this->direccionOrden == 'asc' ? 'desc' : 'asc';
    } else {
        // Si es una columna diferente, cambiar a la nueva columna y establecer la dirección predeterminada
        $this->columnaOrden = $columna;
        $this->direccionOrden = 'asc';
        
       // dd($this->columnaOrden,$this->direccionOrden);
    }
    
    $this->render();
}

    public function render()
    {
        
       
        $usuario_id = Auth::user()->id;

        if(Auth::user()->comercio_id != 1)
        $comercio_id = Auth::user()->comercio_id;
        else
        $comercio_id = Auth::user()->id;

        $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->where('comercio_id',$comercio_id)->select('sucursales.*','users.name')->get();
        
        // Admin flaminco 
        
        
         if(Auth::user()->id == 1) {
     
        $data = User::select('*');
        
        if($this->categoria_profile) {
                
        $data = $data->where('users.profile', $this->categoria_profile);
                
        }
    
        if(0 < strlen($this->buscar)) {
        $data = $data->where( function($query) {
			 $query->where('users.name', 'like',  '%' . $this->buscar . '%')
			->orWhere('users.email', 'like',$this->buscar . '%')
			->orWhere('users.id', 'like',$this->buscar . '%');
		});
	    }
        
        
        $data = $data->orderBy($this->columnaOrden, $this->direccionOrden)
        ->paginate($this->pagination);    
        
        $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
        ->where('sucursales.casa_central_id',$comercio_id)
        ->select('users.*','sucursales.sucursal_id')
        ->get();
        
         }
         


$this->cantidad_usuarios = User::select(User::raw('COUNT(users.id) as cantidad_usuarios'))->where('comercio_id', 'like', $comercio_id)->first();


       return view('livewire.users-administrador.component',[
        'data' => $data,
        'roles' => Role::orderBy('id','asc')->where('name','<>','Admin')->where('name','<>','Cliente')->get(),
        'sucursales' => $this->sucursales

    ])
       ->extends('layouts.theme-pos.app')
       ->section('content');
   }

   public function resetUI()
   {
    $this->agregar = 0;
    $this->name ='';
    $this->email='';
    $this->password='';
    $this->phone='';
    $this->image ='';
    $this->search ='';
    $this->status ='Elegir';
    $this->sucursal_id = Auth::user()->id;
    $this->selected_id =0;
    $this->resetValidation();
    $this->resetPage();
    $this->profile = 'Elegir';
}

public function Agregar(){
    $this->agregar = 1;
    $this->nombre_comercio_a_agregar = null;
}

public function edit(User $user)
{
    $this->agregar = 1;
    $this->nombre_comercio_a_agregar = null;
    $this->selected_id = $user->id;
    $this->name = $user->name;
    $this->phone = $user->phone;
    $this->profile = $user->profile;
    $this->status = $user->status;
    $this->email = $user->email;
    $this->sucursal_id = $user->comercio_id;
    $this->confirmed = $user->confirmed;
    $this->password ='';
    $this->plan_admin = $user->plan;
    if($user->email_verified_at != null) { $validar = 1; } else { $validar = 0; }
    $this->validar_email = $validar;
    
    $decryptedPassword = $user->token_pass ? Crypt::decrypt($user->token_pass) : null;
    $this->token_pass = $decryptedPassword;
    $this->prueba_gratis = Carbon::parse($user->prueba_hasta)->format("Y-m-d");
    //dd($this->validar_email);
    
    $this->emit('show-modal','open!');

}

public function AgregarMostrador(User $user)
{
    
    // si es casa central
    
    if($user->sucursal != 1) {
    $this->casa_central_user_id = $user->casa_central_user_id;
    $this->comercio_id = $user->casa_central_user_id;
    } else {
        
    // si es sucursal
    $this->casa_central_user_id = $user->casa_central_user_id;
    $this->comercio_id = $user->id;    
    }
    
    $this->nombre_comercio_a_agregar = $user->name;
    $this->sucursal_id = $user->id;
    
    $this->agregar = 1;
    $this->selected_id = 0;
    $this->name = '';
    $this->phone = '';
    $this->profile = '';
    $this->status = '';
    $this->email = '';
    $this->password ='';
    $this->plan_admin = $user->plan;

    
    //dd($this->validar_email);
    
    $this->emit('show-modal','open!');

}

public function StoreMostrador()
{
 $rules =[
    'name' => 'required|min:3',
    'email' => 'required|unique:users|email',
    'status' => 'required|not_in:Elegir',
    'profile' => 'required|not_in:Elegir',
    'password' => 'required|min:3'
];

$messages =[
    'name.required' => 'Ingresa el nombre',
    'name.min' => 'El nombre del usuario debe tener al menos 3 caracteres',
    'email.required' => 'Ingresa el correo ',
    'email.email' => 'Ingresa un correo válido',
    'email.unique' => 'El email ya existe en sistema',
    'status.required' => 'Selecciona el estatus del usuario',
    'status.not_in' => 'Selecciona el estatus',
    'profile.required' => 'Selecciona el perfil/role del usuario',
    'profile.not_in' => 'Selecciona un perfil/role distinto a Elegir',
    'password.required' => 'Ingresa el password',
    'password.min' => 'El password debe tener al menos 3 caracteres'
];

$this->validate($rules, $messages);

if(Auth::user()->comercio_id != 1)
$comercio_id = Auth::user()->comercio_id;
else
$comercio_id = Auth::user()->id;

if($this->comercio_id == $this->casa_central_id) {
    $sucursal = 0;
} else {
    $sucursal = 1;
}


$user = User::create([
    'name' => $this->name,
    'email' => $this->email,
    'phone' => $this->phone,
    'status' => $this->status,
    'plan' => $this->plan_admin,
    'profile' => $this->profile,
    'comercio_id' => $this->comercio_id,
    'casa_central_id' => $this->casa_central_id,
    'email_verified_at' => Carbon::now(),
    'prueba_hasta' => $this->prueba_gratis,
    'confirmed' => 1,
    'usuario_nuevo' => 0,
    'sucursal' => $sucursal,
    'password' => bcrypt($this->password)
]);

$user->syncRoles($this->profile);

if($this->image)
{
    $customFileName = uniqid() . ' _.' . $this->image->extension();
    $this->image->storeAs('public/users', $customFileName);
    $user->image = $customFileName;
    $user->save();
}

$this->resetUI();
$this->emit('user-added','Usuario Registrado');

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
 $rules =[
    'name' => 'required|min:3',
    'email' => 'required|unique:users|email',
    'status' => 'required|not_in:Elegir',
    'profile' => 'required|not_in:Elegir',
    'password' => 'required|min:3'
];

$messages =[
    'name.required' => 'Ingresa el nombre',
    'name.min' => 'El nombre del usuario debe tener al menos 3 caracteres',
    'email.required' => 'Ingresa el correo ',
    'email.email' => 'Ingresa un correo válido',
    'email.unique' => 'El email ya existe en sistema',
    'status.required' => 'Selecciona el estatus del usuario',
    'status.not_in' => 'Selecciona el estatus',
    'profile.required' => 'Selecciona el perfil/role del usuario',
    'profile.not_in' => 'Selecciona un perfil/role distinto a Elegir',
    'password.required' => 'Ingresa el password',
    'password.min' => 'El password debe tener al menos 3 caracteres'
];

$this->validate($rules, $messages);

if(Auth::user()->comercio_id != 1)
$comercio_id = Auth::user()->comercio_id;
else
$comercio_id = Auth::user()->id;

$this->plan = User::find($comercio_id);


if($this->profile == "Admin") {

$user = User::create([
    'name' => $this->name,
    'email' => $this->email,
    'phone' => $this->phone,
    'status' => $this->status,
    'plan' => $this->plan_admin,
    'profile' => $this->profile,
    'comercio_id' => $comercio_id,
    'email_verified_at' => Carbon::now(),
    'confirmed' => 1,
    'usuario_nuevo' => 0,
    'prueba_hasta' => $this->prueba_gratis,
    'password' => bcrypt($this->password)
]);

}

if(Auth::user()->sucursal == 1) {
// si es sucursal
    $sucursal = 1;
    $comercio_elegido = $comercio_id;
} else {
// si es casa central
    $sucursal = 0;
    $comercio_elegido = $this->sucursal_id;
}

if($this->profile != "Comercio" && $this->profile != "Admin") {


$user = User::create([
    'name' => $this->name,
    'email' => $this->email,
    'phone' => $this->phone,
    'status' => $this->status,
    'plan' => $this->plan->plan,
    'profile' => $this->profile,
    'comercio_id' => $comercio_elegido,
    'email_verified_at' => Carbon::now(),
    'confirmed' => 1,
    'usuario_nuevo' => 0,
    'sucursal' => $sucursal,
    'casa_central_user_id' => Auth::user()->casa_central_user_id,
    'password' => bcrypt($this->password)
]);

} else {

  $user = User::create([
      'name' => $this->name,
      'email' => $this->email,
      'phone' => $this->phone,
      'status' => $this->status,
      'plan' => $this->plan->plan,
      'profile' => $this->profile,
      'comercio_id' => $comercio_id,
      'usuario_nuevo' => 0,
      'sucursal' => $sucursal,
      'casa_central_user_id' => Auth::user()->casa_central_user_id,
      'password' => bcrypt($this->password)
  ]);

}

$user->syncRoles($this->profile);

if($this->image)
{
    $customFileName = uniqid() . ' _.' . $this->image->extension();
    $this->image->storeAs('public/users', $customFileName);
    $user->image = $customFileName;
    $user->save();
}

$this->resetUI();
$this->emit('user-added','Usuario Registrado');

}



public function StoreFacturacion()
{

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $datos_facturacion = datos_facturacion::create([
     'razon_social' => $this->razon_social,
     'domicilio_fiscal' => $this->domicilio_comercial,
     'condicion_iva' => $this->condicion_iva,
     'iibb' => $this->iibb,
     'cuit' => $this->cuit,
     'comercio_id' => $comercio_id
  ]);

  $this->resetUI();
  $this->emit('user-added','Datos de facturacion registrados');


}

public function Update()
{

    $rules =[
        'email' => ['required',Rule::unique('users')->ignore($this->selected_id)],
       // 'email' => "required|email|unique:users,email,{$this->selected_id}",
        'name' => 'required|min:3',
        'status' => 'required|not_in:Elegir',
        'profile' => 'required|not_in:Elegir'
    ];

    $messages =[
        'name.required' => 'Ingresa el nombre',
        'name.min' => 'El nombre del usuario debe tener al menos 3 caracteres',
        'email.required' => 'Ingresa el correo ',
        'email.email' => 'Ingresa un correo válido',
        'email.unique' => 'El email ya existe en sistema',
        'status.required' => 'Selecciona el estatus del usuario',
        'status.not_in' => 'Selecciona el estatus',
        'profile.required' => 'Selecciona el perfil/role del usuario',
        'profile.not_in' => 'Selecciona un perfil/role distinto a Elegir'
    ];

    $this->validate($rules, $messages);

        if(Auth::user()->sucursal == 1) {
    // si es sucursal
        $sucursal = 1;
        $comercio_elegido = $comercio_id;
    } else {
    // si es casa central
        $sucursal = 0;
        $comercio_elegido = $this->sucursal_id;
    }

    $user = User::find($this->selected_id);
    
    // si el usuario logueado es administrador flaminco
    
    if(auth()->user()->id == 1) {
    // validacion de mail  
    
    if($user->email_verified_at == null){
    if($this->validar_email == 1) { $validar = Carbon::now(); } else { $validar = null; }       
    } else {
    $validar = $user->email_verified_at;
    }
    
    // confirmacion de pago
    if($this->confirmed == 1) {$confirmed_at = Carbon::now();} else {$confirmed_at = null;}
    $confirmed = $this->confirmed;
    $plan =  $this->plan_admin;
    
    } else {
    
    // si el usuario logueado no es administrador flaminco    
    $validar = $user->email_verified_at;
    $confirmed_at = $user->confirmed_at;
    $confirmed = $user->confirmed;
    $plan = $user->plan;
    }
    
    $flujo = $user->flujo; // Valor por defecto del flujo
    
    // Si la fecha de prueba gratuita es mayor que hoy, asignar 2
    if (Carbon::parse($this->prueba_gratis)->gt(Carbon::today())) {
        $flujo = 2;
    }
    
    $decryptedPassword = Crypt::encrypt($this->password);
    
    $user->update([
        'name' => $this->name,
        'email' => $this->email,
        'phone' => $this->phone,
        'status' => $this->status,
        'profile' => $this->profile,
        'confirmed' => $this->confirmed,
        'confirmed_at' => $confirmed_at,
        'email_verified_at' => $validar,
        'prueba_hasta' => $this->prueba_gratis,
        'flujo' => $flujo,
        'plan' => $plan,
        'comercio_id' => $comercio_elegido,
        'password' => strlen($this->password) > 0 ? bcrypt($this->password) : $user->password,
        'token_pass' => strlen($this->password) > 0 ? $decryptedPassword : $user->token_pass
    ]);


    // Llama a tu método personalizado después de la verificación del email
    if($user->lead_soho_id != null){
    $this->updateLeadFromUser($user->id);
    }
         
    $otros_usuarios = User::where('casa_central_user_id',$user->id)->get();
    
    foreach($otros_usuarios as $otro_usuario){
      $u =  User::find($otro_usuario->id);
      $u->update([
        'confirmed' => $this->confirmed,
        'confirmed_at' => $confirmed_at,
        'email_verified_at' => $validar,          
        ]);
    }


    $user->syncRoles($this->profile);


    if($this->image)
    {
        $customFileName = uniqid() . ' _.' . $this->image->extension();
        $this->image->storeAs('public/users', $customFileName);
        $imageTemp = $user->image;

        $user->image = $customFileName;
        $user->save();

        if($imageTemp !=null)
        {
            if(file_exists('storage/users/' . $imageTemp)) {
                unlink('storage/users/' . $imageTemp);
            }
        }


    }

    //////////         DATOS DE FACTURACION           /////////////


    if($this->profile == "Comercio") {


    }

    $this->resetUI();
    $this->emit('user-updated','Usuario Actualizado');

}

public function UpdateFacturacion() {

  if(Auth::user()->comercio_id != 1)
  $comercio_id = Auth::user()->comercio_id;
  else
  $comercio_id = Auth::user()->id;

  $user = datos_facturacion::where('comercio_id',$comercio_id);

  $user->update([
     'razon_social' => $this->razon_social,
     'domicilio_fiscal' => $this->domicilio_comercial,
     'condicion_iva' => $this->condicion_iva,
     'iibb' => $this->iibb,
     'cuit' => $this->cuit,
     'comercio_id' => $comercio_id
  ]);

  $this->resetUI();
  $this->emit('user-updated','Datos de facturacion actualizados');
}

public function destroy(User $user)
{
 if($user) {
    $sales = Sale::where('user_id', $user->id)->count();
    if($sales > 0)  {
        $this->emit('user-withsales','No es posible eliminar el usuario porque tiene ventas registradas');
    } else {
        $user->delete();
        $this->resetUI();
        $this->emit('user-deleted','Usuario Eliminado');
    }
}
}





public function encryptText()
{
    $textToEncrypt = '22774385';

    // Usar bcrypt para cifrar la cadena
    $encryptedText = Hash::make($textToEncrypt);

    // $encryptedText ahora contiene la cadena cifrada
    dd($encryptedText);
}









}
