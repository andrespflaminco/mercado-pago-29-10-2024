<div wire:ignore class="modal fade" id="AgregarContacto" tabindex="-1" role="dialog" aria-labelledby="notesMailModalTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
						<div class="modal-body">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="modal"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
								<div class="notes-box">
										<div class="notes-content">
												<form action="javascript:void(0);" id="notesMailModalTitle">
														<div class="row">
															<div class="col-md-12">
                                <select class="form-control" wire:model.defer="contacto_elegido">
                                <option selected="selected">Elegir</option>
                                @foreach($contactos as $c)
                                <option value="{{$c->tipo}}-{{$c->id}}">{{$c->nombre}}  ( {{$c->tipo}} ) </option>
                                @endforeach
                            </select>
															</div>

												</form>
										</div>
								</div>
						</div>
						<div class="modal-footer">
								<button class="btn" data-dismiss="modal"> CERRAR</button>
								<button id="btn-n-add" class="btn" wire:click="AgregarContacto">AGREGAR</button>
						</div>
				</div>
		</div>
</div>
