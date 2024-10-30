
						<!---- ENVIO ------>
						<div>	
						<!----- Muestra a todos los planes mayores a 1 ----->
						<h6 style="border-bottom: solid 1px #eee;"><b>Tipo de envio</b></h6>
                        <br>
                        
						<input type="checkbox"  wire:click="RetiroSucursal()" wire:model="check_retiro_sucursal"  {{$checked_retiro_sucursal}}> Retiro por sucursal <br>	
                        <input type="checkbox" wire:click="EnviosCliente()" wire:model="check_envio_cliente" {{$checked_envio_cliente}}> Envio a domicilio del cliente <br>
						<input type="checkbox" wire:click="Envios()" wire:model="check_envio" {{$checked_envio}}> Envio a otro domicilio 
					    <br>
					    
					    @if($estado_pedido == 'Pendiente de Retiro en Sucursal')
                        <div class="row mt-3" style="padding: 25px; border:solid 1px #eee; border-radius:5px; ">
                        <div class="col-5">
                    			<span>Codigo de Retiro</span>
                    			<div class="input-group">
                    			    <input class="form-control" wire:model="codigo_retiro">
                    			</div> 
                    		</div>    
                        </div>
                        @endif
                        
						<!--------------------------------------------------->
						<div class=" row mt-2" style="display:{{$envio_visible}}">
						<div class="col-sm-12 col-md-6">
						<h6 style="border-bottom: solid 1px #eee;"><b>Datos de envio</b></h6>
                        <br>	
                        <label>Nombre de quien recibe</label>
                        <input class="form-control" wire:model.lazy="nombre_envio">
                        <label>Telefono</label>
                        <input class="form-control" wire:model.lazy="telefono_envio">
                        
                        <div class="row">
                        <div class="col-8">
                        <label>Calle</label>
                        <input class="form-control" wire:model.lazy="calle_envio">                              
                        </div>
                        <div class="col-4">
                        <label>Altura</label>
                        <input class="form-control" wire:model.lazy="altura_envio">                              
                        </div>  
                        </div>
                        
                        <div class="row">
                        <div  class="col-4">
                        <label>Depto</label>
                        <input class="form-control" wire:model.lazy="depto_envio">                              
                        </div>
                        <div class="col-4">
                        <label>Piso</label>
                        <input class="form-control" wire:model.lazy="piso_envio">                              
                        </div>
                        <div class="col-4">
                        <label>Cod postal</label>
                        <input class="form-control" wire:model.lazy="cod_postal_envio">                              
                        </div>
                        </div>
                        <label>Ciudad</label>
                        <input class="form-control" wire:model.lazy="ciudad_envio">
                        <label>Provincia</label>
                        <select class="form-control" wire:model.lazy="provincia_envio">
                            <option value="Elegir">Elegir</option>
                                @foreach($provincias as $prov)
                                <option value="{{$prov->id}}">{{$prov->provincia}}</option>
                                @endforeach
                        </select>
    
						</div>
						
						<div class="col-sm-12 col-md-6"></div>
						
                        </div>
                        <br>
                        </div>
                        <!---- /ENVIO ------>