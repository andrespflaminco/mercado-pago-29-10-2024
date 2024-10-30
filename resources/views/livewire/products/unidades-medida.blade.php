<div class="row">
    <div class="col-lg-3 col-sm-6 col-12">
			<div class="form-group">
			<label>Tipo de unidad de medida </label>
			<select wire:model="tipo_unidad_medida" {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} class="form-control" >
			    <option value="9"> Unidad </option>
			    <option value="1"> Kilogramo (PESABLE) </option>
			    @foreach($unidades_de_medida as $um)
		<!----	    <option value="{{$um->id}}"> {{$um->nombre_completo}} ({{$um->nombre_tipo_unidad_medida}}) </option> --->
			    @endforeach
			</select>
			</div>
		</div>

								
    <div {{$tipo_producto != 1 ? 'hidden' : '' }} class="col-lg-3 col-sm-6 col-12">
			<div class="form-group">
			<label>Cantidad del contenido</label>
			<input type="number" wire:model="cantidad_unidad_medida" {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} class="form-control" >
			</div>
	</div>
	
	
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

		<div class="col-lg-3 col-sm-6 col-12">
		<div class="form-group mb-0" style="margin-top: 40px !important;">
		<label> </label>
		<input {{ $es_sucursal == 1? 'disabled' : '' }} {{ $forma_edit == 1? 'disabled' : '' }} type="checkbox" wire:model="es_insumo"> Es insumo?
        </div>
	    </div>
	    
		
</div>		
