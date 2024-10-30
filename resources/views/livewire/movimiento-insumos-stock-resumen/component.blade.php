<div>
    
    @if($ver == 0)
					
	                <div class="page-header">
					<div class="page-title">
							<h4>Resumen de movimiento de insumos</h4>
							<h6>Ver listado de movimientos de stock de insumos</h6>
						</div>
						<div class="page-btn">               											    
                			
                			@if(Auth::user()->sucursal != 1)
                			@if(Auth::user()->profile != "Cajero" )
							<a href="{{ url('movimiento-insumos-stock')}}" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Nuevo movimiento</a>
						    @endif
						    @endif
						    
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
				
						<div class="card-body">
							<div class="table-top">
								<div class="search-set">
									<div class="search-path">
										<a class="btn btn-filter" id="filter_search">
											<img src="{{ asset('assets/pos/img/icons/filter.svg') }}"  alt="img">
											<span><img src="{{ asset('assets/pos/img/icons/closes.svg') }}" alt="img"></span>
										</a>
									</div>
									<input type="text" autocomplete="off" wire:model="search" placeholder="Buscar.." class="form-control"	>
									<div hidden class="search-input">
										<a class="btn btn-searchset"><img src="{{ asset('assets/pos/img/icons/search-white.svg') }}" alt="img"></a>
									</div>
								</div>
								<div hidden class="wordset">
									<ul>
										<li>
											<a data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img  src="{{ asset('assets/pos/img/icons/pdf.svg') }}"  alt="img"></a>
										</li>
										<li>
											<a data-bs-toggle="tooltip" data-bs-placement="top" title="excel"><img  src="{{ asset('assets/pos/img/icons/excel.svg') }}" alt="img"></a>
										</li>
										<li>
											<a data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img  src="{{ asset('assets/pos/img/icons/printer.svg') }}" alt="img"></a>
										</li>
									</ul>
								</div>
							</div>
							                									
							 
							<div class="table-responsive">
								<table class="table">
									<thead>
											<th>NRO MOVIMIENTO</th>
											<th >ORIGEN</th>
											<th >DESTINO</th>
                                			<th >FECHA</th>
											<th >MONTO</th>
											<th >CANT. ITEMS</th>
											<th >ACCIONES</th>
											</tr>
												</thead>
									<tbody>
									   	@foreach($data as $m)
													<tr>

														<td>
														{{$m->nro_movimiento}}
														</td>
														<td>@foreach($sucursales as $s)

																@if($s->id == $m->sucursal_origen)

																{{$s->name}}

																@endif

																@endforeach

														</td>
														<td>
																@foreach($sucursales as $s)
																@if($s->id == $m->sucursal_destino)

																{{$s->name}}

																@endif

																@endforeach
														</td>
														<td>
															 {{\Carbon\Carbon::parse($m->created_at)->format('d-m-Y')}}
														</td>

														<td>
															 $ {{$m->total}}
														</td>
														<td>
															{{$m->items}}
														</td>
														<td class="text-center">
														    <a class="me-3" href="javascript:void(0)" wire:click.prevent="RenderFactura({{$m->id}})">
            													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}" alt="img">
            												</a>
            											</td>
													</tr>
													@endforeach
									</tbody>
								</table>
								{{$data->links()}}
							</div>
						</div>
					</div>
					@endif
					
					@if($ver == 1)
					@include('livewire.movimiento-insumos-stock-resumen.ver-movimiento')
					@endif
					<!-- /product list -->
    
</div>



<script>
					    
	function ConfirmEliminarProductoPedido(id) {

		swal({
			title: 'CONFIRMAR',
			text: 'CONFIRMAS ELIMINAR EL REGISTRO?',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
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
