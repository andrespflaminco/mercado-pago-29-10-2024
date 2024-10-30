<?php

namespace App\Http\Livewire;


use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\sucursales;
use App\Models\SuscripcionControl;
use DB;
use Illuminate\Support\Facades\Log;

class SuscripcionesControlController extends Component
{

    use WithPagination;

    public  $user_id, $suscripcion_id, $plan_id, $payer_id, $payer_email, $suscripcion_status, $cobro_status, $nombre_comercio, $fecha_suscripcion; 
    public $monto_mensual, $monto_plan, $users_amount, $users_count;
    public $init_point, $external_reference, $plan_id_flaminco, $proximo_cobro, $pagado, $reintentos, $action;

    public $search, $selected_id, $pageTitle, $componentName;
    private $pagination = 15;


    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }


    public function mount()
    {

        $this->pageTitle = 'Listado';
        $this->componentName = 'Suscripcion Control';
    }

    public function render()
    {  
    $suscripcionesControl = SuscripcionControl::query();
    
    if (!empty($this->search)) {
        $search_fields = 'user_id, suscripcion_id, plan_id, payer_id, payer_email, nombre_comercio';
        $suscripcionesControl = $suscripcionesControl->whereRaw(
            'CONCAT_WS(" ", ' . $search_fields . ') LIKE ?', ['%' . $this->search . '%']
        );
    }
    
    $suscripcionesControl = $suscripcionesControl->orderBy('id', 'DESC')->paginate($this->pagination);

        return view('livewire.suscripciones-control.component', [
            'suscripcionesControl' => $suscripcionesControl,
        ])
            ->extends('layouts.theme-pos.app')
            ->section('content');
    }

    public function CreateSuscripcionControl()
    {
        

        SuscripcionControl::create([
            'payer_email' => $this->payer_email,
        ]);

        $this->emit('suscripcion-control-added', 'Se registró la suscripcion control con éxito');
        $this->resetUI();
    }

    public function Edit(SuscripcionControl $suscripcionControl)
    {
        
        $this->selected_id = $suscripcionControl->id;
        $this->user_id = ($suscripcionControl->user?$suscripcionControl->user->name:'-');
        $this->suscripcion_id = $suscripcionControl->suscripcion_id;
        $this->plan_id = $suscripcionControl->plan_id;
        $this->payer_id = $suscripcionControl->payer_id;
        $this->payer_email = $suscripcionControl->payer_email;
        $this->suscripcion_status = $suscripcionControl->suscripcion_status;
        $this->cobro_status = $suscripcionControl->cobro_status;
        $this->nombre_comercio = $suscripcionControl->nombre_comercio;
        $this->fecha_suscripcion = $suscripcionControl->fecha_suscripcion;
        $this->monto_mensual = $suscripcionControl->monto_mensual;
        $this->monto_plan = $suscripcionControl->monto_plan;
        $this->users_amount = $suscripcionControl->users_amount;
        $this->users_count = $suscripcionControl->users_count;
        $this->init_point = $suscripcionControl->init_point;
        $this->external_reference = $suscripcionControl->external_reference;
        $this->plan_id_flaminco = ($suscripcionControl->planFlaminco?$suscripcionControl->planFlaminco->nombre:'-');
        $this->proximo_cobro = $suscripcionControl->proximo_cobro;
        $this->pagado = $suscripcionControl->pagado;
        $this->reintentos = $suscripcionControl->reintentos;
        $this->action = $suscripcionControl->action;

        $this->emit('show-modal', 'Show modal');
    }

    public function UpdateSuscripcionControl()
    {
        

        $suscripcionControl = SuscripcionControl::find($this->selected_id);
        $suscripcionControl->payer_email = $this->payer_email;
        $suscripcionControl->save();

        $this->emit('suscripcion-control-updated', 'Se actualizó la suscripcion control con éxito');
        $this->resetUI();
    }


    protected $listeners = ['destroy' => 'Destroy'];


    public function Destroy($id)
    {
        SuscripcionControl::find($id)->delete();
        $this->emit('suscripcion-control-deleted', 'Se eliminó la suscripcion control con éxito');
    }



    public function resetUI()
    {
        $this->payer_email = '';
        $this->search = '';
        $this->selected_id = 0;
        $this->resetValidation();
    }

    public function Agregar()
    {
        $this->emit('show-modal', 'show-modal');
    }

    public function Close()
    {
        $this->emit('hide-modal', 'show-modal');
        $this->resetUI();
    }
}
