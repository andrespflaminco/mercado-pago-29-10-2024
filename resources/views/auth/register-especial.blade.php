
        <!-- Meta Pixel Code -->
        <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '946528043924786');
        //fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=946528043924786&ev=PageView&noscript=1"
        /></noscript>
        <!-- End Meta Pixel Code -->  

		<!-- Main Wrapper -->
        <div class="main-wrapper">
                    
            @if(auth()->check())
               {{ Auth::logout(); }}
            @endif
            
			<div class="account-content">
				<div class="login-wrapper">
                    <div class="login-content">
                        <div class="login-userset">
                            <div class="login-logo logo-normal">
                                <img src="../../assets/pos/img/logo.png" alt="img">
                            </div>
							<a href="index.html" class="login-logo logo-white">
								<img src="../../assets/pos/img/logo.png"  alt="">
							</a>
                            <div class="login-userheading">
                                <h3>Create una cuenta</h3>
                                <h4>Y empeza a gestionar tu negocio</h4>
                            </div>
                            
        <form class="mt-0" action="{{ route('register') }}" method="POST">
                            @csrf
                         
                            <!----- Nombre ------->
                            
                            <div class="form-login">
                                <label>Nombre</label>
                                <div class="form-addons">
                                    <input type="text" name="nombre_usuario" value="{{ old('nombre_usuario') }}" required placeholder="Ingresa tu nombre">
                                </div>
                            @error('nombre_usuario')
                                <strong style="color:red;">{{ $message }}</strong>
                            @enderror
                            </div>
                            
                            <!----- Apellido ------->
                            
                            <div class="form-login">
                                <label>Apellido</label>
                                <div class="form-addons">
                                    <input type="text" name="apellido_usuario" value="{{ old('apellido_usuario') }}" required placeholder="Ingresa tu apellido">
                                   
                                </div>
                            @error('apellido_usuario')
                                <strong style="color:red;">{{ $message }}</strong>
                            @enderror
                            </div>
                            
                            <!----- Nombre ------->
                            
                            <div class="form-login">
                                <label>Nombre de tu empresa</label>
                                <div class="form-addons">
                                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ingresa el nombre de tu empresa">
                                    <img src="../../assets/pos/img/icons/users1.svg" alt="img">
                                </div>
                            @error('name')
                                <strong style="color:red;">{{ $message }}</strong>
                            @enderror
                            </div>
                            
                            <!----- Email ------->
                            
                            <div class="form-login">
                                <label>Email</label>
                                <div class="form-addons">
                                    <input name="email" value="{{ old('email') }}" type="text" placeholder="Ingresa tu email">
                                    <img src="../../assets/pos/img/icons/mail.svg" alt="img">
                                </div>
                            @error('email')
                                <strong style="color:red;">{{ $message }}</strong>
                            @enderror
                            </div>
                            
                            <!----- Telefono ------->
                            
                            <div class="form-login">
                                <label>Telefono</label>
                                <div class="form-addons" style="display:flex !important;">
                                <select style="color: #8b8bbc; width: 160px;   height: 40px;  border: 1px solid rgba(145, 158, 171, 0.32);  border-radius: 5px;   padding: 0 15px;  -webkit-transition: all 0.2s ease; -ms-transition: all 0.2s ease;    transition: all 0.2s ease;" name="prefijo_pais" value="{{ old('prefijo_pais') }}"  >
                                        @foreach($countries as $country)
                                            <option value="{{ $country['phone_code_slug'] }}">{{ $country['name'] }}  ({{ $country['phone_code'] }}) </option>
                                        @endforeach
                                    </select>
                                    <input name="phone" value="{{ old('phone') }}" type="text" placeholder="Ingresa tu numero de telefono">
                                 <svg style="position: absolute;  top: 12px; right: 16px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8e9aa4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-phone"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                </div>
                            @error('phone')
                                <strong style="color:red;">{{ $message }}</strong>
                            @enderror
                            </div>
                            
                            <!----- Rubro --------->
                            
                            <div class="form-login">
                                <label>Rubro</label>
                                <div class="form-addons">
                                                
                                <select style="color: #8b8bbc; width: 100%;   height: 40px;  border: 1px solid rgba(145, 158, 171, 0.32);  border-radius: 5px;   padding: 0 15px;  -webkit-transition: all 0.2s ease; -ms-transition: all 0.2s ease;    transition: all 0.2s ease;" id="form_rubro" name="rubro" value="{{ old('rubro') }}" required >
                                
                                  <option value="Elegir" selected>Selecciona el rubro</option>
                                  <option value="Articulos de limpieza">Articulos de limpieza</option>
                                  <option value="Automotriz">Automotriz</option>
                                  <option value="Cosmetico">Cosmetica</option>
                                  <option value="Farmacia / Perfumer���a">Farmacia / Perfumeria</option>
                                  <option value="Ferreteria">Ferreteria</option>
                                  <option value="Kioscos">Kiosco y Almacenes</option>
                                  <option value="Regionales">Regionales</option>
                                  <option value="Restaurante / F���brica de comidas">Restaurante / Fabrica de comidas</option>
                                  <option value="Ropa">Ropa</option>
                                  <option value="Supermercados">Supermercados</option>
                                  <option value="Vinoteca">Vinoteca</option>
                                  
                                  <option value="Otros">Otros</option>


                                  </select>
                                  
                                </div>
                            @error('rubro')
                                <strong style="color:red;">{{ $message }}</strong>
                            @enderror
                            </div>
                            

                            <!----- Cantidad sucursales --------->
                            
                            <div class="form-login">
                                <label>Cantidad sucursales</label>
                                <div class="form-addons">
                                                
                                <select style="color: #8b8bbc; width: 100%;   height: 40px;  border: 1px solid rgba(145, 158, 171, 0.32);  border-radius: 5px;   padding: 0 15px;  -webkit-transition: all 0.2s ease; -ms-transition: all 0.2s ease;    transition: all 0.2s ease;" id="form_rubro" name="cantidad_sucursales" value="{{ old('cantidad_sucursales') }}" required >
                                
                                  <option value="1" selected>1 sucursal</option>
                                  <option value="2 - 5">De 2 a 5 sucursales</option>
                                  <option value="5 - 10">De 5 a 10 sucursales</option>
                                  <option value="10 - 25">De 10 a 25 sucursales</option>
                                  <option value="+ 25">Mas de 25</option>

                                  </select>
                                  
                                </div>
                            @error('cantidad_sucursales')
                                <strong style="color:red;">{{ $message }}</strong>
                            @enderror
                            </div>
                                                        
                            <!----- Cantidad usuarios --------->
                            
                            <div class="form-login">
                                <label>Cantidad empleados</label>
                                <div class="form-addons">
                                                
                                <select style="color: #8b8bbc; width: 100%;   height: 40px;  border: 1px solid rgba(145, 158, 171, 0.32);  border-radius: 5px;   padding: 0 15px;  -webkit-transition: all 0.2s ease; -ms-transition: all 0.2s ease;    transition: all 0.2s ease;" id="form_rubro" name="cantidad_empleados" value="{{ old('cantidad_empleados') }}" required >
                                
                                  <option value="1" selected>1 empleado</option>
                                  <option value="2 - 6">De 2 a 6 empleados</option>
                                  <option value="7 - 11">De 7 a 11 empleados</option>
                                  <option value="12 - 30">De 12 a 30 empleados</option>
                                  <option value="+ 30">Mas de 30</option>

                                  </select>
                                  
                                </div>
                            @error('cantidad_empleados')
                                <strong style="color:red;">{{ $message }}</strong>
                            @enderror
                            </div>
                              
                            <!----- Password ------->
                            <div class="form-login">
                                <label>Contraseña</label>
                                <div class="pass-group">
             
             
                                    <input id="password" type="password" name="password" class="pass-input" required autocomplete="new-password" placeholder="Ingresa tu contraseña">
                                    <span class="fas toggle-password fa-eye-slash"></span>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                </div>
                            </div>
                            
                            
                            <!---- Confirmacion de password ------>
                            
                            <div class="form-login">
                                <label>Confirmar la contraseña</label>
                                <div class="pass-group">
                                     <input id="password-confirm" type="password" class="pass-input" name="password_confirmation" required autocomplete="new-password">
                                    <span class="fas toggle-password fa-eye-slash"></span>
                                </div>
                            </div>
                            
                            
                            <!------------------------------------->                            
                            
                            
                            @error('password')
                            <strong style="color:red;">{{ $message }}</strong>
                            @enderror
                            
                            <input value="{{$slug}}" name="slug" hidden>
                            <input value="{{$intencion_compra}}" name="intencion_compra" hidden>
                            
                            <div class="form-login">
                                <button type="submit" id="boton_registro" class="btn btn-login">Registrarte</button>
                            </div>
                            
                            </form>
                            
                            
                            @if(0 < $intencion_compra)
                            <div class="signinform text-center">
                                <h4>Ya tenes un usuario? <a  href="{{url('suscribirse/'.$intencion_compra)}}" class="hover-a">Iniciar sesion</a></h4>
                            </div>
                            @else
                            <div class="signinform text-center">
                                <h4>Ya tenes un usuario? <a  href="{{url('login')}}" class="hover-a">Iniciar sesion</a></h4>
                            </div>
                            @endif
                            
                            <div hidden class="form-setlogin">
                                <h4>Or sign up with</h4>
                            </div>
                            <div hidden class="form-sociallink">
                                <ul>
                                    <li>
                                        <a href="javascript:void(0);">
                                            <img src="../../assets/img/icons/google.png" class="me-2" alt="google">
                                            Sign Up using Google
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);">
                                            <img src="../../assets/img/icons/facebook.png" class="me-2" alt="google">
                                            Sign Up using Facebook
                                        </a>
                                    </li>
                                </ul>
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
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Obtener el bot���n por su ID
            var botonRegistro = document.getElementById('boton_registro');
        
            // Agregar un event listener al bot���n
            botonRegistro.addEventListener('click', function() {
                // Enviar el evento de Meta cuando se hace clic en el bot���n
                fbq('track', 'Lead');
            });
        });
        </script>