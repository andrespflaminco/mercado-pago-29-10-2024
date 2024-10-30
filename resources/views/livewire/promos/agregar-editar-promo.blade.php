@inject('cart', 'App\Services\CartPromociones')
					<!-- /add -->
					<div class="card">
						<div class="card-body">
							<div class="row">
								@if($tipo_promo == null)
								<div class="col-lg-12 col-sm-12 col-12">
									<div class="form-group">
										<label>Tipo de promocion</label>
										<select wire:model="tipo_promo" class="form-control">
										<option value="" selected>Elegir</option>
										<option value="1">Descuento por cantidad del mismo producto</option>
										<option value="2">Combinacion de productos</option>
										</select>
									</div>
								</div>
								@else
								
								<div class="col-lg-12 col-sm-12 col-12">
									<div class="form-group">
										<label>Nombre de la promocion</label>
										<input type="text" wire:model.defer="nombre_promo" class="form-control" maxlength="255">
										@error('nombre_promo') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
                                
                                @if($tipo_promo == 1)
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Unidad a la que se le aplica el descuento</label>
										<input type="number" wire:model.defer="cantidad" class="form-control" maxlength="255">
										@error('cantidad') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Porcentaje de descuento (%)</label>
										<div class="input-group mb-0">
										<input type="number" wire:model.defer="descuento" class="form-control" maxlength="255">
										<div class="input-group-prepend">
            							<span style="height: 100% !important;" class="input-group-text input-gp">
            								%
            							</span>
            						</div>    
										</div>
										@error('descuento') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
					            @endif
					           
					           @if($tipo_promo == 2)
					            <div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Precio de la promocion</label>
										<input type="number" wire:model="precio_promo_combinada" id="precio_promo" class="form-control" maxlength="255">
										@error('precio_promo_combinada') <span class="text-danger er">{{ $message }}</span> @enderror
									</div>
								</div>
								@endif
				
					            
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Vigencia desde</label>
										<input type="date" {{$limitar_vigencia == 0 ? 'disabled' : '' }} wire:model.defer="vigencia_desde"  class="form-control">  
										<p class="mt-2"><input type="checkbox" {{$limitar_vigencia == 1 ? 'checked' : '' }} wire:model="limitar_vigencia" >   Limitar vigencia de la promocion</p>
										
									</div>	
								</div>
								
								<div class="col-lg-3 col-sm-6 col-12">
									<div class="form-group">
										<label>Vigencia hasta</label>
										<input type="date" {{$limitar_vigencia == 0 ? 'disabled' : '' }} wire:model.defer="vigencia_hasta" class="form-control">   
									</div>
								</div>


										   
								

								<div class="col-lg-12 col-sm-12 col-12">
									<div wire:ignore class="form-group">
									    @if($selected_id < 1)
										<label>Producto al que se le aplica la promocion</label>
										
                                        <select id="miSelect" style="height:200px !important;">
                                            <option value="" disabled selected>Elegir</option>
                                            @foreach($productos as $producto)
                                            <option value="{{$producto->id }}">{{ $producto->barcode }} - {{ $producto->name }}</option>
                                            @endforeach
                                        </select>
                                        @else
                                        <label>Agregar producto</label>
										
                                        <select id="miSelect" style="height:200px !important;">
                                            <option value="" disabled selected>Elegir</option>
                                            @foreach($productos as $producto)
                                                <option value="{{$producto->id }}">{{ $producto->barcode }} - {{ $producto->name }}</option>
                                            @endforeach
                                        </select>
                                        
                                        @endif

									</div>
								</div>
								
								<div class="col-lg-12 col-sm-12 col-12">
								@if ($cart->getContent()->count() > 0)
								<table class="table mb-4">
								    <thead>
								        <tr>
								            <th>Producto</th>
								            @if($tipo_promo == 2)
								            <th>Cantidad</th>
								            @endif
								            @if($tipo_promo != 2)
								            @if(0 < $selected_id)
								            <th>Habilitado</th>
								            @endif
								            @endif
								            <th></th>
								        </tr>
								    </thead>
								    <tbody>
								     	@foreach ($cart->getContent()->sortByDesc('barcode') as $ps)
								        
								        @if($ps['eliminado'] != 1)
								        
								        <tr>
								            <td> {{$ps['barcode']}} - {{$ps['name']}} @if($ps['variacion'] != 0) {{$ps['variacion']}} @endif</td>
								            
								            @if($tipo_promo == 2)
								            <td>
								                <input class="form-control" type="number" value="{{$ps['cantidad']}}" id="q{{$ps['id']}}" wire:change="UpdateQuantity({{$ps['id']}},'{{$ps['referencia_variacion']}}', $('#q' + {{$ps['id']}}).val() )">
								            </td>
								            @endif
								            
								            @if(0 < $selected_id)
								            <td>
								               
								             <div class="status-toggle d-flex justify-content-between align-items-center">
                                                <input 
                                                    type="checkbox" 
                                                    id="check{{$ps['id']}}" 
                                                    class="check" 
                                                    {{ $ps['activo'] == 1 ? 'checked' : '' }} 
                                                    wire:click="ToggleHabilitar({{$ps['id_promos_productos']}})"
                                                >
                                                
                                                <label for="check{{$ps['id']}}" class="checktoggle">checkbox</label>
                                            </div>
								            </td>
								            @endif
								            <td>
								            <a href="javascript:void(0);" wire:click="QuitarProducto({{$ps['id']}},'{{$ps['referencia_variacion']}}')"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
								            </a>
								            </td>
								        </tr>
								        @endif
								        
								        @endforeach
								    </tbody>
								</table>
								@endif
								</div>
								
								<div class="col-lg-12">
								      @if($selected_id < 1)
								      <a href="javascript:void(0);" wire:click.prevent="Store()" class="btn btn-submit me-2">GUARDAR</a>
                                       @else
                                       <a wire:click.prevent="Update()" href="javascript:void(0);" class="btn btn-submit me-2">ACTUALIZAR</a>
                                       @endif
                                       <a wire:click.prevent="resetUI()" href="javascript:void(0);" class="btn btn-cancel">CANCELAR</a>
									
									
								</div>
							
							@endif
							</div>
						</div>
					</div>
					<!-- /add -->
					
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.hook('message.processed', function () {
            // Inicializar el select2
            $('#miSelect').select2();

            // Configurar un evento change en el select
            $('#miSelect').on('change', function() {

                @this.emit('Seleccionados', $('#miSelect').select2('val'));
            });
            
            // Escuchar el evento de Livewire para vaciar el select2
            Livewire.on('nuevoProductoAgregado', function () {
                $('#miSelect').val('').trigger('change');
            });
            
        })
    });
    
        $(document).ready(function() {
            // Inicializar el select2
            $('#miSelect').select2();

            // Configurar un evento change en el select
            $('#miSelect').on('change', function() {

             @this.emit('Seleccionados', $('#miSelect').select2('val'));

            });
        });
    </script>
    
    
<script type="text/javascript">

var checkbox = document.getElementById('check');

checkbox.addEventListener("change", validaCheckbox, false);

function validaCheckbox()
{
    
var checked = checkbox.checked;
var valorCheckbox = checkbox.value;

if(checked){
	var estado = '1';
} else {
	var estado = '0';
}
alert(estado,valorCheckbox);

window.livewire.emit('Habilitado',estado,valorCheckbox)

}
</script>
