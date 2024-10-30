<?php

namespace App\Http\Livewire;


use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use App\Models\descargas;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Carbon\Carbon;


class DescargasController extends Component
{

	use WithFileUploads;
	use WithPagination;

	public $name, $search, $image, $selected_id, $pageTitle, $componentName, $wc_category_id;
	private $pagination = 25;
	private $wc_category;

	public function mount()
	{
		$this->pageTitle = 'Listado';
		$this->componentName = 'Exportaciones';


	}

	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function render()
	{
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		if(strlen($this->search) > 0)
			$reportes = descargas::where('name', 'like', '%' . $this->search . '%')
			->where('user_id', 'like', $comercio_id)
			->paginate($this->pagination);
		else
			$reportes = descargas::where('user_id', 'like', $comercio_id)
			->orderBy('id','desc')
			->paginate($this->pagination);


		return view('livewire.descargas.component', ['reportes' => $reportes])
		->extends('layouts.theme-pos.app')
		->section('content');
	}


public function Descargar($id) {
    
    $descarga = descargas::find($id);
    
    $file = $descarga->nombre;
    
    if($descarga->tipo == "exportar_productos") {
        
    $path = base_path("storage/app/catalogos/". $file . ".xlsx");    
    
        
    }
    
    if($descarga->tipo == "exportar_etiquetas") {
        
    $path = base_path("storage/app/etiquetas/". $file . ".pdf");    
    
        
    }
    
    if($descarga->tipo == "exportar_etiquetas_excel") {
        
    $path = base_path("storage/app/etiquetas/". $file . ".xlsx");    
    
        
    }
    
    
    
    return response()->download($path);
    
}


public function DescargarExcel($id) {
   
    $descarga = descargas::find($id);
    
    $file = $descarga->nombre;
    
    $path = base_path("storage/app/etiquetas/". $file . ".xlsx");    
    
    return response()->download($path);
    
}



}
