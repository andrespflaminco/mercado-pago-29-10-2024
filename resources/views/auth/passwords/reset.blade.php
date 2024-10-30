

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <meta name="description" content="POS - Bootstrap Admin Template">
		<meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, invoice, html5, responsive, Projects">
        <meta name="author" content="Dreamguys - Bootstrap Admin Template">
        <meta name="robots" content="noindex, nofollow">
        <title>Flaminco app</title>
		
		<style>
		   .customizer-links{
		        display:none !important;
		    }
		</style>
		<!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="../../assets/img/favicon.ico"/>
		
		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="../../assets/pos/css/bootstrap.min.css">
		
        <!-- Fontawesome CSS -->
		<link rel="stylesheet" href="../../assets/pos/plugins/fontawesome/css/fontawesome.min.css">
		<link rel="stylesheet" href="../../assets/pos/plugins/fontawesome/css/all.min.css">
		
		<!-- Main CSS -->
        <link rel="stylesheet" href="../../assets/pos/css/style.css">
		
    </head>
    <body class="account-page">
	
<!-- Main Wrapper -->
        <div class="main-wrapper">
			<div class="account-content">
				<div class="login-wrapper">
                    <div class="login-content">
                        <div class="login-userset ">
                            <div class="login-logo">
                                <img src="../../assets/pos/img/logo.png" alt="img">
                            </div>
                            <div class="login-userheading">
                                <h3>Restablecer {{ __('Password') }}</h3>
                                <h4>Ingrese su nueva {{ __('Password') }}</h4>
                            </div>
                            
                                
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        
                        <input type="hidden" name="token" value="{{ $token }}">
                         
                         
                            <div hidden class="form-login">
                                <label>Email</label>
                                       <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
            
                            </div>
                            
                            <!----- Password ------->
                            <div class="form-login">
                                <label>{{ __('Password') }}</label>
                                <div class="pass-group">
                                     <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                           </div>
                            </div>
                            
                            
                            <!---- Confirmacion de password ------>
                            
                            <div class="form-login">
                                <label>{{ __('Confirm Password') }}</label>
                                <div class="pass-group">
                               <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                            </div>
                            
                            
                            <!------------------------------------->      
                            
                            <div class="form-login">
                                <button class="btn btn-login" type="submit">Enviar</button>
                            </div>
                            
                            </form>
                            
                            <div class="form-login">
                                <div class="alreadyuser">
                                    <h4><a  href="{{url('login')}}" class="hover-a">Volver al inicio</a></h4>
                                </div>
                            </div>
                            
                            
                        </div>
                    </div>
                    <div class="login-img">
                        <img src="../../assets/pos/img/login.png" alt="img">
                    </div>
                </div>
			</div>
        </div>
		<!-- /Main Wrapper -->
		
		<!-- jQuery -->
        <script src="../../assets/pos/js/jquery-3.6.0.min.js"></script>

         <!-- Feather Icon JS -->
		<script src="../../assets/pos/js/feather.min.js"></script>
		
		<!-- Bootstrap Core JS -->
        <script src="../../assets/pos/js/bootstrap.bundle.min.js"></script>
		
		<!-- Custom JS -->
		<script src="../../assets/pos/js/script.js"></script>
		
    </body>
</html>


