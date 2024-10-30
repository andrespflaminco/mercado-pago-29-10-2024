
                        @if($cliente != null)
                        
                        @if($cliente->sucursal_id != null)
                        <!-------------- METODO DE PAGO --------------------->
					    <div style="{{$style_metodo_pago}}" class="col-sm-12 col-md-6">

						<h6 style="border-bottom: solid 1px #eee;"><b>Metodo con el que paga la sucursal</b></h6>
						
                        <br>
						<label>Tipo de pago</label>
                    	<div class="input-group mb-0">
            			<select style="font-size: 14px;" wire:model='tipo_pago_sucursal' class="form-control">
							<option value="1">Efectivo</option>
							<!----- Muestra a todos los planes mayores a 1 ----->
							@if(2 < Auth::user()->plan)
							@endif
						    <!-------------------------------------------------->
							@foreach($bancos_sucursal as $bancos_suc)
							<option value="{{$bancos_suc->id}}">{{$bancos_suc->nombre}}</option>
							@endforeach
							
						</select>
						<div hidden class="input-group-append">
            				<span style="height: 100% !important;" class="input-group-text input-gp">
            					<i class="fas fa-plus"></i>
            				</span>
            			</div>
						
						</div>
                        <br>
	                    </div>
	                    
	                    <!-------------- /METODO DE PAGO --------------------->
	                    @endif
	                    
	                    @endif