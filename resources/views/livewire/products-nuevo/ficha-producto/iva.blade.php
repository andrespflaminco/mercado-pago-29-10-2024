    <label>IVA</label>
   <div class="row" style="margin: 0; border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px 3px 2px 14px;">
                                
	 @if(Auth::user()->profile != "Cajero" )
		<div class="col-lg-3 col-sm-6 col-12">
			<div class="form-group">
			<label>IVA venta</label>
			<select wire:model="porcentaje_iva.{{auth()->user()->id}}" {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} class="form-control" >
			    <option value="0">Sin IVA</option>
			    <option value="0.105">10,5%</option>
			    <option value="0.210">21%</option>
			    <option value="0.270">27%</option>
			</select>
			</div>
		</div>

     @endif
     
@foreach ($sucursales as $llave => $sucu)

	 @if(Auth::user()->profile != "Cajero" )
		<div class="col-lg-3 col-sm-6 col-12">
			<div class="form-group">
			<label>IVA venta {{$sucu->name}}</label>
			<select wire:model.defer="porcentaje_iva.{{ $sucu->sucursal_id }}" {{ $es_sucursal == 1? 'readonly' : '' }} {{ $forma_edit == 1? 'readonly' : '' }} class="form-control" >
			    <option value="0">Sin IVA</option>
			    <option value="0.105">10,5%</option>
			    <option value="0.210">21%</option>
			    <option value="0.270">27%</option>
			</select>
			</div>
		</div>

     @endif
@endforeach

</div>
<br>

