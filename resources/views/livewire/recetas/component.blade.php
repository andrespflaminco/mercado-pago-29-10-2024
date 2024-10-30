<div>
    
    
        <div class="page-header">
					<div class="page-title">
							<h4>Recetas</h4>
							<h6>Ver listado de recetas / composiciones de productos</h6>
						</div>
						<div class="page-btn">               											    
                         
                            @if(Auth::user()->sucursal != 1)
                			@if(Auth::user()->profile != "Cajero" )
                            <a href="javascript:void(0)" wire:click="ExportarRecetas" class="btn btn-success">Exportar</a>
                            <a href="{{url('import-recetas')}}" class="btn btn-success">Importar</a>
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
									
									<div style="margin-left: 20px;" class="input-group">
            							<div class="input-group-prepend">
            								<span style="height: 100%;" class="input-group-text input-gp">
            									<i class="fas fa-file"></i>
            								</span>
            							</div>
            							<select class="form-control" wire:model="categoria_id">
            							    <option value="">Todos las categorias</option>
            							    <option value="1">Sin categoria</option>
            							    @foreach($categories as $c)
            							    <option value="{{$c->id}}">{{$c->name}}</option>
            							    @endforeach
            							     
            							</select>
            						</div>
            						@if(Auth::user()->sucursal != 1)
                			        @if(Auth::user()->profile != "Cajero" )
            						<div style="margin-left:15px;"><button class="btn btn-light" wire:click="ActualizarCostosProductosSimples">Recalcular costos</button></div>
            						@endif
            						@endif
            						
            						<div hidden class="form-group">
                                        <label for="filter">Receta con costos</label>
                                        <select id="filter" wire:model="filter" class="form-control">
                                            <option value="all">Todos</option>
                                            <option value="1">Con receta</option>
                                            <option value="0">Sin receta</option>
                                        </select>
                                    </div>

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

							 
					<div class="table-responsive ">
					<table class="table mb-3">
						<thead>
							<tr>
							    <th>CODIGO</th>
								<th>NOMBRE</th>
								<th>COSTO DE LA RECETA</th>
								<th>UNID PRODUCIDAS POR RECETA</th>
								<th>COSTO UNITARIO</th>
								<th>RECETA</th>
								<th>ACCIONES</th>
							</tr>
						</thead>
						<tbody>
						    
						    @php
						    $i = 1;
						    @endphp
							@foreach($data as $product)

							@if($product->producto_tipo == "s")

							<tr>
							    <td>{{$product->barcode}}</td>
							
								<td>{{$product->name}}</td>

								<td>
									@php
										
										$imprimir = '';
										foreach($recetas as $r) {
									
										if(  ($r->product_id == $product->id) ) {
										
										
										$costo_total =	number_format($r->cost,2);
                                        
                                        if(0 < $costo_total) {
                                        
                                        $imprimir .= $costo_total;
                                        
                                        echo '$ '.$costo_total;
                                        } 
                                       
										} 
										
										}
										
										if($imprimir == '') {
										echo '$ 0';
										}
										
										@endphp
								
								</td>
								<td>
								@foreach($recetas as $r) 
								@if( ($r->referencia_variacion == 0) && ($r->product_id == $product->id)) 
								{{$r->rinde}}
								@endif
								@endforeach
								</td>
								<td>
									@php
										
										$imprimir = '';
										foreach($recetas as $r) {
									
										if( ($r->referencia_variacion == 0) && ($r->product_id == $product->id)) {
										
										
										$costo_unitario =	number_format($r->cost/$r->rinde,2);
                                        
                                        if(0 < $costo_unitario) {
                                        
                                        $imprimir .= $costo_unitario;
                                        
                                        echo '$ '.$costo_unitario;
                                        } 
                                       
										} 
										
										}
										
										if($imprimir == '') {
										echo '$ 0';
										}
										
										@endphp
                    			</td>
									
								
								<td>
											
                                    @php
										
										$imprimir = '';
										foreach($recetas as $r) {
									
										if( ($r->referencia_variacion == 0) && ($r->product_id == $product->id)) {
										
										
										$costo_unitario =	number_format($r->cost/$r->rinde,2);
                                        
                                        if(0 < $costo_unitario) {
                                        
                                        $imprimir .= $costo_unitario;
                                        
                                        echo '<span class="badges bg-lightgreen">Contiene receta</span>';
                                        } 
                                       
										} 
										
										}
										
										if($imprimir == '') {
										echo '<span class="badges bg-lightred">Sin receta</span>';
										}
										
										@endphp
                        			</td>

									<td class="text-center">
									    
									    @php
										
										$imprimir = '';
										foreach($recetas as $r) {
									
										if( ($r->referencia_variacion == 0) && ($r->product_id == $product->id)) {
										
										
										$costo_unitario =	number_format($r->cost/$r->rinde,2);
                                        
                                        if(0 < $costo_unitario) {
                                        
                                        $imprimir .= $costo_unitario;
                                        
                                        @endphp
                                        
                                       <a href="{{$appUrl}}/recetas_detalle?product_id={{$product->id}}&referencia_variacion=0&accion=3" class="btn btn-light" title="Ver">
											<i class="fas fa-eye"></i>
											</a>
                                        
                                        @if(auth()->user()->sucursal != 1)    
											<a href="{{$appUrl}}/recetas_detalle?product_id={{$product->id}}&referencia_variacion=0&accion=2"  class="btn btn-light" title="Editar">
												<i class="fas fa-edit"></i>
											</a>

											<a href="javascript:void(0)" onclick="Confirm('{{$product->id}}&0')" class="btn btn-light" title="Eliminar">
												<i class="fas fa-trash"></i>
												</a>
                                        @endif
                                        
                                        @php
                                        } 
                                       
										} 
										
										}
										
										if($imprimir == '') {
										
										@endphp
										
										@if(auth()->user()->sucursal != 1)
											<a href="{{$appUrl}}/recetas_detalle?product_id={{$product->id}}&referencia_variacion=0&accion=1" class="btn btn-light" title="Edit">
											<i class="fa fa-plus"></i>
										    </a>
										@endif
										
										 @php
										}
										
										@endphp
										

									
										</td>

									</tr>


							@endif
							
							@if($product->producto_tipo == "v")

									@foreach($productos_variaciones_datos as $pv)

									@if($pv->product_id == $product->id)
									
									<tr>

									<!--- PRODUCTOS VARIABLES ---->
									<td>  {{$product->barcode}} </td>
									<td>
											{{$product->name}}
											@foreach($variaciones as $v)

											@if($v->referencia_id == $pv->referencia_variacion)

											- {{$v->nombre_variacion}}

											@endif
											@endforeach
									</td>

									<td>
										@php
										
										$imprimir = '';
										foreach($recetas as $r) {
									
										if( ($r->referencia_variacion == $pv->referencia_variacion) && ($r->product_id == $product->id)) {
										
										
										$costo_total =	number_format($r->cost,2);
                                        
                                        if(0 < $costo_total) {
                                        
                                        $imprimir .= $costo_total;
                                        
                                        echo '$ '.$costo_total;
                                        } 
                                       
										} 
										
										}
										
										if($imprimir == '') {
										echo '$ 0';
										}
										
										@endphp
									</td>
									
									<td>
    								@foreach($recetas as $r) 
    								@if( ($r->referencia_variacion == 0) && ($r->product_id == $product->id)) 
    								{{$r->rinde}}
    								@endif
    								@endforeach
    								</td>
									<td>
        								@foreach($recetas as $r) 
        								@if( ($r->referencia_variacion == 0) && ($r->product_id == $product->id)) 
        								{{$r->rinde}}
        								@endif
        								@endforeach
								    </td>
									@php
										
										$imprimir = '';
										foreach($recetas as $r) {
									
										if( ($r->referencia_variacion == $pv->referencia_variacion) && ($r->product_id == $product->id)) {
										
										
										$costo_unitario =	number_format($r->cost/$r->rinde,2);
                                        
                                        if(0 < $costo_unitario) {
                                        
                                        $imprimir .= $costo_unitario;
                                        
                                        echo '$ '.$costo_unitario;
                                        } 
                                       
										} 
										
										}
										
										if($imprimir == '') {
										echo '$ 0';
										}
										
										@endphp
									</td>
									<td>
											
                                            	@php
										
										$imprimir = '';
										foreach($recetas as $r) {
									
										if( ($r->referencia_variacion == $pv->referencia_variacion) && ($r->product_id == $product->id)) {
										
										
										$costo_unitario =	number_format($r->cost/$r->rinde,2);
                                        
                                        if(0 < $costo_unitario) {
                                        
                                        $imprimir .= $costo_unitario;
                                        
                                        echo '<span class="badges bg-lightgreen">Contiene receta</span> ';
                                        } 
                                       
										} 
										
										}
										
										if($imprimir == '') {
										echo '<span class="badges bg-lightred">Sin receta</span> ';
										}
										
										@endphp


											

											
									</td>



									<td class="text-center">
									    
									    @php
										
										$imprimir = '';
										foreach($recetas as $r) {
									
										if( ($r->referencia_variacion == $pv->referencia_variacion) && ($r->product_id == $product->id)) {
										
										
										$costo_unitario =	number_format($r->cost/$r->rinde,2);
                                        
                                        if(0 < $costo_unitario) {
                                        
                                        $imprimir .= $costo_unitario;
                                        
                                        @endphp
                                        
                                       <a href="{{$appUrl}}/recetas_detalle?product_id={{$product->id}}&referencia_variacion={{$pv->referencia_variacion}}&accion=3" class="btn btn-light" title="Ver">
											<i class="fas fa-eye"></i>
											</a>

											<a href="{{$appUrl}}/recetas_detalle?product_id={{$product->id}}&referencia_variacion={{$pv->referencia_variacion}}&accion=2" class="btn btn-light" title="Editar">
												<i class="fas fa-edit"></i>
											</a>

											<a href="javascript:void(0)" onclick="Confirm('{{$product->id}}&{{$pv->referencia_variacion}}')" class="btn btn-light" title="Eliminar">
												<i class="fas fa-trash"></i>
												</a>
                                        
                                        @php
                                        } 
                                       
										} 
										
										}
										
										if($imprimir == '') {
										
										@endphp
										
											<a href="{{$appUrl}}/recetas_detalle?product_id={{$product->id}}&referencia_variacion={{$pv->referencia_variacion}}&accion=1" class="btn btn-light" title="Agregar">
											<i class="fa fa-plus"></i>
										</a>
										
										
										 @php
										}
										
										@endphp
										

									
										</td>



							</tr>
						        	@endif

							@endforeach
							@endif
							@endforeach
						</tbody>
					</table>
					{{$data->links()}}
					
				</div>
				
				
						</div>
					</div>
	@include('livewire.recetas.form')
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
