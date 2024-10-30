<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Redirect;

use Livewire\WithPagination;
use App\Models\Product;
use App\Models\provincias;
use App\Models\productos_lista_precios;
use App\Models\Category;
use App\Models\ModulosSuscripcion;
use App\Models\sucursales;
use App\Models\planes_suscripcion_landings;
use App\Models\etiquetas;
use App\Models\etiquetas_productos;
use App\Models\User;
use Carbon\Carbon;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use App\Models\ecommerce_envio;
use App\Models\ClientesMostrador;
use App\Models\ecommerce;
use App\Services\CartEcommerce;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\Session;
use App\Models\metodo_pago;
use App\Models\cajas;
use App\Models\historico_stock;
use App\Models\bancos;
use DB;
use Illuminate\Support\Facades\URL;

// Suscripciones
use App\Models\Suscripcion;
use App\Models\SuscripcionCobros;
use App\Models\planes_suscripcion;
use App\Services\MPService;
use App\Services\SuscripcionesService;
//Traits
use App\Traits\ConsumesExternalServices;

//Traits
use App\Traits\FacebookTrait;
use Illuminate\Support\Facades\Log;
use App\Traits\ZohoCRMTrait;
use Mail;

class SuscripcionEspecialController extends Component
{

  use ConsumesExternalServices;
  use WithPagination;
  use FacebookTrait;
  use ZohoCRMTrait;

  public $comercio_id, $profile;
  public $name;
  public $email;
  public $phone;
  public $slug;
  public $rubro;
  public $password;
  public $password_confirmation;
  public $quantity;

  protected $baseUri;
  protected $key;
  protected $token;
  protected $preapproval_plan_id;

  public $planId;
  public $initPoint;
  public $suscripcionStatus;
  public $origen, $user;

  private $pagination = 25;
  protected $MPService;

  public function paginationView()
  {
    return 'vendor.livewire.bootstrap';
  }

  public function mount($slug, $quantity = 0)
  {
    $this->slug = $slug;
    $this->quantity = $quantity;
  }


  public function __construct()
  {

    $this->baseUri = config('services.mercadopago.base_uri');
    $this->key = config('services.mercadopago.key');
    $this->token = config('services.mercadopago.secret');
  }

  public function resetUI()
  {
    $this->cantidad = 1;
    $this->selected_id = "";
    $this->name = "";
    $this->barcode = "";
    $this->stock = "";
    $this->price = "";
    $this->cost = "";
    $this->image = "";
  }

  protected $listeners = [
    'IniciarProceso'
  ];

  public function IniciarProceso()
  {
    Log::info('Suscripcion - IniciarProceso');
    // dd($this->slug);
    //$this->Iniciar($this->slug);
  }

  /*
    public function render()
    {
        
      if (auth()->check()) {
        //$this->Iniciar($this->slug);
         
      $event_name = "InitiateCheckout";
      $user_id = auth()->user()->id;
      $url = URL::current();
      $response = $this->enviarEventoFacebook($event_name,$user_id,$url);
      
      return view('auth.suscripcion-especial',[
            'slug' => $this->slug
            ])
       ->extends('layouts.theme-pos-especial.app')
       ->section('content');
    } else {
       
       return view('auth.suscripcion-especial',[
            'slug' => $this->slug
            ])
       ->extends('layouts.theme-pos-especial.app')
       ->section('content');
        
    }
    
    }
    */

  //public function render(Request $request, $planId = 1, $quantity = 0)
  public function render()
  {
    Log::info('Suscripcion - render');

    $urlBase = env('APP_URL');
    
    $plan = planes_suscripcion::where('id', $this->slug)->first();

    $data =  [
      'slug' => $this->slug,
      'plan_suscripcion' => $plan,
      'quantity' => $this->quantity,
      'url_base' => $urlBase,
      'url_redirect' => $urlBase . '/suscribirse/' . $this->slug . '/',
      'user_amount_value' => config('app.USER_AMOUNT_VALUE'), // Agrega esta l¨ªnea
      'users_amount' => 0,
      'PLAN_MONTO' => $plan ? intval($plan->monto) : 18900 // Agrega esta l¨ªnea
    ];

    $monto = $plan->monto;
    $users_amount = config('app.USER_AMOUNT_VALUE');
    $user_amount_value = config('app.USER_AMOUNT_VALUE');
    
    if ($this->quantity) {
      $users_amount = config('app.USER_AMOUNT_VALUE');
      $users_amount = $users_amount * $this->quantity;
      $data['users_amount'] = $users_amount;
      $monto += $users_amount;
    }

    $data['monto'] = $monto;

    if (auth()->check()) {
      $event_name = "InitiateCheckout";
      $user_id = auth()->user()->id;
      $url = URL::current();
      $response = $this->enviarEventoFacebook($event_name, $user_id, $url);

      $data['user'] = auth()->user();
    }

    return view('auth.suscripcion-mercadopago', $data)
      ->extends('layouts.theme-pos-especial.app-base')
      ->section('content');
  }


