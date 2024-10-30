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
        <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico"/>

		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="assets/pos/css/bootstrap.min.css">

        <!-- Fontawesome CSS -->
		<link rel="stylesheet" href="assets/pos/plugins/fontawesome/css/fontawesome.min.css">
		<link rel="stylesheet" href="assets/pos/plugins/fontawesome/css/all.min.css">

		<!-- Main CSS -->
        <link rel="stylesheet" href="assets/pos/css/style.css">

    </head>
    <body class="account-page">

		<!-- Main Wrapper -->
        <div class="main-wrapper">
			<div class="account-content">
				<div class="login-wrapper">
                    <div class="login-content">
                        <div class="login-userset">
                             <div class="login-logo logo-normal">
                                <img src="assets/pos/img/logo.png" alt="img">
                            </div>
							<a href="index.html" class="login-logo logo-white">
								<img src="assets/pos/img/logo.png"  alt="">
							</a>
							 @if(session('message'))
                                <div class="alert alert-warning">
                                    {{ session('message') }}
                                </div>
                            @endif
                            <form class="text-left mt-5" action="{{ route('login') }}" method="POST">
                              @csrf

                            <div class="login-userheading">
                                <h3>Inicio de sesion</h3>
                                <h4>Por favor inicia sesion</h4>
                            </div>
                           <div class="form-login">
                                <label>Email</label>
                                <div class="form-addons">
                                    <input type="text" name="email" id="email" placeholder="Ingresa tu email">
                                    <img src="assets/pos/img/icons/mail.svg" alt="img">
                                </div>
                                @error('email')
                                <strong style="color:red;">{{ $message }}</strong>
                                @enderror

                            </div>
                            <div class="form-login">
                                <label>Password</label>
                                <div class="pass-group">
                                    <input type="password" class="pass-input" name="password" id="password" placeholder="Ingresa tu password">
                                    <span class="fas toggle-password fa-eye-slash"></span>
                                </div>

                                @error('password')
                                <strong style="color:red;">{{ $message }}</strong>
                                @enderror

                            </div>
                            <div class="form-login">
                                <div class="alreadyuser">
                                    <h4><a  href="{{url('password/reset')}}" class="hover-a">¿Olvidaste la contraseña?</a></h4>
                                </div>
                            </div>
                            <div class="form-login">
                                <button class="btn btn-login" type="submit">Iniciar sesion</button>
                            </div>
                            </form>
                            <div class="signinform text-center">
                                <h4>No tenes una cuenta? <a href="{{url('registro-directo/1/0')}}" class="hover-a">Registrate</a></h4>
                            </div>
                            <div class="form-setlogin">
                                <h4 hidden>Or sign up with</h4>
                            </div>
                            <div class="form-sociallink">
                                <ul hidden>
                                    <li>
                                        <a href="javascript:void(0);">
                                            <img src="assets/pos/img/icons/google.png" class="me-2" alt="google">
                                            Sign Up using Google
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">
                                            <img src="assets/pos/img/icons/facebook.png" class="me-2" alt="google">
                                            Sign Up using Facebook
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="login-img">
                        <img src="assets/pos/img/login.png" alt="img">
                    </div>
                </div>
			</div>

            @if(auth()->check())
               {{ Auth::logout(); }}
            @endif

        </div>
		<!-- /Main Wrapper -->

		<!-- jQuery -->
        <script src="assets/pos/js/jquery-3.6.0.min.js"></script>

         <!-- Feather Icon JS -->
		<script src="assets/pos/js/feather.min.js"></script>

		<!-- Bootstrap Core JS -->
        <script src="assets/pos/js/bootstrap.bundle.min.js"></script>

		<!-- Custom JS -->
		<script src="assets/pos/js/script.js"></script>

        <!-- Custom JS -->
        <script src="assets/js/csrf-refresh.js"></script>


    </body>
</html>