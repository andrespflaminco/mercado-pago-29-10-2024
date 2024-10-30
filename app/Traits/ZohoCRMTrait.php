<?php
namespace App\Traits;

use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use GuzzleHttp\Client;

use Carbon\Carbon;

use App\Models\User;
use App\Models\Suscripcion;
use App\Models\SuscripcionControl;


trait ZohoCRMTrait {

public $refresh_token = '1000.28dee8f5f7dafbf879fe3beb492135f8.65e7b93a81b6b60d140a1ecac84ff2bc';


public function refreshToken()
{
    $refreshToken = '1000.28dee8f5f7dafbf879fe3beb492135f8.65e7b93a81b6b60d140a1ecac84ff2bc';
    $clientId = '1000.XEY07Z29NF6RVO7U4SSCE8FANB8QXT';
    $clientSecret = '2ce7effed3b4ea8ad6eac29d66f340bc5cf1f64b88';
    $grantType = 'refresh_token';
    $url = 'https://accounts.zoho.com/oauth/v2/token';

    $client = new Client();

    try {
        $response = $client->post($url, [
            'form_params' => [
                'refresh_token' => $refreshToken,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => $grantType,
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        \Log::info('Token response:', $data);

        // Verifica los alcances
        if (isset($data['scope']) && strpos($data['scope'], 'ZohoCRM.modules.ALL') === false) {
            \Log::warning('Alcance del token es insuficiente');
        }

        return $data;

    } catch (\Exception $e) {
        \Log::error('Error en refreshToken:', ['message' => $e->getMessage()]);
        return response()->json([
            'error' => 'Error al obtener el token',
            'message' => $e->getMessage()
        ], 500);
    }
}


    // Método para obtener el access token
public function GetAccessToken()
{
    try {
    $data = $this->refreshToken();

    if (isset($data['access_token'])) {
        return $data['access_token'];
    } else {
        \Log::warning('No se pudo obtener el access_token', $data);
        return null;
    }
     } catch (\Exception $e) {
        \Log::error('Error en refreshToken:', ['message' => $e->getMessage()]);
        return response()->json([
            'error' => 'Error al obtener el token',
            'message' => $e->getMessage()
        ], 500);
    }
    
}


    // Método para obtener los metadatos
public function ObtenerLeads()
{
    $accessToken = $this->GetAccessToken();

    if ($accessToken === null) {
        return response()->json([
            'error' => 'No se pudo obtener el token de acceso'
        ], 500);
    }

    $client = new Client();

    try {
        $response = $client->get('https://www.zohoapis.com/crm/v2/Potentials', [
            'headers' => [
                'Authorization' => "Zoho-oauthtoken $accessToken",
                'Content-Type' => 'application/json',
            ],
        ]);

        $fields = json_decode($response->getBody(), true);
        // Agrega esto para depurar
        \Log::info('Fields response:', $fields);

     //   dd($fields); // Esto debería detener la ejecución y mostrar los campos

    } catch (\Exception $e) {
        \Log::error('Error en ObtenerMetaDatos:', ['message' => $e->getMessage()]);
        return response()->json([
            'error' => 'Error al obtener los metadatos',
            'message' => $e->getMessage()
        ], 500);
    }
}


public function createLeadFromUser($userId)
{
    $user = User::find($userId);
    
    if (!$user) {
        return response()->json([
            'error' => 'Usuario no encontrado'
        ], 404);
    }

    $user_id = $user->id;
    
    // Mapear campos del modelo User a campos del Lead en Zoho CRM
    
    /*
    ID FLAMINCO: 611 *FLAMINCO_ID -- OK -- OKI
    NOMBRE DE LA EMPRESA: DEMO ETIXEN 2 *Company --OK -- OKI
    NOMBRE: NOMBRE ETIXEN 2 *Nombre_usuario -- OK -- OKI
    APELLIDO: APELLIDO ETIXEN 2 *Apellido_usuario --OK -- OKI
    EMAIL: demoetixen2@flaminco.com *Email --OK -- OKI
    TELEFONO: 5493513927912 *Phone *Mobile --OK --OKI
    PROVINCIA: * State -- OK
    CUIDAD: * City --OK
    RUBRO: Supermercados *Rubro --OK
    CANTIDAD SUCURSALES: 5 - 9 *Sucursales --OK
    CANTIDAD DE LOGUEOS: *Cantidad_de_logueos --OK
    VALIDA MAIL: "NO" *Valida_mail -- OK
    FECHA VALIDA MAIL: *Fecha_de_valida_mail -- OK
    FECHA DE CREACION: *Created_Time ---OK 
    CANTIDAD EMPLEADOS: + 30 *Numero_de_empleados ---VER --- No_of_Employees data invalida pide numero
    
    -------------------- FALTA CREAR ----------------
    Intento_de_pago = 'Intento_de_pago' "ARREPENTIMIENTO", "PAGO RECHAZADO" --> SI LLEGA Y HACE CLICK EN PAGAR ES ARREPENTIMIENTO , SI LO RECHAZA ES PAGO RECHAZADO
    
    -------------------- A VER ----------------------
    
    PLAN: Medianas Empresas --- FALTA EN FLAMINCO ---VER
    
    -------------------- FALTA CRM ----------------------
    
    ESTADO PAGO: 'Estado_de_pago'  ---> "NO" --- OK
    FECHA PAGO: --- 'Fecha_de_pago' ---> FECHA  --- OK
    FECHA DE VENCIMIENTO PRUEBA: 'Vencimiento_demo' OK
    ULTIMO LOGUEO: --- 'Ultimo_logueo' VER FALTA EN CRM ---> FECHA
    */
    
    $valida_mail = $user->email_verified_at ? "SI" : "NO";
    
    $fecha_valida_mail_date = $user->created_at ? Carbon::parse($user->created_at)->format('Y-m-d') : null;
    $fecha_valida_mail_date_time = $user->created_at ? Carbon::parse($user->created_at)->format('Y-m-d\TH:i:sP') : null;

    $estado_pago = $user->confirmed_at ? "SI" : "NO";
    $fecha_confirmed_at = $user->confirmed_at ? Carbon::parse($user->confirmed_at)->format('Y-m-d\TH:i:sP') : null;

    $prueba_hasta = $user->prueba_hasta 
    ? Carbon::parse($user->confirmed_at)->addDays(14)->format('Y-m-d\TH:i:sP') 
    : null;
    $ultimo_logueo = $user->last_login ? Carbon::parse($user->last_login)->format('Y-m-d\TH:i:sP') : null;
    if($user->cantidad_login == 0){$cantidad_logueo = 1;} else {$cantidad_logueo = $user->cantidad_login;}
    
    $flujo = (string) $user->flujo;
    /*     
    $leadData = [
        'FLAMINCO_ID' => $user->id,
        'Company' => $user->name, // Mapea 'sucursal' a 'Company'
        'Full_Name' => $user->nombre_usuario . ' ' . $user->apellido_usuario, // Nombre completo
        'Last_Name' => $user->apellido_usuario, // Apellido
        'First_Name' => $user->nombre_usuario,
        'Email' => $user->email, // Correo electrónico
        'Phone' => $user->prefijo_pais . $user->phone, // Teléfono
        'Mobile' =>  $user->prefijo_pais . $user->phone, // Mapea 'phone' a 'Mobile'
        'Nombre_usuario' => $user->nombre_usuario, // Nombre de usuario
        'Apellido_usuario' => $user->apellido_usuario, // Apellido de usuario
        'Fecha_de_valida_mail' => $fecha_valida_mail_date,
        'Valida_mail' => $valida_mail,
        'Cantidad_de_logueos' => $cantidad_logueo,
        'Sucursales' => $user->cantidad_sucursales,
        'Rubro' => $user->rubro,
        'Numero_de_empleados' => $user->cantidad_empleados,
        'Country' => null,
        'State' => null,
        'City' => null,
        'Estado_de_pago' => $estado_pago,
        'Fecha_de_pago' => $fecha_confirmed_at,
        'Vencimiento_demo' => $prueba_hasta,
        'ltimo_Logueo' => $ultimo_logueo,
        'Autogestion' => $user->autogestion,
        'Flujo' => (string) $user->flujo, // Conversión de integer a string
    ];
    */
        $leadData = [
        'FLAMINCO_ID' => $user->id,
        'Company' => $user->name, // Mapea 'sucursal' a 'Company'
        'Full_Name' => $user->nombre_usuario . ' ' . $user->apellido_usuario, // Nombre completo
        'Last_Name' => $user->apellido_usuario, // Apellido
        'First_Name' => $user->nombre_usuario,
        'Email' => $user->email, // Correo electrónico
        'Phone' => $user->prefijo_pais . $user->phone, // Teléfono
        'Mobile' =>  $user->prefijo_pais . $user->phone, // Mapea 'phone' a 'Mobile'
        'Nombre_usuario' => $user->nombre_usuario, // Nombre de usuario
        'Apellido_usuario' => $user->apellido_usuario, // Apellido de usuario
        'Fecha_de_valida_mail' => $fecha_valida_mail_date,
        'Valida_mail' => $valida_mail,
        'Cantidad_de_logueos' => $cantidad_logueo,
        'Sucursales' => $user->cantidad_sucursales,
        'Rubro' => $user->rubro,
        'Numero_de_empleados' => $user->cantidad_empleados,
        'Country' => null,
        'State' => null,
        'City' => null,

        // cambios
        'Plan' => null,
        'Importe' => null,
        'Inicio de demo' => null,
        'Vencimiento_demo' => null,
        'Registro_su_tarjeta' => null,
        'Primer_pago' => null,
        'Fecha_ultimo_pago' => null,
        'Autogestion' => $user->autogestion,
        'Flujo' => (string) $flujo, // Conversión de integer a string
    ];

    //dd($user,$leadData);
    
    // Configurar cliente Guzzle
    $client = new Client();

    try {
        $accessToken = $this->GetAccessToken(); // Obtén el token de acceso

        // Realiza la solicitud POST para crear un nuevo lead
        $response = $client->post('https://www.zohoapis.com/crm/v2/Leads', [
            'headers' => [
                'Authorization' => "Zoho-oauthtoken $accessToken",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'data' => [$leadData], // Enviar los datos del lead en formato JSON
            ],
        ]);

        // Obtén la respuesta
        $responseBody = json_decode($response->getBody(), true);
        
        
        \Log::info('Lead creado:', $responseBody);
        // Extrae el ID del lead desde la respuesta
        $leadId = $responseBody['data'][0]['details']['id'];

        $usuario = User::find($user_id);
        $usuario->lead_soho_id = $leadId;
        $usuario->save();
        
        return response()->json([
            'success' => 'Lead creado con éxito',
            'data' => $responseBody,
        ]);

    } catch (\Exception $e) {
        \Log::error('Error al crear el lead:', ['message' => $e->getMessage()]);
        return response()->json([
            'error' => 'Error al crear el lead',
            'message' => $e->getMessage()
        ], 500);
    }
}
/*
public function updateLeadFromUser($userId)
{
    $user = User::find($userId);
    $leadId = $user->lead_soho_id;

    $leadData = $this->ObtenerDatosDelUsuario($userId);
    
    \Log::info('Lead data:', $leadData);
    // Configurar cliente Guzzle
    $client = new Client();

    try {
        $accessToken = $this->GetAccessToken(); // Obtén el token de acceso

        // Realiza la solicitud PUT para actualizar el lead
        $response = $client->put("https://www.zohoapis.com/crm/v2/Leads/$leadId", [
            'headers' => [
                'Authorization' => "Zoho-oauthtoken $accessToken",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'data' => [$leadData], // Enviar los datos del lead en formato JSON
            ],
        ]);

        // Obtén la respuesta
        $responseBody = json_decode($response->getBody(), true);
        
        \Log::info('Lead actualizado:', $responseBody);

        return response()->json([
            'success' => 'Lead actualizado con éxito',
            'data' => $responseBody
        ]);

    } catch (\Exception $e) {
        \Log::error('Error al actualizar el lead:', ['message' => $e->getMessage()]);
        return response()->json([
            'error' => 'Error al actualizar el lead',
            'message' => $e->getMessage()
        ], 500);
    }
}
*/
public function actualizarLead($userId)
{
    try {
        // Llamar a la función de actualización de contacto en Zoho
        $response = $this->updateLeadFromUser1($userId);

        // Si la actualización es correcta, emitir evento de éxito
        $this->emit('msg-success', 'Se actualizó correctamente.');
    } catch (\Exception $e) {
        // Si ocurre un error, emitir evento de error
        \Log::error('Error al actualizar en Zoho', ['message' => $e->getMessage()]);
        $this->emit('msg-error', 'Hubo un error al actualizar.');
    }
}

public function updateLeadFromUser($userId){
    
}
public function updateLeadFromUser1($userId)
{
    $user = User::find($userId);
    $leadId = $user->lead_soho_id;

    $leadData = $this->ObtenerDatosDelUsuario($userId);
    
    \Log::info('Lead data:', $leadData);
    // Configurar cliente Guzzle
    $client = new Client();

    try {
        $accessToken = $this->GetAccessToken(); // Obtén el token de acceso

        // Realiza la solicitud PUT para actualizar el lead
        $response = $client->put("https://www.zohoapis.com/crm/v2/Leads/$leadId", [
            'headers' => [
                'Authorization' => "Zoho-oauthtoken $accessToken",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'data' => [$leadData], // Enviar los datos del lead en formato JSON
            ],
        ]);

        // Obtén la respuesta
        $responseBody = json_decode($response->getBody(), true);
        
        
        if ((isset($responseBody['data']) && count($responseBody['data']) > 0 ) && $responseBody['data'][0]['status'] !== "error") {
            // Lead encontrado y actualizado
            \Log::info('Lead actualizado:', $responseBody);

            return response()->json([
                'success' => 'Lead actualizado con éxito',
                'data' => $responseBody
            ]);
        } else {
            // Lead no encontrado, llamar a la función de actualización de contacto
            \Log::warning('Lead no encontrado, actualizando contacto en Zoho');

            // Llamar a la función que actualiza el contacto (puedes pasar el $userId o lo que necesites)
            $this->actualizarContactoEnZoho($userId);

            return response()->json([
                'success' => 'Lead no encontrado, contacto actualizado en Zoho',
            ]);
        }

    } catch (\Exception $e) {
        \Log::error('Error al actualizar el lead:', ['message' => $e->getMessage()]);
        return response()->json([
            'error' => 'Error al actualizar el lead',
            'message' => $e->getMessage()
        ], 500);
    }
}


public function buscarPorIdFlaminco($idFlaminco, $modulo)
{
    try {
        // Criterio de búsqueda
        $criteria = '(FLAMINCO_ID:equals:' . $idFlaminco . ')';

        // Establecer la URL base del módulo (Contactos o Potentials)
        $urlModulo = 'https://www.zohoapis.com/crm/v2/' . $modulo . '/search';

        // Inicializar el cliente HTTP
        $client = new Client();

        // Obtener el token de acceso
        $accessToken = $this->GetAccessToken();

        // Realizar la solicitud GET para buscar el registro
        $response = $client->request('GET', $urlModulo, [
            'headers' => [
                'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
                'Content-Type' => 'application/json',
            ],
            'query' => ['criteria' => $criteria]
        ]);

        // Convertir la respuesta en JSON
        $data = json_decode($response->getBody(), true);

        // Verificar si se encontraron registros en la respuesta
        if (!empty($data['data'])) {
            // Obtener el ID del primer registro encontrado
            $registroId = $data['data'][0]['id'];
            
            // Retornar el ID del registro encontrado
            return $registroId;
        } else {
            // Si no se encontró, puedes manejar el caso aquí o retornar null
       //     dd('No se encontró ningún contacto con el ID de Flaminco: ' . $idFlaminco);
        }
    } catch (\Exception $e) {
        // Manejar errores o excepciones
   //     dd('Error al buscar el contacto: ' . $e->getMessage());
        //return null; // Puedes lanzar una excepción si prefieres manejarla de otra forma
    }
}

public function ObtenerDatosDelUsuario($userId){
    
    $user = User::find($userId);
    
    if (!$user) {
        return response()->json([
            'error' => 'Usuario no encontrado'
        ], 404);
    }
    
    $suscripcion = Suscripcion::where("user_id",$user->id)->first();
    $monto_suscripcion = $suscripcion ? $suscripcion->monto_mensual : 0;
    
    $valida_mail = $user->email_verified_at ? "SI" : "NO";
    $estado_pago = $user->confirmed_at ? "SI" : "NO";

    $fecha_valida_mail_date = $user->created_at ? Carbon::parse($user->created_at)->format('Y-m-d') : null;
    $fecha_valida_mail_date_time = $user->created_at ? Carbon::parse($user->created_at)->format('Y-m-d\TH:i:sP') : null;
    $fecha_confirmed_at = $user->confirmed_at ? Carbon::parse($user->confirmed_at)->format('Y-m-d\TH:i:sP') : null;
    $prueba_hasta = $user->prueba_hasta 
    ? Carbon::parse($user->confirmed_at)->addDays(14)->format('Y-m-d\TH:i:sP') 
    : null;
   
    
    $suscripcion_control = SuscripcionControl::where('user_id',$user->id)->orderBy('id','desc')->first();
    $suscripcion_paga_primer_pago = SuscripcionControl::where('user_id',$user->id)->where('cobro_status','pagada')->orderBy('id','asc')->first();	
    $suscripcion_paga_ultimo_pago = SuscripcionControl::where('user_id',$user->id)->where('cobro_status','pagada')->orderBy('id','desc')->first();	
    $suscripcion = suscripcion::where("user_id",$user->id)->where('suscripcion_status','<>','pending')->first();
    //dd($suscripcion);
    
    if($suscripcion){
        $plan = $suscripcion->plan_id_flaminco;
        if($plan == 1){$plan_nombre = "Emprendedor";}
        if($plan == 2){$plan_nombre = "Pequeñas empresas";}
        if($plan == 3){$plan_nombre = "Medianas empresas";}
        if($plan == 4){$plan_nombre = "Grandes empresas";}
        
        $importe = $suscripcion->monto_mensual;
        
        $inicio_demo = $suscripcion ? Carbon::parse($suscripcion->created_at)->format('Y-m-d\TH:i:sP') : null;
        $vencimiento_demo = $suscripcion ? Carbon::parse($suscripcion->created_at)->addDays(14)->format('Y-m-d\TH:i:sP') : null;
        
        $primer_pago = $suscripcion_paga_primer_pago ?  Carbon::parse($suscripcion_paga_primer_pago->created_at)->format('Y-m-d\TH:i:sP') : null;
        $fecha_ultimo_pago = $suscripcion_paga_ultimo_pago ? Carbon::parse($suscripcion_paga_ultimo_pago->created_at)->format('Y-m-d\TH:i:sP') : null;
        
        $registro_su_tarjeta = $suscripcion ? 'SI' : 'NO';
        $estado_pago = $suscripcion_control ? $suscripcion_control->action : null;
         $ultimo_logueo = $user->last_login ? Carbon::parse($user->last_login)->format('Y-m-d\TH:i:sP') : null;
            if($user->cantidad_login == 0){$cantidad_logueo = 1;} else {$cantidad_logueo = $user->cantidad_login;}
            
    } else {
        $plan_nombre = null;
        $importe = null;
        $inicio_demo = null;
        $vencimiento_demo = null;
        $primer_pago = null;
        $registro_su_tarjeta = 'NO';
        $fecha_ultimo_pago = null;
        $estado_pago = null;
        $ultimo_logueo = null;
        $cantidad_logueo = null;
    }
    $flujo = (string) $user->flujo;
  //  dd($primer_pago);
    
    $leadData = [
        'FLAMINCO_ID' => $user->id,
        'Company' => $user->name, // Mapea 'sucursal' a 'Company'
        'Full_Name' => $user->nombre_usuario . ' ' . $user->apellido_usuario, // Nombre completo
        'Last_Name' => $user->apellido_usuario, // Apellido
        'First_Name' => $user->nombre_usuario,
        'Email' => $user->email, // Correo electrónico
        'Phone' => $user->prefijo_pais . $user->phone, // Teléfono
        'Mobile' =>  $user->prefijo_pais . $user->phone, // Mapea 'phone' a 'Mobile'
        'Nombre_usuario' => $user->nombre_usuario, // Nombre de usuario
        'Apellido_usuario' => $user->apellido_usuario, // Apellido de usuario
        'Fecha_de_valida_mail' => $fecha_valida_mail_date,
        'Valida_mail' => $valida_mail,
        'Cantidad_de_logueos' => $cantidad_logueo,
        'Sucursales' => $user->cantidad_sucursales,
        'Rubro' => $user->rubro,
        'Numero_de_empleados' => $user->cantidad_empleados,
        'Country' => null,
        'State' => null,
        'City' => null,

        // cambios
        'Plan' => $plan_nombre,
        'Importe' => (string) $importe,
        'Inicio_de_demo' => $inicio_demo,
        'Vencimiento_demo' => $vencimiento_demo,
        'Registro_su_tarjeta' => $registro_su_tarjeta,
        'Primer_pago' => $primer_pago,
        'Fecha_ultimo_pago' => $fecha_ultimo_pago,
        'Estado_de_pago1' => $estado_pago,
        'Autogestion' => $user->autogestion,
        'Estado_de_pago1' => $estado_pago,
        'ltimo_Logueo' => $ultimo_logueo,
        'Flujo' => (string) $flujo, // Conversión de integer a string
    ];

    return $leadData;
}

public function actualizarContactoEnZoho($userId)
{
    try {
        // Paso 1: Obtener los datos del usuario
        $userData = $this->ObtenerDatosDelUsuario($userId);

        // Paso 2: Buscar el contacto en Zoho CRM usando el ID de Flaminco
        $contactoId = $this->buscarPorIdFlaminco($userData['FLAMINCO_ID'],'Contacts');

        if ($contactoId) {
            // Paso 3: Preparar los datos que se actualizarán en Zoho CRM
            $updatedData = [
                'data' => [
                    [
                        'id' => $contactoId,  // ID del contacto en Zoho CRM
                        'Company' => $userData['Company'],
                        'Full_Name' => $userData['Full_Name'],
                        'Last_Name' => $userData['Last_Name'],
                        'First_Name' => $userData['First_Name'],
                        'Email' => $userData['Email'],
                        'Phone' => $userData['Phone'],
                        'Mobile' => $userData['Mobile'],
                        'Nombre_usuario' => $userData['Nombre_usuario'],
                        'Apellido_usuario' => $userData['Apellido_usuario'],
                        'Fecha_de_valida_mail' => $userData['Fecha_de_valida_mail'],
                        'Valida_mail' => $userData['Valida_mail'],
                        'Cantidad_de_logueos' => $userData['Cantidad_de_logueos'],
                        'Sucursales' => $userData['Sucursales'],
                        'Rubro' => $userData['Rubro'],
                        'Numero_de_empleados' => $userData['Numero_de_empleados'],
                        'Plan' => $userData['Plan'],
                        'Importe' => $userData['Importe'],
                        'Inicio_de_demo' => $userData['Inicio_de_demo'],
                        'Vencimiento_demo' => $userData['Vencimiento_demo'],
                        'Registro_su_tarjeta' => $userData['Registro_su_tarjeta'],
                        'Primer_pago' => $userData['Primer_pago'],
                        'Fecha_ultimo_pago' => $userData['Fecha_ultimo_pago'],
                        'Estado_de_pago' => $userData['Estado_de_pago1'],
                        'Autogestion' => $userData['Autogestion'],
                        'ltimo_Logueo' => $userData['ltimo_Logueo'],
                        'Flujo' => $userData['Flujo'],
                    ]
                ]
            ];

            // Paso 4: Realizar la solicitud PUT para actualizar el contacto
            $client = new Client();
            $accessToken = $this->GetAccessToken(); // Obtén el token de acceso

            // Realiza la solicitud PUT para actualizar el contacto
            $response = $client->request('PUT', 'https://www.zohoapis.com/crm/v2/Contacts', [
                'headers' => [
                    'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $updatedData // Datos que se actualizarán
            ]);

            // Verifica la respuesta de Zoho CRM
            $data = json_decode($response->getBody(), true);

            if (!empty($data['data'])) {
                $this->actualizarPotentialEnZoho($userId);
                \Log::info('Contacto actualizado correctamente', ['data' => $data]);
            } else {
                \Log::error('Error al actualizar el contacto', ['response_data' => $data]);
            }
        } else {
            \Log::info('No se encontró el contacto en Zoho CRM', ['userId' => $userId]);
        }
    } catch (\Exception $e) {
        \Log::error('Error al actualizar el contacto en Zoho', ['message' => $e->getMessage()]);
    }
}

public function actualizarPotentialEnZoho($userId)
{
    try {
        // Paso 1: Obtener los datos del usuario
        $userData = $this->ObtenerDatosDelUsuario($userId);

        // Paso 2: Buscar el ID del "Potential" en Zoho CRM usando el ID de Flaminco
        $potentialId = $this->buscarPorIdFlaminco($userData['FLAMINCO_ID'],'Potentials');

        if ($potentialId) {
            // Paso 3: Preparar los datos que se actualizarán en Zoho CRM
            $updatedData = [
                'data' => [
                    [
                        'id' => $potentialId,  // ID del "Potential" en Zoho CRM
                        'Company' => $userData['Company'],
                        'Full_Name' => $userData['Full_Name'],
                        'Last_Name' => $userData['Last_Name'],
                        'First_Name' => $userData['First_Name'],
                        'Email' => $userData['Email'],
                        'Phone' => $userData['Phone'],
                        'Mobile' => $userData['Mobile'],
                        'Nombre_usuario' => $userData['Nombre_usuario'],
                        'Apellido_usuario' => $userData['Apellido_usuario'],
                        'Fecha_de_valida_mail' => $userData['Fecha_de_valida_mail'],
                        'Valida_mail' => $userData['Valida_mail'],
                        'Cantidad_de_logueos' => $userData['Cantidad_de_logueos'],
                        'Sucursales' => $userData['Sucursales'],
                        'Rubro' => $userData['Rubro'],
                        'Numero_de_empleados' => $userData['Numero_de_empleados'],
                        'Plan' => $userData['Plan'],
                        'Importe_21' => $userData['Importe'],
                        'Amount' => $userData['Importe'],
                        'Inicio_de_demo' => $userData['Inicio_de_demo'],
                        'Vencimiento_demo' => $userData['Vencimiento_demo'],
                        'Registro_su_tarjeta' => $userData['Registro_su_tarjeta'],
                        'Primer_pago' => $userData['Primer_pago'],
                        'Fecha_ultimo_pago' => $userData['Fecha_ultimo_pago'],
                        'Estado_de_pago' => $userData['Estado_de_pago1'],
                        'Autogestion' => $userData['Autogestion'],
                        'Ultimo_Logueo' => $userData['ltimo_Logueo'],
                        'Flujo' => $userData['Flujo'],
                    ]
                ]
            ];

            // Paso 4: Realizar la solicitud PUT para actualizar el "Potential"
            $client = new Client();
            $accessToken = $this->GetAccessToken(); // Obtén el token de acceso

            // Realiza la solicitud PUT para actualizar el "Potential"
            $response = $client->request('PUT', 'https://www.zohoapis.com/crm/v2/Potentials', [
                'headers' => [
                    'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $updatedData // Datos que se actualizarán
            ]);

            // Verifica la respuesta de Zoho CRM
            $data = json_decode($response->getBody(), true);

            if (!empty($data['data'])) {
                \Log::info('Potential actualizado correctamente', ['data' => $data]);
            } else {
                \Log::error('Error al actualizar el Potential', ['response_data' => $data]);
            }
        } else {
            \Log::info('No se encontró el Potential en Zoho CRM', ['userId' => $userId]);
        }
    } catch (\Exception $e) {
        \Log::error('Error al actualizar el Potential en Zoho', ['message' => $e->getMessage()]);
    }
}



public function actualizarContacto($userId)
{
    try {
        // Llamar a la función de actualización de contacto en Zoho
        $this->actualizarContactoEnZoho($userId);

        // Si la actualización es correcta, emitir evento de éxito
        $this->emit('msg-success', 'El contacto se actualizó correctamente.');
    } catch (\Exception $e) {
        // Si ocurre un error, emitir evento de error
        \Log::error('Error al actualizar el contacto en Zoho', ['message' => $e->getMessage()]);
        $this->emit('msg-error', 'Hubo un error al actualizar el contacto.');
    }
}


public function actualizarLeadsBulk($userIds)
{
    try {
        // Llamar a la función de actualización de contactos en Zoho
        $response = $this->updateLeadsFromUsersBulk($userIds);

        // Si la actualización es correcta, emitir evento de éxito
        $this->emit('msg-success', 'Leads actualizados correctamente.');
    } catch (\Exception $e) {
        // Si ocurre un error, emitir evento de error
        dd($e->getMessage());
        \Log::error('Error al actualizar leads en Zoho', ['message' => $e->getMessage()]);
        $this->emit('msg-error', 'Hubo un error al actualizar los leads.');
    }
}

public function updateLeadsFromUsersBulk($userIds)
{
    $leadsData = [];

    // Recorre todos los userIds y genera los datos de cada lead
    foreach ($userIds as $userId) {
        $user = User::find($userId);
        $leadId = $user->lead_soho_id;

        // Genera los datos del lead usando tu método ObtenerDatosDelUsuario
        $leadData = $this->ObtenerDatosDelUsuario($userId);
        $leadData['id'] = $leadId; // Asegúrate de agregar el ID del lead
        $leadsData[] = $leadData;
    }

    // Log para verificar los datos que se van a enviar
    \Log::info('Leads data:', $leadsData);

    // Configurar cliente Guzzle
    $client = new Client();

    try {
        $accessToken = $this->GetAccessToken(); // Obtén el token de acceso

        // Realiza la solicitud PUT para actualizar varios leads a la vez
        $response = $client->put('https://www.zohoapis.com/crm/v2/Leads', [
            'headers' => [
                'Authorization' => "Zoho-oauthtoken $accessToken",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'data' => $leadsData, // Enviar los datos de los leads en formato JSON
            ],
        ]);

        // Obtén la respuesta
        $responseBody = json_decode($response->getBody(), true);

        if (isset($responseBody['data']) && count($responseBody['data']) > 0) {
            // Todos los leads se actualizaron correctamente
            \Log::info('Leads actualizados:', $responseBody);

            return response()->json([
                'success' => 'Leads actualizados con éxito',
                'data' => $responseBody
            ]);
        } else {
            \Log::warning('Algunos leads no se encontraron o no se actualizaron');

            return response()->json([
                'error' => 'Error al actualizar algunos leads',
                'data' => $responseBody
            ]);
        }

    } catch (\Exception $e) {
        \Log::error('Error al actualizar los leads:', ['message' => $e->getMessage()]);
        return response()->json([
            'error' => 'Error al actualizar los leads',
            'message' => $e->getMessage()
        ], 500);
    }
}







}

