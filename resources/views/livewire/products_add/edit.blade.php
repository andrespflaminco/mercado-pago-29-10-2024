
<div class="row sales layout-top-spacing">


	@include('livewire.products_price.form-prices')

	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>{{$name_product->name}} | Precios por sucursal </b>
				</h4>
				<ul class="tabs tab-pills">
                        @can('product_create')
					<li>
						<a href="javascript:void(0)" class="tabmenu bg-dark" data-toggle="modal" data-target="#theModalPrices">Agregar</a>

					</li>
                        @endcan
				</ul>
			</div>
			<div class="row justify-content-between">

					<div class="col-lg-3 col-md-3 col-sm-3">

						<div class="input-group mb-4">
							<div class="input-group-prepend">
								<span class="input-group-text input-gp">
									<i class="fas fa-search"></i>
								</span>
							</div>
							<input type="text" autocomplete="off" wire:model="search" placeholder="Buscar" class="form-control"
							>
						</div>

					</div>



			</div>
			<div class="widget-content">

				<div class="table-responsive">
					<table  id="default-ordering" class="table table-hover">
						<thead class="text-white" style="background: #3B3F5C; vertical-align: middle !important;">
							<tr>

								<th style="background: #3B3F5C;   vertical-align: middle !important; padding:5px !important;" class="table-th text-white text-center">
									SUCURSAL
								</th>
								<th style="background: #3B3F5C;   vertical-align: middle !important; padding:5px !important;" class="table-th text-white text-center">
									PRECIO
								</th>
								<th style="background: #3B3F5C;   vertical-align: middle !important; padding:5px !important;" class="table-th text-white text-center">
									STOCK
								</th>

								<th style="background: #3B3F5C; vertical-align: middle !important; font-size: 12px;" class="table-th text-white text-center">MANEJA STOCK?</th>
								<th style="background: #3B3F5C; vertical-align: middle !important; font-size: 12px;" class="table-th text-white text-center">ALMACEN</th>
								<th style="width:20%; vertical-align: middle !important; font-size: 12px;" class="table-th text-white text-center">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($data as $product)
							<tr>
								<td>
									<h6 class="text-center">{{$product->name_sucursal}}</h6>
								</td>

								<td style="margin: 0 auto;" class="text-center">
									<div style=" width: 55%;  margin: 0 auto;" class="input-group text-center">
									  <div class="input-group-prepend">
									    <span class="input-group-text">$</span>
									  </div>
									  	<input type="text" class="form-control"  value="{{$product->price}}">
									</div>

								</td>
								<td style="margin: 0 auto;" class="text-center">
									<div style=" width: 55%;  margin: 0 auto;">
										<input type="text" class="form-control" value="{{$product->stock}}">
									</div>
								</td>

									<td class="text-center">
											<span class="badge {{ $product->stock_descubierto == 'si' ? 'badge-success' : 'badge-danger' }} text-uppercase">{{ $product->stock_descubierto == 'si' ? 'Activo' : 'Inactivo' }}</span>
									</td>
									<td>
										<h6 class="text-center">{{$product->almacen}}</h6>
									</td>


								<td class="text-center">
									@can('product_update')
									<a  wire:click.prevent="Edit({{$product->id}})" class="btn btn-dark mtmobile" title="Edit">
										<i class="fas fa-edit"></i>
									</a>

									@endcan
									@can('product_destroy')
									<a href="javascript:void(0)" onclick="Confirm('{{$product->id}}')" class="btn btn-dark" title="Delete">
										<i class="fas fa-trash"></i>
										</a>

										@endcan
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>

			</div>


		</div>


	</div>

</div>


<script>
	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('product-added', msg => {
			$('#theModalPrices').modal('hide')
			noty(msg)
		});

		window.livewire.on('show-modal-prices', msg => {
			$('#theModalPrices').modal('show')
		});

		window.livewire.on('category-added', msg => {
			$('#Categoria').modal('hide')
			$('#theModal').modal('show')
			noty(msg)
		});

		window.livewire.on('almacen-added', msg => {
			$('#Almacen').modal('hide')
			$('#theModal').modal('show')
			noty(msg)
		});


		window.livewire.on('product-updated', msg => {
			$('#theModal').modal('hide')

			noty(msg)
		});
		window.livewire.on('product-deleted', msg => {
			// noty
		});
		window.livewire.on('modal-show', msg => {
			$('#theModal').modal('show')
		});

		window.livewire.on('mostrar-precios', msg => {
			$('#theModal').modal('show')
		});

		window.livewire.on('modal-categoria-show', msg => {
			$('#Categoria').modal('show')
			$('#theModal').modal('hide')
		});

		window.livewire.on('modal-almacen-show', msg => {
			$('#Almacen').modal('show')
			$('#theModal').modal('hide')
		});

		window.livewire.on('modal-hide', msg => {
			$('#theModal').modal('hide')
		});
		window.livewire.on('hidden.bs.modal', msg => {
			$('.er').css('display', 'none')
		});
		$('#theModal').on('hidden.bs.modal', function(e) {
			$('.er').css('display', 'none')
		})
		$('#theModal').on('shown.bs.modal', function(e) {
			$('.product-name').focus()
		})



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

	function ConfirmCheck(id) {

		swal({
			title: 'CONFIRMAR',
			text: '¿CONFIRMAS ELIMINAR LOS REGISTROS?',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if (result.value) {
				window.livewire.emit('ConfirmCheck', id)
				swal.close()
			}

		})
	}
</script>
<script>
		$('#default-ordering').DataTable( {
				"stripeClasses": [],
				drawCallback: function () { $('.dataTables_paginate > .pagination').addClass(' pagination-style-13 pagination-bordered mb-5'); }
	} );
</script>
<script type="text/javascript">

function getEditar(item)
{

    var id =item.value;

		var x = document.getElementById("id"+id);
		var y = document.getElementById("id2-"+id);

		if (x.style.display === "block") {
			x.style.display = "none";
			y.style.display = "block";
		} else {
			x.style.display = "block";
			y.style.display = "none";
		}


}

function getCerrarEditar(item)
{

    var id =item.value;

		var y = document.getElementById("id"+id);
		var x = document.getElementById("id2-"+id);

		if (x.style.display === "block") {
			x.style.display = "none";
			y.style.display = "flex";
		} else {
			x.style.display = "flex";
			y.style.display = "none";
		}


}

//
</script>
<script type="text/javascript">

function getEditarPrice(item)
{

    var id =item.value;

		var a = document.getElementById("idprice"+id);
		var b = document.getElementById("idprice2-"+id);

		if (a.style.display === "block") {
			a.style.display = "none";
			b.style.display = "block";
		} else {
			a.style.display = "block";
			b.style.display = "none";
		}


}

function getCerrarEditarPrice(item)
{

    var id =item.value;

		var b = document.getElementById("idprice"+id);
		var a = document.getElementById("idprice2-"+id);

		if (a.style.display === "block") {
			a.style.display = "none";
			b.style.display = "flex";
		} else {
			a.style.display = "flex";
			b.style.display = "none";
		}


}

//
</script>
<script type="text/javascript">
document.getElementById("file-input").onchange = function(e) {
// Creamos el objeto de la clase FileReader
let reader = new FileReader();

// Leemos el archivo subido y se lo pasamos a nuestro fileReader
reader.readAsDataURL(e.target.files[0]);

// Le decimos que cuando este listo ejecute el código interno
reader.onload = function(){
	let preview = document.getElementById('image-upload'),
					image = document.createElement('img');

	image.src = reader.result;

	preview.innerHTML = '';
	preview.append(image);
};
}
</script>
