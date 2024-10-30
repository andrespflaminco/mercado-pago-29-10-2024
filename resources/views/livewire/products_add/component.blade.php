
<div class="row sales layout-top-spacing">

	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<ul class="tabs tab-pills">
					<li>
						<button wire:click="SeleccionarSucursal('0')" class="tabmenu bg-dark mr-3">Lista Base</button>
						@foreach($sucursales as $suc)
						<button wire:click="SeleccionarSucursal('{{$suc->id}}')" class="tabmenu bg-dark mr-3">{{$suc->name}}</button>
						@endforeach
					</li>
				</ul>
			</div>

			<div class="widget-heading">
				<h4 class="card-title">
					<b>{{$componentName}} | {{$pageTitle}}</b>
				</h4>
				<ul class="tabs tab-pills">
                        @can('product_create')
					<li>
						<button href="javascript:void(0)" style="font-size: 14px;  letter-spacing: 1px;  font-weight: 600;
                        color: #fff; border-radius: 4px;" onclick="ConfirmCheck('1')" class="tabmenu bg-dark mr-3">Eliminar</button>
						<a href="{{ url('report-producto/excel' ) }}" class="tabmenu bg-dark mr-3">Exportar</a>
						<a href="{{url('import')}}" class="tabmenu bg-dark mr-3">Importar</a>
						<a href="{{url('products-add')}}" class="tabmenu bg-dark" >Agregar</a>

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

						<div class="col-lg-3 col-md-3 col-sm-3">

									<div class="input-group mb-4">
										<div class="input-group-prepend">
											<span class="input-group-text input-gp">
												<i class="fas fa-list"></i>
											</span>
										</div>
										<select wire:model='id_categoria' class="form-control">
											<option value="Elegir" disabled >Elegir</option>
											<option value="0" >Todos</option>
											@foreach ($categories as $cat)
											<option value="{{$cat->id}}" >{{$cat->name}}</option>

											@endforeach
										</select>

									</div>

								</div>

				<div class="col-lg-3 col-md-3 col-sm-3">

					<div class="input-group mb-4">
						<div class="input-group-prepend">
							<span class="input-group-text input-gp">
								<i class="fas fa-home"></i>
							</span>
						</div>
						<select wire:model='id_almacen' class="form-control">
							<option value="Elegir" disabled >Elegir</option>
							<option value="0" >Todos</option>
							@foreach ($almacenes as $al)
							<option value="{{$al->id}}" >{{$al->nombre}}</option>

							@endforeach
						</select>

					</div>

				</div>

				<div class="col-lg-3 col-md-3 col-sm-3">

					<div class="input-group mb-4">
						<div class="input-group-prepend">
							<span class="input-group-text input-gp">
								<i class="fas fa-users"></i>
							</span>
						</div>
						<select wire:model='proveedor_elegido' class="form-control">
							<option value="Elegir" disabled >Elegir</option>
							<option value="0" >Todos</option>
							@foreach($prov as $pr)
							<option value="{{$pr->id}}">{{$pr->nombre}}</option>
							@endforeach
						</select>

					</div>

				</div>

			</div>
			<div class="widget-content">

				<div class="table-responsive">
					<table  id="default-ordering" class="table table-hover">
						<thead class="text-white" style="background: #3B3F5C; vertical-align: middle !important;">
							<tr>
								<th class="table-th text-white">
									<div class="n-chk">
	    						<label class="new-control new-checkbox new-checkbox-text checkbox-dark">
										<input type="checkbox" class="new-control-input" wire:model="selectedAll" class="form-checkbox" name="selectedAll">

	      					<span style="border: solid 1px #939495 !important;"  class="new-control-indicator"></span><span class="new-chk-content">.</span>
	    						</label>
									</div>


								  </th>


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

								<th wire:click="sort('barcode')" style="background: #3B3F5C;     min-width: 140px; vertical-align: middle !important; padding:5px !important;" class="table-th text-white text-center">
									<button type="button" style="background:transparent; border:none; color: white;  font-weight: 700; font-size: 12px; letter-spacing: 1px; ">
										BARCODE
										@if ($sortColumn == 'barcode')
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

									<th wire:click="sort('category_id')" style="background: #3B3F5C;  vertical-align: middle !important; padding:5px 0px 5px 0px !important;" class="table-th text-white text-center">
										<button type="button" style="background:transparent; border:none; color: white; min-width: 110px; font-weight: 700; font-size: 12px; letter-spacing: 1px; ">
											CATEGORIA
											@if ($sortColumn == 'category_id')
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

							<th wire:click="sort('stock')" style="background: #3B3F5C;     min-width: 80px; vertical-align: middle !important; padding:5px !important;" class="table-th text-white text-center">
								<button type="button" style="background:transparent; border:none; color: white;  font-weight: 700; font-size: 12px; letter-spacing: 1px; ">
									STOCK
									@if ($sortColumn == 'stock')
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

								<th wire:click="sort('proveedor_id')" style="background: #3B3F5C; vertical-align: middle !important; font-size: 12px;" class="table-th text-white text-center">PROVEEDOR</th>
								<th style="background: #3B3F5C; vertical-align: middle !important; font-size: 12px;" class="table-th text-white text-center">IMAGEN</th>
								<th style="width:20%; vertical-align: middle !important; font-size: 12px;" class="table-th text-white text-center">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($data as $product)
							<tr>
								<td>
									<div class="n-chk">
	    						<label class="new-control new-checkbox new-checkbox-text checkbox-dark">
	      					<input type="checkbox" class="new-control-input" wire:model="SelectedProducts" value="{{$product->id}}">
	      					<span class="new-control-indicator"></span><span class="new-chk-content">.</span>
	    						</label>
									</div>
								</td>
								<td>
									<h6 class="text-left">{{$product->name}}</h6>
								</td>
								<td>
									<h6 class="text-center">{{$product->barcode}}</h6>
								</td>
								<td>
									<h6 class="text-center">{{$product->category}}</h6>
								</td>

								<td class="text-center">

									<div style="display:none;" id="id{{$product->id}}">
										<div style="min-width: 130px;"  class="input-group mb-4">
											<input type="text" type="number" style="text-align:center;" class="form-control"  value="{{$product->stock}}" id="qty{{$product->id}}"
											 min="1" >
											<div class="input-group-append">
												<button class="btn-md btn-outline-success" style="padding: 10px;" type="button">	<i class="fas fa-check" wire:click="UpdateQty({{$product->id}}, $('#qty' + {{$product->id}}).val() )"></i></button>
												 <button class="btn-md btn-outline-danger" style="padding: 10px;" value="{{$product->id}}" onclick='getCerrarEditar(this);' type="button">	<i class="fas fa-times"></i></button>
											</div>
										</div>
									</div>




									<div id="id2-{{$product->id}}" style="padding-left: 30%;
									min-width: 130px;
							    padding-top: 10%;
							    text-align: center;" class="input-group mb-4">
										<h6 class="text-center {{$product->stock <= $product->alerts ? 'text-danger' : '' }} " style="vertical-align: middle !important; padding: 10px 10px 10px 10px; margin: 0 !important;">
											{{$product->stock}}
										</h6>
										<div class="input-group-append">
											 <button class="boton-editar-products" value="{{$product->id}}" onclick='getEditar(this);' type="button">	<i class="fas fa-edit"></i></button>
										</div>
									</div>

								</td>

									<td>
										<h6 class="text-center">{{$product->nombre_proveedor}}</h6>
									</td>

								<td class="text-center">
									<span>

										<img src="{{ asset('storage/products/' . $product->imagen ) }}" alt="{{$product->name}}" height="70" width="80" class="rounded">

									</span>
								</td>

								<td class="text-center">
									@can('product_update')
									<a  href="{{url('product-added/'. $product->id)}}" class="btn btn-dark mtmobile" title="Edit">
										<i class="fas fa-home"></i>
									</a>

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
					{{$data->links()}}
				</div>

			</div>


		</div>


	</div>

	@include('livewire.products_add.form')
	@include('livewire.products_add.form-categoria')
	@include('livewire.products_add.form-almacen')
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
