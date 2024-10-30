<div wire:ignore.self class="modal fade" id="DescuentoEnLote" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
        	<b>DESCUENTO DEL COSTO @if($es_descuento_individual != 1) EN LOTE @endif </b>
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">


<div class="container mt-4">
    <div class="filtro-container">
        <form>
            @foreach($descuentos_productos as $index => $descuento)
                <div class="mb-3">
                    <label for="palabra{{ $index }}" class="form-label">Descuento {{ $index + 1 }}</label>
                    <input type="text" class="form-control" id="palabra{{ $index }}"
                        wire:model.defer="descuentos_productos.{{ $index }}.descuento"
                        placeholder="Ingrese descuento">
                </div>
            @endforeach
        </form>
    </div>
</div>

<button wire:click="AgregarDescuento" class="btn btn-primary mt-3">+ Agregar otro descuento</button>

        </div>
		 <div class="modal-footer">

           <a wire:click.prevent="resetUIDescuento()" href="javascript:void(0);" class="btn btn-cancel">CERRAR</a>
           @if($es_descuento_individual == 1)
           <a onclick="GuardarDescuento()" href="javascript:void(0);" class="btn btn-submit me-2">GUARDAR</a>
           @else
           <a onclick="GuardarDescuentosEnLotes()" href="javascript:void(0);" class="btn btn-submit me-2">GUARDAR EN LOTE</a>
           @endif

		 </div>
	 </div>
 </div>
</div>
