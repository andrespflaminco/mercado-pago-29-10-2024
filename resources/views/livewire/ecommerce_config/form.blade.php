
<div wire:ignore.self class="modal fade" id="theModal" tabindex="-1" aria-labelledby="create"  aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						 <h5 class="modal-title" >	<b>{{$nombre_metodo}}</b> </h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
                        						    
                           @if($metodo == 1)
                        
                           <div class="col-sm-12 col-md-12">
                            <div class="form-group">
                             <label>Mensaje</label>
                               <textarea  class="form-control" style="width:100%;" wire:model.prevent="mensaje_efectivo" rows="8" cols="80">
                                 Paga en efectivo en el momento de la entrega.
                               </textarea>
                               @error('banco') <span class="text-danger err">{{ $message }}</span> @enderror
                           </div>
                           </div>
                        
                           @endif
                        
                        
                        @if($metodo == 2)
                        
                        <div class="col-sm-12 col-md-12">
                         <div class="form-group">
                          <label>Elija un banco</label>
                            <select wire:model='banco' class="form-control">
                              <option value="Elegir" disabled >Elegir</option>
                              @foreach($bancos as $b)
                                <option value="{{$b->id}}" >{{$b->nombre}}</option>
                              @endforeach
                            </select>
                            @error('banco') <span class="text-danger err">{{ $message }}</span> @enderror
                        </div>
                        </div>
                        
                        <div class="col-sm-12 col-md-12">
                         <div class="form-group">
                          <label>Mensaje</label>
                            <textarea  class="form-control" style="width:100%;" wire:model="mensaje_transferencia" rows="8" cols="80">
                            </textarea>
                            @error('banco') <span class="text-danger err">{{ $message }}</span> @enderror
                        </div>
                        </div>
                        
                        @endif
                        
                        
                        @if($metodo == 3)
                        
                        <div class="col-sm-12 col-md-12">
                         <div class="form-group">
                          <label>PUBLIC KEY</label>
                            <input type="text" wire:model="mp_key" class="form-control">
                            @error('mp_key') <span class="text-danger err">{{ $message }}</span> @enderror
                        </div>
                        </div>
                        
                        <div class="col-sm-12 col-md-12">
                         <div class="form-group">
                          <label>ACCESS TOKEN</label>
                            <input type="text" wire:model="mp_token" class="form-control">
                            @error('mp_token') <span class="text-danger err">{{ $message }}</span> @enderror
                        </div>
                        </div>
                        
                        <div class="col-sm-12 col-md-12">
                         <div class="form-group">
                          <label>Mensaje</label>
                            <textarea  class="form-control" style="width:100%;" wire:model="mensaje_mp" rows="8" cols="80">
                            </textarea>
                            @error('mensaje_mp') <span class="text-danger err">{{ $message }}</span> @enderror
                        </div>
                        </div>
                        
                        @endif
                        

						</div>
						<div class="col-lg-12">
							
							 <a class="btn btn-submit me-2" id="btn-caja-abrir" type="button" wire:loading.attr="disabled" wire:click.prevent="Update('{{$metodo}}')">Actualizar</a>
							<a class="btn btn-cancel" wire:click.prevent="resetUI()" data-bs-dismiss="modal">Cancelar</a>
							
						</div>
					</div>
				</div>
			</div>
		</div>