  public function customLogin()
  {

    /*
 		$rules  =[
		    'email' => 'required|string|unique:users,email|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ];

		$messages = [
			'email.required' => 'Nombre del producto requerido',
			'email.unique' => 'El nombre del producto ya esta en uso',
			'email.required' => 'El codigo del producto requerido'
		];

		$this->validate($rules, $messages);
        */


    $credentials = [
      'email' => $this->email,
      'password' =>  $this->password,
    ];

    //dd($credentials);

    if (Auth::attempt($credentials)) {
      $event_name = "InitiateCheckout";
      $user_id = auth()->user()->id;
      $url = URL::current();
      $response = $this->enviarEventoFacebook($event_name, $user_id, $url);
      $this->emit("enviar-face", "");
    } else {
      dd("credenciales invalidas");
    }

    //   return redirect('ecommerce-login/'.$request->slug)->with('message', 'Las credenciales no son validas.');
  }


  public function Iniciar($slug)
  {
    Log::info('Suscripcion - Iniciar');

    $user = Auth::user();
    $this->IniciarSuscripcion($slug, $user);
  }

  public function IniciarSuscripcion($plan_id, $user)
  {
    Log::info('Suscripcion - IniciarSuscripcion');

    $plan = planes_suscripcion::find($plan_id);
    $this->origen = $plan->origen;
    $this->plan_id_flaminco = $plan->plan_id;
    $this->preapproval_plan_id = $plan->preapproval_plan_id;
    $this->planId =  $this->GetPlan($this->preapproval_plan_id);
    $this->initPoint = $this->suscribirse($this->planId, $user);
    $usuario = User::find($user->id);
    $planes_suscripcion_landings = planes_suscripcion_landings::find($plan->origen);
    $usuario->intencion_compra = 0;
    $usuario->origen = $plan->origen;
    $usuario->url_origen = $planes_suscripcion_landings->url;
    $usuario->save();
    return redirect($this->initPoint);
    //return redirect::away($this->initPoint);
  }



