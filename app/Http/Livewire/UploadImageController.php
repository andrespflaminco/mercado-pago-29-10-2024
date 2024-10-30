<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\imagenes;
use Intervention\Image\ImageManagerStatic as Image;
use Carbon\Carbon;

class UploadImageController extends Component
{
  use WithFileUploads;

    public $files = [];


    public $images = [];
    public $imagenes = [];
    public $base64Images = [];
    public $nombre_imagen;
    public $progress = 0;


    protected function redirectLogin()
    {
        return \Redirect::to("login");
    }
	public function render()
	{
   
    if(!Auth::check()) {
            // Redirigir al inicio de sesión y retornar una vista vacía
            $this->redirectLogin();
            return view('auth.login');
        }
        
         if(Auth::user()->comercio_id != 1)
		$comercio_id = Auth::user()->comercio_id;
		else
		$comercio_id = Auth::user()->id;
         
         $this->imagenes = imagenes::where('comercio_id', $comercio_id)->where('eliminado',0)->orderBy('created_at','desc')->get();

        return view('livewire.image-upload.component', [
        'imagenes' => $this->imagenes
		])
		->extends('layouts.theme-pos.app')
		->section('content');



     }
     
   	protected $listeners =[
		'deleteImage' => 'DestroyImage',
		'StoreImagen' => 'StoreImagen',
		'fileUpload' => 'handleFileUpload',
        'fileUploadProgress' => 'updateProgress',
	];


    
     public function updatedImages()
    {
        $this->progress = 0;
        $this->saveImages();
    }


    public function handleFileUpload($uploadedImages)
    {
        $this->images = $uploadedImages;
        $this->saveImages();
    }

    public function updateProgress($progress)
    {
        $this->progress = $progress;
    }

    public function saveImages()
    {
        foreach ($this->images as $key => $image) {
            $this->validate([
                'images.' . $key => 'image|max:1024', // 1MB Max
            ]);

            $originalName = $image->getClientOriginalName();

            // Generar un nombre único para cada imagen
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            // Guardar la imagen en el directorio /storage/products/
            $imagePath = $image->storeAs('products', $imageName, 'public');

            // Leer el contenido del archivo para convertirlo a base64
            $imageContent = Storage::disk('public')->get($imagePath);
            $base64Image = base64_encode($imageContent);
            $casa_central_id = Auth::user()->casa_central_user_id;
    
            // Almacenar los datos en la base de datos
            imagenes::create([
                'name' => $originalName,
                'url' => $imageName,
                'base64' => $base64Image,
                'comercio_id' => $casa_central_id, // Asegúrate de ajustar esto a tu lógica
                'eliminado' => false,
            ]);

            // Emitir progreso de carga
            $this->progress = ($key + 1) / count($this->images) * 100;

            // Emitir evento para actualizar el progreso
            $this->emit('fileUploadProgress', $this->progress);
        }

        // Resetear imágenes después de la carga
        $this->images = [];
        $this->progress = 0;
    }
    
   public function StoreImagen($imagenBase64, $nombreArchivo)
    {
   
    //dd($nombreArchivo);
    // dd($base64);
   
    if(Auth::user()->comercio_id != 1)
	$comercio_id = Auth::user()->comercio_id;
	else
	$comercio_id = Auth::user()->id;
    
    // Obtener el nombre de la imagen
    $nombre_imagen1 = $nombreArchivo;
    
    $nombre_imagen2 =str_replace(' ', '+|-|+', $nombre_imagen1); 
        
    $nombre_imagen2 = Carbon::now()->format('d_m_Y_H_i_s') . '_' . $nombre_imagen2;

 
    // Decodificar la cadena Base64 y guardar la imagen
    $image = Image::make($imagenBase64);
    $image->save(public_path('/storage/products/'.$nombre_imagen2));
    
     
    if ($image) {
    
    imagenes::create([
        'name' => $nombre_imagen1,
        'url' => $nombre_imagen2,
        'base64' => $imagenBase64,
        'comercio_id' => $comercio_id,
        'eliminado' => 0
    ]);


    return redirect('image');
    
        
    } else {
    
    }

    
   
    }


public function DestroyImage($id) {


$imagen = imagenes::find($id);

$imageTemp = $imagen->url;
$imagen->eliminado = 1;
$imagen->save();

		if($imageTemp !=null) {
			if(file_exists('storage/products/' . $imageTemp )) {
				unlink('storage/products/' . $imageTemp);
			}
		}
    
}


}
