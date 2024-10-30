<?php

namespace App\Http\Livewire;

use App\Models\ModulosSuscripcion;
use Livewire\Component;
use App\Models\User;
use App\Models\planes_suscripcion;
use App\Models\suscripciones;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


use App\Services\MPService;

use Illuminate\Support\Facades\Redirect;
//Add Lucas

//Traits
use App\Traits\ConsumesExternalServices;
use App\Traits\CartTrait;

use App\Models\Suscripcion;
use App\Models\SuscripcionCobros;
use Illuminate\Http\Request;

use App\Traits\FacebookTrait;

class SuscripcionesConfiguracionController extends Scaner //Component
{

	use WithPagination;
	use WithFileUploads;
	use CartTrait;
	use ConsumesExternalServices;
	use FacebookTrait;


	public $name, $barcode, $rentabilidad, $price, $stock, $alerts, $categoryid, $search, $image, $selected_id, $pageTitle, $componentName, $comercio_id, $almacen, $stock_descubierto, $inv_ideal, $proveedor, $mensajes, $cod_proveedor, $kg, $proveedor_elegido, $status, $noticias, $archivo;
	public $id_almacen;
	public $id_categoria;
	public $id_proveedor;
	public $plan_id_flaminco;
	private $pagination = 25;

    protected $MPService;
	
	public $SelectedProducts = [];
	public $selectedAll = FALSE;

	//Add Lucas
	public $user;
	protected $baseUri;
	protected $key;
	protected $token;
	protected $preapproval_plan_id;
	public $suscripciones_cobros;

	public $planId;
	public $initPoint;
	public $suscripcionStatus;


	public function paginationView()
	{
		return 'vendor.livewire.bootstrap';
	}

	public function __construct()
	{

		$this->baseUri = config('services.mercadopago.base_uri');
		$this->key = config('services.mercadopago.key');
		$this->token = config('services.mercadopago.secret');

		//Plan id via bana produccio
		$this->preapproval_plan_id = '2c9380848e87412a018e9fc3a7940d1f';

		//$this->preapproval_plan_id = '2c9380848e85fedf018e9c1ee7800c1a'; 

		//$this->key =  'APP_USR-1478347a-0edd-4cc7-a0a5-8d9f2dd665de';
		//$this->token= 'APP_USR-8912887278767826-040119-5551546dd17dd52bd509b403dd2ac5b3-307823377';

	}


    protected function redirectLogin()
    {
        return \Redirect::to("login");
    }
    
	public function mount()
	{
        if (!Auth::check()) {
            // Redirigir al inicio de sesión y retornar una vista vacía
            $this->redirectLogin();
            return view('auth.login');
        }
     
		//return dd($this->preapproval_plan_id);

		$this->metodo_pago = session('MetodoPago');
		$this->pageTitle = 'Listado';
		$this->proveedor = '1';
		$this->componentName = 'Productos';
		$this->categoryid = 'Elegir';
		$this->almacen = 'Elegir';
		$this->stock_descubierto = 'Elegir';
		$this->mensajes = [];
		$this->noticias = [];
		$this->suscripcionStatus = false;

		//Add Lucas
		$this->user = User::find(Auth::user()->id);

		$url = $_SERVER['REQUEST_URI'];

		// Dividir la URL en partes usando el "?" como delimitador
		$url_parts = explode('?', $url);

		if (count($url_parts) > 1) {
			$this->corroboracion_estado = 1;
		} else {
			$this->corroboracion_estado = 0;
		}
	}

	public function IniciarSuscripcion($plan_id)
	{
		$this->plan_id_flaminco = $plan_id;
		$plan = planes_suscripcion::find($plan_id);
		$this->preapproval_plan_id = $plan->preapproval_plan_id;
		$this->planId =  $this->GetPlan($this->preapproval_plan_id);
		$this->initPoint = $this->suscribirse($this->planId);
		// Redireccionar a la página proporcionada por $initPoint
		return Redirect::away($this->initPoint);
	}


