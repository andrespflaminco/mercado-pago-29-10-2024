<?php

namespace App\Services;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use App\Traits\ConsumesExternalServices;
use App\Services\CurrencyConversionService;

class MercadoPagoService
{
    use ConsumesExternalServices;

    public $comercio_id;

    protected $baseUri;

    protected $key;

    protected $secret;

    protected $baseCurrency;

    protected $converter;

    public function __construct(CurrencyConversionService $converter)
    {
        $this->baseUri = config('services.mercadopago.base_uri');
        $this->key = config('services.mercadopago.key');
        $this->secret = config('services.mercadopago.secret');
        $this->baseCurrency = config('services.mercadopago.base_currency');

        $this->converter = $converter;
    }

    public function resolveAuthorization(&$queryParams, &$formParams, &$headers)
    {
        $queryParams['access_token'] = $this->resolveAccessToken();
    }

    public function decodeResponse($response)
    {
        return json_decode($response);
    }

    public function resolveAccessToken()
    {
        return $this->secret;
    }

    public function handlePayment(Request $request)
    {


        $request->validate([
            'card_network' => 'required',
            'card_token' => 'required',
            'email' => 'required',
        ]);

        $payment = $this->createPayment(
            $request->value,
            $request->currency,
            $request->card_network,
            $request->card_token,
            $request->email,
        );




        if ($payment->status === "approved") {

            $name = $payment->payer->first_name;
            $currency = strtoupper($payment->currency_id);
            $amount = number_format($payment->transaction_amount, 0, ',', '.');

            $originalAmount = $request->value;
            $originalCurrency = strtoupper($request->currency);


            $user = User::where('email',$request->email_form)->first();

            $user->update([
                'confirmed' => 1,
                'confirmed_at' => Carbon::now(),
                'nro_operacion_mp' => $payment->id,
                'plan' => $request->plan
            ]);

            Auth::login($user);

            return redirect('/pos');

        }

        return redirect()
            ->route('regist')
            ->withErrors('No pudimos procesar el pago, pruebe de nuvo mas tarde.');
    }

    public function handleApproval()
    {
        //
    }

    public function createPayment($value, $currency, $cardNetwork, $cardToken, $email, $installments = 1)
    {
        return $payment = $this->makeRequest(

            'POST',
            '/v1/payments',
            [],
            [
                'payer' => [
                    'email' => $email,
                ],
                'binary_mode' => true,
                'transaction_amount' => round($value * $this->resolveFactor($currency)),
                'payment_method_id' => $cardNetwork,
                'token' => $cardToken,
                'installments' => $installments,
                'statement_descriptor' => config('app.name'),
            ],
            [],
            $isJsonRequest = true,
        );



    }

    public function createSuscription($value, $currency, $cardNetwork, $_token, $email, $installments = 1)
    {



        return $this->makeRequest(

          'POST',
          'preapproval',
          [],
          [

            'back_url' => 'https://www.mercadopago.com.ar',
            'reason' => 'Subscripción a paquete',
            'external_reference' => '1245AT234562',
            'card_token_id' => $_token,
            'payer_email' => $email,
            'status' => 'authorized',
            'auto_recurring' => [
                    'frequency' => 1,
                    'frequency_type' => "months",
                    'currency_id' => 'ARS',
                    'transaction_amount' => round($value * $this->resolveFactor($currency)),
                    'statement_descriptor' => config('app.name'),
                  ],

          ],
          [],
          $isJsonRequest = true,
        );



    }

    public function handleSuscription($id, $value, $currency, $cardNetwork, $cardToken, $email, $installments = 1)
    {


        return $this->makeRequest(
          'POST',
          'preapproval',
          [],
          [
            'preapproval_plan_id' => $id,
            'card_token_id' => $cardToken,
            'payer_email' => $email

          ],
          [],
          $isJsonRequest = true,
        );



    }

    public function resolveFactor($currency)
    {
        return $this->converter
            ->convertCurrency($currency, $this->baseCurrency);
    }
}
