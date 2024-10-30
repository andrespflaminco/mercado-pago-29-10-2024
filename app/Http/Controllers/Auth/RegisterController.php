<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\planes_suscripcion_landings;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Mail;

//Traits
use App\Traits\FacebookTrait;
use App\Traits\ZohoCRMTrait;

use Illuminate\Support\Facades\Crypt;

class RegisterController extends Controller
{
    use RegistersUsers;
    use FacebookTrait;
    use ZohoCRMTrait;

    public $comercio_id;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }


    protected function validator(array $data)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'rubro' => ['not_in:Elegir'],
            'phone' => ['nullable', 'string', 'numeric'], // Regla para el teléfono
        ];

        $messages = [
            'email.unique' => 'El email ya existe, pruebe con otro.',
            'email.required' => 'El email es necesario',
            'email.email' => 'El email debe contener @',
            'name.required' => 'El nombre del comercio es requerido',
            'rubro.not_in' => 'Elija un rubro por favor',
            'phone.numeric' => 'El teléfono permite solo números',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ];

        return Validator::make($data, $rules, $messages)->setAttributeNames([
            'name' => 'Nombre',
            'email' => 'Correo electrónico',
            'password' => 'Contraseña',
            'rubro' => 'Rubro',
            'phone' => 'Teléfono',
        ]);
    }

    protected function create(array $data)
    {

        $comercio_id = 1;
        $data['name'] = strtoupper($data['name']);
        
        $slug = $data['slug'] ?? 1;
        $intencion_compra = $data['intencion_compra'] ?? 0;
        
        if($intencion_compra == 0){
        $planes_suscripcion_landings = planes_suscripcion_landings::find($slug);
        if($planes_suscripcion_landings != null){$url = $planes_suscripcion_landings->url;} else {$url = null;}
        } else {
        $url = 'https://app.flamincoapp.com.ar/suscribirse/'.$intencion_compra;    
        }
        
        $fecha = Carbon::now(); // Obtiene la fecha y hora actual
        $fecha_actual = $fecha;
        $prueba_hasta = $fecha->addDays(14); // Agrega 15 días a la fecha actual
        $TokenPass = Crypt::encrypt($data['password']);
        
        
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'rubro' => $data['rubro'],
            'prefijo_pais' => $data['prefijo_pais'],
            'cantidad_sucursales' => $data['cantidad_sucursales'],
            'cantidad_empleados' => $data['cantidad_empleados'],
            'nombre_usuario' => $data['nombre_usuario'],
            'apellido_usuario' => $data['apellido_usuario'],
            'origen' => $slug,
            'url_origen' => $url,
            'intencion_compra' => $intencion_compra,
            'password' => Hash::make($data['password']),
            'comercio_id' => $comercio_id,
            'profile' => 'Comercio',
            'plan' => 1,
            'usuario_nuevo' => 0,
            'prueba_hasta' => $prueba_hasta,
            'last_login' => $fecha_actual,
            'email_verified_at' => $fecha_actual,
            'cantidad_login' => 0,
            'token_pass' => $TokenPass
            
        ]);
        
        $user_id = $user->id;
        
        $user->update([
            'casa_central_user_id' => $user_id
            ]);


        $user->syncRoles('Comercio');
        
        $this->createLeadFromUser($user_id);
        
        $event_name = "Lead";
        $user_id = $user->id;
        $url = URL::current();
        
        $this->enviarEventoFacebook($event_name,$user_id,$url);
    
        $this->AvisoPorMail("andrespasquetta@gmail.com",$user);
        $this->AvisoPorMail("ch.pivatto@gmail.com",$user);
        
    //    $this->AvisoPorMailCRM("andrespasquetta@gmail.com",$user);
    //    $this->AvisoPorMailCRM("ch.pivatto@gmail.com",$user);
    //    $this->AvisoPorMailCRM("ak36x45@parser.zohocrm.com",$user);
    //    $this->AvisoPorMailCRM("lucas.loughry@etixen.com",$user);
        
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


public function AvisoPorMailOld($email, $user){
    
    $data["email"] = $email;
    $data["title"] = "Nuevo cliente potencial de Flaminco";
    $data["body"] = "Se creó un nuevo usuario. Datos de contacto:\n"
                  . "- Usuario: ".$user->name."\n"
                  . "- Email: ".$user->email."\n"
                  . "- Teléfono: ".$user->phone."\n"
                  . "Creado el ". Carbon::now()->format("d/m/Y i:h"). "hs";
        
    Mail::send('mail', $data, function ($message) use ($data) {
        $message->to($data["email"], $data["email"])
                ->subject($data["title"]);
    });
}

public function AvisoPorMailCRM($email, $user) {
    $title = "Nuevo cliente potencial de Flaminco";
    $created_at = Carbon::now()->format("d/m/Y H:i");

    Mail::send([], [], function ($message) use ($email, $user, $title, $created_at) {
        $message->to($email)
                ->subject($title)
                ->setBody(view('emails.crm', compact('title', 'user', 'created_at'))->render(), 'text/html');
    });
}  
    
}