	public function render()
	{
		//$this->ChequearEstadoPagoSuscripcionesVencidas();

		$this->suscripcion = Suscripcion::join('users', 'users.id', 'suscripcions.user_id')
			->join('planes_suscripcions', 'planes_suscripcions.id', 'suscripcions.plan_id_flaminco')
			->select('suscripcions.*', 'users.name as comercio', 'planes_suscripcions.nombre as plan_elegido')
			->where('suscripcions.user_id', $this->user->id)
			->orderBy('id', 'desc')
			->first();


		$this->suscripciones_cobros = SuscripcionCobros::where('user_id', $this->user->id)->orderBy('id', 'desc')->get();

		$planes = planes_suscripcion::get();//where('origen',1)->get();
		$modulos = ModulosSuscripcion::where('activo',1)->get();
		$user_amount_value = config('app.USER_AMOUNT_VALUE'); // chequear esto
		$user_count_max_value = config('app.USER_COUNT_MAX_VALUE'); // chequear esto
		
		
		$user_amount_value = 5000; // chequear esto
		$user_count_max_value = 20; // chequear esto
        
        if($this->suscripcion != null){
        $modulos_selected = $this->suscripcion->modulos_id;
        } else {
        $modulos_selected = null;    
        }
		$modulos_selected = explode(',',$modulos_selected);

		return view('livewire.suscripciones-configuracion.component', [
			'suscripcion' => $this->suscripcion,
			'suscripciones_cobros' => $this->suscripciones_cobros,
			'planes' => $planes,
			'modulos' => $modulos,
			'user_amount_value' => $user_amount_value,
			'user_count_max_value' => $user_count_max_value,
			'modulos_selected' => $modulos_selected,
		])
			->extends('layouts.theme-pos.app')
			->section('content');
	}


	protected $listeners = [
		'deleteRow' => 'Destroy',
		'ConfirmCheck' => 'DeleteSelected'
	];

	//Traer datos suscripcion por id
	public function GetPlan($planId)
	{
		$metodo = 'GET';
		$endPoint = 'preapproval_plan/' . $planId;
		//$endPoint = 'preapproval_plan/2c938084892b330d01892cce42d800a6' ;
		$header = [
			'Authorization' => 'Bearer ' . $this->token,
			'Accept' => 'application/json',
		];

		$result = $this->hacerRequest($this->baseUri, $metodo,  $endPoint, $header);
		return $result;
	}

	/////// //REGISTRAR SUSCRIPCIONES NUEVAS
	public function suscribirse($datos_suscripcion)
	{

		$suscripcion = Suscripcion::where([
			['user_id', '=',  $this->user->id],
		])->first();

		// dd($suscripcion);


		$metodo = 'POST';
		$endPoint = '/preapproval';
		$header = [
			'Authorization' => 'Bearer ' . $this->token,
			'Accept' => 'application/json',
		];

		//Usuario id: 
		$userId = Auth::id();

		$nombreComercio = $this->user->name;
		$telefono = $this->user->phone;

		//Email del comprador
		$payer_email =  $this->user->email;

		//Fecha de creacion de suscripcion
		$fecha = Carbon::now()->addHour()->format('Y-m-d\TH:i:s.BP');

		$montoMensual = $datos_suscripcion['auto_recurring']['transaction_amount'];

		$external_reference = "FLA-" . $userId . "-" . $this->preapproval_plan_id . "-" . uniqid();

		//Datos suscripcion pre aproval
		$preapproval_data = [
			'payer_email' =>  $this->user->email,
			// 'payer_email' =>  $payer_email_pruebas,
			'back_url' => 'https://testing.flamincoapp.com.ar/regist',
			'reason' => $datos_suscripcion['reason'],
			'external_reference' => $external_reference,   // Aca hacer con un codigo unico 'external_reference' para despues buscarlo
			'auto_recurring' => [
				'frequency' => $datos_suscripcion['auto_recurring']['frequency'],
				'frequency_type' => $datos_suscripcion['auto_recurring']['frequency_type'],
				'transaction_amount' => $montoMensual, //Falta traerlo desde la api
				'currency_id' => 'ARS',
				'preapproval_plan_id' => $this->preapproval_plan_id,
				'start_date' => $fecha,
			],
		];


		$result = $this->hacerRequest($this->baseUri, $metodo,  $endPoint, $header, $preapproval_data);

		//registrar  suscripciones en base de datos       

		if ($suscripcion !== null) {
			$suscripcion->update([
				'user_id' => $userId,
				'suscripcion_id' => $result["id"],
				'payer_id' => $result["payer_id"],
				'payer_email' => $payer_email,
				'suscripcion_status' => 'inactiva',
				'nombre_comercio' => $nombreComercio,
				'telefono' => $telefono,
				'fecha' => $fecha,
				'external_reference' => $external_reference,
				'plan_id_flaminco' => $this->plan_id_flaminco,
				'monto_mensual' => $montoMensual,
				'init_point' => $result["init_point"]
			]);
		} else {
			Suscripcion::create([
				'user_id' => $userId,
				'suscripcion_id' => $result["id"],
				'payer_id' => $result["payer_id"],
				'payer_email' => $payer_email,
				'suscripcion_status' => 'inactiva',
				'nombre_comercio' => $nombreComercio,
				'telefono' => $telefono,
				'fecha' => $fecha,
				'external_reference' => $external_reference,
				'plan_id_flaminco' => $this->plan_id_flaminco,
				'monto_mensual' => $montoMensual,
				'init_point' => $result["init_point"]
			]);
		}



		return $result["init_point"];
	}

