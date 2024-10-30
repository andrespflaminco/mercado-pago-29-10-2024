<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;

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
use Mail;

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
use Illuminate\Support\Facades\Crypt;


class SuscripcionDirectaController extends Component
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
  
  
      // Declaración de variables para los campos del formulario
    public $name_form;                 // Nombre
    public $apellido_usuario_form;                 // Nombre
    public $nombre_usuario_form;              // Apellido
    public $email_form;                // Correo electrónico
    public $phone_form;                // Teléfono (con código de país)
    public $rubro_form;      // Rubro del negocio
    public $cantidad_empleados_form;   // Cantidad de sucursales
    public $cantidad_sucursales_form;  // Cantidad de empleados
    public $password;             // Contraseña
    public $password_confirmation; // Confirmación de la contraseña
    
    public $paso;
    
    

  public function paginationView()
  {
    return 'vendor.livewire.bootstrap';
  }

  public function mount($slug, $quantity = 0)
  {
    $this->paso = 1;
    $this->slug = $slug;
    $this->quantity = $quantity ?? 0;
    $this->cantidad_empleados_form = "Elegir";
    $this->cantidad_sucursales_form = "Elegir";
    
  }
  
  public function IrPaso1(){
      $this->paso = 1;
  }  
  public function IrPaso2(){
      $this->paso = 2;
  }
  public function IrPaso3(){
      $this->paso = 3;
      $event_name = "Lead";
  //  $user_id = $user->id;
      $url = URL::current();
        
  //    $this->enviarEventoFacebook($event_name,$user_id,$url);
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
    Log::info('SuscripcionEspecialController - IniciarProceso');
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
    Log::info('SuscripcionEspecialController - render');

    $urlBase = env('APP_URL');
    
    $plan = planes_suscripcion::where('id', $this->slug)->first();

    $data =  [
      'slug' => $this->slug,
      'plan_suscripcion' => $plan,
      'quantity' => $this->quantity,
      'url_base' => $urlBase,
      'url_redirect' => $urlBase . '/suscribirse/' . $this->slug . '/',
      'user_amount_value' => config('app.USER_AMOUNT_VALUE'), // Agrega esta línea
      'users_amount' => 0,
      'PLAN_MONTO' => $plan->monto // Agrega esta línea
    ];

    $monto = $plan->monto;
    $users_amount = config('app.USER_AMOUNT_VALUE');
    $user_amount_value = config('app.USER_AMOUNT_VALUE');
    
    if ($this->quantity) {
      $users_amount = config('app.USER_AMOUNT_VALUE') ?? 5000;
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


      
    return view('auth.suscripcion-mercadopago-directa', $data)
      ->extends('layouts.theme-pos-especial.app-base')
      ->section('content');
  }



  public function Iniciar($slug)
  {
    Log::info('SuscripcionEspecialController - Iniciar');

    $user = Auth::user();
    $this->IniciarSuscripcion($slug, $user);
  }

  public function IniciarSuscripcion($plan_id, $user)
  {
    Log::info('SuscripcionEspecialController - IniciarSuscripcion');

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
    return view('auth.suscripcion-mercadopago-success', $data);
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

    return redirect('/suscripcion-configuracion')->with('message', 'Suscripci贸n cancelada correctamente');
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

    return redirect('/suscripcion-configuracion')->with('message', 'Suscripci贸n actualizada correctamente');
  
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
                    $descripcion = $descripcion . ' + M贸dulo: ' . $modulo->nombre . ' ($' . $modulo_amount_format . ')';
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
                    'currency_id' => 'ARS', // O la moneda que estés utilizando
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
        
        // Capturar los parámetros adicionales
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
    
    $this->updateLeadFromUser($user->id); 
    
    Log::info('SuscripcionEspecialController -confirmarSuscripcion');
    Log::info($request);

    $data =  [
      'plan_suscripcion' => $request['plan_suscripcion'],
      'quantity' => $request['users_quantity'],
    ];

    $data['user'] = auth()->user();

    $this->MPService = new MPService();
    $preferenciaMP = $this->MPService->getPresapprovalPlanMP($data);

    if ($preferenciaMP) {
      Log::info('SuscripcionEspecialController -confirmarSuscripcion -- preferenciaMP');
      Log::info($preferenciaMP);
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
          Log::info('SuscripcionEspecialController - confirmarSuscripcion - MP procesado correctamente -- redirect');
          Log::info($preferenciaMP['preference']['init_point']);
          return redirect($preferenciaMP['preference']['init_point']);
        }
      }
    }

    Log::info('SuscripcionEspecialController - confirmarSuscripcion - error al procesar MP');
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
  
