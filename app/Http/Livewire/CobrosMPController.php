<?php

namespace App\Http\Livewire;


use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Carbon\Carbon;
use MP;


class CobrosMPController extends Component
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
		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		if(strlen($this->search) > 0)
			$data = Category::where('name', 'like', '%' . $this->search . '%')
			->where('comercio_id', 'like', $comercio_id)
			->paginate($this->pagination);
		else
			$data = Category::where('comercio_id', 'like', $comercio_id)
			->orderBy('id','desc')
			->paginate($this->pagination);


		return view('livewire.cobros-mp.categories', ['categories' => $data])
		->extends('layouts.theme.app')
		->section('content');
	}



	public function Edit($id)
	{
		$record = Category::find($id, ['id','name','image']);
		$this->name = $record->name;
		$this->selected_id = $record->id;
		$this->image = null;

		$this->emit('show-modal', 'show modal!');
	}

	public function Click()
	{

	    $preapproval_data = [
	      'payer_email' => 'agariobadcell@gmail.com',
	      'back_url' => 'http://labhor.com.ar/laravel/public/preapproval',
	      'reason' => 'Subscripción a paquete premium',
	      'external_reference' => '8161551216975588',
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



	public function Store()
	{
		$rules = [
			'name' => 'required|min:3'
		];

		$messages = [
			'name.required' => 'Nombre de la categoría es requerido',
			'name.min' => 'El nombre de la categoría debe tener al menos 3 caracteres'
		];

		$this->validate($rules, $messages);

		if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		$category = Category::create([
			'name' => $this->name,
			'comercio_id' => $comercio_id
		]);


		$customFileName;
		if($this->image)
		{
			$customFileName = uniqid() . '_.' . $this->image->extension();
			$this->image->storeAs('public/categories', $customFileName);
			$category->image = $customFileName;
			$category->save();
		}

		$this->resetUI();
		$this->emit('category-added','Categoría Registrada');

	}


	public function Update()
	{
		$rules =[
			'name' => "required|min:3|unique:categories,name,{$this->selected_id}"
		];

		$messages =[
			'name.required' => 'Nombre de categoría requerido',
			'name.min' => 'El nombre de la categoría debe tener al menos 3 caracteres'
		];

		$this->validate($rules, $messages);


		$category = Category::find($this->selected_id);
		$category->update([
			'name' => $this->name
		]);

		if($this->image)
		{
			$customFileName = uniqid() . '_.' . $this->image->extension();
			$this->image->storeAs('public/categories', $customFileName);
			$imageName = $category->image;

			$category->image = $customFileName;
			$category->save();

			if($imageName !=null)
			{
				if(file_exists('storage/categories' . $imageName))
				{
					unlink('storage/categories' . $imageName);
				}
			}

		}

		$this->resetUI();
		$this->emit('category-updated', 'Categoría Actualizada');



	}


	public function resetUI()
	{
		$this->name ='';
		$this->image = null;
		$this->search ='';
		$this->selected_id =0;
	}

	protected $listeners =[
		'deleteRow' => 'Destroy'
	];


	public function Destroy(Category $category)
	{

		$imageName = $category->image;
		$category->delete();

		if($imageName !=null) {
			unlink('storage/categories/' . $imageName);
		}

		$this->resetUI();
		$this->emit('category-deleted', 'Categoría Eliminada');

	}



}
