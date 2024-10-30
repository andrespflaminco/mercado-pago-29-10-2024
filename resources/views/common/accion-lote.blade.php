	<div class="row justify-content-between">						                
	<!---------- ACCIONES EN LOTE -------->
	<div class="col-10">
	@can('accion en lote productos')  
	<div style="padding-left: 0;" class="col-12 ml-0">
						<div  class="input-group mt-2 mb-1">
							<select style="padding: 0px 6px; border-color: #bfc9d4;" type="text" wire:model.defer="accion_lote" placeholder="Acciones en lote">
								<option value="Elegir">Acciones en lote</option>
								
								@if($estado_filtro == 0)
								<option value="1">Eliminar</option>
								@endif
								@if($estado_filtro == 1)
								<option value="0">Restaurar</option>
								@endif

								</select>
							<div class="input-group-append">
								<button style="background:white; border: solid 1px #bfc9d4;" onclick="ConfirmAccionEnLote()" type="button">Aplicar</button>
							</div>
						</div>

					</div>
	@endcan				
	<input value="{{$estado_filtro}}" id="id_accion" type="hidden">    
	</div>
	

	<!------------------------------------>

    <!---------- FILTRO DE ESTADO -------->
	
	@can('eliminar productos')   
	<div id="accion-lote" class="col-2 mt-2 ">
	<div>
	<div style="padding-left: 0;" class="col-12 ml-0">
	<div  class="input-group">
	<a class="{{ $estado_filtro == 0 ? 'estado-activo' : 'estado' }}" href="javascript:void(0)" wire:click="Filtro(0)">Activos</a> | <a class="{{ $estado_filtro == 1 ? 'estado-activo' : 'estado' }}" href="javascript:void(0)" wire:click="Filtro(1)">Papelera</a>    
	</div>	
	</div>	    
	</div>    		    

	</div>
	@endcan
			    
	<!----------------------------------->
	
            
			    

					
	</div>