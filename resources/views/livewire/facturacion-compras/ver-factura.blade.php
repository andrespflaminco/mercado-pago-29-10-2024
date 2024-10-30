
<div wire:ignore.self class="modal fade" id="VerFactura" tabindex="-1" role="dialog" aria-labelledby="tabsModalLabel" aria-hidden="true">
  <div style="max-width:1000px;" class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tabsModalLabel">Factura 
        @foreach($detalle_facturas as $detalle_factura)
        {{$detalle_factura->nro_factura}} 
        @endforeach
        </h5>
        <button type="button" class="close" wire:click="CerrarModalResumen" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div style="padding: 2.5rem; width:100% !important;" class="modal-body">
      	<div class="row" style="width:100% !important;">
      	@foreach($detalle_facturas as $detalle_factura)
		   <div class="total-order" style="max-widht:1200px !important;">
			<ul>
			    <li>
				    <h4> Cliente</h4>
				    <h5>{{ $cliente_detalle_factura->nombre}} </h5>
				</li>
				@if($detalle_factura->cuit_comprador != 0)
				<li>
				    <h4> CUIT cliente </h4>
				    <h5> {{ $detalle_factura->cuit_comprador}} </h5>
				</li>
				@endif
				<li>
				    <h4> Subtotal</h4>
				    <h5>$ {{number_format($detalle_factura->subtotal ,2)}} </h5>
				</li>
				<li>
				    <h4> IVA</h4>
				    <h5>$ {{number_format($detalle_factura->iva ,2)}} </h5>
				</li>
				<li class="total">
					<h4>Total</h4>
				    <h5>$ {{number_format($detalle_factura->total ,2)}} </h5>
				</li>
				<li>
				</li>
				<li>
				    <h4> CAE</h4>
				    <h5> {{ $detalle_factura->cae }} </h5>
				</li>
			</ul>
			</div>
		@endforeach
	    </div>
      </div>
      <div class="modal-footer">
          <a href="javascript:void(0);" wire:click="CerrarModalResumen" class="btn btn-cancel" data-dismiss="modal">Cerrar</a>
      </div>
    </div>
  </div>
</div>
