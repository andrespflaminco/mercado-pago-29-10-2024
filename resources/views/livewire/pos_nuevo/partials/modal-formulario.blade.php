<!-- Modal -->
<div class="modal" id="ModalFormulario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		    <div class="modal-dialog" role="document">
		        <div class="modal-content">
		            <div class="modal-header">
		                <h5 class="modal-title" id="exampleModalLabel">Ayudanos a conocerte mejor</h5>
		                <button type="button" wire:click="CerrarFormulario" class="close" aria-label="Close">
		                  x
		                </button>
		            </div>
		            <div style="margin:0 !important;" class="modal-body">
		            <div class="col-12">
									<label for="">¿Cuantos empleados tiene tu empresa?</label>
									<select  wire:model.defer="cantidad_empleados" class="form-control">
										<option value="Elegir"> Elegir</option>
										<option value="1">1 empleado</option>
										<option value="2">2 empleados</option>
										<option value="3">3 empleados</option>
										<option value="4">4 empleados</option>
										<option value="5">5 empleados</option>
										<option value="6">+ de 5 empleados</option>
									</select>

									 @error('cantidad_empleados') <span class="error">{{ $message }}</span> @enderror

		            </div>
								<div class="col-12">
									<label for="">¿Cuenta con un sistema de administración?</label>
									<select wire:model="cuenta_con_sistema" wire:change='CuentaConSistema($event.target.value)'  class="form-control">
										<option value="Elegir">Elegir</option>
										<option value="Si">Si</option>
										<option value="No">No</option>
									</select>

									 @error('cuenta_con_sistema') <span class="error">{{ $message }}</span> @enderror
								</div>

								@if($cuenta_con_sistema == 'No')
								<br>
								<div class="col-12">
									<label for="">¿Que tan importante considera el uso de un sistema?</label> <br>
									<select wire:model.defer="importancia"  class="form-control">
										<option value="Elegir">Elegir</option>
										<option value="Mucho">Muy importante</option>
										<option value="Medio">Medianamente importante</option>
										<option value="Poco">Poco importante</option>
									</select>

									@error('importancia') <span class="error">{{ $message }}</span> @enderror

								</div>
								<br>
								<div class="col-12">
									<label for="">¿Porque motivo esta buscando un sistema?</label> <br>
									<input type="checkbox" wire:model.defer="motivo_no" value="stock"> Problemas de stock <br>
									<input type="checkbox" wire:model.defer="motivo_no" value="cajas"> Problemas de cajas <br>
									<input type="checkbox" wire:model.defer="motivo_no"  value="acceso remoto"> Controlar mi negocio sin estar presente todo el dia <br>
									<input type="checkbox"  wire:model.defer="motivo_no"  value="ventas"> Automatizar el registro de ventas <br>
									<input type="checkbox"  wire:model.defer="motivo_no"  value="facturacion"> Facturacion <br>
									<input type="checkbox"  wire:model.defer="motivo_no"  value="ecommerce"> E-commerce <br>

									@error('motivo_no') <span class="error">{{ $message }}</span> @enderror
								</div>
								@endif


								@if($cuenta_con_sistema == 'Si')
								<br>
								<div class="col-12">
									<label for="">¿Porque motivo esta buscando un nuevo sistema?</label> <br>
									<input type="checkbox" wire:model.defer='motivo_si' value="precio"> Precio <br>
									<input type="checkbox" wire:model.defer='motivo_si' value="funcionalidades"> Funcionalidades <br>
									<input type="checkbox" wire:model.defer='motivo_si' value="soporte"> Soporte <br>
									<input type="checkbox" wire:model.defer='motivo_si' value="suriosidad"> Curiosidad/Estoy viendo <br>
									<input type="checkbox" wire:model.defer='motivo_si' value="ns"> NS/NC <br>

								</div>
								@error('motivo_si') <span class="error">{{ $message }}</span> @enderror
								<br>


								<div class="col-12">
									<label for="">¿Con que urgencia usted considera que debe cambiarse de sistema?</label> <br>
									<select wire:model.defer="urgencia"  class="form-control">
										<option value="Elegir">Elegir</option>
										<option value="Mucha">Muy urgente</option>
										<option value="Media">Medianamente urgente</option>
										<option value="Poca">Poco urgente</option>
									</select>
									@error('urgencia') <span class="error">{{ $message }}</span> @enderror

								</div>

								@endif
		            </div>
		            <div class="modal-footer">
		                <button class="btn" wire:click="CerrarFormulario"><i class="flaticon-cancel-12"></i> OMITIR</button>
		                <button type="button" wire:click="GuardarInfoFormulario()" class="btn btn-primary">ACEPTAR</button>
		            </div>
		        </div>
		    </div>
		</div>