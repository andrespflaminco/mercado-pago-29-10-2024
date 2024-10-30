<?php
namespace App\Traits;

use App\Models\User;

use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;


use App\Models\Suscripcion;

//Traits
//use App\Traits\ConsumesExternalServices;

use GuzzleHttp\Client;
use WhichBrowser\Parser;

trait FacebookTrait {

	
    public function cifrar($cadena)
    {
        $hash = hash('sha256', $cadena);
        return $hash;
    }
		
    public function GetUserDatos($user_id){
        return User::find($user_id);
    }

    
    public function enviarEventoFacebook($event_name, $user_id, $url)
    {
        $client = new Client();
        
        $user = $this->GetUserDatos($user_id);
        
        $pixel_id = "946528043924786"; // "25618489701075434";
        $url = 'https://graph.facebook.com/v19.0/'.$pixel_id.'/events';
        
        $em = $this->cifrar($user->email);
        $ph = $this->cifrar($user->phone);
        
        $timestamp = time(); 
        $ip = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $fbclid = Request::query('fbclid');
    
        $token = "EAAGWMax2sZB8BO8TXQgxYYkZCuOmsGagvh1sg05kCNCfvt7agKhcxHnhJEMxvh0KMcJzD7TRaSZA1dPJmQ4QCY8NHctzZAr1VuZBXPAVuEhc1lKZAP40PCW3ZBmi0jBXc1suYVY6Oy89aKnRZA3OpRgGCMLy144OPI4MktNRlKw6uuCCi9e8UIzArpaqGsnnUJjYigZDZD";
        
        $form_params = [
            'data' => [
                [
                    "event_name" => $event_name,
                    "event_time" => $timestamp,
                    "user_data" => [
                        "em" => [$em],
                        "ph" => [
                            $ph
                        ],
                        "client_ip_address" => "$ip",
                        "client_user_agent" => "$userAgent",
                        "fbc" => "fb.1.1554763741205.AbCdEfGhIjKlMnOpQrStUvWxYz1234567890",
                        "fbp" => "fb.1.1558571054389.1098115397"
                    ],
                    "event_source_url" => $url,
                    "action_source" => "website"
                ]
            ],
            'access_token' => $token
        ];
    
    
        
        // Agregar "custom_data" solo si el evento es "Purchase"
        if ($event_name === "Purchase") {
        
        $suscripciones = Suscripcion::where('user_id',$user_id)->first();
        
            $form_params['data'][0]['custom_data'] = [
                "currency" => "ARS",
                "value" => $suscripciones->monto_mensual,
                "contents" => [
                    [
                        "id" => $suscripciones->plan_id_flaminco,
                        "quantity" => 1,
                        "delivery_category" => "home_delivery"
                    ]
                ]
            ];
        }
    
        $response = $client->post($url, [
            'form_params' => $form_params
        ]);
    
        $body = $response->getBody();
        $data = json_decode($body, true);
        
        //dd($data);
        return $data;
    }

}

