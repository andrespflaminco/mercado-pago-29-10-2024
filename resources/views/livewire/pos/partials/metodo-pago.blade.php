
						<div class="row">    
					    
                        <h6 style="border-bottom: solid 1px #eee;"><b>Forma de cobro</b></h6>
                        <br>
                        
                        <!-------------- FORMA DE PAGO --------------------->
					    <div class="col-sm-12 col-md-6">
                        
                        <div class="row mt-3">
                            <div class="col-6">
                                
                                @if($es_pago_dividido == 1)
                                
                                <button  wire:click="SwitchFormaCobro({{$es_pago_dividido}},0,1)"  style="width:100%; border:solid 1px #c8c8c8;" class="btn text-center">Pago  <br>  total</button>
                                @else
                                @if($pago_parcial == 0)
                                <button  wire:click="SwitchFormaCobro({{$es_pago_dividido}},0,1)"  style="background:grey; color: white; width:100%; border:solid 1px #c8c8c8;" class="btn text-center">Pago <br>  total</button>
                                @else
                                <button  wire:click="SwitchFormaCobro({{$es_pago_dividido}},0,1)"  style="width:100%; border:solid 1px #c8c8c8;" class="btn text-center">Pago  <br> total</button>
                                @endif
                                @endif
                                
                            </div>
                            <!----- 
                            <div hidden class="col-4">
                               @if($es_pago_dividido == 1)
                                <button  wire:click="SwitchFormaCobro({{$es_pago_dividido}},0,0)"  style=" width:100%; border:solid 1px #c8c8c8;" class="btn text-center">Pago parcial</button>
                                @else
                                @if($pago_parcial == 1)
                                <button  wire:click="SwitchFormaCobro({{$es_pago_dividido}},0,0)"  style="background:grey; color: white; width:100%; border:solid 1px #c8c8c8;" class="btn text-center">Pago parcial</button>
                                @else
                                <button  wire:click="SwitchFormaCobro({{$es_pago_dividido}},0,0)"  style=" width:100%; border:solid 1px #c8c8c8;" class="btn text-center">Pago parcial</button>
                                @endif
                                @endif
                            
                            
                                @if($es_pago_dividido == 0)
                                <button style="width:100%; border:solid 1px #c8c8c8;" <button  wire:click="SwitchFormaCobro({{$es_pago_dividido}},1,null)"  class="btn text-center">Pago parcial</button>
                                @else
                                <button style="width:100%; background:grey; color: white; border:solid 1px #c8c8c8;" <button  wire:click="SwitchFormaCobro({{$es_pago_dividido}},1,null)"  class="btn text-center">Pago parcial</button>
                                @endif                                
                            </div>
                            ----->
                            
                            <div class="col-6">
                                @if($es_pago_dividido == 0)
                                <button style="width:100%; border:solid 1px #c8c8c8;" <button  wire:click="SwitchFormaCobro({{$es_pago_dividido}},1,null)"  class="btn text-center">Pago <br> Cuenta Corriente</button>
                                @else
                                <button style="width:100%; background:grey; color: white; border:solid 1px #c8c8c8;" <button  wire:click="SwitchFormaCobro({{$es_pago_dividido}},1,null)"  class="btn text-center">Pago <br> Cuenta Corriente </button>
                                @endif
                            </div>
                            
                        </div>   
                        <br>
                        <div>
                            
                        </div>
                            
					    </div>
					    
					    <!-------------- /FORMA DE COBRO --------------------->
					    
					    <div class="col-sm-12 col-md-6">
					    </div>
    
                        <!-------------- METODO DE COBRO --------------------->
					    <div style="{{$style_metodo_pago}}" class="col-sm-12 col-md-6">

						<h6 style="border-bottom: solid 1px #eee;"><b>Metodo de cobro</b></h6>
						
                        <br>
						<label>Tipo de cobro</label>
                    	<div class="input-group mb-0">
            			<select style="font-size: 14px;" wire:model='tipo_pago' wire:change='TipoPago($event.target.value)'  class="form-control">
							<option value="1">Efectivo</option>
							<option hidden value="3">Cheque</option>
							<!----- Muestra a todos los planes mayores a 1 ----->
							@if(2 < Auth::user()->plan)
							<option hidden value="2">Pago dividido</option>
							@endif
						    <!-------------------------------------------------->
							@foreach($tipos_pago as $tipos)
							<option value="{{$tipos->id}}">{{$tipos->nombre}}</option>
							@endforeach
							
							 @if(Auth::user()->profile != "Cajero" )
							<option value="OTRO" class="btn btn-dark">Agregar otro banco/plataforma</option>
							@endif

						</select>
						<div hidden class="input-group-append">
            				<span style="height: 100% !important;" class="input-group-text input-gp">
            					<i class="fas fa-plus"></i>
            				</span>
            			</div>
						
						</div>

						@if($tipo_pago !=2 && $tipo_pago !=3)
    
						<label>Forma de cobro</label>

						<select style="font-size: 14px;" wire:model='metodo_pago' 	wire:change='MetodoPago($event.target.value)' class="form-control">
							<option disabled value="Elegir">Elegir</option>
						    @if($tipo_pago == 1)
						    <option  value="1">Efectivo</option>
						    @endif
							@foreach($metodos as $metodo_pago)
							<option value="{{$metodo_pago->id}}">{{$metodo_pago->nombre}}</option>
							@endforeach
							<option hidden  value="1">Efectivo</option>
						   	<option hidden value="2">Pago dividido</option>
						   	 @if(Auth::user()->profile != "Cajero" )
							<option value="OTRO" class="btn btn-dark" >Agregar otro medio de pago</option>
							@endif
						</select>
						@endif
	                    <br>
	                    </div>
	                    
	                    <!-------------- /METODO DE COBRO --------------------->
                        
                        <!--------------  MONTO A COBRAR --------------------->
                        
	                    <div style="{{$style_metodo_pago}}" class="col-sm-12 col-md-6">

						<h6 style="border-bottom: solid 1px #eee;"><b>Monto a pagar</b></h6>
						<br>
						<div class="form-group mb-0">
						@if($pago_parcial == 0)
						    <label style="margin-bottom:0;">Ingresa el monto con el que va a pagar tu cliente</label>
						@endif
						@if($pago_parcial == 1)
						
						
						    <label style="margin-bottom:0;">Ingresa el monto que quiere cubrir tu cliente @if($relacion_precio_iva == 2) (IVA incluido) @endif</label>
						@endif
						    <div class="input-group input-group-md mb-1">
							<input min="0" type="number" id="cash"
								onchange="cambio();"
								onkeyup="cambio();"
								wire:model="efectivo"
								wire:change='cambio($event.target.value)'
								wire:keyup.debounce.1000ms='cambio($event.target.value)'
								class="form-control text-left"
								style="font-size: 14px;"
								>
						</div>
						</div>
						
						@if($pago_parcial == 1)
						
						@if($relacion_precio_iva == 1)
						<div class="form-group mb-0">
						    <label  style="margin-bottom:0;">Recargo</label>
						    <div class="input-group input-group-md mb-1">
							<input readonly style="font-size: 14px;" readonly class="form-control text-left" value="{{number_format($recargo_total,2)}}">
						    </div>
						</div>
						
						<div class="form-group mb-0">
						    <label  style="margin-bottom:0;">IVA pago parcial</label>
						    <div class="input-group input-group-md mb-1">
							<input readonly style="font-size: 14px;" readonly class="form-control text-left" value="{{number_format($sum_iva_pago+$sum_iva_recargo,2)}}">
						    </div>
						</div>
						@endif
						
						@if($relacion_precio_iva == 0)
						<div class="form-group mb-0">
						    <label  style="margin-bottom:0;">Recargo</label>
						    <div class="input-group input-group-md mb-1">
							<input readonly style="font-size: 14px;" readonly class="form-control text-left" value="{{number_format($recargo_total,2)}}">
						    </div>
						</div>
						@endif
						
						@if($relacion_precio_iva == 2)
						<div class="form-group mb-0">
						    <label  style="margin-bottom:0;">Recargo (IVA incluido)</label>
						    <div class="input-group input-group-md mb-1">
							<input readonly style="font-size: 14px;" readonly class="form-control text-left" value="{{number_format($recargo_total+$iva_recargo,2)}}">
						    </div>
						</div>

						@endif
						
												
						<div class="form-group mb-0">
						    <label  style="margin-bottom:0;">Monto a cobrar</label>
						    <div class="input-group input-group-md mb-1">
							<input style="font-size: 14px;" class="form-control text-left" 								
							wire:model="a_cobrar_parcial"
							wire:change='cambioACobrar($event.target.value)'
							wire:keyup='cambioACobrar($event.target.value)'>
							
						</div>
						</div>
						@endif

						@if($pago_parcial == 0)
						
						
						<div class="form-group mb-0">
						@if( 0 < ($change) )
						    <label  style="margin-bottom:0;">Falta para cubrir</label>
						@else
						    <label  style="margin-bottom:0;">Vuelto a entregar</label>
						@endif
						
						    <div class="input-group input-group-md mb-1">
							<input readonly style="font-size: 14px;" class="form-control text-left" value="{{number_format(-1*$change,2)}}">
						    </div>
						</div>
						@endif
						
						@if($pago_parcial == 1)
						<div class="form-group mb-0">
						    <label  style="margin-bottom:0;">Deuda</label>
						    <div class="input-group input-group-md mb-1">
							<input readonly style="font-size: 14px;" class="form-control text-left" value="{{number_format($change,2)}}">
						    </div>
						</div>
						@endif
                    	
                    	
                    	<button hidden wire:click.prevent="ACash(0)" class="btn btn-dark btn-block den">
						Pago exacto
						</button>
						
						
						<!-------------- /MONTO A  PAGAR --------------------->
						
						</div>
						
						<!-------------- / MONTO A COBRAR --------------------->
						
						<!---- @include('livewire.pos.pago-dividido') --->
						 
						 
						
						 @include('livewire.pos.pago-dividido-dinamico')
						 
						 <!--- 9-1-2024 ---->
						 @include('livewire.pos.partials.metodo-pago-sucursal')
						<!------------------>
						
						
						</div>