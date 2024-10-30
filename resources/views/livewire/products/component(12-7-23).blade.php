

<div class="row sales layout-top-spacing">


								
	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<ul class="nav nav-tabs  mb-3">
				<li class="nav-item">
						<a class="nav-link active" href="{{url('products-precios')}}" > CATALOGO  </a>
				</li>
				<li class="nav-item">
						<a class="nav-link" href="{{url('products-precios')}}" > PRECIOS </a>
				</li>
				<li class="nav-item">
						<a class="nav-link" href="{{url('products-stock')}}" > STOCK </a>
				</li>


			</ul>

			<div class="widget-heading">

                         @if(session('status'))
                         <strong style="padding: 5px 5px 5px 5px !important; border-radius: 3px; margin-right: 15px!important; color:#e2a03f !important" >{{ session('status') }}</strong>
                         @endif


				<h4 class="card-title">
					<b>{{$componentName}} | {{$pageTitle}} </b>
				</h4>
				<ul class="tabs tab-pills">
                        @can('product_create')
					<li>
                         <button hidden wire:click="EliminarCatalogo()" style="font-size: 14px; letter-spacing: 1px; font-weight: 600;  color: #fff; border-radius: 4px;" class="tabmenu bg-dark mr-3">Eliminar todos los productos</button>

						<a href="{{url('descargas')}}" class="tabmenu bg-dark mr-3">Ver Exportaciones</a>
						 <button onclick="ExportarCatalogo()" wire:click="ExportarCatalogo()" style="font-size: 14px; letter-spacing: 1px; font-weight: 600;  color: #fff; border-radius: 4px;" class="tabmenu bg-dark mr-3">Generar Exportacion</button>
                            	<a href="{{url('movimiento-stock')}}" class="tabmenu bg-dark mr-3">Mover Stock</a>
						<a href="{{url('import')}}" class="tabmenu bg-dark mr-3 {{ Auth::user()->sucursal == 1 ? 'hide-sucursales' : '' }}">Importar Catalogo</a>

						<a href="javascript:void(0)" class="tabmenu bg-dark {{ Auth::user()->sucursal == 1 ? 'hide-sucursales' : '' }}" wire:click="ModalAgregar()">Agregar Producto</a>

						<button hidden class="tabmenu bg-dark" wire:click="list_wc()">Listar</button>

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

				<!---------- ACCIONES EN LOTE -------->
					<div style="padding-left: 0;" class="col-3 ml-0">
						<div  class="input-group mt-2 mb-1">
							<select style="padding: 6px; border-color: #bfc9d4;" type="text" wire:model.defer="accion_lote" placeholder="Acciones en lote">
								<option value="Elegir">Acciones en lote</option>
								<option value="1">Eliminar</option>

								</select>
							<div class="input-group-append">
								<button style="background:white; border: solid 1px #bfc9d4;" onclick="ConfirmAccionProductos()" type="button">Aplicar</button>
							</div>
						</div>

					</div>

					<!----....................------>

					<div wire:loading wire:target="ElegirVista('2')">
						<div id="load_screen">
						<div class="container-fluid loader-wrap">
								<div class="row h-100">
										<div class="col-10 col-md-6 col-lg-5 col-xl-3 mx-auto text-center align-self-center">
												<div class="logo-wallet">
														<img style="width:65px; margin-top:15px;" src="assets/img/favicon.ico" alt="">
												</div>
												<p class="mt-4"><span class="text-secondary">Cargando stock.</span><br><strong> Por favor espere...</strong></p>
										</div>
								</div>
						</div>
					</div>
					</div>


				<div class="table-responsive">
					<table  id="default-ordering" class="table table-hover">
						<thead class="text-white" style="background: #3B3F5C; vertical-align: middle !important;">
							<tr>
								<th>
                                <input name="Todos" type="checkbox" value="1" onclick="CheckTodosProductos()" class="check_todos"/>    
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

								@if($vista_id == 1)

								<th wire:click="sort('price')" style="background: #3B3F5C;   vertical-align: middle !important; padding:5px !important;" class="table-th text-white text-center">
									<button type="button" style="background:transparent; border:none; color: white;  font-weight: 700; font-size: 12px; letter-spacing: 1px; ">
										PRECIO BASE
										@if ($sortColumn == 'price')
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

							@foreach($lista_precios as $list)
									<th style="background: #3B3F5C;     min-width: 80px; vertical-align: middle !important; padding:5px !important;" class="table-th text-white text-center">
									PRECIO	{{$list->nombre}}
									</th>
							@endforeach

							@endif

							@if($vista_id == 2)


							<th wire:click="sort('stock')" style="background: #3B3F5C;     min-width: 80px; vertical-align: middle !important; padding:5px !important;" class="table-th text-white text-center">
								<button type="button" style="background:transparent; border:none; color: white;  font-weight: 700; font-size: 12px; letter-spacing: 1px; ">
									STOCK CASA CENTRAL

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

						@foreach($sucursales as $suc)

						<th wire:click="sort('stock')" style="background: #3B3F5C;     min-width: 80px; vertical-align: middle !important; padding:5px !important;" class="table-th text-white text-center">
					  STOCK	{{$suc->name}}
						</th>

						@endforeach

						@endif

						@if($vista_id != 1)
								<th wire:click="sort('stock_descubierto')" style="background: #3B3F5C; vertical-align: middle !important; font-size: 12px;" class="table-th text-white text-center">MANEJA STOCK?</th>

						@endif
								<th style="background: #3B3F5C; vertical-align: middle !important; font-size: 12px;" class="table-th text-white text-center">IMAGEN</th>
								<th style="width:20%; vertical-align: middle !important; font-size: 12px;" class="table-th text-white text-center">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($data as $product)
							<tr>
							 <td class="text-center">
							 <input type="checkbox" wire:model.defer="id_check" tu-attr-id="{{($product->id)}}"  class="mis-checkboxes" value="{{$product->id}}">
							 </td>
                                     
							<td>
									<h6 class="text-left">{{$product->name}}
									@if($product->wc_canal == 1)
									<i style="margin-right:4px;" class="fab fa-wordpress-simple"></i>
									@endif
									</h6>
								</td>
								<td>
									<h6 class="text-center">{{$product->barcode}}</h6>
								</td>
								<td>
									<h6 class="text-center">{{$product->category}}</h6>
								</td>

								@if($vista_id == 1)


								<td class="text-center">

									<div style="display:none;" id="idprice{{$product->id}}">
										<div style="min-width: 130px;"  class="input-group mb-4">
											<input type="text" type="number" style="text-align:center;" class="form-control"  value="{{$product->price}}" id="price{{$product->id}}"
											 min="1" >
											<div class="input-group-append">
												<button class="btn-md btn-outline-success" style="padding: 10px;" type="button">	<i class="fas fa-check" wire:click="UpdatePrice({{$product->id}}, $('#price' + {{$product->id}}).val() )"></i></button>
												 <button class="btn-md btn-outline-danger" style="padding: 10px;" value="{{$product->id}}" onclick='getCerrarEditarPrice(this);' type="button">	<i class="fas fa-times"></i></button>
											</div>
										</div>
									</div>




									<div id="idprice2-{{$product->id}}" style="padding-left: 30%;
									min-width: 180px;
							    padding-top: 10%;
							    text-align: center;" class="input-group mb-4">

									@foreach($productos_lista_precios as $pl)

									@if($pl->product_id == $product->id)

									@if($pl->lista_id == 0)

									<h6 class="text-center" style="vertical-align: middle !important; padding: 10px 10px 10px 10px; margin: 0 !important;">

									<b>

									@foreach($productos_variaciones as $pv)
										@if($pl->referencia_variacion == $pv->referencia_id)
										{{$pv->nombre_variacion}}

										@endif
										@endforeach

										</b>
								 $	{{$pl->precio_lista}}
									</h6>


									@endif

									@endif

									@endforeach


										<div class="input-group-append">
											 <button class="boton-editar-products" value="{{$product->id}}" onclick='getEditarPrice(this);' type="button">	<i class="fas fa-edit"></i></button>
										</div>
									</div>

								</td>

								@foreach($lista_precios as $list)
									<td class="text-center">

										@foreach($productos_lista_precios as $pl)

										@if($pl->product_id == $product->id)

										@if($list->id == $pl->lista_id)

										<h6 class="text-center" style="vertical-align: middle !important; padding: 10px 10px 10px 10px; margin: 0 !important;">
											<b>
												@foreach($productos_variaciones as $pv)
												@if($pl->referencia_variacion == $pv->referencia_id)
												{{$pv->nombre_variacion}}
												@endif
												@endforeach

											</b>
									$	{{$pl->precio_lista}}
										</h6>


										@endif

										@endif

										@endforeach

									</td>
								@endforeach

								@endif

								@if($vista_id == 2)

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




									@foreach($stock_sucursales as $pl)


									@if($pl->sucursal_id == 0)

									@if($product->id == $pl->product_id)

									<h6 class="text-center" style="vertical-align: middle !important; padding: 10px 10px 10px 10px; margin: 0 !important;">
									<b>
										@foreach($productos_variaciones as $pv)
										@if($pl->referencia_variacion == $pv->referencia_id)
										{{$pv->nombre_variacion}}
										@endif
										@endforeach


									</b>

									{{$pl->stock}} unid.
									</h6>


									@endif

									@endif

									@endforeach


								</td>



								@foreach($sucursales as $suc)
									<td class="text-center">


										@foreach($stock_sucursales as $pl)


										@if($suc->sucursal_id == $pl->sucursal_id)

										@if($product->id == $pl->product_id)


										<h6 class="text-center" style="vertical-align: middle !important; padding: 10px 10px 10px 10px; margin: 0 !important;">
											<b>
												@foreach($productos_variaciones as $pv)
												@if($pl->referencia_variacion == $pv->referencia_id)
												{{$pv->nombre_variacion}}
												@endif
												@endforeach


											</b>

									 {{$pl->stock}} Unid.
										</h6>


										@endif

										@endif

										@endforeach

									</td>
								@endforeach


								@endif

									@if($vista_id != 1)

									<td class="text-center">
											<span class="badge {{ $product->stock_descubierto == 'si' ? 'badge-success' : 'badge-danger' }} text-uppercase">{{ $product->stock_descubierto == 'si' ? 'Activo' : 'Inactivo' }}</span>
									</td>

									@endif

								<td class="text-center">
									<span>

										<img src="{{ asset('storage/products/' . $product->imagen ) }}" alt="{{$product->name}}" height="70" width="80" class="rounded">

									</span>
								</td>

								<td class="text-center">

									<a href="javascript:void(0)" wire:click.prevent="Edit({{$product->id}})" class="btn btn-dark mtmobile {{ $sucursal_id != $comercio_id ? 'hide-sucursales' : '' }}" title="Edit">
										<i class="fas fa-edit"></i>
									</a>

									@can('product_destroy')
									<a href="javascript:void(0)" onclick="Confirm('{{$product->id}}')" class="btn btn-dark" title="Delete">
										<i class="fas fa-trash"></i>
										</a>

										@endcan
										<button hidden type="button" wire:click.prevent="ScanCode('{{$product->barcode}}')" class="btn btn-dark"><i class="fas fa-shopping-cart"></i></button>
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
@include('livewire.products.exportar-lista')
@include('livewire.products.exportar-stock')
@include('livewire.products.form')
@include('livewire.products.form_cropp')
@include('livewire.products.form-categoria')
@include('livewire.products.form-imagen')
@include('livewire.products.form-almacen')
@include('livewire.products.modal-cambio-sucursal')
</div>

