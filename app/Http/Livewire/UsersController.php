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

class UsersController extends Component
{
    use WithPagination;
    use WithFileUploads;
    use ZohoCRMTrait;

    public $name,$phone,$email,$validar_email,$nombre_comercio_a_agregar,$validacion_email, $detalle_facturacion,$agregar, $sucursal_id,$buscar, $cuit, $domicilio_comercial, $selected_id_facturacion, $iibb, $condicion_iva, $razon_social, $status,$image,$password,$selected_id,$fileLoaded,$profile, $comercio_id, $usuario_id, $confirmed, $cantidad_usuarios;
    public $pageTitle, $count_usuarios, $componentName,$lista_precios, $search, $categoria_profile, $plan_admin,$sucursales;
    private $pagination = 30;
    
    public $sucursal_filtro;
    
    
    public $roles = [];

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function mount()
    {
        $this->sucursal_filtro = "all";
        $this->buscar = '';
        $this->detalle_facturacion = [];
        $this->pageTitle ='Listado';
        $this->componentName ='Usuarios';
        $this->status ='Elegir';
        $this->buscar = '';
        $this->sucursal_id = Auth::user()->id;
        $this->nombre_comercio_a_agregar = null;
    }


    public function render()
    {
        
       
        $usuario_id = Auth::user()->id;

        if(Auth::user()->comercio_id != 1)
        $comercio_id = Auth::user()->comercio_id;
        else
        $comercio_id = Auth::user()->id;

        $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')->where('comercio_id',$comercio_id)->select('sucursales.*','users.name')->get();
        
        $count_usuarios = User::where('casa_central_user_id',$comercio_id)->count();
        
        $this->count_usuarios = $count_usuarios;
        
        // Admin flaminco 
        
        
         if(Auth::user()->id == 1) {
     
        $data = User::select('*');
        
        if($this->categoria_profile) {
                
        $data = $data->where('users.profile', $this->categoria_profile);
                
        }
        
        if(0 < strlen($this->buscar)) {
        $data = $data->where('users.name', 'like',  '%' . $this->buscar . '%');    
        }
        
        
        $data = $data->orderBy('profile','desc')
        ->paginate($this->pagination);    
        
        $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
        ->where('sucursales.casa_central_id',$comercio_id)
        ->select('users.*','sucursales.sucursal_id')
        ->get();
        
         }
         
         if(Auth::user()->id != 1) {
        //dd(strlen($this->buscar));
        // <!---- Si es casa central ------>
        
        if(Auth::user()->sucursal != 1) {
        
        $data = User::select('*');
        
        if($this->categoria_profile) {
                
        $data = $data->where('users.profile', $this->categoria_profile);
                
        }
        
        if(0 < strlen($this->buscar)) {
        $data = $data->where('users.name', 'like',  '%' . $this->buscar . '%');    
        }
        
        if($this->sucursal_filtro != "all"){
        $data = $data->where('users.comercio_id', $this->sucursal_filtro)->orWhere('users.id', $this->sucursal_filtro);    
        }
        
        $data = $data->where('users.casa_central_user_id', $comercio_id);
        
        if($this->sucursal_filtro == "all"){
        $data = $data->orWhere('users.id', 'like', $usuario_id);
        }
                
                
        $data = $data->orderBy('profile','asc')
        ->paginate($this->pagination);    
        
        $this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
        ->where('sucursales.casa_central_id',$comercio_id)
        ->select('users.*','sucursales.sucursal_id')
        ->get();
        
        
        }
        
        // <!---- Si es sucursal ------>
        
        if(Auth::user()->sucursal == 1) {
       
        $data = User::select('*');
        
        if(Auth::user()->profile == "Admin") {
                
        if($this->categoria_profile) {
                
        $data = $data->where('users.profile', $this->categoria_profile);
                
        }
        }
        
        if(0 < strlen($this->buscar)) {
        $data = $data->where('name', 'like',  '%' . $this->buscar . '%');    
        }
        
        $data = $data->where('comercio_id', 'like', $comercio_id)
        ->orWhere('id', 'like', $usuario_id);
        
        
        $data = $data->orderBy('profile','asc')
        ->paginate($this->pagination);
        
        $this->sucursales = null;   
        }
        
        }


$this->cantidad_usuarios = User::select(User::raw('COUNT(users.id) as cantidad_usuarios'))->where('comercio_id', 'like', $comercio_id)->first();

$this->comercio_id = $comercio_id;

//dd($this->sucursales);

// Obtener un array de los IDs
if($this->sucursales != null){
$this->ids_sucursales = $this->sucursales->pluck('sucursal_id')->toArray();
}

        $roles = Role::orderBy('id','asc');

        if(Auth::user()->sucursal != 1){

        if(0 < $this->sucursales->count()) {
			$roles = $roles->where( function($query) {
				 $query->whereIn('comercio_id',$this->ids_sucursales)
				    ->orWhere('comercio_id',$this->comercio_id)
					->orWhere('comercio_id',1);
				});
        } else{
			$roles = $roles->where( function($query) {
				 $query->where('comercio_id',$this->comercio_id)
					->orWhere('comercio_id',1);
				});            
        }

		} else {

			$roles = $roles->where( function($query) {
				 $query->where('comercio_id',$this->comercio_id)
					->orWhere('comercio_id',1);
				});
		    
		}
		
		
				
		$roles = $roles->where('name','<>','Admin')->where('name','<>','Cliente')->get();
				
		$this->roles = $roles;
		
       return view('livewire.users.component',[
        'data' => $data,
        'roles' => $roles,
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
}

public function edit(User $user)
{
    $this->agregar = 1;
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

    //dd($this->validar_email);
    
    $this->emit('show-modal','open!');

}

public function AgregarMostrador(User $user)
{
    
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

$TokenPass = Crypt::encrypt($this->password);

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
    'password' => bcrypt($this->password),
    'token_pass' => $TokenPass
]);

}

$u = User::find($this->sucursal_id);

if($u->sucursal == 1) {
// si es sucursal
    $sucursal = 1;
    $comercio_elegido = $this->sucursal_id;
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
    'prueba_hasta' => $this->plan->prueba_hasta,
    'usuario_nuevo' => 0,
    'sucursal' => $sucursal,
    'casa_central_user_id' => Auth::user()->casa_central_user_id,
    'password' => bcrypt($this->password),
    'token_pass' => $TokenPass
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
      'prueba_hasta' => $this->plan->prueba_hasta,
      'casa_central_user_id' => Auth::user()->casa_central_user_id,
      'password' => bcrypt($this->password),
      'token_pass' => $TokenPass
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


//////////         DATOS DE FACTURACION           /////////////





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

    $user = User::find($this->selected_id);
    $TokenPass = Crypt::encrypt($this->password);
    
    // si el usuario logueado es administrador flaminco
    
    if(auth()->user()->id == 1) {
    // validacion de mail  
    if($this->validar_email == 1) { $validar = Carbon::now(); } else { $validar = null; }   
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
    
    $user->update([
        'name' => $this->name,
        'email' => $this->email,
        'phone' => $this->phone,
        'status' => $this->status,
        'profile' => $this->profile,
        'confirmed' => $this->confirmed,
        'confirmed_at' => $confirmed_at,
        'email_verified_at' => $validar,
        'plan' => $plan,
        'password' => strlen($this->password) > 0 ? bcrypt($this->password) : $user->password,
        'token_pass' => strlen($this->password) > 0 ? $TokenPass : $user->password 
    ]);


   $user->syncRoles($this->profile);

    // Llama a tu método personalizado después de la verificación del email
    if($user->lead_soho_id != null){
    $this->updateLeadFromUser($user->id);
    }
            
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

    $user->syncRoles($this->profile);
    
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
