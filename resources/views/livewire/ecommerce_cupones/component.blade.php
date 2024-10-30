<div class="row sales layout-top-spacing">

	@if($ecommerce != null)
	<div class="col-sm-12">




		<div class="widget widget-chart-one">

			<ul class="nav nav-tabs  mb-3">
				<li class="nav-item">
						<a class="nav-link" href="{{ url('ecommerce-ajustes') }}"> Ajustes </a>
				</li>
    <li class="nav-item">
        <a class="nav-link " href="{{ url('ecommerce-config') }}" > Pago</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ url('ecommerce-envios') }}"> Envios </a>
    </li>
		<li class="nav-item">
				<a class="nav-link active" href="{{ url('ecommerce-cupones') }}"> Cupones </a>
		</li>
</ul>

<div class="tab-content" id="animateLineContent-4">
    <div class="tab-pane fade show active" id="animated-underline-home" role="tabpanel" aria-labelledby="animated-underline-home-tab">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>Cupones de descuento</b>
				</h4>
				<ul class="tabs tab-pills">
					 	<a hidden href="javascript:void(0)" class="tabmenu bg-dark" data-toggle="modal" data-target="#theModal">Agregar</a>
				</ul>
			</div>
			<div class="widget-content">

				<div class="table-responsive">
					<table class="table table-bordered table striped mt-1">
						<thead class="text-white" style="background: #3B3F5C;">
							<tr>
								<th class="table-th text-white text-center">CODIGO DEL CUPON</th>
								<th class="table-th text-white text-center">DESCUENTO</th>
								<th class="table-th text-white text-center"></th>
							</tr>
						</thead>
						<tbody>
							@foreach($cupones as $c)
							<tr>
								<td class="text-center"> <h6>{{$c->cupon}}</h6> </td>
								<td class="text-center">
									 <h6>{{$c->descuento}} %</h6>
								 </td>
								<td class="text-center"> <button wire:click="Edit('{{$c->id}}')" class="btn btn-outline-secondary mb-2">GESTIONAR</button> </td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>

			</div>

    </div>



		</div>


	</div>
</div>

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

@include('livewire.ecommerce_cupones.form')
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
checkbox2.addEventListener("change", validaCheckbox, false);
function validaCheckbox()
{
var checked2 = checkbox2.checked;
if(checked2){
	var estado = '1';
} else {
	var estado = '0';
}
var metodo = 2;

window.livewire.emit('Habilitado',estado,metodo)
}
</script>

<script type="text/javascript">

var checkbox3 = document.getElementById('check3');
checkbox3.addEventListener("change", validaCheckbox, false);
function validaCheckbox()
{
var checked3 = checkbox3.checked;
if(checked3){
	var estado = '1';
} else {
	var estado = '0';
}

var metodo = 3;

window.livewire.emit('Habilitado',estado,metodo)
}
</script>
