
<div class="row sales layout-top-spacing">

	<div class="col-sm-12 ">

		<div class="widget widget-chart-one">
			<div class="widget-heading">
					<h4 class="card-title text-center"><b>Resumen de compras</b></h4>


		@include('livewire.compras-resumen.agregar-pago')
		@include('livewire.gastos.estado-pedido-pos')
	    @include('livewire.compras-resumen.form')
	    @include('livewire.reports.variaciones')

<div style="float: right;">
         <button  class="btn btn-dark  {{count($data) <1 ? 'disabled' : '' }}"
              wire:click="ExportarReporte('{{ ( ($search == '' ? '0' : $search) . '/' . ($proveedor_elegido == '' ? '0' : $proveedor_elegido)  .  '/' . ($estado_pago == '' ? '0' : ($estado_pago == 'Pago' ? '1' : '2')) . '/'  . $dateFrom . '/' . $dateTo) }}')">Exportar a Excel</button>
          
          
		 <button type="button" style="margin-right:10px;" class="btn btn-dark" onClick="muestra_oculta('contenido')"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>  FILTROS</button>

						<a  class="btn btn-dark" href="{{ url('compras-elegir') }}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>

								 Nueva compra </a>


</div>


					</div>





			<div id="contenido" class="card component-card_1">
			<div class="card-body">
				<div class="row">
					<div class="col-sm-12 col-md-4">
					 <div class="form-group">
						<label>ID de compra</label>
						<input type="text" wire:model="search" placeholder="Id de compra" class="form-control"	>
					</div>
					</div>


					<div class="col-sm-12 col-md-4">
					 <div class="form-group">
						<label>Proveedor</label>
						<select wire:model='proveedor_elegido' class="form-control">
							<option value="Elegir" disabled >Elegir</option>
							<option value="0" >Todos</option>
							@foreach($prov as $pr)
							<option value="{{$pr->id}}">{{$pr->nombre}}</option>
							@endforeach
						</select>
					</div>
					</div>


					<div class="col-sm-12 col-md-4">
					 <div class="form-group">
						<label>Estado de pago</label>

								<select wire:model="estado_pago" class="form-control">
									<option value="">Todos</option>
									<option value="Pendiente">Pendiente</option>
									<option value="Pago">Pagos</option>

							</select>
					</div>
					</div>


					<div class="col-sm-12 col-md-4">
					 <div class="form-group">
						<label>Fecha desde</label>
						<input type="date" wire:model="dateFrom" class="form-control">

					</div>
					</div>

					<div class="col-sm-12 col-md-4">
					 <div class="form-group">
						<label>Fecha hasta</label>
						<input type="date" wire:model="dateTo" class="form-control">

					</div>
					</div>

			 </div>

			 </div>

			 </div>

			 <div class="card component-card_1">
			 <div class="card-body">
				 <div class="row">
					 <div class="col-sm-12 col-md-4">
						<div style="text-align:center !important; vertical-align: middle !important;" class="form-group">

							<h5>Compras: $ {{number_format($this->suma_totales,2)}}</h5>


											 </div>
										 </div>

								<div class="col-sm-12 col-md-4">
								<div style="text-align:center !important; vertical-align: middle !important;" class="form-group">

									<h5>Pagos: $ {{number_format($this->suma_totales-$this->suma_deuda,2)}}</h5>


								</div>
								</div>

								<div class="col-sm-12 col-md-4">
								<div style="text-align:center !important; vertical-align: middle !important;" class="form-group">

									<h5>Deuda: $ {{number_format($this->suma_deuda,2)}}</h5>


								</div>
								</div>
	 </div>
		 </div>
		 </div>

			<div style="margin-top:2%;" class="widget-content">

									<div class="connect-sorting-content">
										<div class="table-responsive">
											<table  id="default-ordering" class="table table-hover">
												<thead class="text-white" style="background: #3B3F5C; vertical-align: middle !important;">
													<tr>
																<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white">ID DE COMPRA</th>
														<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">FECHA</th>
														<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">PROVEEDOR</th>
														<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">SUBTOTAL</th>
														<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">IVA</th>
														<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">TOTAL</th>
														<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">PAGOS</th>
														<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">DEUDA</th>
														<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">FACTURA</th>
														<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">ESTADO</th>
													
														<th style="width:10%; vertical-align: middle !important;" class="table-th text-white text-center">ACCIONES</th>
													</tr>
												</thead>
												<tbody>
													@foreach($data as $compra)
													<tr>

														<td>
															<h6 class="text-left">{{$compra->id}}</h6>
														</td>
														<td>
															<h6 class="text-center"> {{\Carbon\Carbon::parse($compra->created_at)->format('d-m-Y')}}</h6>
														</td>
	                                                    	<td>
															<h6 class="text-center">{{$compra->nombre_proveedor}}</h6>
														</td>
														<td>
															<h6 class="text-center"> $ {{$compra->subtotal}}</h6>
														</td>
														<td>
															<h6 class="text-center"> $ {{$compra->iva}}</h6>
														</td>
														<td>
															<h6 class="text-center"> $ {{$compra->total}}</h6>
														</td>
													    <td>
															<h6 class="text-center"> $ {{number_format($compra->total-$compra->deuda,2)}}</h6>
														</td>
														<td>
															<h6 class="text-center"> $ {{$compra->deuda}}</h6>
														</td>
														<td>
															<h6 class="text-center"> 
															@if($compra->numero_factura != null)
															{{$compra->tipo_factura}} - {{$compra->numero_factura}}
															@else
															-
															@endif
															</h6>
														</td>
													
														
														<td>
														    <h6 class="text-center">
															  @if($compra->status == 1)
                                                              <span style="   min-width: 130px; margin-bottom: 0 !important;  margin-top: -2px !important;  margin-right: 15px;  padding: 3px !important;"
                                                                class="btn btn-warning mb-2">
                                                                Pendiente
                                                            </span>
                                                            @endif
                                                            @if($compra->status == 2)
                                                              <span style="    min-width: 130px; margin-bottom: 0 !important;  margin-top: -2px !important;  margin-right: 15px;  padding: 3px !important;" 
                                                                class="btn btn-success mb-2">
                                                                En proceso
                                                            </span>
                                                            @endif
                                                            @if($compra->status == 4)
                                                              <span style="    min-width: 130px; margin-bottom: 0 !important;  margin-top: -2px !important;  margin-right: 15px;  padding: 3px !important;"
                                                                class="btn btn-danger mb-2">
                                                                Cancelado
                                                            </span>
                                                            @endif
                                                            @if($compra->status == 3)
                                                              <span style="    min-width: 130px; margin-bottom: 0 !important;  margin-top: -2px !important;  margin-right: 15px;  padding: 3px !important;"
                                                                class="btn btn-secondary mb-2">
                                                                Entregado
                                                            </span>
                                                            @endif
                                                            </h6>
														</td>
														<td class="text-center">

															<div class="btn-group mb-1 mr-1" role="group">
																<button id="btndefault" type="button" class="btn btn-dark btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></button>
																<div class="dropdown-menu" aria-labelledby="btndefault">
																<a href="javascript:void(0);" wire:click.prevent="RenderFactura({{$compra->id}})" class="dropdown-item"><i class="flaticon-dots mr-1"></i>  Ver </a>
                                                              @if($compra->sale_casa_central == null)
                                                                <a href="javascript:void(0);" onclick="ConfirmEliminar({{$compra->id}})" class="dropdown-item"><i class="flaticon-dots mr-1"></i>  Eliminar </a>
                                                              @endif
															</div>
															</div>
														</td>
													</tr>
													@endforeach
												</tbody>
											</table>
											{{$data->links()}}
										</div>

										
								</div>
						</div>
				</div>

				</a>


	</div>