  /////// //REGISTRAR SUSCRIPCIONES NUEVAS
  public function suscribirse($datos_suscripcion, $user)
  {
    Log::info('SuscripcionEspecialController -suscribirse');

    $suscripcion = Suscripcion::where([
      ['user_id', '=',  $user->id],
    ])->orderBy('id', 'desc')->first();

    // dd($suscripcion);


    $metodo = 'POST';
    $endPoint = '/preapproval';
    $header = [
      'Authorization' => 'Bearer ' . $this->token,
      'Accept' => 'application/json',
    ];

    //Usuario id: 
    $userId = Auth::id();

    $nombreComercio = $user->name;
    $telefono = $user->phone;

    //Email del comprador
    $payer_email =  $user->email;

    //Fecha de creacion de suscripcion
    $fecha = Carbon::now()->addHour()->format('Y-m-d\TH:i:s.BP');

    $montoMensual = $datos_suscripcion['auto_recurring']['transaction_amount'];

    $external_reference = "FLA-" . $userId . "-" . $this->preapproval_plan_id . "-" . uniqid();

    //Datos suscripcion pre aproval
    $preapproval_data = [
      'payer_email' =>  $user->email,
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




  public function mercadoPagoSuccess(Request $request)
  {
    $this->MPService = new MPService();

    $preapproval_id = $request['preapproval_id'];
    $result = $this->MPService->mercadoPagoSuccess($preapproval_id);

    $data = [
      'result' => $result
    ];
    
    if($result){
        $user = Auth::user();

        /*
        $this->createLeadFromUser($user->id);

        $this->AvisoPorMail("andrespasquetta@gmail.com",$user);
        $this->AvisoPorMail("ch.pivatto@gmail.com",$user);
        */
        
        $event_name = "Compra";
        $user_id = Auth::user()->id;
        $url = URL::current();
        
        $this->enviarEventoFacebook($event_name,$user_id,$url);
    } else {
        $event_name = "AddPaymentInfo";
        $user_id = Auth::user()->id;
        $url = URL::current();
        
        $this->enviarEventoFacebook($event_name,$user_id,$url);
    }
    
    
    return view('auth.suscripcion-mercadopago-success', $data);
  }

public function AvisoPorMail($email, $user) {
    $title = "Nuevo cliente potencial de Flaminco - Con tarjeta";
    $created_at = Carbon::now()->format("d/m/Y H:i");

    Mail::send([], [], function ($message) use ($email, $user, $title, $created_at) {
        $message->to($email)
                ->subject($title)
                ->setBody(view('emails.nuevo_cliente', compact('title', 'user', 'created_at'))->render(), 'text/html');
    });
}
  public function mercadoPagoIpn(Request $request)
  {
    Log::info('SuscripcionEspecialController -mercadoPagoIpn');
    Log::info($request);

    return true;
  }

  public function mercadoPagoWebhooks(Request $request)
  {
    Log::info('SuscripcionEspecialController -mercadoPagoWebhooks');
    Log::info($request);

    return true;
  }

  public function cancelarSuscripcion(Request $request, $suscripcion_id)
  {
    Log::info('SuscripcionEspecialController -cancelarSuscripcion');
    Log::info($request);
    Log::info($suscripcion_id);

    $suscripcionesService = new SuscripcionesService();
    $preference = $suscripcionesService->cancelarSuscripcion($suscripcion_id);

    return redirect('/suscripcion-configuracion')->with('message', 'SuscripciÃ³n cancelada correctamente');
  }

public function actualizarSuscripcionConfirmed(Request $request, $suscripcion_id, $plan_suscripcion_id, $users_count = null, $modulos_id = null)
  {

    Log::info('SuscripcionEspecialController -actualizarSuscripcion');
    Log::info($request);
    Log::info('$suscripcion_id - ' . $suscripcion_id . ' | plan_suscripcion_id - ' . $plan_suscripcion_id . ' | users_count - ' . $users_count . ' | modulos_id - ' . $modulos_id);

    $suscripcionesService = new SuscripcionesService();
    $data = [
      'suscripcion_id' => $suscripcion_id,
      'plan_id' => $plan_suscripcion_id,
      'users_count' => $users_count,
      'modulos_id' => $modulos_id,
      'plan_suscripcion' => $plan_suscripcion_id,
      'quantity' => $users_count,   
    ];

    $preference = $suscripcionesService->actualizarSuscripcion($data);

    Log::info('$preference');
    Log::info($preference);

    return redirect('/suscripcion-configuracion')->with('message', 'SuscripciÃ³n actualizada correctamente');
  
  }

  public function actualizarSuscripcion(Request $request, $suscripcion_id, $plan_suscripcion_id, $users_count = null, $modulos_id = null){
    Log::info('SuscripcionEspecialController - actualizarSuscripcion');
    Log::info($request);
    Log::info('$suscripcion_id - ' . $suscripcion_id . ' | plan_suscripcion_id - ' . $plan_suscripcion_id . ' | users_count - ' . $users_count . ' | modulos_id - ' . $modulos_id);

        try {
            
            //dd($plan_suscripcion_id,$users_count,$modulos_id);
            
            $suscripcion = Suscripcion::where('suscripcion_id', $suscripcion_id)->first();
            $plan_id = $suscripcion->plan_id;

            $planSuscripcion = planes_suscripcion::find($plan_suscripcion_id);
            $user = Auth::user();

            $monto_plan = $planSuscripcion->monto;
            $monto = $planSuscripcion->monto;
            $monto_format = number_format($planSuscripcion->monto, 0, ',', '.');
            $descripcion = 'FLA-' . $user->id . ' | Plan ' . $planSuscripcion->id . ': ' . $planSuscripcion->nombre . ' ($' . $monto_format . ')';
        
            $users_amount = 0;
            if ($users_count > 0) {
                $users_amount = $users_count * floatval(5000);
                $users_amount_format = number_format($users_amount, 0, ',', '.');
                $monto =  $monto + $users_amount;
                $descripcion = $descripcion . ' + ' . $users_count . ' usuarios/s ($' . $users_amount_format . ')';
            }

            $modulos_amount = 0;
        //    $modulos_id='';
            if (isset($modulos_id) && $modulos_id) {
                $modulos_seleccionados = explode(',', $modulos_id);

                foreach ($modulos_seleccionados as $modulo_id) {
                    $modulo = ModulosSuscripcion::find($modulo_id);
                    $modulo_amount_format = number_format($modulo->monto, 0, ',', '.');
                    $monto =  $monto + $modulo->monto;
                    $modulos_amount += $modulo->monto;
                    $descripcion = $descripcion . ' + MÃ³dulo: ' . $modulo->nombre . ' ($' . $modulo_amount_format . ')';
                }
            }
        
        //dd($monto);
        $diferencia_monto = $monto - $suscripcion->monto_mensual;
        $fechaHoy = Carbon::now();
        $proximoCobro = Carbon::parse($suscripcion->proximo_cobro);

        $diferenciaDias = $fechaHoy->diffInDays($proximoCobro);
        $porcentaje = $diferenciaDias/30;
        if($porcentaje !=  0 || $porcentaje !=  null){
        $monto = $porcentaje * $diferencia_monto;    
        } else {
        $monto = 1 * $diferencia_monto;    
        }
        
        
        if(0 < $monto){
            
	    $this->MPService = new MPService();

	    $checkoutData = [
            'items' => [
                [
                    'title' => $descripcion,
                    'description' => $descripcion,
                    'quantity' => 1,
                    'currency_id' => 'ARS', // O la moneda que est¨¦s utilizando
                    'unit_price' => floatval($monto)
                ]
            ],
            'payer' => [
                'name' => Auth::user()->nombre_usuario,
                'surname' => Auth::user()->apellido_usuario,
                'email' => null
            ],
            'back_urls' => [
                'success' => route('checkout.success', [
                    'suscripcion_id' => $suscripcion_id,
                    'plan_suscripcion_id' => $plan_suscripcion_id,
                    'users_count' => $users_count,
                    'modulos_id' => $modulos_id
                ]),
                'failure' => route('checkout.failure', [
                    'suscripcion_id' => $suscripcion_id,
                    'plan_suscripcion_id' => $plan_suscripcion_id,
                    'users_count' => $users_count,
                    'modulos_id' => $modulos_id
                ]),
                'pending' => route('checkout.pending', [
                    'suscripcion_id' => $suscripcion_id,
                    'plan_suscripcion_id' => $plan_suscripcion_id,
                    'users_count' => $users_count,
                    'modulos_id' => $modulos_id
                ])
            ],
            'auto_return' => 'approved'
        ];
        
	    $preference = $this->MPService->CrearCheckoutPreference($checkoutData);
	    //dd($preference);
	    
	    if (isset($preference['response']['init_point'])) {
           return Redirect::away($preference['response']['init_point']);
        } else {
        //    return redirect()->route('checkout.failure')->with('error', 'No se pudo crear la preferencia de pago.');
        }

        } else {
        // aca mandar directamente a actualizar suscripcion    
        return $this->actualizarSuscripcionConfirmed($request, $suscripcion_id, $plan_suscripcion_id, $users_count, $modulos_id);
        }
        
        
        } catch (Exception $e) {
            Log::info('SuscripcionesService - actualizarSuscripcion - ' . $e->getMessage());
        }    

  }
  
    public function CheckoutSuccess(Request $request)
    {
        // Capturar los datos del pago exitoso
        $paymentId = $request->query('payment_id');
        $status = $request->query('status');
        $merchantOrderId = $request->query('merchant_order_id');
        
        // Capturar los par¨¢metros adicionales
        $suscripcion_id = $request->query('suscripcion_id');
        $plan_suscripcion_id = $request->query('plan_suscripcion_id');
        $users_count = $request->query('users_count');
        $modulos_id = $request->query('modulos_id');
    
        Log::info('Checkout Success', [
            'payment_id' => $paymentId,
            'status' => $status,
            'merchant_order_id' => $merchantOrderId,
            'suscripcion_id' => $suscripcion_id,
            'plan_suscripcion_id' => $plan_suscripcion_id,
            'users_count' => $users_count,
            'modulos_id' => $modulos_id
        ]);
    
        // Realizar las acciones necesarias con los datos capturados
    
         return $this->actualizarSuscripcionConfirmed($request, $suscripcion_id, $plan_suscripcion_id, $users_count, $modulos_id);
    }

    public function CheckoutFailure(Request $request)
    {
        // Capturar los datos del pago fallido
        $paymentId = $request->query('payment_id');
        $status = $request->query('status');
        $merchantOrderId = $request->query('merchant_order_id');

        Log::info('Checkout Failure', [
            'payment_id' => $paymentId,
            'status' => $status,
            'merchant_order_id' => $merchantOrderId
        ]);

        // Realizar las acciones necesarias con los datos capturados

        return view('checkout.failure', compact('paymentId', 'status', 'merchantOrderId'));
    }

    public function CheckoutPending(Request $request)
    {
        // Capturar los datos del pago pendiente
        $paymentId = $request->query('payment_id');
        $status = $request->query('status');
        $merchantOrderId = $request->query('merchant_order_id');

        Log::info('Checkout Pending', [
            'payment_id' => $paymentId,
            'status' => $status,
            'merchant_order_id' => $merchantOrderId
        ]);

        // Realizar las acciones necesarias con los datos capturados

        return view('checkout.pending', compact('paymentId', 'status', 'merchantOrderId'));
    }
    
    
  public function confirmarSuscripcion(Request $request)
  {
      
    $user = User::find(auth()->user()->id);
    $user->intento_pago = "ARREPENTIMIENTO";
    $user->save();
    $free_days = $request['free_days'] ?? 0;
    
    $this->updateLeadFromUser($user->id); 
    
    Log::info('SuscripcionEspecialController -confirmarSuscripcion');
    Log::info($request);

    $data =  [
      'plan_suscripcion' => $request['plan_suscripcion'],
      'quantity' => $request['users_quantity'],
      'free_days' => $free_days
    ];

    $data['user'] = auth()->user();

    $this->MPService = new MPService();
    $preferenciaMP = $this->MPService->getPresapprovalPlanMP($data);

    if ($preferenciaMP) {
    //  Log::info('SuscripcionEspecialController -confirmarSuscripcion -- preferenciaMP');
    //  Log::info($preferenciaMP);
      if (isset($preferenciaMP['status']) && $preferenciaMP['status'] == 201) {
        $preferenciaMP['nombre_comercio'] = $preferenciaMP['user_name'];
        $preferenciaMP['telefono'] = $preferenciaMP['user_telefono'];
        $preferenciaMP['users_count'] = $preferenciaMP['quantity'];
        $preferenciaMP['plan_id_flaminco'] = $preferenciaMP['plan_suscripcion_id'];
        $preferenciaMP['suscripcion_status'] = 'pending';
        $preferenciaMP['cobro_status'] = 'pending';

        $suscripcionesService = new SuscripcionesService();
        $suscripcionesService->actualizarSuscripcionFlaminco($preferenciaMP);

        if (isset($preferenciaMP['preference']['init_point'])) {
          Log::info('Suscripcion - se proporciona el init_point al usuario');
          Log::info($preferenciaMP['preference']['init_point']);
          return redirect($preferenciaMP['preference']['init_point']);
        }
      }
    }

    Log::info('Suscripcion - error al procesar MP del init_point');
    return redirect('/')->with('message-error', 'error al procesar MP');
  }

  public function mercadoPagoCreateUserTest(Request $request)
  {
    $data = [
      'description' => 'carlos test',
      'site_id' => 'MLA',
    ];

    $this->MPService = new MPService();
    $preferenciaMP = $this->MPService->createUserTest($data);
  }
  
    public function PagarEnMercadoPago($url){
    //dd($url);
    $user = User::find(auth()->user()->id);
    $user->intento_pago = "ARREPENTIMIENTO";
    $user->save();
    
    $this->updateLeadFromUser($user->id); 
    
    return redirect()->to($url);  
  }
  
  
  
}
