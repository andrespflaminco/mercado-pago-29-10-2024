
<div class="row sales layout-top-spacing">

	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>{{$componentName}} | {{$pageTitle}}</b>
				</h4>
				<ul class="tabs tab-pills">
					<li>
						<button hidden href="javascript:void(0)" style="font-size: 14px;  letter-spacing: 1px;  font-weight: 600;
                        color: #fff; border-radius: 4px;" onclick="ConfirmCheck('1')" class="tabmenu bg-dark mr-3">Eliminar</button>
						<a hidden href="{{ url('report-producto/excel' ) }}" class="tabmenu bg-dark mr-3">Exportar</a>
						<a hidden href="{{url('import')}}" class="tabmenu bg-dark mr-3">Importar</a>

					</li>
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


								<th wire:click="sort('name')" style="background: #3B3F5C;   vertical-align: middle !important; padding:5px !important;" class="table-th text-white">
									<button type="button" style="background:transparent; border:none; color: white;  font-weight: 700; font-size: 12px; letter-spacing: 1px; ">
										NOMBRE
										@if ($sortColumn == 'name')
	                  @if ($sortDirection == 'asc')
	                  &darr;
	                  @else
										&uarr;

	                  @endif
										@else
										&darr;
										&uarr;
	                  @endif
									</button>

								</th>

								<th style="background: #3B3F5C; vertical-align: middle !important; font-size: 12px;" class="table-th text-white text-center">COSTO</th>
								<th style="background: #3B3F5C; vertical-align: middle !important; font-size: 12px;" class="table-th text-white text-center">RECETA</th>
								<th style="width:20%; vertical-align: middle !important; font-size: 12px;" class="table-th text-white text-center">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($data as $product)

							@if($product->producto_tipo == "s")

							<tr>
								<td>
									<h6 class="text-left">
										<b>
											{{$product->name}}
										</b>
									</h6>
								</td>

								<td>
									<h6 class="text-center">$ {{number_format($product->cost,2)}}</h6>
								</td>
									<td class="text-center">
											<span class="badge {{ $product->cost != 0 ? 'badge-success' : 'badge-danger' }} text-uppercase">{{ $product->cost != 0 ? 'Contiene receta' : 'Sin receta' }}</span>
									</td>



								<td class="text-center">

									@if($product->cost != 0 )
									<a href="javascript:void(0)" wire:click.prevent="Edit('{{$product->id}}&0')" class="btn btn-dark" title="Delete">
										<i class="fas fa-eye"></i>
										</a>

										<a href="{{ url('componentes_editar/' . $product->id . '&0')}}" class="btn btn-dark mtmobile" title="Edit">
											<i class="fas fa-edit"></i>
										</a>

										<a href="javascript:void(0)" onclick="Confirm('{{$product->id}}&0')" class="btn btn-dark" title="Delete">
											<i class="fas fa-trash"></i>
											</a>

									@else

									<a href="{{ url('componentes_detalle/' . $product->id . '&0')}}" class="btn btn-dark mtmobile" title="Edit">
										<i class="fa fa-plus"></i>
									</a>

									@endif
									</td>

									</tr>


							@else

									@foreach($productos_variaciones_datos as $pv)

									@if($pv->product_id == $product->id)
									<tr>

									<!--- PRODUCTOS VARIABLES ---->
									<td>
										<h6 class="text-left">
											<b>
											{{$product->name}}
											@foreach($variaciones as $v)

											@if($v->referencia_id == $pv->referencia_variacion)

											- {{$v->nombre_variacion}}

											@endif
											@endforeach
											</b>
										</h6>
									</td>

									<td>
										<h6 class="text-center">$
											@if($product->referencia_variacion == $pv->referencia_variacion)

											{{number_format($product->cost,2)}}

											@else
											0
											@endif

											 </h6>
									</td>
										<td class="text-center">
											@if($product->referencia_variacion == $pv->referencia_variacion)

											<span class="badge badge-success text-uppercase">Contiene receta</span>

											@else
											<span class="badge badge-danger text-uppercase">Sin receta</span>
											@endif
										</td>



									<td class="text-center">

										@if($product->referencia_variacion == $pv->referencia_variacion)

										@if($product->cost != 0 )
										<a href="javascript:void(0)" wire:click.prevent="Edit('{{$product->id}}&{{$pv->referencia_variacion}}')" class="btn btn-dark" title="Delete">
											<i class="fas fa-eye"></i>
											</a>

											<a href="{{ url('componentes_editar/' . $product->id . '&' . $pv->referencia_variacion)}}" class="btn btn-dark mtmobile" title="Edit">
												<i class="fas fa-edit"></i>
											</a>

											<a href="javascript:void(0)" onclick="Confirm('{{$product->id}}&{{$pv->referencia_variacion}}')" class="btn btn-dark" title="Delete">
												<i class="fas fa-trash"></i>
												</a>

										@else

										<a href="{{ url('componentes_detalle/' . $product->id . '&' . $pv->referencia_variacion)}}" class="btn btn-dark mtmobile" title="Edit">
											<i class="fa fa-plus"></i>
										</a>

										@endif
										@else
										<a href="{{ url('componentes_detalle/' . $product->id . '&' . $pv->referencia_variacion)}}" class="btn btn-dark mtmobile" title="Edit">
											<i class="fa fa-plus"></i>
										</a>
										@endif
										</td>



							</tr>
						        	@endif
						        	
							@endforeach
							@endif
							@endforeach
						</tbody>
					</table>
				</div>

			</div>


		</div>


	</div>

	@include('livewire.produccion_recetas.form')
</div>


<script>
	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('product-added', msg => {
			$('#theModal').modal('hide')
			noty(msg)
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
			text: '¿CONFIRMAS ELIMINAR LA RECETA?',
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