	//
	public function ResetDatos()
	{

		$suscripcion = Suscripcion::where([
			['user_id', '=',  $this->user->id],
		])->first();

		$suscripcion->delete();
		$user = User::find($this->user->id);
		$user->confirmed_at = null;
		$user->confirmed = 0;
		$user->save();
	}


	// Cancelar suscripcion
	public function CancelarSuscripcion()
	{

		$suscripcion = Suscripcion::where([
			['user_id', '=',  $this->user->id],
		])->first();

		$preapproval_id = $suscripcion->suscripcion_id; // Aquí debes proporcionar el ID de la suscripción que deseas cancelar o pausar

		$metodo = 'PUT';
		$endPoint = '/preapproval/' . $preapproval_id;
		$header = [
			'Authorization' => 'Bearer ' . $this->token,
			'Accept' => 'application/json',
			'Content-Type' => 'application/json',
		];

		$status = 'cancelled'; // O 'paused' según sea necesario

		$data = [
			'status' => $status
		];

		$result = $this->hacerRequest($this->baseUri, $metodo, $endPoint, $header, $data);


		if ($result['status'] == "cancelled") {
			$suscripcion->update([
				'suscripcion_status' => 'cancelada',
			]);
		}
	}

	//Traer datos suscripcion por id
	public function GetSuscripcion($suscripcionId)
	{
		$metodo = 'GET';
		$endPoint = 'preapproval/' . $suscripcionId;
		$header = [
			'Authorization' => 'Bearer ' . $this->token,
			'Accept' => 'application/json',
		];

		$result = $this->hacerRequest($this->baseUri, $metodo,  $endPoint, $header);
		return $result;
	}
	
	public function GetSuscripcionByUser()
	{
		$suscripcion = Suscripcion::where([
			['user_id', '=',  $this->user->id],
		])->first();

		$preapproval_id = $suscripcion->plan_id; // Aquí debes proporcionar el ID de la suscripción que deseas cancelar o pausar

		$metodo = 'GET';
		$endPoint = 'preapproval/' . $preapproval_id;
		$header = [
			'Authorization' => 'Bearer ' . $this->token,
			'Accept' => 'application/json',
		];

		$result = $this->hacerRequest($this->baseUri, $metodo,  $endPoint, $header);
		return $result;
	}

	public function GetSuscripcionLastPago($suscripcion)
	{

		if ($suscripcion !== null) {

			$today =  substr(Carbon::now(), 0, 10);

			$metodo = 'GET';

			//$endPoint ='/v1/payments/search?payer.id=' . $suscripcion->payer_id . '&range=date_created&begin_date=' . $today . 'T00:00:00Z&end_date=' . $today . 'T23:59:59Z';

			$endPoint = '/v1/payments/search?external_reference=' . $suscripcion->external_reference;


			$header = [
				'Authorization' => 'Bearer ' . $this->token,
				'Accept' => 'application/json',
			];

			$pagos = $this->hacerRequest($this->baseUri, $metodo,  $endPoint, $header);

			$pagosResult = json_decode(json_encode($pagos['results']), false);

			$PagoUltimo = collect($pagosResult)->sortByDesc('date_created')->first();

			return $PagoUltimo;
		} else {
			return false;
		}
	}

	// HASTA ACA 