<script>

function ExportarCatalogo() {
    noty("Estamos exportando su catalogo.");
}
    		
</script>

<script>
	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('product-added', msg => {
			$('#theModal').modal('hide')
			noty(msg)
		});

		window.livewire.on('modal-lista-hide', msg => {
			$('#ExportarLista').modal('hide')
		});

		window.livewire.on('modal-stock-hide', msg => {
			$('#ExportarStock').modal('hide')
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
			noty(msg)
		});
		window.livewire.on('modal-show', msg => {
			$('#theModal').modal('show')
		});

		window.livewire.on('modal-categoria-show', msg => {
			$('#Categoria').modal('show')
			$('#theModal').modal('hide')
		});
		
		window.livewire.on('modal-imagen-show', msg => {
			$('#Imagenes').modal('show')
			$('#theModal').modal('hide')
		});

		window.livewire.on('modal-imagen-hide', msg => {
			$('#Imagenes').modal('hide')
			$('#theModal').modal('show')
		});

		window.livewire.on('modal-almacen-show', msg => {
			$('#Almacen').modal('show')
			$('#theModal').modal('hide')
		});

		window.livewire.on('modal-hide', msg => {
			$('#theModal').modal('hide')
		});

		window.livewire.on('modal-cambio-sucursal', msg => {
			$('#ModalCambioSucursal').modal('show')
		});
		
		window.livewire.on('hide-cropp', msg => {
			$('#ModalCroppr').modal('hide')
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


		window.livewire.on('confirm-eliminar', id => {

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




		function ConfirmVariacion(id) {

			swal({
				title: 'CONFIRMAR',
				text: '¿CONFIRMAS ELIMINAR LA VARIACION?',
				type: 'warning',
				showCancelButton: true,
				cancelButtonText: 'Cerrar',
				cancelButtonColor: '#fff',
				confirmButtonColor: '#3B3F5C',
				confirmButtonText: 'Aceptar'
			}).then(function(result) {
				if (result.value) {
					window.livewire.emit('deleteVariacion', id)
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
	let preview = document.getElementById('image-upload'),
					image = document.createElement('img');

	image.src = reader.result;

	preview.innerHTML = '';
	preview.append(image);
};
}
</script>
