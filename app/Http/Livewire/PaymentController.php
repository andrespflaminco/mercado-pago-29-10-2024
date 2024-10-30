<?php

namespace App\Http\Livewire;

use App\Models\PaymentPlatform;
use App\Resolvers\PaymentPlatformResolver;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Livewire\Component;
use MP;

class PaymentController extends Component
{

  protected $paymentPlatformResolver;

  public function __construct(PaymentPlatformResolver $paymentPlatformResolver)
  {

      $this->paymentPlatformResolver = $paymentPlatformResolver;
  }

  public $currencies = ['usd'];

    
    public function render()
    {
      $paymentPlatforms = PaymentPlatform::all();


        return view('livewire.payments.component',[
          'paymentPlatforms' => $paymentPlatforms,

     ])
        ->extends('layouts.theme2.app')
        ->section('content');
    }


        public function click2($valor)
    {
      dd($valor);

    }

    public function AbrirModal()
    {
      dd('modal');
    }

    public function click()
{

  $preapproval_data = [
    'payer_email' => 'bognimelisa@gmail.com',
    'back_url' => 'http://127.0.0.1:8000/paymentss',
    'reason' => 'Subscripción a paquete premium',
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
        ];

        $request->validate($rules);


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
