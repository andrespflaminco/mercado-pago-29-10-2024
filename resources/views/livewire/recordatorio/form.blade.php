<div class="modal fade" id="ModalRecordatorio" tabindex="-1" role="dialog" aria-labelledby="notesMailModalTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
						<div class="modal-body">
								<svg wire:click="resetUI" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="modal"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
								<div class="notes-box">
										<div class="notes-content">
												<form action="javascript:void(0);" id="notesMailModalTitle">
														<div class="row">
															<div class="col-md-12">
																	<div class="d-flex note-title">
																			<input type="text" id="n-title" class="form-control" maxlength="25" wire:model.defer="titulo" placeholder="Titulo">
																	</div>
																	<span class="validation-text"></span>
															</div>
																<div class="col-md-12">
																		<div class="d-flex note-title">
																				<input type="date" id="n-title" class="form-control" maxlength="25" wire:model.defer="fecha" placeholder="fecha">
																		</div>
																		<span class="validation-text"></span>
																</div>
																<div class="col-md-12">
																	<div class="d-flex note-title">
																		<div style="padding: 10px 30px 0px 8px;">
																		Contacto:
																		</div>
																		@if($nombre_elegido === 0)
																		
																		<button type="button" wire:click="ModalContacto" class="btn btn-primary" name="button">+</button>
																		@else
																		
																			<button type="button" wire:click="ModalContacto" class="btn btn-primary" name="button">{{$nombre_elegido}}</button>
																	
																		@endif


																	</div>
																</div>



																<div class="col-md-12">
																		<div class="d-flex note-description">
																				<textarea id="n-description" class="form-control" maxlength="60" wire:model.defer="descripcion" placeholder="Descripcion.." rows="3"></textarea>
																		</div>
																		<span class="validation-text"></span>
																</div>

																<div class="col-md-12">
																	<div class="d-flex note-title">
																		<div style="padding: 13px 20px 0px 8px;">
																		Color:
																		</div>
																		
																	   
																		
																		
																		@if($color_elegido === "note-personal")
																		
																		<button class="btn" wire:click="ModalColor"  style="border:solid 1px #eee;  width: 10%; padding:12px; margin-top:10px; border-radius:6px; background: #bae7ff; border: 1px solid #2196f3;" class="nav-item">

																			</button>


																		@endif
																		
																		@if($color_elegido === "note-social")

																		<button class="btn" wire:click="ModalColor"  style="border:solid 1px #eee; width: 10%; padding:12px;  margin-top:10px; border-radius:6px; background: #dccff7; border: 1px solid #5c1ac3;" class="nav-item" >
																			
																		</button>
																		
																		@endif
																		
																		@if($color_elegido === "note-warning")
																			<button class="btn" wire:click="ModalColor" style="border:solid 1px #eee; background: #ffeccb;  border: 1px solid #e2a03f; width: 10%; padding:12px; cursor: pointer; margin-top:10px; border-radius:6px;" class="nav-item">
																			
																			</button>
																			
																		@endif
																		
																		@if($color_elegido === "note-important")
																			<button class="btn" wire:click="ModalColor"  style="border:solid 1px #eee; padding:5px; background: #ffe1e2; border: 1px solid #e7515a; width: 10%; padding:12px; margin-top:10px; border-radius:6px;" class="nav-item">
																			
																			</button>
																			
																		@endif
																		
																		@if($color_elegido === "note-dark")
																			<button class="btn" wire:click="ModalColor"  style="border:solid 1px #eee; background: #c3baac;  border: 1px solid #0e1726; width: 10%; padding:12px;  margin-top:10px; border-radius:6px;" class="nav-item" >
																		
																			</button>
																		@endif
																		
																		@if($color_elegido === "note-green")
																			<button class="btn" wire:click="ModalColor" style="border:solid 1px #eee; background: #e5f9c9;  border: 1px solid #28a745; width: 10%; padding:12px;  margin-top:10px; border-radius:6px;" class="nav-item" >
																			
																			</button>
															            @endif
															            
															           
																		 @if($color_elegido === 0)
																	     <button type="button" wire:click="ModalColor" class="btn btn-primary" name="button">+</button>	
																		@endif
																	


																	</div>
																</div>
                                                                    

														</div>

												</form>
										</div>
								</div>
						</div>
						<div class="modal-footer">
								<button class="btn" data-dismiss="modal" wire:click="resetUI"> CERRAR</button>
								<button id="btn-n-add" class="btn" wire:click="Store">GUARDAR</button>
						</div>
				</div>
		</div>
</div>
