<head>
  <style media="screen">
  @media (min-width: 576px) {
.modal-body {
  margin: 0 auto !important;
}
}
  </style>
</head>
<div wire:ignore.self style="z-index:99999999999999999 !important;" class="modal fade" id="CondicionIva" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Condicion de facturacion</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div class="modal-body">
              <h6>Por favor elija la condicion de facturacion</h6>
              <br><br>
              @if($venta_form != null)
              @if($datos_facturacion_elegidos != null )
              @if($datos_facturacion_elegidos->condicion_iva == "IVA Responsable inscripto")
              	<div class="col-lg-12 col-sm-12 col-12">
					<div class="form-group">
						<label>Relacion PRECIO - IVA</label>
							<select wire:model="relacion_precio_iva_form" class="form-control">
							<option value="0">Sin IVA</option>
							<option value="1">Precio + IVA</option>
							<option value="2">IVA incluido en el precio</option>
							</select>
				    	</div>
					</div>
					
					
													
					<div class="col-lg-12 col-sm-12 col-12">
						<div class="form-group">
						<label>IVA</label>
							<select wire:model="alicuota_iva_form" class="form-control">
							<option value="0">Sin IVA</option>
							<option value="0.105">10.5%</option>
							<option value="0.21">21%</option>
							<option value="0.27">27%</option>
							</select>
						</div>
					</div>
					
					@if($datos_facturacion_elegidos->condicion_iva == "IVA Responsable inscripto" && $venta_form->cliente_id != 1) 
					
					<div class="col-lg-12 col-sm-12 col-12">
						<div class="form-group">
						<label>Tipo de comprobante</label>
							<select wire:model="tipo_comprobante" class="form-control">
							<option value="B">B</option>
							<option value="A">A</option>
							</select>
						</div>
					</div>
					
					@endif
					
					
								
              @endif
              @endif
              
              
              @if($datos_facturacion_elegidos->condicion_iva == "Monotributo")
              <h6>¿Confirma facturar como Monotributo?</h6>
              @endif
              
              </div>
                  <div class="modal-footer">
                    <button type="button" wire:click.prevent="resetUICondicionIVA()" class="btn btn-cancel" data-dismiss="modal">CERRAR</button>
                   
                    @if($datos_facturacion_elegidos != null )
                    @if($datos_facturacion_elegidos->condicion_iva == "IVA Responsable inscripto")
                          <button type="button" wire:click.prevent="RecalcularIVA()" class="btn btn-submit" >FACTURAR</button>
                    @endif
                    @endif
                    
                 @endif   


                 </div>
          </div>
      </div>
  </div>
