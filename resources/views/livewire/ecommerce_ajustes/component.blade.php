<div>
	<div class="page-header">
	<div class="page-title">
	<h4>Ecommerce Flaminco</h4>
	<h6>Configuracion general de tu tienda</h6>
	</div>
	<div class="page-btn">
	</div>
	</div>
	
	@if($ecommerce != null)

    					<!-- /product list -->
					<div class="card">
					    <ul class="nav nav-tabs  mb-3">
					        <li class="nav-item">
            						<a class="nav-link active"href="{{ url('ecommerce-ajustes') }}" > AJUSTES  </a>
            				</li>
            				<li class="nav-item">
            						<a class="nav-link"href="{{ url('ecommerce-config') }}" > PAGOS  </a>
            				</li>
            				<li class="nav-item">
            						<a class="nav-link " href="{{ url('ecommerce-envios') }}" > ENVIOS </a>
            				</li>
            				<li hidden class="nav-item">
            						<a class="nav-link" href="{{ url('ecommerce-cupones') }}" > CUPONES </a>
            				</li>
            			</ul>
					
						<div class="card-body">
						<div class="row">
						<div class="row">
					<div class="form-group col-6">
					 <label>Slug</label>
					 <div class="input-group mb-4">
						  <div class="input-group-prepend">
						    <span style="height:100% !important;" class="input-group-text" id="basic-addon7">tienda/</span>
						  </div>
						  <input type="text" class="form-control" id="slug" onkeyup="SlugGuardar()" wire:model.defer="slug" >

							<div style="height:100% !important;" class="input-group-append">
						    <button type="button" class="btn btn-dark" wire:click="Update()">Guardar</button>
						  </div>
						</div>
					 @error('slug') <span class="text-danger er">{{ $message }}</span> @enderror


					</div>


					<div class="form-group col-6">
					</div>

					<div class="form-group col-6">
					 <label>Mostrar tienda</label>
					 <select  wire:model="tipo" wire:change='TipoEcommerce($event.target.value)'  class="form-control">
							<option value="1">Forma de cuadricula</option>
							<option value="2">Forma de lista</option>

					</select>

					 

					</div>
					<div class="form-group col-6">
					</div>
					<br>
					<div class="form-group col-6">
					 <label>Forma de comunicacion con el cliente</label>
					 <select  wire:model="comunicacion" wire:change='FormaComunicacion($event.target.value)'  class="form-control">
							
							<option value="1">Email</option>
							<option value="2">Whatsapp</option>

					</select>

					 

					</div>
										<div class="form-group col-6">
					</div>
					<br>
					<div class="form-group col-6">
					 <label>El cliente tiene que registrar un usuario para poder pedir?</label>
					 <select  wire:model="forma_registro" wire:change='FormaRegistro($event.target.value)'  class="form-control">
							
							<option value="1">Si</option>
							<option value="2">No</option>

					</select>

					 

					</div>
					<br>
					
					 <label>Color del texto de la tienda</label>
					 <input style="margin-left: 12px;  width: 60px;" type="color"   wire:model="color" wire:change='CambiarColor($event.target.value)'  class="form-control">
                     @error('color') <span class="text-danger er">{{ $message }}</span> @enderror
                    
                     <label>Color de la tienda</label>
					 <input style="margin-left: 12px;  width: 60px;"  type="color" wire:model="background_color" wire:change='CambiarFondo($event.target.value)'  class="form-control">
                     @error('background_color') <span class="text-danger er">{{ $message }}</span> @enderror


				</div>
				<br>
				<br>
				<br>
					
					<p style="margin-top: 20px; cursor:pointer;" onclick="copyToClipboard('#copy')">


					El link que debera enviar a sus cliente es: https://www.app.flamincoapp.com.ar/tienda/{{$ecommerce->slug}}

					</p>
					<br>

					<div style="margin-left:5px;" class="row">
					<p style="margin-right:15px; cursor:pointer;" onclick="copyToClipboard('#copy')">
					Copiar Link
					<svg style="margin-left:5px;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
					</p>
                    
                    <p>
                	Vista previa
					<a style="margin-left:5px;" href="https://www.app.flamincoapp.com.ar/tienda/{{$ecommerce->slug}}" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>
        
                    </p>
				
						<p hidden id="copy"> https://www.app.flamincoapp.com.ar/tienda/{{$ecommerce->slug}} </p>
				</div>
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
	 <button style="float:right; height:100% !important;" type="button" wire:click.prevent="SaveSlug()" class="btn btn-dark close-modal" >GUARDAR</button>


	</div>
	</div>
	<br><br><br><br>
	</div>


@endif

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
