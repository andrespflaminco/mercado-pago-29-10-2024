
<div class="row sales layout-top-spacing">

	<div class="col-sm-12 ">

		<div class="widget widget-chart-one">
			<div class="widget-heading">
					<h4 class="card-title text-center"><b>Resumen de presupuestos</b></h4>

                        @include('livewire.reports.variaciones')
						@include('livewire.presupuesto-resumen.agregar-pago')
						
	                    @include('livewire.presupuesto-resumen.form')

<div style="float: right;">
		 <button type="button" style="margin-right:10px;" class="btn btn-dark" onClick="muestra_oculta('contenido')"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>  FILTROS</button>

						<a  class="btn btn-dark" href="{{ url('presupuesto') }}">

							+	 Nuevo presupuesto </a>


</div>


					</div>





			<div hidden id="contenido" class="card component-card_1">
			<div class="card-body">
				<div class="row">
					<div class="col-sm-12 col-md-4">
					 <div class="form-group">
						<label>ID de presupuesto</label>
						<input type="text" wire:model="search" placeholder="Id de presupuesto" class="form-control"	>
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

							<h5>Presupuestos: $ {{number_format($this->suma_totales,2)}}</h5>


											 </div>
										 </div>

								<div class="col-sm-12 col-md-4">
								<div style="text-align:center !important; vertical-align: middle !important;" class="form-group">

									<h5>Cantidad de productos: {{$this->suma_cantidades}}</h5>


								</div>
								</div>

								<div class="col-sm-12 col-md-4">
								<div style="text-align:center !important; vertical-align: middle !important;" class="form-group">


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
																<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white">ID</th>
														<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">FECHA</th>
														<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">CLIENTE</th>
														<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">MONTO</th>
														<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">CANT. ITEMS</th>
														<th style="background: #3B3F5C; vertical-align: middle !important;" class="table-th text-white text-center">VALIDEZ HASTA</th>
														<th style="width:20%; vertical-align: middle !important;" class="table-th text-white text-center">ACCIONES</th>
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
															<h6 class="text-center">{{$compra->nombre_cliente}}</h6>
														</td>

														<td>
															<h6 class="text-center"> $ {{$compra->total}}</h6>
														</td>
														<td>
															<h6 class="text-center">{{$compra->items}}</h6>
														</td>

														<td>
															<h6 class="text-center">    {{\Carbon\Carbon::parse($compra->created_at)->add($compra->vigencia, 'days')->format('d/m/Y')}}</h6>
														</td>
														<td class="text-center">

															<div class="btn-group mb-1 mr-1" role="group">
																	<button id="btndefault" type="button" class="btn btn-dark btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></button>
																	<div class="dropdown-menu" aria-labelledby="btndefault">
																		<a href="javascript:void(0);" wire:click.prevent="RenderFactura({{$compra->id}})" class="dropdown-item"><i class="flaticon-dots mr-1"></i>  Ver </a>



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
<script>
      
  
  function ConfirmEliminar(id) {

    swal({
      title: 'CONFIRMAR',
      text: '¿QUIERE ELIMINAR EL PRODUCTO?',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('deleteRow', id)
        swal.close()
      }

    })
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
								
								window.livewire.on('cerrar-venta', Msg => {
										$('#theModal-venta').modal('show')
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

function muestra_oculta(id){

if (document.getElementById){
   //se obtiene el id
var el = document.getElementById(id); //se define la variable "el" igual a nuestro div
el.style.display = (el.style.display == 'none') ? 'block' : 'none'; //damos un atributo display:none que oculta el div
}

}
window.onload = function(){
  /*hace que se cargue la funci贸n lo que predetermina que div estar谩 oculto hasta llamar a la
  funci贸n nuevamente*/
muestra_oculta('contenido');/* "contenido_a_mostrar" es el nombre que le dimos al DIV */

}


</script>

@include('livewire.pos.scripts.shortcuts')
@include('livewire.pos.scripts.events')
@include('livewire.pos.scripts.general')
