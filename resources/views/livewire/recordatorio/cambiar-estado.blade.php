<div wire:ignore class="modal fade" id="ModalCambiarEstado" tabindex="-1" role="dialog" aria-labelledby="notesMailModalTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
						<div class="modal-body">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="modal"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
								<div class="notes-box">
										<div class="notes-content">
												<form action="javascript:void(0);" id="notesMailModalTitle">
														<div class="row">
																<div class="col-md-12">

																		<div class="d-flex note-title">
																				<input type="date" wire:model="fecha_nueva" class="form-control" >
																		</div>
																		<span class="validation-text"></span>
																</div>

																<div class="col-md-12">

																		<div class="d-flex note-description">

																				<textarea wire:model="comentario" class="form-control" maxlength="60" placeholder="Agregar comentario.." rows="3"></textarea>
																		</div>
																		<span class="validation-text"></span>
																</div>
														</div>

												</form>
										</div>
								</div>
						</div>
						<div class="modal-footer">
							<button class="btn" data-dismiss="modal"> CERRAR </button>
								<button id="btn-n-add" class="btn btn-dark" wire:click="Reprogramar" >GUARDAR</button>
						</div>
				</div>
		</div>
</div>
