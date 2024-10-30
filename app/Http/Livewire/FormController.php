<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class FormController extends Component
{
  use WithPagination;
	use WithFileUploads;


	public $nombre,$recargo,$descripcion,$price,$stock,$alerts,$categoryid,$search,$image,$selected_id,$pageTitle,$componentName,$comercio_id, $almacen, $stock_descubierto, $metodos, $metodo, $categoria, $cuit, $CBU, $tipo, $id_check;
	private $pagination = 25;


	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function mount($plan)
	{
		$this->plan_id = $plan;
	}





	public function render()
	{

		return view('auth.form.component', [
		    'plan_id' => $this->plan_id
		])
		->extends('layouts.theme2.app')
		->section('content');

	}



}
