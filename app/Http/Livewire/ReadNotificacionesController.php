<?php

namespace App\Http\Livewire;


use App\Models\Category;
use App\Models\User;
use Notification;
use App\Notifications\NotificarCambios;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Automattic\WooCommerce\Client;
use App\Models\descargas;
use Livewire\WithFileUploads;
use Livewire\WithPagination;


class ReadNotificacionesController extends Component
{

	public function mount($id)
	{
	    $this->id = $id;
	}

	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}


	public function render()
	{
	    
	$user = User::find(auth()->user()->id);
    foreach ($user->unreadNotifications as $notification) {
    $notification->markAsRead();

    return redirect('descargas');
    }

	}


}
