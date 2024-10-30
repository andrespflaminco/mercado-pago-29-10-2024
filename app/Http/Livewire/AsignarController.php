<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\sucursales;
use Livewire\WithPagination;
use DB;


class AsignarController extends Component
{
    use WithPagination;

    public $role,$search, $componentName, $permisosSelected = [], $old_permissions =[];
    private $pagination = 25;
    
    public $checkboxValues;

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function mount()
    {
        $this->role = 'Elegir';
        $this->componentName ='Asignar Permisos';      
        
        if(Auth::user()->comercio_id != 1)
    	$comercio_id = Auth::user()->comercio_id;
    	else
    	$comercio_id = Auth::user()->id;
    	
    	$this->comercio_id = $comercio_id;
    	
         $this->Map();
    }



    public function Map()
    {
        

        
        $casa_central_id = Auth::user()->casa_central_user_id;
        
        $this->permisos_modulos = Permission::all();
  
      	
    	$this->sucursales = sucursales::join('users','users.id','sucursales.sucursal_id')
        ->where('sucursales.casa_central_id',Auth::user()->casa_central_user_id)
        ->select('users.*','sucursales.sucursal_id')
        ->get();
    	
    	// Obtener un array de los IDs
        $this->ids_sucursales = $this->sucursales->pluck('sucursal_id')->toArray();
        array_push($this->ids_sucursales,$casa_central_id);    
        
      
        $permisos_map = Permission::join('role_has_permissions', 'role_has_permissions.permission_id', 'permissions.id')
            ->rightJoin('roles', 'roles.id', 'role_has_permissions.role_id')
           ->select(Permission::raw('CONCAT(roles.name, "|", roles.id) as rol'), 'permissions.name', 'permissions.id');
         
         //dd(Auth::user()->sucursal);
         // si es casa central
         if(Auth::user()->sucursal != 1){
         if(0 < $this->sucursales->count()) {
        		$permisos_map = $permisos_map->where( function($query) {
        				 $query->whereIn('roles.comercio_id',$this->ids_sucursales)
        					->orWhere('roles.comercio_id',1);
        		});
        		
            } else {
                
                $permisos_map = $permisos_map->where( function($query) {
            		 $query->where('roles.comercio_id', $this->comercio_id)
            		->orWhere('roles.comercio_id', 1);
            	});
            	        
            }
            
         } else {
            
            // Si es sucursal
            $permisos_map = $permisos_map->where('roles.mostrar_en_sucursales',1); // Filtrar solo aquellos roles que queremos mostrar en la sucursal

            $permisos_map = $permisos_map->where( function($query) {
        		 $query->where('roles.comercio_id', $this->comercio_id)
        		 ->orWhere('roles.comercio_id', Auth::user()->casa_central_user_id)
        		->orWhere('roles.comercio_id', 1);
        	});             
         }               

        	
        	$permisos_map = $permisos_map->where('roles.name','<>','Cliente');
		    
		    if(Auth::user()->profile != "Admin") {
		    $permisos_map = $permisos_map->where('roles.name','<>','Comercio');
		    }
		    
		    if(strlen($this->search) > 0) {
		     $permisos_map = $permisos_map->where('permissions.name', 'like', '%' . $this->search . '%');   
		    }
		    
            $permisos_map = $permisos_map
            ->orderBy('roles.id', 'asc')
            ->orderBy('roles.name', 'asc')
            ->orderBy('permissions.id', 'asc')
            ->get();

        // Obtener nombres únicos de permisos y roles
        $permisos_unicos = $permisos_map->pluck('name')->unique()->toArray();
        $roles_unicos = $permisos_map->pluck('rol')->unique()->toArray();

        // Inicializar la matriz con todos los valores predeterminados a 0
        $matriz = [];

        foreach ($permisos_unicos as $permiso) {
            foreach ($roles_unicos as $rol) {
                $matriz[$permiso][$rol] = 0;
            }
        }

        // Actualizar la matriz con 1 si hay coincidencia
        foreach ($permisos_map as $fila) {
            $matriz[$fila->name][$fila->rol] = 1;
        }

        // Obtén los ID de los permisos para pasar a la vista
        $permisos_ids = [];

        foreach ($permisos_map as $fila) {
            $permisos_ids[$fila->name] = $fila->id;
        }

        $this->permisos_ids = $permisos_ids;
        $this->matriz = $matriz;
        $this->roles_unicos = $roles_unicos;
        $this->permisos_unicos = $permisos_unicos;
    
        
        // Obtener una colección de todos los permisos
        $todosLosPermisos = Permission::pluck('id');

        foreach ($roles_unicos as $rol) {
            
            $partes = explode("|",$rol);
            $nombre_rol = $partes[0];
            $id_rol = $partes[1];
            
            // Obtener una colección de permisos para el rol actual
            $permisosRol = $permisos_map->where('rol', $rol)->pluck('id');
            
            //dd($permisosRol);
            
            // Verificar si el rol tiene todos los permisos
            $tieneTodosLosPermisos = $todosLosPermisos->diff($permisosRol)->isEmpty();
            
            // Establecer el valor del checkbox según si tiene todos los permisos o no
            
            $this->checkboxValues[$id_rol] = $tieneTodosLosPermisos;
        }
    }


