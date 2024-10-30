<!-- Main Wrapper -->
<div class="main-wrapper">
			<div class="account-content">
				<div class="login-wrapper">
                    <div class="login-content">
                        <div class="login-userset">
                             <div class="login-logo logo-normal">
                                <img src="/assets/pos/img/logo.png" alt="img">
                            </div>
							<a href="index.html" class="login-logo logo-white">
								<img src="/assets/pos/img/logo.png"  alt="">
							</a>
							                                
                            <div class="login-userheading">
                                <h3>Inicio de sesion...</h3>
                                <h4>Por favor inicia sesion para continuar la suscripcion</h4>
                            </div>
                           <div class="form-login">
                                <label>Email</label>
                                <div class="form-addons">
                                    <input type="text" wire:model.defer="email" id="email" placeholder="Ingresa tu email">
                                    <img src="/assets/pos/img/icons/mail.svg" alt="img">
                                </div>
                                
                                @error('email')
                                <strong style="color:red;">{{ $message }}</strong>
                                @enderror
                                    
                            </div>
                            <div class="form-login">
                                <label>Password</label>
                                <div class="pass-group">
                                    <input type="password" class="pass-input" wire:model.defer="password" id="password" placeholder="Ingresa tu password">
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
                                <button class="btn btn-login" type="button" wire:click="customLogin()">Iniciar sesion</button>
                            </div>
                            
                            <div class="signinform text-center">
                                <h4>No tenes una cuenta? <a href="{{url('registers-suscripcion/'.$slug)}}" class="hover-a">Registrate</a></h4>
                            </div>
                            <div class="form-setlogin">
                                <h4 hidden>Or sign up with</h4>
                            </div>
                            <div class="form-sociallink">
                                <ul hidden>
                                    <li>
                                        <a href="javascript:void(0);">
                                            <img src="/assets/pos/img/icons/google.png" class="me-2" alt="google">
                                            Sign Up using Google
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">
                                            <img src="/assets/pos/img/icons/facebook.png" class="me-2" alt="google">
                                            Sign Up using Facebook
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="login-img">
                        <img src="/assets/pos/img/login.png" alt="img">
                    </div>
                </div>
			</div>
        </div>
		<!-- /Main Wrapper -->