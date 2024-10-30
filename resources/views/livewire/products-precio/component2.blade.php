

<div class="row sales layout-top-spacing">


								
	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<ul class="nav nav-tabs  mb-3">
				<li class="nav-item">
						<a class="nav-link" href="{{url('products')}}"  > CATALOGO  </a>
				</li>
				<li class="nav-item">
						<a class="nav-link active" href="{{url('products-precios')}}" > PRECIOS </a>
				</li>
				<li class="nav-item">
						<a class="nav-link" href="{{url('products-stock')}}" > STOCK </a>
				</li>


			</ul>

			<div class="widget-heading">
				<h4 class="card-title">
					<b>{{$componentName}} | {{$pageTitle}}</b>
				</h4>
				
				@if((new \Jenssegers\Agent\Agent())->isDesktop() || (new \Jenssegers\Agent\Agent())->isTablet()  )
				
				<ul class="tabs tab-pills {{ $sucursal_id != $comercio_id ? 'hide-sucursales' : '' }}">
                        @can('product_create')
					<li>
						  @if($wc_yes != 0)
						<a hidden href="javascript:void(0)" class="tabmenu bg-dark mr-3" wire:click="wc_sincronizar()">
							<i style="margin-right:4px;" class="fab fa-wordpress-simple"></i>
							Sincronizar
						</a>
						@endif

						<a href="javascript:void(0)" data-toggle="modal" data-target="#ExportarLista" class="tabmenu bg-dark mr-3">Exportar Precios</a>
						<a href="{{url('import-precios')}}" class="tabmenu bg-dark mr-3 {{ Auth::user()->sucursal == 1 ? 'hide-sucursales' : '' }}">Importar Precios</a>
						
						<a href="javascript:void(0)" class="tabmenu bg-dark {{ Auth::user()->sucursal == 1 ? 'hide-sucursales' : '' }}" wire:click="ModalAgregar()">Agregar Producto</a>

						<button hidden class="tabmenu bg-dark" wire:click="list_wc()">Listar</button>

					</li>
                        @endcan
				</ul>
				
				@endif
				
				@if((new \Jenssegers\Agent\Agent())->isMobile())
                    <ul class="tabs tab-pills">
                    <li>    
                    <a onclick="FuncionMobile()" class="btn btn-light" style="background-color: #3b3f5c !important;">
                   <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg> </a>
                    <a  style="background-color: #3b3f5c !important;" class="btn btn-light {{ Auth::user()->sucursal == 1 ? 'hide-sucursales' : '' }}" wire:click="ModalAgregar()">
                   <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> </a>
                    </li>
                    </ul>
                   
                @endif
				
				
			</div>
			
			@if((new \Jenssegers\Agent\Agent())->isDesktop() || (new \Jenssegers\Agent\Agent())->isTablet()  )
			
			@include('common.filtros-productos')
			
			@endif
			
						 <!---- FILTROS MOBILE ----->
			 @if((new \Jenssegers\Agent\Agent())->isMobile())
			 
			 @include('common.filtros-productos-mobile')
			 
			 @endif
			 
			 
			<div class="widget-content">
                <!---- FILTROS ESTADO ----->
                
                @include('common.filtro-estado')  
                    
                <!-------------------------->
				                
                <!---- ACCIONES EN LOTE ----->
                
                @include('common.accion-lote')  
                    
                <!-------------------------->

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


				<div class="table-responsive" style="overflow-x: auto !important;">
					<table  id="default-ordering" class="table table-hover">
						<thead class="text-white" style="background: #3B3F5C; vertical-align: middle !important;">
							<tr>
								<th>
                                <input name="Todos" type="checkbox" value="1" onclick="CheckTodosLote()" class="check_todos"/>    
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
										CODIGO
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
                                
                                    
                                       
                                   <!---- Si el producto es variable ----->
                                   
                                    @if($product->producto_tipo == "v")
                                   
                                    @foreach($productos_lista_precios as $pl)
                                    
                                    @if($pl->lista_id == 0)
                                    
                                    @if($pl->product_id == $product->id)
                                    
                                    <h6 class="text-center" style="vertical-align: middle !important; padding: 10px 10px 10px 10px; margin: 0 !important;">
									<b> {{$pl->variaciones }}</b>
									$ {{$pl->precio_lista }}
									</h6>
                                    
                                    @endif
                                    
                                    @endif
                                    
                                    @endforeach
                                    
                                    @else
                                    
                                    <!---- Si el producto es simple ----->
                                    
									@foreach($productos_lista_precios as $pl)

									@if($pl->product_id == $product->id)

									@if($pl->lista_id == 0)

									<h6 class="text-center" style="vertical-align: middle !important; padding: 10px 10px 10px 10px; margin: 0 !important;">
                                     $	{{$pl->precio_lista}}
									</h6>


									@endif

									@endif

									@endforeach
									
									@endif
									
									<!--------------------------------------->


									</div>

								</td>

								@foreach($lista_precios as $list)
									<td class="text-center">

									 <!---- Si el producto es variable ----->
                                   
                                    @if($product->producto_tipo == "v")
                                   
                                    @foreach($productos_lista_precios as $pl)
                                    
                                    @if($pl->lista_id == $list->id)
                                    
                                    @if($pl->product_id == $product->id)
                                    
                                    <h6 class="text-center" style="vertical-align: middle !important; padding: 10px 10px 10px 10px; margin: 0 !important;">
									<b> {{$pl->variaciones }}</b>
									$ {{$pl->precio_lista }}
									</h6>
                                    
                                    @endif
                                    
                                    @endif
                                    
                                    @endforeach
                                    
                                    @else
                                    
                                    <!---- Si el producto es simple ----->
                                    
									@foreach($productos_lista_precios as $pl)

									@if($pl->product_id == $product->id)

									@if($pl->lista_id == $list->id)

									<h6 class="text-center" style="vertical-align: middle !important; padding: 10px 10px 10px 10px; margin: 0 !important;">
                                     $	{{$pl->precio_lista}}
									</h6>


									@endif

									@endif

									@endforeach
									
									@endif
									
									<!--------------------------------------->

									</td>
								@endforeach


								<td class="text-center">
									<span>

									   @if($product->image != null)
										<img src="{{ asset('storage/products/' . $product->image ) }}" alt="{{$product->name}}" height="70" width="80" class="rounded">
										@else
										<img src="{{ asset('storage/products/noimg.png') }}" alt="{{$product->name}}" height="70" width="80" class="rounded">
										@endif

									</span>
								</td>

								<td class="text-center">
                                    @if($estado_filtro == 0)
                                
									<a href="javascript:void(0)" wire:click.prevent="Edit({{$product->id}})" class="btn btn-dark mtmobile {{ $sucursal_id != $comercio_id ? 'hide-sucursales' : '' }}" title="Edit">
										<i class="fas fa-edit"></i>
									</a>

									@can('product_destroy')
									<a href="javascript:void(0)" onclick="Confirm('{{$product->id}}')" class="btn btn-dark" title="Delete">
										<i class="fas fa-trash"></i>
										</a>

										@endcan
										<button hidden type="button" wire:click.prevent="ScanCode('{{$product->barcode}}')" class="btn btn-dark"><i class="fas fa-shopping-cart"></i></button>
								
								    @else
							        <a href="javascript:void(0);" onclick="RestaurarProducto({{$product->id}})" class="btn btn-dark"> Restaurar </a>
							        @endif
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
	@include('livewire.products.form-imagen')
	@include('livewire.products.form_cropp')
    @include('livewire.products.form-proveedor')
	@include('livewire.products.form-categoria')
	@include('livewire.products.form-almacen')
	@include('livewire.products.form-lista-precios')
	@include('livewire.products.modal-cambio-sucursal')
