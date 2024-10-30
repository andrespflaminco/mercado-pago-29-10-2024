<div class="row">
    <div class="col-lg-3 col-sm-6 col-12">
			<div class="form-group">
			<label>Tipo de unidad de medida	@if($es_insumo == true) del insumo @endif </label>
			<select wire:model="tipo_unidad_medida" {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} class="form-control" >
			    <option value="9"> Unidad </option>
			    <option value="1"> Kilogramo (PESABLE) </option>
			    @foreach($unidades_de_medida as $um)
		<!----	    <option value="{{$um->id}}"> {{$um->nombre_completo}} ({{$um->nombre_tipo_unidad_medida}}) </option> --->
			    @endforeach
			</select>
			</div>
		</div>

	@if($es_insumo == true)
    <div {{$tipo_producto != 1 ? 'hidden' : '' }} class="col-lg-3 col-sm-6 col-12">
			<div class="form-group">
			<label>Contenido del insumo</label>
			<input type="number" wire:model="cantidad_unidad_medida" {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} class="form-control" >
			</div>
	</div>
	@endif
	
	
		<div class="col-lg-3 col-sm-6 col-12">
		<div class="form-group">
		<label>Origen</label>
		<select class="form-control" wire:model="tipo_producto"  {{ $es_sucursal == 1? 'disabled' : '' }} {{ $forma_edit == 1? 'disabled' : '' }} wire:change="ProductoTipo()">
         <option value="1">Compra</option>
         <option value="2">Produccion</option>
         <option value="3">Ensamblaje al momento de la venta</option>
        </select>
        @error('producto_tipo') <span class="text-danger err">{{ $message }}</span> @enderror
        @if ($mostrarErrorTipoProducto)
        <span class="text-danger err">Debe elegir el tipo de producto</span>
        @endif
		</div>
	    </div>
        
        @if($es_insumo == true)
		<div class="col-lg-3 col-sm-6 col-12">
		<div class="form-group mb-0" style="margin-top: 40px !important;">
		<label> </label>
		<input hidden {{ $es_sucursal == 1? 'disabled' : '' }} {{ $forma_edit == 1? 'disabled' : '' }} type="checkbox" wire:model="es_insumo"> 
		<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
		Es insumo?
        </div>
	    </div>
	    @endif
	    
		
</div>		
