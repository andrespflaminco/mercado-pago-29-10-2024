<div wire:ignore.self class="modal fade" id="theModalEditarVariacion" tabindex="-1" aria-labelledby="create"  aria-hidden="true">
			<div  class="modal-dialog modal-lg modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						 <h5 class="modal-title" >		<b>{{$nombre_atributo}} </b> | {{ $selected_id > 0 ? 'EDITAR' : 'CREAR' }} </h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
					<div class="row">
                        <div class="col-sm-12">
                        <div class="input-group mb-4">
                        	
                        <input type="text" wire:model.lazy="search_atributo" wire:change="BuscarProductoAtributo({{$id_atributo}})" placeholder="Buscar" class="form-control" autocomplete="off" >
                        
                        <div class="input-group-prepend">
                        	<span style="cursor:pointer; height: 100%;" class="input-group-text input-gp">
                        	<i class="fas fa-search"></i>
                        	</span>
                        	</div>
                        </div>
                        
                        </div>
                        <div class="col-8">
                        
                        </div>
                        <div class="col-4" style="text-align: right !important;">
                        <a href="javascript:void(0)" wire:click="AgregarVariacion2({{$id_atributo}})" class="btn btn-dark mtmobile" title="Agregar Atributos">
                        + Agregar Variacion
                        </a>    
                        
                        </div>
                        
                        
                        <div class="col-6 mt-3">Modificar Nombre de la variacion</div>
                        <div class="col-6 mt-3" style="padding-left:40px;">Modificar Atributo al que esta asociado</div>
                        <div class="col-sm-12 mt-2">
                        
                        @foreach($lista_atributos as $la)
                        
                        <div class="input-group mb-0">
                        <input type="text" wire:model.defer="name_variacion_editar.{{$la->id}}" class="form-control" style="border:none; border-bottom: solid 1px #eee;">
                        <div class="input-group-prepend">
                        @if($la->cantidad == 0)   
                         	<span style="cursor:pointer; height: 100%;" onclick="ConfirmVariacion('{{$la->id}}')" class="input-group-text input-gp">
                        	<i class="fas fa-trash"></i>
                        	</span>
                        @endif
                        
                        @if(0 < $la->cantidad)   
                         	<span style="cursor:pointer; height: 100%;" onclick="TieneProductos('{{$la->id}}')" class="input-group-text input-gp">
                        	<i class="fas fa-trash"></i>
                        	</span>
                        @endif
                        
                        
                        </div>
                        <span style="width: 50px;">
            
                        </span>
                        <select class="form-control" id="a{{$la->id}}" wire:model.defer="atributo_editar.{{$la->id}}">
                            @foreach($atributos as $a)
                            <option value="{{$a->id}}">{{$a->nombre}}</option>
                            @endforeach
                        </select>

                        </div>
                        	
                        <br>
                        
                        @endforeach
                        
                        
                        
                        </div>
                        
                        
                        
                        </div>
						<div class="col-lg-12">
                        	<a class="btn btn-cancel" wire:click.prevent="resetUI()"  data-bs-dismiss="modal">Cancelar</a>
						    <a class="btn btn-submit me-2" wire:click.prevent="UpdateVariacion()"  >Actualizar</a>
						</div>
					</div>
				</div>
			</div>
		</div>

