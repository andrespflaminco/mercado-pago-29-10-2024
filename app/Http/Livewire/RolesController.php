<?php

namespace App\Http\Livewire;


use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\sucursales;

use DB;


class RolesController extends Component
{

    use WithPagination;

    public $roleName, $search, $selected_id, $pageTitle, $componentName, $mostrar_en_sucursales;
    private $pagination = 15;


    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }


    public function mount()
    {

       $this->pageTitle = 'Listado'; 
       $this->componentName = 'Roles'; 
       $this->mostrar_en_sucursales = 1;
   }

   public function render()
   {
   

    if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;
	$casa_central_id = Auth::user()->casa_central_user_id;
	
	$this->comercio_id = $comercio_id;
	
	$this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
    ->where('sucursales.casa_central_id',Auth::user()->casa_central_user_id)
    ->select('users.*','sucursales.sucursal_id')
    ->get();
	
	// Obtener un array de los IDs
    $this->ids_sucursales = $this->sucursales->pluck('sucursal_id')->toArray();
    //dd($this->ids_sucursales);
    array_push($this->ids_sucursales,$casa_central_id);

    $roles = Role::select("*");
        
    if(Auth::user()->profile != "Admin") {  

    if(Auth::user()->sucursal != 1){
    
    if(0 < $this->sucursales->count()) {
		$roles = $roles->where( function($query) {
				 $query->whereIn('comercio_id',$this->ids_sucursales)
					->orWhere('comercio_id',1);
		});
		
    } else {
        $roles = $roles->where( function($query) {
    		 $query->where('comercio_id', $this->comercio_id)
    		->orWhere('comercio_id', 1);
    	});
    	        
    }
    } else {
        $roles = $roles->where( function($query) {
    		 $query->where('comercio_id', $this->comercio_id)
    		->orWhere('comercio_id', 1);
    	});
    	
    }
   
    }
    
    $roles = $roles->where('name','<>','Admin')->where('name','<>','Cliente');
    
    if(strlen($this->search) > 0) {
        $roles = $roles->where('name','like', '%' . $this->search . '%');    
    }    
    $roles = $roles->paginate($this->pagination);
    
    // dd($roles);
     
    $comercio_ids = $roles->pluck('comercio_id')->unique()->toArray();
    
    $users = User::whereIn('id',$comercio_ids)->get();
    
   return view('livewire.roles.component',[
    'roles' => $roles,
    'users' => $users
    ])
   ->extends('layouts.theme-pos.app')
   ->section('content');
}

public function CreateRole()
{
    
    if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;
		
    $rules = ['roleName' => 'required|min:2|unique:roles,name'];

    $messages = [
        'roleName.required' => 'El nombre del role es requerido',
        'roleName.unique' => 'El role ya existe',
        'roleName.min' => 'El nombre del role debe tener al menos 2 caracteres'
    ];

    $this->validate($rules, $messages);

    Role::create(['name' => $this->roleName,
    'comercio_id' => $comercio_id , 'mostrar_en_sucursal' => $this->mostrar_en_sucursales]);

    $this->emit('role-added', 'Se registró el role con éxito');
    $this->resetUI();

}

public function Edit(Role $role)
{
    //$role = Role::find($id);
    $this->selected_id = $role->id;
    $this->roleName = $role->name;
    $this->mostrar_en_sucursales = $role->mostrar_en_sucursales;

    $this->emit('show-modal','Show modal');
}

public function UpdateRole()
{
    $rules = ['roleName' => "required|min:2|unique:roles,name, {$this->selected_id}"];

    $messages = [
        'roleName.required' => 'El nombre del role es requerido',
        'roleName.unique' => 'El role ya existe',
        'roleName.min' => 'El nombre del role debe tener al menos 2 caracteres'
    ];

    $this->validate($rules, $messages);

    $role = Role::find($this->selected_id);
    $role->name = $this->roleName;
    $role->mostrar_en_sucursales = $this->mostrar_en_sucursales;
    $role->save();

    $this->emit('role-updated', 'Se actualizó el role con éxito');
    $this->resetUI();

}


protected $listeners = ['destroy' => 'Destroy'];


public function Destroy($id)
{
    $permissionsCount = Role::find($id)->permissions->count();
    if($permissionsCount > 0)
    {
        $this->emit('role-error', 'No se puede eliminar el role porque tiene permisos asociados');
        return;        
    }

    Role::find($id)->delete();
    $this->emit('role-deleted', 'Se eliminó el role con éxito');


} 



public function resetUI()
{
    $this->roleName ='';
    $this->search ='';
    $this->selected_id =0;
    $this->muestra_en_sucursales = 1;
    $this->resetValidation();
}

public function Agregar(){
    $this->emit('show-modal','show-modal');
}

public function Close(){
    $this->emit('hide-modal','show-modal');
    $this->resetUI();
}


}