public function comprobarDatosRegistro(Request $request)
{
    // Definir las reglas de validación
    $rules = [
        'nombre_usuario' => 'required|string|max:255',
        'apellido_usuario' => 'required|string|max:255',
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone' => 'required|numeric',
        'password' => 'required|string|min:8|confirmed',
        'cantidad_sucursales' => 'required|string',
        'cantidad_empleados' => 'required|string',
        'rubro' => 'required|string',
    ];

    // Mensajes personalizados para los errores
    $messages = [
        'email.unique' => 'El email ingresado ya está registrado.',
        'phone.numeric' => 'El número de teléfono debe contener solo números.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
    ];

    // Crear una instancia del validador
    $validator = Validator::make($request->all(), $rules, $messages);


    if ($validator->fails()) {
        // Redirigir de vuelta con los errores de validación
        return redirect()->back()->withErrors($validator->errors())->withInput();
    }

    // Si la validación pasa, continuar con el código
    $validated = $validator->validated();

    // Aquí puedes procesar los datos validados o redirigir a otra página
    return redirect()->route('ruta.success')->with('success', 'Registro exitoso!');
}

public function comprobarDatosRegistroPaso1()
{
    // Definir las reglas de validacion
    $rules = [
        'nombre_usuario_form' => 'required|string|max:255',
        'apellido_usuario_form' => 'required|string|max:255',
        'name_form' => 'required|string|max:255',
        'email_form' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
    ];

    // Mensajes personalizados para los errores
    $messages = [
        'nombre_usuario_form.required' => 'El nombre del usuario es obligatorio.',
        'nombre_usuario_form.string' => 'El nombre del usuario debe ser un texto valido.',
        'nombre_usuario_form.max' => 'El nombre del usuario no puede superar los 255 caracteres.',

        'apellido_usuario_form.required' => 'El apellido del usuario es obligatorio.',
        'apellido_usuario_form.string' => 'El apellido del usuario debe ser un texto valido.',
        'apellido_usuario_form.max' => 'El apellido del usuario no puede superar los 255 caracteres.',

        'name_form.required' => 'El nombre de la empresa es obligatorio.',
        'name_form.string' => 'El nombre de la empresa debe ser un texto valido.',
        'name_form.max' => 'El nombre de la empresa no puede superar los 255 caracteres.',

        'email_form.required' => 'El correo electronico es obligatorio.',
        'email_form.email' => 'Debe ingresar un correo electronico valido.',
        'email_form.unique' => 'El correo electronico ingresado ya esta registrado.',

        'password.required' => 'La contrasena es obligatoria.',
        'password.string' => 'La contrasena debe ser un texto.',
        'password.min' => 'La contrasena debe tener al menos 8 caracteres.',
        'password.confirmed' => 'Las contrasenas no coinciden.',
    ];

    // Realizar la validacion con las reglas y mensajes personalizados
    $this->validate($rules, $messages);
    
    $this->IrPaso2();
}