    public function actualizarMatriz($permisoName, $rol, $state, $id_permiso)
    {
        
        // Emite un evento para informar a Livewire que el permiso ha sido actualizado
        $this->syncMatriz($state, $permisoName,$rol);
    }

    public function syncMatriz($state, $permisoName,$rol)
    {
        
        //dd($rol,$this->comercio_id);
        
        $partes = explode("|",$rol);
        $nombre_rol = $partes[0];
        $id_rol = $partes[1];
        
            $roleName = Role::find($id_rol);
            
            if($state)
            {
                $roleName->givePermissionTo($permisoName);
                $this->emit('permi',"Permiso asignado correctamente ");
    
            } else {
                $roleName->revokePermissionTo($permisoName);
                $this->emit('permi',"Permiso eliminado correctamente ");
            }

        $this->Map();
    }    
    
    
    public function render()
    {
        if(Auth::user()->comercio_id != 1)
    	$comercio_id = Auth::user()->comercio_id;
    	else
    	$comercio_id = Auth::user()->id;
    	
    	$this->comercio_id = $comercio_id;
    	
        $permisos = Permission::select('name','id', DB::raw("0 as checked") )
        ->orderBy('modulo','desc')
        ->orderBy('name','asc')
        ->paginate($this->pagination);

       
        
       // dd($permisos_map);
        
        if($this->role != 'Elegir')
        {
            $list = Permission::join('role_has_permissions as rp','rp.permission_id','permissions.id')
            ->where('role_id', $this->role)->pluck('permissions.id')->toArray();
            $this->old_permissions = $list;            
        }

        if($this->role != 'Elegir') 
        {
            foreach ($permisos as $permiso) {
                $role = Role::find($this->role);
                $tienePermiso = $role->hasPermissionTo($permiso->name);
                if($tienePermiso) {
                    $permiso->checked = 1;
                }
            }
        }

        $roles = Role::orderBy('name','asc');
        
        if(Auth::user()->profile == "Comercio"){
        $roles = $roles->where('comercio_id',$comercio_id)->orWhere('comercio_id',0);    
        }
        
        $roles = $roles->get();
        
        return view('livewire.asignar.component',[
            'roles' => Role::orderBy('name','asc')->get(),
            'permisos' => $permisos
        ])->extends('layouts.theme-pos.app')->section('content');
    }

    public $listeners = ['revokeall' => 'RemoveAll'];


    public function RemoveAll()
    {
       if($this->role =='Elegir')
       {
        $this->emit('sync-error','Selecciona un role válido');
        return;
    }

    $role = Role::find($this->role);
    $role->syncPermissions([0]);
    $this->emit('removeall',"Se revocaron todos los permisos al rol $role->name ");

}


    public function syncAll($rol)
    {
    
    // Accede al valor del checkbox utilizando la propiedad
    $valorCheckbox = $this->checkboxValues[$rol];
    
    //dd($rol,$valorCheckbox);
    
    if($valorCheckbox == true) {
        
    $role = Role::find($rol);
    $permisos = Permission::pluck('id')->toArray();
    $role->syncPermissions($permisos);

    $this->emit('syncall',"Se sincronizaron todos los permisos al rol $role->name ");
    
    } else {
    
    $role = Role::find($rol);
    $role->syncPermissions([0]);
    $this->emit('removeall',"Se revocaron todos los permisos al rol $role->name ");
    
    
    
    }
    
    $this->Map();
}


public function syncPermiso($state, $permisoName)
{
    
    
    
    if($this->role !='Elegir')
    {
        $roleName = Role::find($this->role);

        if($state)
        {
            dd($roleName, $permisoName);
            
            $roleName->givePermissionTo($permisoName);
            $this->emit('permi',"Permiso asignado correctamente ");

        } else {
            $roleName->revokePermissionTo($permisoName);
            $this->emit('permi',"Permiso eliminado correctamente ");
        }

    } else {
        $this->emit('permi',"Elige un role válido");
    }
    
}




}