	public function actualizarStatusSuscripcionUsuario()
	{
		$user = User::where('id', Auth::id())->first();

		$suscripcion = Suscripcion::where([
			['user_id', '=',  $this->user->id],
		])->first();

		if ($suscripcion->suscripcion_status = 'activa') {
			$user->confirmed_at = Carbon::now();
			$user->confirmed = 1;
			$user->save();
		}

		if ($suscripcion->suscripcion_status = 'inactiva') {
			$user->confirmed_at = null;
			$user->confirmed = 0;
			$user->save();
		}
	}


	function calacularDiferenciaDias($startDate, $endDate)
	{
		$start = Carbon::parse($startDate);
		$end = Carbon::parse($endDate);

		return $start->diffInDays($end);
	}

	public function GetPayerEmail($payerid)
	{
		$metodo = 'GET';
		$endPoint = '/v1/payments/search?payer.id=' . $payerid;
		$header = [
			'Authorization' => 'Bearer ' . $this->token,
			'Accept' => 'application/json',
		];

		$result = $this->hacerRequest($this->baseUri, $metodo,  $endPoint, $header);
		return $result;
	}

	public function UpdatePayerEmail()
	{
		$suscripciones = Suscripcion::all();


		foreach ($suscripciones as $suscripcion) {
			$payments  = $this->GetPayerEmail($suscripcion->payer_id);
			$updated = false;

			foreach ($payments["results"] as $payment) {
				if ($payment["payer"]["email"] !== null) {
					Suscripcion::where('payer_id', '=',  $suscripcion->payer_id)->update(['payer_email' =>   $payment["payer"]["email"]]);

					print_r("\n" . 'SUSCRPCION ID:  ' . $suscripcion->payer_id . 'PAYER ID: ' . $suscripcion->payer_id . 'EMAIL ACTUALIZADO CORRECTAMENTE');

					$updated = true;
					break;
				}
			}

			if ($updated === false) {
				print_r("\n" . 'SUSCRPCION ID:  ' . $suscripcion->payer_id . 'PAYER ID: ' . $suscripcion->payer_id . 'ERROR: EL EMAIL NO PUDO SER ACTUALIZADO');
			}
		}
	}

	public function testDuplicateDB()
	{
		$suscripcionesCobros = SuscripcionCobros::all();

		//return $suscripcionesCobros;
		$duplicados = [];

		//array_push($duplicados, 'test');
		foreach ($suscripcionesCobros as $suscripcionCobro) {
			//print_r($suscripcionCobro->suscripcion_id . '</br>');
			foreach ($suscripcionesCobros as $suscripcionCobroCheck) {
				//if($suscripcionCobro->id !== $suscripcionCobroCheck->id && $suscripcionCobro->date_created === $suscripcionCobroCheck->date_created && $suscripcionCobro->suscripcion_id === $suscripcionCobroCheck->suscripcion_id ){


				//print_r(substr($suscripcionCobro->date_created, 0, 10));

				if ($suscripcionCobro->id !== $suscripcionCobroCheck->id && substr($suscripcionCobro->date_created, 0, 10) === substr($suscripcionCobroCheck->date_created, 0, 10) && $suscripcionCobro->suscripcion_id === $suscripcionCobroCheck->suscripcion_id) {
					//if($suscripcionCobro->id !== $suscripcionCobroCheck->id && $suscripcionCobro->suscripcion_id === $suscripcionCobroCheck->suscripcion_id ){
					// if($suscripcionCobro->date_created > '2022-11-08T00:00:00')
					// {
					//if($suscripcionCobro->id !== $suscripcionCobroCheck->id && $suscripcionCobro->suscripcion_id === $suscripcionCobroCheck->suscripcion_id ){
					array_push($duplicados, [$suscripcionCobro->id, $suscripcionCobro->suscripcion_id, $suscripcionCobroCheck->id, $suscripcionCobroCheck->suscripcion_id]);
					// }

				}
				/*else{
          array_push($duplicados,'test');
         }
         */
			}
		}
		return $duplicados;
	}

	public function UpdateIdUser()
	{

		$suscripcionesOld = SuscripcionsOld::all();
		//$suscripciones= Suscripcion::all();

		foreach ($suscripcionesOld as $suscripcion) {
			$suscripcionSelect = Suscripcion::where([
				['payer_id', '=', $suscripcion->payer_id],
				['user_id', '=', null]
			])->first();

			if ($suscripcionSelect !== null) {
				$suscripcionSelect->user_id = $suscripcion->user_id;
				$suscripcionSelect->save();
			}
		}
	}


