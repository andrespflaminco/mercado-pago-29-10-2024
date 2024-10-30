                               <div class="col-sm-12 col-md-12">
                                <div class="form-group">
                                  <label>Variaciones </label>
                            
                                   <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px ;">
                            
                                     <div class="row">
                                        @foreach($atributos_var as $a)
                                       <div class="col-3">
                                         <select {{ $forma_edit == 1? 'disabled' : '' }} class="form-control" wire:model="variacion_atributo.{{ $a->id }}">
                                            <option value="c"> Cualquier {{$a->nombre}}</option>
                            
                                           @foreach($variaciones as $v)
                                            @if($a->id == $v->atributo_id)
                                                <option value="{{$v->id}}">{{$v->nombre}}</option>
                                            @endif
                                           @endforeach
                                         </select>
                                       </div>
                                       
                                       @endforeach
                                        @if ($mostrarErrorVariacion)
                                            <div style="color:red; font-weight:bold;">
                                                Debe agregar alguna variacion de alguno de los atributos.
                                            </div>
                                        @endif
                                       @if(count($variaciones) < 1)
                                       <p style="margin-left:10px;" class="text-danger">Debe agregar variaciones para asociarlas al producto</p>
                                       @else
                                        <div class="mt-2 col-12">
                                       <button type="button" class="btn btn-dark"  wire:click="GuardarVariacion">+ Agregar
                                       </button>     
                                        </div>
                                       
                                       @endif
                            
                                     </div>
                            
                                    </div>
                            
                            
                               </div>
                                @error('stock_sucursal') <span class="text-danger er">{{ $message }}</span> @enderror
                                @error('precio_lista') <span class="text-danger er">{{ $message }}</span> @enderror
                          
                                <?php //debug {{$testGuardarReferernciaID}} ?>
                            
                            
                               </div>
                               
                                @if ($cart->getContent()->count() > 0)
                                <?php $i = 1; ?>
                                <div class="col-12">
                                @foreach ($cart->getContent() as $key => $variaciones)
								
								
								<div class="form-group"   style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;">
                                
                                <!------ Titulo de la variacion ------->
                                
                                <div style="background: #ebedf2; color: #3b3f5c;  border: none; border-radius: 4px;">
                                        <div style="padding:12px;">
                                        <b>
                                          {{$variaciones['var_nombre']}}
                                         </b>
                                          <button type="button" style="float:right;"  onclick="ConfirmVariacion('{{$variaciones['id']}}')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                          </button>
                                         <button type="button" style="float:right; display:flex;" onclick="mostrarOcultarDiv({{$variaciones['referencia_id']}});">
                                           <svg  id="123-{{$variaciones['referencia_id']}}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                        </button>
                                        </div>
                                </div>
                                
                                <!------/ Caja titulo de la variacion ------->
								<div class="col-12" id="{{$variaciones['referencia_id']}}">
								<div class="row" style="padding:12px;">
								
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>SKU variacion</label>
                                         <input maxlength="20" required {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} type="text" wire:model.lazy="cod_variacion.{{$variaciones['referencia_id']}}" class="form-control">
                                        <p style="color:#637381;">* Maximo 20 caracteres</p> 
                                        @error('cod_variacion.' . $variaciones['referencia_id'] ) <span class="text-danger er">{{ $message }}</span> @enderror 
									</div>
								</div>
								
								<div class="col-9"></div>
								<!--------- PRECIOS NUEVOS ------------>
								<div class="col-12">
								
								@php
								    $datos_variacion = explode("-",$variaciones['referencia_id']);
	                                $variacion = $datos_variacion[0];
	                            @endphp
	                            
                                @include('livewire.products-nuevo.ficha-producto.precios')
                                
                                </div>
								<br>
								</div>
								<br>
								<br>
								<div style="padding: 12px;">
							      @include('livewire.products-nuevo.ficha-producto.stocks') 	    
								</div>
                                </div>
                                </div>
								

                                @endforeach
                                </div>
                                @endif