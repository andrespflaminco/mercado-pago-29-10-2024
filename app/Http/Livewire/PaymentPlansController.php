<?php

namespace App\Http\Livewire;

use App\Models\PaymentPlatform;
use App\Resolvers\PaymentPlatformResolver;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Livewire\Component;
use MP;

class PaymentPlansController extends Component
{

  public $plan;
  protected $paymentPlatformResolver;

  public function __construct(PaymentPlatformResolver $paymentPlatformResolver)
  {

      $this->paymentPlatformResolver = $paymentPlatformResolver;
  }

  public $currencies = ['usd'];

  public function mount($plan)
  {
    $this->plan = $plan;

  }

  public function AbrirModal()
  {

dd('hola');

$this->emit('abrir-modal','');
}
    public function render()
    {

      if($this->plan == 1) {
        $this->nombre_plan = "INICIAL";
        $this->valor_plan = 1990;
      }
      if($this->plan == 2) {
          $this->nombre_plan = "INTERMEDIO";
          $this->valor_plan = 3500;
      }
      if($this->plan == 3) {
          $this->nombre_plan = "FULL";
          $this->valor_plan = 4900;
      }

      $paymentPlatforms = PaymentPlatform::all();


        return view('livewire.payments-plans.component',[
          'paymentPlatforms' => $paymentPlatforms,
          'nombre_plan' => $this->nombre_plan,
          'valor_plan' => $this->valor_plan

     ])
        ->extends('layouts.theme2.app')
        ->section('content');
    }

    public function click()
{

  $preapproval_data = [
    'payer_email' => 'bognimelisa@gmail.com',
    'back_url' => 'http://127.0.0.1:8000/paymentss',
    'reason' => 'SubscripciÃ³n a paquete premium',
    'external_reference' => '2c9380847eb6850d017ebc11609f0216',
    'auto_recurring' => [
      'frequency' => 1,
      'frequency_type' => 'months',
      'transaction_amount' => 1,
      'currency_id' => 'ARS',
      'start_date' => Carbon::now()->addHour()->format('Y-m-d\TH:i:s.BP'),
      'end_date' => Carbon::now()->addMonth()->format('Y-m-d\TH:i:s.BP'),
    ],
  ];

  MP::create_preapproval_payment($preapproval_data);

  return dd($preapproval);
}



    public function pay(Request $request)
    {
        $rules = [
            'value' => ['required', 'numeric', 'min:5'],
            'currency' => ['required'],
            'payment_platform' => ['required', 'exists:payment_platforms,id'],
            'name' => ['required'],
            'email_form' => ['required', 'email','unique:users,email'],
            'phone' => ['required', 'numeric', 'min:5'],
        ];
        
       $messages =[
        'name.required' => 'Ingresa el nombre',
        'name.min' => 'El nombre del usuario debe tener al menos 3 caracteres',
        'email_form.required' => 'Ingresa el correo ',
        'email_form.email' => 'Ingresa un correo v¨¢lido',
        'email_form.unique' => 'El email ya existe en sistema',
    ];

        $request->validate($rules, $messages);

        $usuario = User::create([
            'name' => $request->name,
            'email' => $request->email_form,
            'phone' => $request->phone,
            'rubro' => $request->rubro,
            'profile' => 'Comercio',
            'email_verified_at' => Carbon::now(),
            'comercio_id'=> 1,
            'password' => bcrypt($request->password)
        ]);

        $usuario->syncRoles('Comercio');

        $request->request->add(['user_id' => $usuario->id]); //add request

        $paymentPlatform = $this->paymentPlatformResolver
            ->resolveService($request->payment_platform);

        session()->put('paymentPlatformId', $request->payment_platform);

        return $paymentPlatform->handlePayment($request);
    }

    public function approval()
    {
        if (session()->has('paymentPlatformId')) {
            $paymentPlatform = $this->paymentPlatformResolver
                ->resolveService(session()->get('paymentPlatformId'));

            return $paymentPlatform->handleApproval();
        }

        return redirect()
            ->route('home')
            ->withErrors('No pudimos recibir su pago, por favor intente de nuevo.');
    }

public function cancelled()
{
    return redirect()
        ->route('home')
        ->withErrors('You cancelled the payment');
}
}