	public function CerrarSesion()
	{
		Auth::logout();
		return \Redirect::to("login");
	}

	public function ChequearEstadoPagoSuscripcionesVencidas()
	{
		$hoy = Carbon::now();

		$suscripciones_impagas = Suscripcion::where('proximo_cobro', '<', $hoy)->where('user_id', Auth::user()->id)->get();

		foreach ($suscripciones_impagas as $si) {
			$this->ChequearEstadoPago($si->id);
		}
	}

	public function ChequearEstadoPago($id)
	{
		$suscripcion = Suscripcion::find($id);
		$pago = $this->GetSuscripcionLastPago($suscripcion);

		$estado = $pago->status;
		$fechaCarbon = Carbon::parse($pago->date_created);
		$fecha_cobro = $fechaCarbon->format('Y-m-d H:i:s');

		$this->registrarPagoIndividual($pago);

		// Si la fecha de proximo cobro es antes a la fecha del cobro en cuestion, chequeamos el estado
		if ($suscripcion->proximo_cobro < $fecha_cobro) {
			if ($estado != "approved") {
				$suscripcion->cobro_status = "pendiente";
				$suscripcion->save();
			} else {
				$suscripcion->cobro_status = "pago";
				$fechaProximoCobro = $fechaCarbon->addMonth();
				$fecha_proximo_cobro = $fechaProximoCobro->format('Y-m-d H:i:s');
				$suscripcion->proximo_cobro = $fecha_proximo_cobro;
				$suscripcion->save();
			}
		}
	}



	public function registrarPagoIndividual($payment)
	{

		// dd($payment);

		if (isset($payment->payer->id) && isset($payment->point_of_interaction->transaction_data->subscription_id)) {

			$SuscripcionTicketAbierto = SuscripcionCobros::where([
				['pago_id', '=', $payment->id],
				['status_ticket', '=', 'abierto']
			])->latest()->first();

			if (isset($SuscripcionTicketAbierto)) {

				if ($payment['status'] === 'approved') {
					$SuscripcionTicketAbierto->status_ticket = 'cerrado';
					$SuscripcionTicketAbierto->status = $payment->status;
					$SuscripcionTicketAbierto->status_detail = $payment->status_detail;
					$SuscripcionTicketAbierto->save();
				} elseif ($payment['status'] === 'rejected') {
					$SuscripcionTicketAbierto->status = $payment->status;
					$SuscripcionTicketAbierto->status_detail = $payment->status_detail;
					$SuscripcionTicketAbierto->intento_cobro = $SuscripcionTicketAbierto->intento_cobro + 1;
					$SuscripcionTicketAbierto->save();
				}
			} else {
				$SuscripcionCobros = new SuscripcionCobros;
				$SuscripcionCobros->payer_id = $payment->payer->id;
				$SuscripcionCobros->collector_id = $payment->collector_id;
				$SuscripcionCobros->suscripcion_id = $payment->point_of_interaction->transaction_data->subscription_id;
				$SuscripcionCobros->payer_email = $payment->payer->email;
				$SuscripcionCobros->pago_id = $payment->id;
				$SuscripcionCobros->payment_method = $payment->payment_method_id;
				$SuscripcionCobros->external_reference = $payment->external_reference;
				$SuscripcionCobros->monto_mensual = $payment->transaction_details->total_paid_amount;
				$SuscripcionCobros->status = $payment->status;
				$SuscripcionCobros->user_id = Auth::user()->id;
				$SuscripcionCobros->status_detail = $payment->status_detail;
				$SuscripcionCobros->external_reference = $payment->external_reference;
				$SuscripcionCobros->description = $payment->description;
				$SuscripcionCobros->date_created = $payment->date_created;
				$SuscripcionCobros->date_approved = $payment->date_approved;
				$SuscripcionCobros->date_last_updated = $payment->date_last_updated;
				$SuscripcionCobros->status_ticket = ($payment->status === 'approved') ?  'cerrado' : 'abierto';
				$SuscripcionCobros->save();
			}
		}
	}

	public function ClickFacebook()
	{

		$this->enviarEventoFacebook(Auth::user()->id);
	}
	
	public function ContratarModulo($modulo_id){
	    
	}
}
