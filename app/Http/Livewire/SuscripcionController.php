<?php

namespace App\Http\Livewire;


use Livewire\Component;
use App\Models\User;
use App\Models\planes_suscripcion;
use App\Models\suscripciones;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Illuminate\Support\Facades\Redirect;
//Add Lucas

//Traits
use App\Traits\ConsumesExternalServices;
use App\Traits\CartTrait;

use App\Models\Suscripcion;
use App\Models\SuscripcionCobros;
use App\Services\SuscripcionesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

//Traits
use App\Traits\FacebookTrait;

class SuscripcionController extends Component
{

	use WithPagination;
	use WithFileUploads;
	use CartTrait;
	use ConsumesExternalServices;
	use FacebookTrait;

	public $name, $barcode, $origen, $rentabilidad, $price, $stock, $alerts, $categoryid, $search, $image, $selected_id, $pageTitle, $componentName, $comercio_id, $almacen, $stock_descubierto, $inv_ideal, $proveedor, $mensajes, $cod_proveedor, $kg, $proveedor_elegido, $status, $noticias, $archivo;
	public $id_almacen;
	public $id_categoria;
	public $id_proveedor;
	public $plan_id_flaminco;
	private $pagination = 25;

	public $SelectedProducts = [];
	public $selectedAll = FALSE;

	//Add Lucas
	public $user;
	public $slug;
	public $planes_disponibles;

	protected $baseUri;
	protected $key;
	protected $token;
	protected $preapproval_plan_id;

	public $planId;
	public $initPoint;
	public $suscripcionStatus;

	public $corroboracion_estado;
	public $suscripcion;
	public $suscripciones;


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


	public function mount()
	{

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

		$this->planes_disponibles = planes_suscripcion::where('origen', 0)->get();
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
		$this->origen = $plan->origen;
		$this->preapproval_plan_id = $plan->preapproval_plan_id;
		$this->planId =  $this->GetPlan($this->preapproval_plan_id);
		$this->initPoint = $this->suscribirse($this->planId);
		// Redireccionar a la página proporcionada por $initPoint
		return Redirect::away($this->initPoint);
	}


