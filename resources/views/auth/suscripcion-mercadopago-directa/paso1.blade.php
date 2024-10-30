<div @if($paso != 1) hidden @endif>
                            <!----- Nombre ------->
                            
                            <div class="form-login">
                                <label>Nombre</label>
                                <div class="form-addons">
                                    <input type="text" wire:model="nombre_usuario_form" value="{{ old('nombre_usuario_form') }}" required placeholder="Ingresa tu nombre">
                                </div>
                            @error('nombre_usuario_form')
                                <strong style="color:red;">{{ $message }}</strong>
                            @enderror
                            </div>
                            
                            <!----- Apellido ------->
                            
                            <div class="form-login">
                                <label>Apellido</label>
                                <div class="form-addons">
                                    <input type="text" wire:model="apellido_usuario_form" value="{{ old('apellido_usuario_form') }}" required placeholder="Ingresa tu apellido">
                                   
                                </div>
                            @error('apellido_usuario_form')
                                <strong style="color:red;">{{ $message }}</strong>
                            @enderror
                            </div>
                            
                            <!----- Nombre ------->
                            
                            <div class="form-login">
                                <label>Nombre de tu empresa</label>
                                <div class="form-addons">
                                    <input type="text" wire:model="name_form" value="{{ old('name_form') }}" required placeholder="Ingresa el nombre de tu empresa">
                                    <img src="../../assets/pos/img/icons/users1.svg" alt="img">
                                </div>
                            @error('name_form')
                                <strong style="color:red;">{{ $message }}</strong>
                            @enderror
                            </div>
                            
                            <!----- Email ------->
                            
                            <div class="form-login">
                                <label>Email</label>
                                <div class="form-addons">
                                    <input wire:model="email_form" value="{{ old('email_form') }}" type="text" placeholder="Ingresa tu email">
                                    <img src="../../assets/pos/img/icons/mail.svg" alt="img">
                                </div>
                            @error('email_form')
                                <strong style="color:red;">{{ $message }}</strong>
                            @enderror
                            </div>
                            
                            
                                                          
                            <!----- Password ------->
                            <div class="form-login">
                                <label>Contraseña</label>
                                <div class="pass-group">
             
             
                                    <input id="password" type="password" wire:model="password" class="pass-input" required autocomplete="new-password" placeholder="Ingresa tu contraseña">
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
                                     <input id="password-confirm" type="password" class="pass-input" wire:model="password_confirmation" required autocomplete="new-password">
                                    <span class="fas toggle-password fa-eye-slash"></span>
                                </div>
                                                            
                            @error('password')
                            <strong style="color:red;">{{ $message }}</strong>
                            @enderror
                            
                            </div>
                            
                            
                            <!------------------------------------->                            
                            
                            
</div>

                            
                            @if($paso == 1)
                            <div class="form-login">
                                 <button wire:click="comprobarDatosRegistroPaso1" class="btn btn-login">Siguiente > </button>
                            </div>
                            @endif