</div>




<script src="{{ asset('js/keypress.js') }}"></script>
<script src="{{ asset('js/onscan.js') }}"></script>
<script>

try{

    onScan.attachTo(document, {
    suffixKeyCodes: [13],
    onScan: function(barcode) {
        console.log(barcode)
        window.livewire.emit('scan-code', barcode)
    },
    onScanError: function(e){
        //console.log(e)
    }
})

    console.log('Scanner ready!')


} catch(e){
    console.log('Error de lectura: ', e)
}


</script>
<script>
document.addEventListener('DOMContentLoaded', function(){

            livewire.on('scan-code', action => {
                $('#code').val('')
            })
            
            
						window.livewire.on('sale-ok', Msg => {
								$('#theModal2').modal('hide')
								noty(Msg)
								
								
						})
						
								window.livewire.on('variacion-elegir', Msg => {
                        			$('#Variaciones').modal('show')
                        		})
                        
                        		window.livewire.on('variacion-elegir-hide', Msg => {
                        			$('#Variaciones').modal('hide')
                        		})
                        		

								window.livewire.on('agregar-cliente', Msg => {
										$('#theModal-cliente').modal('show')
								})

								window.livewire.on('agregar-pago', Msg =>{
										$('#AgregarPago').modal('show')
								})



								window.livewire.on('agregar-pago-hide', Msg =>{
										$('#AgregarPago').modal('hide')
								})

								window.livewire.on('pago-dividido', Msg =>{
										$('#PagoDividido').modal('show')
								})

								window.livewire.on('pago-dividido-hide', Msg =>{
										$('#PagoDividido').modal('hide')
								})


								window.livewire.on('hide-modal2', Msg =>{
										$('#modalDetails2').modal('hide')
								})

								window.livewire.on('cerrar-factura', Msg =>{
										$('#theModal1').modal('hide')
								})

								window.livewire.on('modal-show', msg => {
									$('#theModal1').modal('show')
								})


								window.livewire.on('abrir-hr-nueva', msg => {
									$('#theModal').modal('show')
								})

								window.livewire.on('hide-modal3', Msg =>{
										$('#modalDetails3').modal('hide')
								})


								window.livewire.on('modal-hr-hide', Msg =>{
										$('#theModal').modal('hide')
								})

								window.livewire.on('hr-added', Msg => {
									noty(Msg)
								})

								window.livewire.on('modal-estado', Msg =>{
										$('#modalDetails-estado-pedido').modal('show')
								})

								window.livewire.on('modal-estado-hide', Msg =>{
										$('#modalDetails-estado-pedido').modal('hide')
								})

								window.livewire.on('hr-asignada', Msg => {
									noty(Msg)
								})

								window.livewire.on('pago-agregado', Msg => {
									noty(Msg)
								})

								window.livewire.on('pago-actualizado', Msg => {
									noty(Msg)
								})

								window.livewire.on('pago-eliminado', Msg => {
									noty(Msg)
								})
								//events
								window.livewire.on('product-added', Msg => {
									$('#theModal').modal('hide')
									noty(Msg)
								})

								window.livewire.on('no-stock', Msg => {
									noty(Msg, 2)
								})

								//eventos
								window.livewire.on('show-modal', Msg =>{
										$('#modalDetails').modal('show')
								})

								var total = $('#suma_totales').val();
								$('#ver_totales').html('Ventas: '+total);


    });

</script>

<script type="text/javascript">

    function ConfirmEliminar(id) {

    swal({
      title: 'CONFIRMAR',
      text: 'QUIERE ELIMINAR LA COMPRA?',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('EliminarCompra', id)
        swal.close()
      }

    })
  }
  
function muestra_oculta(id){

if (document.getElementById){
   //se obtiene el id
var el = document.getElementById(id); //se define la variable "el" igual a nuestro div
el.style.display = (el.style.display == 'none') ? 'block' : 'none'; //damos un atributo display:none que oculta el div
}

}
window.onload = function(){
  /*hace que se cargue la función lo que predetermina que div estará oculto hasta llamar a la
  función nuevamente*/
muestra_oculta('contenido');/* "contenido_a_mostrar" es el nombre que le dimos al DIV */

}


</script>

@include('livewire.pos.scripts.shortcuts')
@include('livewire.pos.scripts.events')
@include('livewire.pos.scripts.general')