</div>


<script>
	document.addEventListener('DOMContentLoaded', function() {
        
        window.livewire.on('modal-lista-precios-show', msg => {
			$('#theModalListaPrecios').modal('show')
			$('#theModal').modal('hide')
		});
		
		window.livewire.on('modal-lista-precios-hide', msg => {
			$('#theModalListaPrecios').modal('hide')
			$('#theModal').modal('show')
			noty(msg)
		});

		
		window.livewire.on('modal-imagen-show', msg => {
			$('#Imagenes').modal('show')
			$('#theModal').modal('hide')
		});

		window.livewire.on('modal-imagen-hide', msg => {
			$('#Imagenes').modal('hide')
			$('#theModal').modal('show')
		});
		
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

		window.livewire.on('modal-cambio-sucursal', msg => {
			$('#ModalCambioSucursal').modal('show')
		});
		
		window.livewire.on('modal-proveedor-show', msg => {
			$('#Proveedor').modal('show')
			$('#theModal').modal('hide')
		});
		
		
		window.livewire.on('proveedor-added', msg => {
			$('#Proveedor').modal('hide')
			$('#theModal').modal('show')
			noty(msg)
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

	
	function RestaurarProducto(id) {

    swal({
      title: 'CONFIRMAR',
      text: 'QUIERE RESTAURAR EL PRODUCTO?',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('RestaurarProducto', id)
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
reader.onload = function(){
	let preview = document.getElementById('image-upload'),
					image = document.createElement('img');

	image.src = reader.result;

	preview.innerHTML = '';
	preview.append(image);
};
}
</script>
