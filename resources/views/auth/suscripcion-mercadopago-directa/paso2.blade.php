<div @if($paso != 2) hidden @endif>
                            <!----- Telefono ------->
                            @php
                                 $countries = [
                                ['name' => 'Argentina', 'phone_code' => '+54','phone_code_slug' => '549'],
                                ['name' => 'Bolivia', 'phone_code' => '+591','phone_code_slug' => '5919'],
                                ['name' => 'Brasil', 'phone_code' => '+55','phone_code_slug' => '559'],
                                ['name' => 'Canadá', 'phone_code' => '+1','phone_code_slug' => '19'],
                                ['name' => 'Chile', 'phone_code' => '+56','phone_code_slug' => '569'],
                                ['name' => 'Colombia', 'phone_code' => '+57','phone_code_slug' => '579'],
                                ['name' => 'Costa Rica', 'phone_code' => '+506','phone_code_slug' => '5069'],
                                ['name' => 'Cuba', 'phone_code' => '+53','phone_code_slug' => '539'],
                                ['name' => 'Ecuador', 'phone_code' => '+593','phone_code_slug' => '5939'],
                                ['name' => 'El Salvador', 'phone_code' => '+503','phone_code_slug' => '5039'],
                                ['name' => 'Estados Unidos', 'phone_code' => '+1','phone_code_slug' => '19'],
                                ['name' => 'Guatemala', 'phone_code' => '+502','phone_code_slug' => '5029'],
                                ['name' => 'Honduras', 'phone_code' => '+504','phone_code_slug' => '5049'],
                                ['name' => 'México', 'phone_code' => '+52','phone_code_slug' => '529'],
                                ['name' => 'Nicaragua', 'phone_code' => '+505','phone_code_slug' => '5059'],
                                ['name' => 'Panamá', 'phone_code' => '+507','phone_code_slug' => '5079'],
                                ['name' => 'Paraguay', 'phone_code' => '+595','phone_code_slug' => '5959'],
                                ['name' => 'Perú', 'phone_code' => '+51','phone_code_slug' => '519'],
                                ['name' => 'Uruguay', 'phone_code' => '+598','phone_code_slug' => '5989'],
                            ];
                            
                            @endphp
                            
                            <div class="form-login">
                                <label>Telefono</label>
                                <div class="form-addons" style="display:flex !important;">
                                <select style="color: #8b8bbc; width: 160px;   height: 40px;  border: 1px solid rgba(145, 158, 171, 0.32);  border-radius: 5px;   padding: 0 15px;  -webkit-transition: all 0.2s ease; -ms-transition: all 0.2s ease;    transition: all 0.2s ease;" wire:model="prefijo_pais_form" value="{{ old('prefijo_pais_form') }}"  >
                                        @foreach($countries as $country)
                                            <option value="{{ $country['phone_code_slug'] }}">{{ $country['name'] }}  ({{ $country['phone_code'] }}) </option>
                                        @endforeach
                                    </select>
                                    <input wire:model="phone_form" value="{{ old('phone_form') }}" type="text" placeholder="Ingresa tu numero de telefono">
                                 <svg style="position: absolute;  top: 12px; right: 16px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8e9aa4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-phone"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                </div>
                            @error('phone_form')
                                <strong style="color:red;">{{ $message }}</strong>
                            @enderror
                            </div>
                            
                            <!----- Rubro --------->
                            
                            <div class="form-login">
                                <label>Rubro</label>
                                <div class="form-addons">
                                                
                                <select style="color: #8b8bbc; width: 100%;   height: 40px;  border: 1px solid rgba(145, 158, 171, 0.32);  border-radius: 5px;   padding: 0 15px;  -webkit-transition: all 0.2s ease; -ms-transition: all 0.2s ease;    transition: all 0.2s ease;" id="form_rubro" wire:model="rubro_form" value="{{ old('rubro_form') }}" required >
                                
                                  <option value="Elegir" selected>Selecciona el rubro</option>
                                  <option value="Articulos de limpieza">Articulos de limpieza</option>
                                  <option value="Automotriz">Automotriz</option>
                                  <option value="Cosmetico">Cosmetica</option>
                                  <option value="Farmacia / Perfumería">Farmacia / Perfumeria</option>
                                  <option value="Ferreteria">Ferreteria</option>
                                  <option value="Kioscos">Kiosco y Almacenes</option>
                                  <option value="Regionales">Regionales</option>
                                  <option value="Restaurante / Fábrica de comidas">Restaurante / Fabrica de comidas</option>
                                  <option value="Ropa">Ropa</option>
                                  <option value="Supermercados">Supermercados</option>
                                  <option value="Vinoteca">Vinoteca</option>
                                  
                                  <option value="Otros">Otros</option>


                                  </select>
                                  
                                </div>
                            @error('rubro_form')
                                <strong style="color:red;">{{ $message }}</strong>
                            @enderror
                            </div>
                            

                            <!----- Cantidad sucursales --------->
                            
                            <div class="form-login">
                                <label>Cantidad sucursales</label>
                                <div class="form-addons">
                                                
                                <select style="color: #8b8bbc; width: 100%;   height: 40px;  border: 1px solid rgba(145, 158, 171, 0.32);  border-radius: 5px;   padding: 0 15px;  -webkit-transition: all 0.2s ease; -ms-transition: all 0.2s ease;    transition: all 0.2s ease;" id="form_rubro" wire:model="cantidad_sucursales_form" value="{{ old('cantidad_sucursales_form') }}" required >
                                  <option value="Elegir" selected>Elegir</option>
                                  <option value="1">1 sucursal</option>
                                  <option value="2 - 5">De 2 a 5 sucursales</option>
                                  <option value="5 - 10">De 5 a 10 sucursales</option>
                                  <option value="10 - 25">De 10 a 25 sucursales</option>
                                  <option value="+ 25">Mas de 25</option>

                                  </select>
                                  
                                </div>
                            @error('cantidad_sucursales_form')
                                <strong style="color:red;">{{ $message }}</strong>
                            @enderror
                            </div>
                                                        
                            <!----- Cantidad usuarios --------->
                            
                            <div class="form-login">
                                <label>Cantidad empleados</label>
                                <div class="form-addons">
                                                
                                <select style="color: #8b8bbc; width: 100%;   height: 40px;  border: 1px solid rgba(145, 158, 171, 0.32);  border-radius: 5px;   padding: 0 15px;  -webkit-transition: all 0.2s ease; -ms-transition: all 0.2s ease;    transition: all 0.2s ease;" id="form_rubro" wire:model="cantidad_empleados_form" value="{{ old('cantidad_empleados_form') }}" required >
                                  <option value="Elegir" selected>Elegir</option>
                                  <option value="1">1 empleado</option>
                                  <option value="2 - 6">De 2 a 6 empleados</option>
                                  <option value="7 - 11">De 7 a 11 empleados</option>
                                  <option value="12 - 30">De 12 a 30 empleados</option>
                                  <option value="+ 30">Mas de 30</option>

                                  </select>
                                  
                                </div>
                            @error('cantidad_empleados_form')
                                <strong style="color:red;">{{ $message }}</strong>
                            @enderror
                            </div>
                              
                            </div>
                            
                            @if($paso == 2)
                            <div class="form-login" style="display: flex !important;">
                                 <button wire:click="IrPaso1" class="btn btn-login" style="width: 50% !important; background: #333 !important; border: solid #333 !important; margin-right:3px !important;">< Anterior </button>
                                <button wire:click="comprobarDatosRegistroPaso2" class="btn btn-login" style="width: 50% !important; margin-left:3px !important;">Siguiente ></button>
                            </div>
                            @endif
                            