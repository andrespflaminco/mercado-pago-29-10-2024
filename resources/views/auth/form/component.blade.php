<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
  <title>FLAMINCO EXPRESS</title>
  <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico"/>
  <!-- BEGIN GLOBAL MANDATORY STYLES -->
  <link href="https://fonts.googleapis.com/css?family=Quicksand:400,500,600,700&display=swap" rel="stylesheet">
  <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('assets/css/plugins.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('assets/css/structure.css') }}" rel="stylesheet" type="text/css" class="structure" />
  <link href="{{ asset('assets/css/authentication/form-1.css') }}" rel="stylesheet" type="text/css" />
  <!-- END GLOBAL MANDATORY STYLES -->
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/forms/theme-checkbox-radio.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/forms/switches.css') }}">


  <link href="{{ asset('plugins/font-icons/fontawesome/css/fontawesome.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('css/fontawesome.css') }}" rel="stylesheet" type="text/css" />

  <!--  BEGIN CUSTOM STYLE FILE  -->
  <link href="{{ asset('assets/css/scrollspyNav.css') }}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('assets/css/components/custom-modal.css') }}" rel="stylesheet" type="text/css" />
  <script src="https://code.iconify.design/2/2.1.2/iconify.min.js"></script>

  <style media="screen">
  .form-form .form-form-wrap form .field-wrapper.input {
  position: relative;
  padding: 8px 0 8px 0;
  border-bottom: none;
}

.form-form {
width: 100% !important;
}
  </style>
</head>
<!-- Modal -->

<style media="screen">
   body {
    background-color: #F9FAFB;  
   }

#boton-azul {
    background: #1763AA !important;
    border: solid 1px #1763AA !important;
}
.titulos {
color: #6B7280;    
font-family: "Inter";
font-weight: 700;
font-size: 19px;
}


.logo {
    position: absolute;
    left: 2.19%;
    right: 83.91%;
    top: 3.52%;
    bottom: 92.13%;
    height:47px;
   }

#caja {
padding: 35px;
 background-color: #FFFFFF;
 border:solid 1px #D1D1D1;
 border-radius: 6px;
 position: absolute;
 left: 30.49%;
 right: 31.54%;
 top: 6.06%;
 bottom: 0.05%;
 border: 1px solid #D1D1D1;
}

.titulo1 {
font-family: "Inter";
font-weight: 700;
font-size: 31px;
line-height: 38px;
/* identical to box height */
color: #1F2937;

}

.parrafo1 {
/* ¿Ya tienes una cuenta? */

position: absolute;
left: 82.13%;
right: 6.67%;
top: 3.8%;
bottom: 94.07%;

font-family: 'Inter';
font-style: normal;
font-weight: 700;
font-size: 14px;
line-height: 23px;
/* identical to box height */


color: #9C9C9C;
}

.parrafo2 {
position: absolute;
left: 34.06%;
right: 45.57%;
top: 14.91%;
bottom: 83.15%;

font-family: 'Inter';
font-style: normal;
font-weight: 700;
font-size: 17px;
line-height: 21px;
}

/* Mobile */

@media screen and (max-width: 600px) { 


.parrafo1 {
    position: absolute;
    left: 6.13%;
    top: 10.8%;
    bottom: 94.07%;
    font-family: Inter;
    font-style: normal;
    font-weight: 700;
   font-size: 0.875rem;
    line-height: 23px;
    color: #9C9C9C;
}

.parrafo2 {
position: absolute;
left: 6.13%;
right: 5.57%;
top: 14.91%;
bottom: 83.15%;

font-family: 'Inter';
font-style: normal;
font-weight: 700;
font-size: 17px;
line-height: 21px;
}

#caja {
    padding: 35px;
    background-color: #FFFFFF;
    border: solid 1px #D1D1D1;
    border-radius: 6px;
    position: absolute;
    top: 18.06%;
    bottom: 0.05%;
    border: 1px solid #D1D1D1;
    left: 3%;
    right: 3%;
    height:850px;
}

}



</style>


<br><br>
<body>
  <div class="row">
      <img src="../assets/img/LOGO_03.png" class="logo" alt="logo">
      <p class="parrafo1">Ya tienes una cuenta? <a href="{{url('login')}}" style="color: #4181FA !important;">Acceder</a></p>
        <div id="caja">
                  <p class="titulo1">Registrate {{$plan_id == 1? 'gratis' : ''}} en Flaminco</p>
                    <p>Sin limites y sin necesidad de tarjeta de credito</p>
                    <br>
                  <form class="mt-0" action="{{ route('register') }}" method="POST">
                    @csrf
                    
                    
                     <div class="form-group">

                        <label class="titulos">Nombre completo</label>
                      <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Nombre del comercio">


                    </div>

                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    
                    <div class="form-group">
                        <label class="titulos">Telefono</label>
                      <input type="text" name="phone" required class="form-control mb-2" id="exampleInputEmail2" placeholder="Ingrese su telefono">
                    </div>
                    
                    
                    <div class="form-group">
                    <label class="titulos">Email</label>
                      <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Ingrese su email">

                      @error('email')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror

                    </div>

                    <div class="form-group">
                    <label class="titulos">Rubro</label>
                      <select name="rubro" required class="form-control mb-2" >
                        <option value="Rubro" selected>Rubro</option>
                        <option value="Kioscos">Kiosco y Almacenes</option>
                        <option value="Supermercados">Supermercados</option>
                        <option value="Ferreteria">Ferreteria</option>
                        <option value="Vinoteca">Vinoteca</option>
                        <option value="Articulos de limpieza">Articulos de limpieza</option>
                        <option value="Cosmetico">Cosmetica</option>
                        <option value="Regionales">Regionales</option>
                        <option value="Otros">Otros</option>

                        </select>
                    </div>


                    <div class="form-group">
                        <label class="titulos">Ingresar la contrase単a</label>
                      <input id="password" placeholder="Contrase単a" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                      @error('password')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror



                    </div>

                    <div class="form-group">
                    <label class="titulos">Confirmar la Contrase単a</label>
                      <input id="password-confirm" type="password" class="form-control" placeholder="Confirmar la Contrase単a" name="password_confirmation" required autocomplete="new-password">


                      @error('password')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror



                    </div>
                    <input name="plan_id" type="hidden" value="{{$plan_id}}">
                    <button type="submit" id="boton-azul" class="btn btn-primary mt-2 mb-2 btn-block">Empezar {{$plan_id == 1? 'gratis' : ''}} </button>
                  </form>

                  <div class="division">
                  </div>

                  <div class="social">

                  </div>


                </div>


  </div>

</body>
