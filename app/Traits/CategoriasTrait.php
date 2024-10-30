<?php
namespace App\Traits;


// Trait


// Modelos
use App\Models\Category;
use App\Models\Product;
use App\Models\wocommerce;

// services 

use App\Services\CartVariaciones;

// Otros

use Illuminate\Support\Facades\Storage;
use Notification;
use App\Notifications\NotificarCambios;
use Illuminate\Validation\Rule;
use DB;
use Intervention\Image\Facades\Image;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use Carbon\Carbon;


trait CategoriasTrait {

public $name_categoria, $image_categoria;


    // Funcion que crea una nueva categoria
	
	public function StoreCategoria()
	{
	    
	    if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;

		
		$rules = [
		'name_categoria' => 'required|min:3'
		];

		$messages = [
			'name_categoria.required' => 'Nombre de la categoría es requerido',
			'name_categoria.min' => 'El nombre de la categoría debe tener al menos 3 caracteres'
		];

		$this->validate($rules, $messages);

			////////// WooCommerce ////////////

			$wc = wocommerce::where('comercio_id', $comercio_id)->first();

			if($wc != null){

			$woocommerce = new Client(
				$wc->url,
				$wc->ck,
				$wc->cs,

					[
							'version' => 'wc/v3',
					]
			);

			$data = [
			    'name' => $this->name_categoria,
			    'image' => [
			        'src' => ''
			    ]
			];

			$this->wc_category = $woocommerce->post('products/categories', $data);

		}

        if($wc != null){
            $this->wc_category_id = $this->wc_category->id;
        } else {
            $this->wc_category_id = 0;
        }
		////////////////////////////////////////////////

		$category = Category::create([
			'name' => $this->name_categoria,
			'comercio_id' => $comercio_id,
			'wc_category_id' => $this->wc_category_id
		]);


		$customFileName;
		if($this->image_categoria)
		{
			$customFileName = uniqid() . '_.' . $this->image_categoria->extension();
			$this->image_categoria->storeAs('public/categories', $customFileName);
			$category->image = $customFileName;
			$category->save();
		}

		$this->resetUI();
		$this->emit('category-added','Categoría Registrada');

	}
	
	// Funcion que trae los datos de la categoria y las muestra en el modal
	
	public function EditCategoria($id) {
	    
	    $record = Category::find($id, ['id','name','image']);
		$this->name_categoria = $record->name;
		$this->selected_id = $record->id;
		$this->image_categoria = null;

		$this->emit('show-modal', 'show modal!');
	
	    
	}
	
	// Funcion que actualiza la categoria
	
	public function UpdateCategoria() {
	    
	    if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
		
		$rules =[
		'name_categoria' => ['required',Rule::unique('categories')->ignore($this->selected_id)->where('comercio_id',$comercio_id)->where('eliminado',0)],
		];

		$messages =[
		'name_categoria.required' => 'Nombre de categoría requerido',
		'name_categoria.min' => 'El nombre de la categoría debe tener al menos 3 caracteres'
		];

		$this->validate($rules, $messages);


		$category = Category::find($this->selected_id);

		////////// WooCommerce ////////////
	
		$wc = wocommerce::where('comercio_id', $comercio_id)->first();

		if($wc != null){

		$woocommerce = new Client(
			$wc->url,
			$wc->ck,
			$wc->cs,

				[
						'version' => 'wc/v3',
				]
		);

		$data = [
				'name' => $this->name_categoria,
				'image' => [
						'src' => ''
				]
		];

		$woocommerce->put('products/categories/'.$category->wc_category_id , $data);

	}

	////////////////////////////////////////////////

		$category->update([
			'name' => $this->name_categoria
		]);

		if($this->image)
		{
			$customFileName = uniqid() . '_.' . $this->image_categoria->extension();
			$this->image_categoria->storeAs('public/categories', $customFileName);
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

}
