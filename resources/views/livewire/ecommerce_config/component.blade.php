<div>
	<div class="page-header">
	<div class="page-title">
	<h4>Ecommerce Flaminco</h4>
	<h6>Configura los pagos de tu tienda</h6>
	</div>
	<div class="page-btn">
	</div>
	</div>
	
	@if($ecommerce != null)

    					<!-- /product list -->
					<div class="card">
					    <ul class="nav nav-tabs  mb-3">
					        <li class="nav-item">
            						<a class="nav-link"href="{{ url('ecommerce-ajustes') }}" > AJUSTES  </a>
            				</li>
            				<li class="nav-item">
            						<a class="nav-link active"href="{{ url('ecommerce-config') }}" > PAGOS  </a>
            				</li>
            				<li class="nav-item">
            						<a class="nav-link " href="{{ url('ecommerce-envios') }}" > ENVIOS </a>
            				</li>
            				<li hidden class="nav-item">
            						<a class="nav-link" href="{{ url('ecommerce-cupones') }}" > CUPONES </a>
            				</li>
            			</ul>
					
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
								<div class="wordset">
									<ul>
								
									</ul>
								</div>
							</div>
							<!-- /Filter -->
							<!-- /Filter -->
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th class="text-center">Metodo</th>
											<th>Estado</th>
											<th> </th>
										</tr>
									</thead>
									<tbody>
							<tr>
								<td class="text-center"> <h6>Contado contrareembolso</h6> </td>
								<td class="text-center">
									    <div class="status-toggle d-flex justify-content-between align-items-center">
													<input type="checkbox" id="check1" class="check" @if($ecommerce->efectivo_habilitado) checked @endif >
													<label for="check1" class="checktoggle">checkbox</label>
										</div>
								 </td>
								<td class="text-center"> <button wire:click="Edit('{{$id}}' , 1)" class="btn btn-outline-secondary mb-2">GESTIONAR</button> </td>
							</tr>
							<tr>
								<td class="text-center"> <h6>Transferencia bancaria</h6> </td>
								<td class="text-center">
								     <input type="hidden" value="{{$ecommerce->banco_id}}" id="banco">
								    
								    <div class="status-toggle d-flex justify-content-between align-items-center">
										<input type="checkbox" id="check2" class="check" @if($ecommerce->transferencia_habilitado) checked @endif>
										<label for="check2" class="checktoggle">checkbox</label>
									</div>
										
								</td>
								<td class="text-center"><button wire:click="Edit('{{$id}}' , 2)" class="btn btn-outline-secondary mb-2">GESTIONAR</button> </td>
							</tr>
							<tr>
								<td class="text-center">
									<h6>Mercado pago</h6>

								</td>
								<td class="text-center">
								    <input type="hidden" value="{{$ecommerce->mp_key}}" id="check_mp_key">
								    <input type="hidden" value="{{$ecommerce->mp_token}}" id="check_mp_token">
								    
								    <div class="status-toggle d-flex justify-content-between align-items-center">
										<input type="checkbox" id="check3" class="check" @if($ecommerce->mp_habilitado) checked	 @endif>
										<label for="check3" class="checktoggle">checkbox</label>
									</div>
								
								</td>
								<td class="text-center"> <button wire:click="Edit('{{$id}}' , 3)" class="btn btn-outline-secondary mb-2">GESTIONAR</button> </td>
							</tr>
						</tbody>
								</table>
							</div>
						</div>
					</div>
				
					<!-- /product list -->


@else
<div style="margin:0 auto; padding:80px 50px 80px 50px;" class="col-4">

<div class="form-group">
	<h5>Tienda nueva</h5>
 <div style="margin-bottom: 0 !important;" class="input-group mb-4">
	<input type="text" wire:model.lazy="slug" required class="form-control" placeholder="Ingrese un nombre">
	 <button style="float:right;" type="button" wire:click.prevent="SaveSlug()" class="btn btn-dark close-modal" >GUARDAR</button>


	</div>
	</div>
	<br><br><br><br>
	</div>


@endif

@include('livewire.ecommerce_config.form')
</div>
<script>
	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('gestionar-added', msg => {
			$('#theModal').modal('hide')
		});
		window.livewire.on('gestionar-updated', msg => {
			$('#theModal').modal('hide')
			noty(msg)
		});



		window.livewire.on('gestionar-show', msg => {
			$('#theModal').modal('show')
		});



	});

	function Confirm(id) {

		swal({
			title: 'CONFIRMAR',
			text: '¿CONFIRMAS ELIMINAR EL REGISTRO?',
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

<script type="text/javascript">

var checkbox1 = document.getElementById('check1');
checkbox1.addEventListener("change", validaCheckbox, false);
function validaCheckbox()
{
var checked1 = checkbox1.checked;
if(checked1){
	var estado = '1';
} else {
	var estado = '0';
}

var metodo = 1;

window.livewire.emit('Habilitado',estado,metodo)

}
</script>



<script type="text/javascript">

var checkbox2 = document.getElementById('check2');
var banco = document.getElementById('banco');

checkbox2.addEventListener("change", validaCheckbox, false);
function validaCheckbox()
{
    
if( banco.value != '' && banco.value != null  ) {
    
var checked2 = checkbox2.checked;
if(checked2){
	var estado = '1';
} else {
	var estado = '0';
}
var metodo = 2;

window.livewire.emit('Habilitado',estado,metodo)

} else {
   alert('Debe incluir el Banco. Apriete GESTIONAR')
   checkbox2.checked = false;  
}
}
</script>

<script type="text/javascript">

var checkbox3 = document.getElementById('check3');
var check_mp_key = document.getElementById('check_mp_key');
var check_mp_token = document.getElementById('check_mp_token');

checkbox3.addEventListener("change", validaCheckbox, false);
function validaCheckbox()
{

if( (check_mp_key.value != '' && check_mp_key.value != null) && (check_mp_token.value != '' && check_mp_token.value != null)  ) {
    
var checked3 = checkbox3.checked;
if(checked3){
	var estado = '1';
} else {
	var estado = '0';
}

var metodo = 3;

window.livewire.emit('Habilitado',estado,metodo)
    
} else {
    alert('Debe incluir primero el MP key y el MP token. Apriete GESTIONAR')
    checkbox3.checked = false;
}

}
</script>
