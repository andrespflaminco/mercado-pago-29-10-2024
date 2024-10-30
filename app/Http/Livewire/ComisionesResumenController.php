<?php

namespace App\Http\Livewire;

use App\Models\ComisionUsuario;
use App\Models\Product;
use App\Models\User;
use App\Models\Sale;
use App\Models\sucursales;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use App\Models\wocommerce;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;


use Carbon\Carbon;


class ComisionesResumenController extends Component
{

	use WithFileUploads;
	use WithPagination;

	public $name, $search, $image, $agregar,$id_check,$selected_id, $pageTitle, $componentName, $wc_category_id;
	private $pagination = 25;
	private $wc_category;
	public $comision, $vendedores, $user_id,$listado_ventas,$datos_vendedor,$total_comisiones;
	
	public $dateFrom, $dateTo;

	public function mount()
	{
	    $this->estado_filtro = 0;
	    $this->accion_lote = 'Elegir';
		$this->pageTitle = 'Listado';
		$this->componentName = 'Categorías';
		$this->vendedor_id = "Elegir";

        // Obtener el primer día de este año
        $this->dateFrom = $dateFrom ?? Carbon::now()->firstOfYear();
        // hasta
        $this->dateTo = $dateTo ?? Carbon::now()->format('d-m-Y');

        $this->listado_ventas = [];
	}

	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function render()
	{
	    
	    $this->SetFechas();
	    
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

        $this->vendedores = User::where('comercio_id',$this->comercio_id)->get();
        	
        $this->comisiones = $this->ComisionesPorVendedor();
        
        
		return view('livewire.comisiones-resumen.component', [
		    'comisiones' => $this->comisiones,
		    'vendedores' => $this->vendedores
		    ])
		->extends('layouts.theme-pos.app')
		->section('content');
	}
    
    public function FechaElegida($startDate, $endDate)
    {
      // Manejar las fechas seleccionadas aquí
      $this->dateFrom  = $startDate;
      $this->dateTo = $endDate;;
    }
    
    public function SetFechas() {
      if($this->dateFrom !== '' || $this->dateTo !== '')
      {
        $this->from = Carbon::parse($this->dateFrom)->format('Y-m-d').' 00:00:00';
        $this->to = Carbon::parse($this->dateTo)->format('Y-m-d').' 23:59:59';
    
      }
      
    }

    // 28-8-2024
    public function ComisionesPorVendedor(){
    
        
        $ventas = Sale::select(
                 'users.id as user_id',
                 'users.name',
                 Sale::raw('(SUM(sales.comision)) as comision'),
                 Sale::raw('(SUM(sales.subtotal) - IFNULL(SUM(sales.descuento_promo),0) - SUM(sales.descuento) + SUM(sales.recargo) + SUM(sales.iva) ) as total_ventas')
                 );
                $ventas = $ventas->where('users.comercio_id', $this->comercio_id);
                $ventas = $ventas->whereBetween('sales.created_at', [$this->from, $this->to]);
                $ventas = $ventas->where('sales.status', '<>' , 'Cancelado')
               ->join('users','users.id','sales.user_id')
               ->where('sales.eliminado',0)
               ->groupBy('users.id','users.name')
               ->orderBy('users.name','asc')
               ->get();
        
        return $ventas;
    
    }
	public function Edit($id)
	{
	    
	    $this->vendedores = User::where('comercio_id',$this->comercio_id)->get();
	    $this->agregar = 1;
		$record = ComisionUsuario::find($id);
		$this->selected_id = $record->id;
		$this->comision = $record->porcentaje_comision;
		$this->vendedor_id = $record->user_id;
		$this->image = null;

		$this->emit('show-modal', 'show modal!');
	}


    public function Ver($user_id) {
        $this->vendedor_id = $user_id;
        
        $this->datos_vendedor = User::find($user_id);
        
        $this->listado_ventas = Sale::where('user_id',$user_id)
        ->where('status','<>','Cancelado')
        ->where('eliminado',0)
        ->where('sales.comision','>',0)
        ->get();
        
        $this->total_comisiones = $this->listado_ventas->sum('comision');
        
        $this->agregar = 1;
    }
    
        public function resetUI() {
        $this->agregar = 0;
        }


	protected $listeners =[
	    'FechaElegida' => 'FechaElegida',
		'deleteRow' => 'Destroy',
		'Restaurar' => 'Restaurar',
        'accion-lote' => 'AccionEnLote'
	];

	public function Filtro($estado)
	{
	   $this->estado_filtro = $estado;
	   
	}


}
