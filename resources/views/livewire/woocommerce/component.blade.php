<div>

<!-- /product list -->
					<div class="card">
						<div class="card-body">
						
<div class="tab-content" id="animateLineContent-4">
    <div class="tab-pane fade show active" id="animated-underline-home" role="tabpanel" aria-labelledby="animated-underline-home-tab">
			<div class="widget-heading">
				<h4 class="card-title">

					<b>	<i style="margin-right:4px;" class="fab fa-wordpress-simple"></i> Ajustes de WooCommerce</b>
				</h4>
			</div>
			<div class="widget-content">
				<div class="row">
					<div class="form-group col-12">
					 <strong>URL de la aplicación de Woocommerce:</strong>
					<input type="text" wire:model.defer="url" class="form-control">

					 @error('url') <span class="text-danger er">{{ $message }}</span> @enderror


					</div>
					<div class="form-group col-6">
					 <strong>Usuario:</strong>
					<input type="text" wire:model.defer="user" class="form-control">

					 @error('user') <span class="text-danger er">{{ $message }}</span> @enderror


					</div>

					<div class="form-group col-6">
					 <strong>Contraseña:</strong>
					<input type="text" wire:model.defer="pass" class="form-control">

					 @error('pass') <span class="text-danger er">{{ $message }}</span> @enderror


					</div>


					<div class="form-group col-6">
					 <strong>Woocommerce Clave del cliente:</strong>
					<input type="text" wire:model.defer="ck" class="form-control">

					 @error('ck') <span class="text-danger er">{{ $message }}</span> @enderror


					</div>

					<div class="form-group col-6">
					 <strong>Woocommerce Clave secreta de cliente:</strong>
					<input type="text" wire:model.defer="cs" class="form-control">

					 @error('cs') <span class="text-danger er">{{ $message }}</span> @enderror


					</div>

					<div class="form-group col-6">
					</div>
					
					
					<div class="form-group col-12">
					@if($valido == false)
					<div class="alert alert-danger" role="alert">
                    CREDENCIALES NO VALIDAS
                    </div>
                    @else
                    <div class="alert alert-success" role="alert">
                    CREDENCIALES CORRECTAS
                    </div>
                    @endif
					</div>

					@if($ecommerce_id != 0)
					<div class="form-group col-12">
					    <button type="button" class="btn btn-danger" wire:click="Delete">DESINCRONIZAR</button>
						<button type="button" class="btn btn-dark" wire:click="Update">ACTUALIZAR</button>
						<button type="button" class="btn btn-success" wire:click="GetWocommerceProductsList('{{$comercio_id}}')">SINCRONIZAR PRODUCTOS</button>
					</div>
					@else
					<div class="form-group col-12">
						<button type="button" class="btn btn-dark" wire:click="Store">GUARDAR</button>
					</div>
					@endif


				</div>


				</div>



			</div>

    </div>
	
        
        

									
        @if(!empty($productos_creados))
        <h5>Productos creados</h5>
	    <div class="table-responsive">
		<table class="table">
	    <th>
	        <tr>Codigo</tr>
	        <tr>Nombre</tr>
	    </th>
	    <tbody>
        @foreach($productos_creados as $pc)
        <tr>
            <td>{{ optional($pc)->barcode }}</td>
            <td>{{ optional($pc)->name }}</td>
        </tr>
        @endforeach
        
	    </tbody>
	</table>    
        </div>
        @endif
		

	</div>
	</div>
	

	


	@include('livewire.woocommerce.importador')
	
	</div>
				
					<!-- /product list -->
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


		window.livewire.on('importador', msg => {
			$('#Importador').modal('show')
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

<script type="text/javascript">
function copyToClipboard(elemento) {
  var $temp = $("<input>")
  $("body").append($temp);
  $temp.val($(elemento).text()).select();
  document.execCommand("copy");
  $temp.remove();
  noty('LINK COPIADO.');
}
</script>
<script type="text/javascript">

 window.onload = function () {
	 var slug = $("#slug").val();
 	$("#slug_guardar").val(slug);

 }

	function SlugGuardar() {
		var slug = $("#slug").val();
		$("#slug_guardar").val(slug);
	}
</script>
