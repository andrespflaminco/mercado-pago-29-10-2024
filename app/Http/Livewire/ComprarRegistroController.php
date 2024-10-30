<?php

namespace App\Http\Livewire;


use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use MP;


class ComprarRegistroController extends Component
{

	use WithFileUploads;
	use WithPagination;



	public $name, $search, $image, $selected_id, $pageTitle, $componentName;
	private $pagination = 5;


	public function mount()
	{
		$this->pageTitle = 'Listado';
		$this->componentName = 'Categorías';


	}

	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function render()
	{


		return view('livewire.comprar-registro.component')
		->extends('layouts.theme2.app')
		->section('content');
	}


	public function getCreatePreapproval()
  {
    $preapproval_data = [
      'payer_email' => 'agariobadcell@gmail.com',
      'back_url' => 'http://labhor.com.ar/laravel/public/preapproval',
      'reason' => 'Subscripción a paquete premium',
      'external_reference' => $subscription->id,
      'auto_recurring' => [
        'frequency' => 1,
        'frequency_type' => 'months',
        'transaction_amount' => 99,
        'currency_id' => 'ARS',
        'start_date' => Carbon::now()->addHour()->format('Y-m-d\TH:i:s.BP'),
        'end_date' => Carbon::now()->addMonth()->format('Y-m-d\TH:i:s.BP'),
      ],
    ];

    MP::create_preapproval_payment($preapproval_data);

    return dd($preapproval);





}


}