	public function render()
	{
		$url = $_SERVER['REQUEST_URI'];

		// Dividir la URL en partes usando el "?" como delimitador
		$url_parts = explode('?', $url);

		if (count($url_parts) > 1) {

			$event_name = "Purchase";
			$user_id = auth()->user()->id;
			$url = URL::current();

			$this->enviarEventoFacebook($event_name, $user_id, $url);

			$this->corroboracion_estado = 1;
			$user_e = User::find($this->user->id);
			$user_e->email_verified_at =  Carbon::now();
			$user_e->save();
		} else {
			$this->corroboracion_estado = 0;
		}


		$this->suscripcionStatus = $this->CheckSuscripcionPagoStatus(); // Aca chequea si la suscripcion esta activa o no

		$this->suscripcion = Suscripcion::where('user_id', $this->user->id)->orderBy('id', 'desc')->first();
		//dd($this->suscripcion);

		//$this->initPoint = $this->suscribirse($this->planId);				

		return view('livewire.suscripcion.component', [
			'suscripcion' => $this->suscripcion,
			'urlMercadoPago' => $this->initPoint,
			//'suscripcion' => $this->suscripcion,
		])
			->extends('layouts.theme-pos-especial.app')
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
			'back_url' => 'https://app.flamincoapp.com.ar/regist',
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

		dd($result['status']);
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

	public function CheckSuscripcionPagoStatus()
	{

		$suscripcion = Suscripcion::where([
			['user_id', '=',  $this->user->id],
		])->first();


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

			$plan_flaminco = $suscripcion->plan_id_flaminco;

			//dd($PagoUltimo);

			$this->registrarPagoIndividual($PagoUltimo);

			if ($PagoUltimo !== null && $PagoUltimo->status === 'approved') {

				// Parsear la fecha con Carbon
				$fechaCarbon = Carbon::parse($PagoUltimo->date_created);
				// Formatear la fecha según el formato deseado
				$fechaFormateada = $fechaCarbon->format('Y-m-d H:i:s');
				// Sumar un mes
				$fechaProximoPago = $fechaCarbon->addMonth();

				//Actualizar usuario
				$user = User::where('id', Auth::id())->first();
				$user->confirmed = 1;
				$user->plan = $plan_flaminco;
				$user->confirmed_at = Carbon::now();
				$user->save();

				$otros_usuarios = User::where('casa_central_user_id', $user->id)->get();

				foreach ($otros_usuarios as $otro_usuario) {
					$u =  User::find($otro_usuario->id);
					$u->update([
						'confirmed' => 1,
						'confirmed_at' => Carbon::now()
					]);
				}

				//Actualiza status sucripcon
				$suscripcion->suscripcion_status = 'activa';
				$suscripcion->cobro_status = 'pago';
				$suscripcion->proximo_cobro = $fechaProximoPago;
				$suscripcion->save();

				return true;
			} else {
				return false;
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


	// HASTA ACA 


	public function ActualizarNomina()
	{

		$this->suscripciones = Suscripcion::all();

		foreach ($this->suscripciones as $s) {
			$user = User::find($s->user_id);

			$user->update([
				'confirmed_at' => Carbon::now()
			]);
		}
	}

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


	/////////////SUSCRIPCIONES ANTIGUAS
	public function GetReportPaymentTodos()
	{
		$metodo = 'GET';

		$yesterday  = substr(Carbon::yesterday(), 0, 10);

		//$today =  substr(Carbon::now(), 0, 10);

		//$endPoint ='/v1/payments/search?limit=1000&offset=0&range=date_created&begin_date=2023-04-04T00:00:00Z&end_date=2023-04-09T23:59:59Z';

		$endPoint = '/v1/payments/search?limit=1000&offset=0&range=date_created&begin_date=' . $yesterday . 'T00:00:00Z&end_date=' . $yesterday . 'T23:59:59Z';

		$header = [
			'Authorization' => 'Bearer ' . $this->token,
			'Accept' => 'application/json',
		];

		$result = $this->hacerRequest($this->baseUri, $metodo,  $endPoint, $header);

		//return dd($result);
		//return print_r($result);
		return  $result['results'];
	}

	public function BuscarPagoTodos()
	{

		$payments =  $this->GetReportPaymentTodos();

		dd($payments[0]);

		foreach ($payments as $payment) {
			if ($payment["operation_type"] == "recurring_payment") {
				//dd($payment);

				$user = User::where('email', $payment["payer"]['email'])->first();

				if ($user != null) {
					$CheckearSuscripcion = Suscripcion::where("payer_email", $payment["payer"]['email'])->first();
					// si no existe la suscri la registra en base de datos
					if ($CheckearSuscripcion == null) {
						//$this->registrarSuscripcionDb($user->id, $payment["point_of_interaction"]["transaction_data"]["subscription_id"], $payment["payer"]['id'], $payment["payer"]['email'], 'inactiva', $user->name,  $user->phone,  $payment["date_created"],  $payment["transaction_details"]['total_paid_amount']);
					}
				}

				$SuscripcionCobros = SuscripcionCobros::where([
					['payer_id', '=', $payment["payer"]['id']],
					['status_ticket', '=', 'abierto']
				])->first();


				if ($SuscripcionCobros !== null) {

					if ($payment["status_detail"] === 'accredited' || $this->diasDeMora($SuscripcionCobros->date_created) > 30) {
						SuscripcionCobros::find($SuscripcionCobros->id)
							->update([
								'status_ticket' =>  'cerrado',
								'status' =>  $payment["status"],
								'status' =>  $payment["status_detail"]
							]);
					}  //Si no
					else {
						//Sumar 1 intento de cobro
						SuscripcionCobros::find($SuscripcionCobros->id)
							->update([
								'intento_cobro' =>  $SuscripcionCobros->intento_cobro + 1,
								'status' =>  $payment["status"],
								'status' =>  $payment["status_detail"]
							]);
						//  $this->enviarEmail();
					}
				} else {
					$SuscripcionCobros = new SuscripcionCobros;
					$SuscripcionCobros->payer_id = $payment["payer"]['id'];
					$SuscripcionCobros->collector_id = $payment["collector_id"];
					$SuscripcionCobros->payer_email = $payment["payer"]['email'];
					$SuscripcionCobros->monto_mensual = $payment["transaction_details"]['total_paid_amount'];
					$SuscripcionCobros->status = $payment["status"];
					$SuscripcionCobros->status_detail = $payment["status_detail"];
					$SuscripcionCobros->external_reference = $payment["external_reference"];
					$SuscripcionCobros->description = $payment["description"];
					$SuscripcionCobros->date_created = $payment["date_created"];
					$SuscripcionCobros->date_approved = $payment["date_approved"];
					$SuscripcionCobros->date_last_updated = $payment["date_last_updated"];
					$SuscripcionCobros->status_ticket = ($payment["status"]  === 'approved') ?  'cerrado' : 'abierto';
					$SuscripcionCobros->save();
				}
			}
		}
	}

	public function registrarPago()
	{
		$this->cerrarTicketMora();

		//TRAEMOS PAGOS
		$payments =  $this->GetReportPaymentTodos();

		foreach ($payments as $payment) {
			if (isset($payment["payer"]["id"]) && isset($payment['point_of_interaction']["transaction_data"]["subscription_id"])) {

				$SuscripcionTicketAbierto = SuscripcionCobros::where([
					['payer_id', '=', $payment["payer"]['id']],
					['status_ticket', '=', 'abierto']
				])->latest()->first();

				if (isset($SuscripcionTicketAbierto)) {

					if ($payment['status'] === 'approved') {
						$SuscripcionTicketAbierto->status_ticket = 'cerrado';
						$SuscripcionTicketAbierto->status = $payment["status"];
						$SuscripcionTicketAbierto->status_detail = $payment["status"];
						$SuscripcionTicketAbierto->save();
					} elseif ($payment['status'] === 'rejected') {
						$SuscripcionTicketAbierto->status = $payment["status"];
						$SuscripcionTicketAbierto->status_detail = $payment["status"];
						$SuscripcionTicketAbierto->intento_cobro = $SuscripcionTicketAbierto->intento_cobro + 1;
						$SuscripcionTicketAbierto->save();

						Suscripcion::where('payer_id', '=',  $payment["payer"]['id'])
							->update(['suscripcion_status' =>   'inactiva']);
					}
				} else {
					$SuscripcionCobros = new SuscripcionCobros;
					$SuscripcionCobros->payer_id = $payment["payer"]['id'];
					$SuscripcionCobros->collector_id = $payment["collector_id"];
					$SuscripcionCobros->suscripcion_id = $payment['point_of_interaction']["transaction_data"]["subscription_id"];
					$SuscripcionCobros->payer_email = $payment["payer"]['email'];
					$SuscripcionCobros->pago_id = $payment["id"];
					$SuscripcionCobros->monto_mensual = $payment["transaction_details"]['total_paid_amount'];
					$SuscripcionCobros->status = $payment["status"];
					$SuscripcionCobros->status_detail = $payment["status_detail"];
					$SuscripcionCobros->external_reference = $payment["external_reference"];
					$SuscripcionCobros->description = $payment["description"];
					$SuscripcionCobros->date_created = $payment["date_created"];
					$SuscripcionCobros->date_approved = $payment["date_approved"];
					$SuscripcionCobros->date_last_updated = $payment["date_last_updated"];
					$SuscripcionCobros->status_ticket = ($payment['status'] === 'approved') ?  'cerrado' : 'abierto';
					$SuscripcionCobros->save();

					if ($payment['status'] === 'approved') {
						Suscripcion::where('payer_id', '=',  $payment["payer"]['id'])
							->update(['suscripcion_status' =>   'activa']);
					}
				}
			}
		}
	}


	function calacularDiferenciaDias($startDate, $endDate)
	{
		$start = Carbon::parse($startDate);
		$end = Carbon::parse($endDate);

		return $start->diffInDays($end);
	}

	public function cerrarTicketMora()
	{
		$ticketsAbiertos =  SuscripcionCobros::where([
			['status_ticket', '=', 'abierto']
		])->get();


		foreach ($ticketsAbiertos as $ticket) {

			$fs = substr($ticket->date_created, 0, 10);
			//$fe = '2023-04-09';           
			$fe = substr(Carbon::now(), 0, 10);
			$dif = $this->calacularDiferenciaDias($fs, $fe);

			if ($dif > 30) {
				SuscripcionCobros::where('id', '=',  $ticket->id)
					->update(['status_ticket' =>   'cerrado']);
			}
		}
	}


	//CALCULAR DIAS DE MORA
	function diasDeMora($fecha)
	{
		$f = substr($fecha, 0, 10);
		$fechaPago = Carbon::createFromFormat('Y-m-d', $f);
		$diasTranscurridos = $fechaPago->diffInDays(Carbon::now());

		return  $diasTranscurridos;
	}


	public function registrarSuscripcionAnteriores()
	{
		$user =  User::find(Auth::id());
		$payments =  $this->GetReportPaymentTodos();

		foreach ($payments as $payment) {
			if (isset($payment["payer"]["id"])) {
				$suscripcionDB = Suscripcion::where([
					['payer_id', '=', $payment["payer"]['id']]
				])->first();

				/*$suscripcionDB = Suscripcion::where([
            ['payer_id', '=', $payment["payer"]['id']]
           ])->first();*/


				$usuario = User::where([
					['email', '=', $payment['payer']["email"]]
				])->first();
			} else {
				$suscripcionDB = null;
			}

			if ($suscripcionDB === null && isset($payment["payer"]["id"]) && isset($payment['point_of_interaction']["transaction_data"]["subscription_id"])) {
				$suscripcion = new Suscripcion;
				$suscripcion->user_id = null;
				$suscripcion->suscripcion_id = $payment['point_of_interaction']["transaction_data"]["subscription_id"];
				$suscripcion->payer_id = $payment['payer']["id"];

				if ($usuario !== null) {
					$suscripcion->user_id = $usuario->id;
					$suscripcion->payer_email = $usuario->email;
					$suscripcion->nombre_comercio = $usuario->nro_heladeria;
					$suscripcion->telefono =  $usuario->phone;
					$suscripcion->fecha =  $payment['date_created'];
					$suscripcion->monto_mensual = null;
				} else {
					$suscripcion->user_id = null;
					$suscripcion->payer_email = null;
					$suscripcion->nombre_comercio = null;
					$suscripcion->telefono = null;
					$suscripcion->fecha =  null;
					$suscripcion->monto_mensual = null;
				}
				$suscripcion->suscripcion_status = 'inactiva';
				$suscripcion->save();
			}
		}
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

	public function getPreapprovalBySuscriptionId($id, SuscripcionesService $suscripcionServices)
	{
		$data = $suscripcionServices->getPreapprovalBySuscriptionId($id);

		return $data;
	}

	public function updateAllSubscription(SuscripcionesService $suscripcionServices)
	{
		$data = $suscripcionServices->updateAllSubscription();

		return $data;
	}
}
