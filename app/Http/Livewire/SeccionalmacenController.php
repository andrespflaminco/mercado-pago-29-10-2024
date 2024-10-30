<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\seccionalmacen;
use App\Models\Product;
use App\Models\User;
use App\Models\sucursales;

class SeccionalmacenController extends Component
{
  use WithPagination;
  use WithFileUploads;

  public $name, $agregar, $search, $selected_id,$nombre,$comercio_id,$pageTitle,$componentName;
  private $pagination = 7;


    public function mount()
    {
      $this->pageTitle = 'Listado';
      $this->componentName = 'Secciones del almacen';
      
    if(Auth::user()->comercio_id != 1)
    $this->comercio_id = Auth::user()->comercio_id;
    else
    $this->comercio_id = Auth::user()->id;
    
    }

    public function updatingSearch()
    {
      //$this->gotoPage(1);
       $this->resetPage();
    }


    protected function redirectLogin()
    {
        return \Redirect::to("login");
    }
    
	public function render()
	{
        if (!Auth::check()) {
            // Redirigir al inicio de sesi贸n y retornar una vista vac铆a
            $this->redirectLogin();
            return view('auth.login');
        }
        
        if(Auth::user()->comercio_id != 1)
        $this->comercio_id = Auth::user()->comercio_id;
        else
        $this->comercio_id = Auth::user()->id;
        
        $this->tipo_usuario = User::find($this->comercio_id);
        $this->sucursal_id = $this->comercio_id;
        		    
        if($this->tipo_usuario->sucursal != 1) {
        
        $this->casa_central_id = $this->comercio_id;
        	
        } else {
        	  
        $this->casa_central = sucursales::where('sucursal_id', $this->comercio_id)->first();
        $this->casa_central_id = $this->casa_central->casa_central_id;
        
        }

      if(strlen($this->search) > 0)
        $data = seccionalmacen::where('nombre', 'like', '%' . $this->search . '%')
        ->where('comercio_id', 'like', $this->casa_central_id)
        ->where('eliminado',0)
        ->paginate($this->pagination);
      else
        $data= seccionalmacen::where('comercio_id', 'like', $this->casa_central_id)
        ->where('eliminado',0)
        ->orderBy('nombre','asc')
        ->paginate($this->pagination);

      return view('livewire.seccionalmacens.component',[
        'seccionalmacen' => $data
      ])
      ->extends('layouts.theme-pos.app')
      ->section('content');
    }

    public function Edit($id)
    {
      $this->agregar = 1;
      $record = seccionalmacen::find($id);
      $this->selected_id = $record->id;
      $this->nombre = $record->nombre;

      $this->emit('show-modal','Show modal!');
      
    }


    public function Store()
    {
      //validation rules
      $rules = [
        'nombre' => ['required',Rule::unique('seccionalmacens')->where('comercio_id',$this->comercio_id)->where('eliminado',0)],
      ];

      //custom messages
      $customMessages = [
        'nombre.required' => 'Nombre del almacen requerido',
        'nombre.unique' => 'Ya existe el almacen',
      ];

      //execute validate
      $this->validate($rules, $customMessages);


      //insert
      $category =  seccionalmacen::create([
        'nombre' => $this->nombre,
        'comercio_id' => $this->comercio_id,
        'eliminado' => 0
      ]);

            // clear inputs
      $this->resetUI();
      // emit frontend notification
      $this->emit('almacen-added', 'Almacen Registrado');
      $this->emit('msg', 'Almacen Registrado');
    }





    public function Update()
    {
      //validation rules
      $rules = [
      'nombre' => ['required',Rule::unique('seccionalmacens')->ignore($this->selected_id)->where('comercio_id',$this->comercio_id)->where('eliminado',0)],
      ];


      //custom messages
      $customMessages = [
        'nombre.required' => 'Nombre del almacen requerido',
        'nombre.unique' => 'Ya existe el almacen',
      ];

      //execute validate
      $this->validate($rules, $customMessages);

      //update
      $category = seccionalmacen::find($this->selected_id);
      $category->update([
        'nombre' => $this->nombre
      ]);


      $this->resetUI();
      $this->emit('almacen-updated', 'Categoría Actualizada');
      $this->emit('msg', 'Almacen Actualizado');
    }


    // reset values inputs
    public function resetUI()
    {
      $this->name ='';
      $this->nombre ='';
      $this->image = null;
      $this->search ='';
      $this->selected_id = 0;
      //$this->gotoPage(1);
      $this->resetPage();
      $this->agregar = 0;

    }
    //events listeners
    protected $listeners = [
      'deleteRow'   => 'Destroy',
      'scan-code' =>'ScanCode'
    ];

    public function Destroy(seccionalmacen $id)
    {
      $id->eliminado = 1;
      $id->save();


        // Volver todos los productos a sin almacen
        
        $g = Product::where('seccionalmacen_id',$id->id)->get();
    
        foreach($g as $p) {
        $p->seccionalmacen_id = 1;
        $p->save();
        }
        
        //

      $this->resetUI();
      $this->emit('almacen-deleted', 'Producto Eliminado');
      $this->emit('msg', 'Almacen Eliminado');
    }


public function Agregar() {
    $this->agregar = 1;
}




}