public function comprobarDatosRegistroPaso2()
{
    // Definir las reglas de validacion
    $rules = [
        'phone_form' => 'required|numeric',
        'cantidad_sucursales_form' => 'not_in:Elegir',
        'cantidad_empleados_form' => 'not_in:Elegir',
        'rubro_form' => 'required|string|max:255',
    ];

    // Mensajes personalizados para los errores
    $messages = [

        'phone_form.required' => 'El numero de telefono es obligatorio.',
        'phone_form.numeric' => 'El numero de telefono debe contener solo numeros.',

        'cantidad_sucursales_form.not_in' => 'La cantidad de sucursales es obligatoria.',
        'cantidad_empleados_form.not_in' => 'La cantidad de empleados es obligatoria.',

        'rubro_form.required' => 'El rubro del negocio es obligatorio.',
        'rubro_form.string' => 'El rubro del negocio debe ser un texto valido.',
        'rubro_form.max' => 'El rubro del negocio no puede superar los 255 caracteres.',

    ];

    // Realizar la validacion con las reglas y mensajes personalizados
    $this->validate($rules, $messages);
    
    $this->emit("enviar-face-lead","");
    
    $this->IrPaso3();
}  

    public function ConfirmarRegistro()
    {
        $user = $this->CrearUsuario();
        
        // aca tengo que hacer submit en el form
        if($user){
            Auth::login($user);
                    
            $event_name = "InitiateCheckout";
            $user_id = $user->id;
            $url = URL::current();
        
            $this->enviarEventoFacebook($event_name,$user_id,$url);
        
            $this->emit('confirmado', '');    
        }
        
        
    }
        
    public function CrearUsuario()
    {

        $comercio_id = 1;
        $this->name_form = strtoupper($this->name_form);
        
        $slug = $this->slug ?? 1;
        $intencion_compra = $this->slug ?? 1;
        
        if($intencion_compra == 0){
        $planes_suscripcion_landings = planes_suscripcion_landings::find($slug);
        if($planes_suscripcion_landings != null){$url = $planes_suscripcion_landings->url;} else {$url = null;}
        } else {
        $url = 'https://app.flamincoapp.com.ar/suscribirse/'.$intencion_compra;    
        }
        
        $fecha = Carbon::now(); // Obtiene la fecha y hora actual
        $fecha_actual = $fecha;
        $prueba_hasta = $fecha->addDays(14); // Agrega 14 días a la fecha actual
        
        $user_encontrado = User::where('email',$this->email_form)->first();
        
        if($user_encontrado == null){
        
        $TokenPass = Crypt::encrypt($this->password);
        
           $user = User::create([
            'name' => $this->name_form,
            'email' => $this->email_form,
            'phone' => $this->phone_form,
            'rubro' => $this->rubro_form,
            'prefijo_pais' => $this->prefijo_pais_form ?? '+54',
            'cantidad_sucursales' => $this->cantidad_sucursales_form,
            'cantidad_empleados' => $this->cantidad_empleados_form,
            'nombre_usuario' => $this->nombre_usuario_form,
            'apellido_usuario' => $this->apellido_usuario_form,
            'origen' => $slug,
            'url_origen' => $url,
            'intencion_compra' => $intencion_compra,
            'password' => Hash::make($this->password),
            'comercio_id' => $comercio_id,
            'profile' => 'Comercio',
            'plan' => 1,
            'usuario_nuevo' => 0,
            'prueba_hasta' => null,
            'last_login' => $fecha_actual,
            'email_verified_at' => $fecha_actual,
            'cantidad_login' => 0,
            'token_pass' => $TokenPass,
            'autogestion' => 'NO COMPLETADO',
            'flujo' => 1
        ]); 

        $user_id = $user->id;

        $user->update([
            'casa_central_user_id' => $user_id
            ]);


        $user->syncRoles('Comercio');
        
        $this->createLeadFromUser($user_id);

        $this->AvisoPorMail("andrespasquetta@gmail.com",$user);
        $this->AvisoPorMail("ch.pivatto@gmail.com",$user);
        
        } else {
            $user = $user_encontrado;
        }
        

        
        return $user;
    }
    
public function AvisoPorMail($email, $user) {
    $title = "Nuevo cliente potencial de Flaminco";
    $created_at = Carbon::now()->format("d/m/Y H:i");

    Mail::send([], [], function ($message) use ($email, $user, $title, $created_at) {
        $message->to($email)
                ->subject($title)
                ->setBody(view('emails.nuevo_cliente', compact('title', 'user', 'created_at'))->render(), 'text/html');
    });
}



}
