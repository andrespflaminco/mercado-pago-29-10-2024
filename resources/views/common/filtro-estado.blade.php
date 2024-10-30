                
                <!---------- FILTRO DE ESTADO -------->
			    
			    <div style="padding-left: 0;" class="col-12 ml-0">
				<div  class="input-group mt-2 mb-1">
			    <a class="{{ $estado_filtro == 0 ? 'estado-activo' : 'estado' }}" href="javascript:void(0)" wire:click="Filtro(0)">Activos</a> | <a class="{{ $estado_filtro == 1 ? 'estado-activo' : 'estado' }}" href="javascript:void(0)" wire:click="Filtro(1)">Papelera</a>    
			    </div>	
			    </div>
			    
			    <!----------------------------------->