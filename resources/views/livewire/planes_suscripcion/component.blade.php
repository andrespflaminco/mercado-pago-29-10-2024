<div>	                

                    @if($seccion == 1)
	                
	                @if($agregar == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>Planes de suscripcion</h4>
							<h6>Ver listado de planes de suscripcion</h6>
						</div>
						<div class="page-btn">               											    
                			
                			<a href="javascript:void(0)" wire:click="Agregar()" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar </a>
						    
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
					        <ul class="nav nav-tabs  mb-3">
                            <li style="background:white; border: solid 1px #eee;" class="nav-item">
                                <a style="{{ $seccion == 2 ? 'color: #e95f2b;' : '' }}" class="nav-link  {{ $seccion == 2 ? 'active' : '' }} " href="javascript:void(0)"  wire:click="ElegirSeccion(2)"  > Pagina registro</a>
                            </li>
                            <li style="background:white; border: solid 1px #eee;" class="nav-item">
                                <a style="{{ $seccion == 3 ? 'color: #e95f2b;' : '' }}" class="nav-link  {{ $seccion == 3 ? 'active' : '' }} " href="javascript:void(0)"  wire:click="ElegirSeccion(3)"  > Landing precios</a>
                            </li>                
                            <li style="background:white; border: solid 1px #eee;" class="nav-item">
                                <a style="{{ $seccion == 1 ? 'color: #e95f2b;' : '' }}" class="nav-link  {{ $seccion == 1 ? 'active' : '' }} " href="javascript:void(0)"  wire:click="ElegirSeccion(1)"  > Planes de suscripcion </a>
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
							                											    
                			 @include('common.accion-lote')
							 
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th>
												<label class="checkboxs">
												    <input name="Todos" type="checkbox" value="1" onclick="CheckTodosLote()" class="check_todos"/>    
                            						<span class="checkmarks"></span>
												</label>
												
												</th>
											<th>Nombre</th>
											<th>Plan</th>
											<th>Origen</th>
											<th>Monto</th>
											<th>URL DE MERCADO PAGO </th>
											<th>URL de la suscripcion</th>
											<th>Plan id de Mercado Pago</th>
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>
									    @foreach($datos as $dato)
										<tr>
											<td>
												<label class="checkboxs">
												    <input type="checkbox" wire:model.defer="id_check" tu-attr-id="{{($dato->id)}}"  class="mis-checkboxes" value="{{$dato->id}}">
													<span class="checkmarks"></span>
												</label>
											</td>
											<td>
												<a href="javascript:void(0);" wire:click.prevent="Edit({{$dato->id}})">{{$dato->nombre}}</a>
											</td>
											<td>
											@if($dato->plan_id == 1)    
										    EMPRENDEDOR
											@endif
											
											@if($dato->plan_id == 2)    
										    PEQUE���AS EMPRESAS	
											@endif
											
											@if($dato->plan_id == 3)    
											MEDIANAS EMPRESAS
											@endif
											
											@if($dato->plan_id == 4)    
											GRANDES EMPRESAS
											@endif
											
											</td>
											<td>
											{{$dato->origen}}
											</td>
											<td>
											$ {{number_format($dato->monto,0,",",".")}}
											</td>
											<td>
											    https://www.mercadopago.com.ar/subscriptions/checkout?preapproval_plan_id={{$dato->preapproval_plan_id}}
											    <button class="btnCopiar" value="https://www.mercadopago.com.ar/subscriptions/checkout?preapproval_plan_id={{ $dato->preapproval_plan_id }}" onclick="copiarTexto(this)"><i class="fas fa-copy"></i> Copiar</button>

											</td>
											
											<td>
											https://app.flamincoapp.com.ar/suscribirse/{{$dato->id}}
                                            <button class="btnCopiar" value="https://app.flamincoapp.com.ar/suscribirse/{{ $dato->id }}" onclick="copiarTexto(this)"><i class="fas fa-copy"></i> Copiar</button>

											</td>

											<td>
											{{$dato->preapproval_plan_id}}
											</td>
											<td>
											    @if($estado_filtro == 0 )
												<a class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$dato->id}})" >
													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
												</a>
												<a href="javascript:void(0)" onclick="Confirm('{{$dato->id}}')"  >
													<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
												</a>
												@else
            								    <a href="javascript:void(0)" onclick="RestaurarCategoria('{{$dato->id}}')" class="btn btn-light" title="Restaurar">
            										RESTAURAR
            									</a>
            								    
            								    @endif
            								    
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
								{{$datos->links()}}
							</div>
						</div>
					</div>
					
					<!-- /product list -->
					@endif 
					
					@if($agregar == 1)
					@include('livewire.planes_suscripcion.agregar-editar')
					@endif 
					
					@endif
					
					
				   @if($seccion == 2)
	               @if($agregar == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>Landings page</h4>
							<h6>Ver listado de las paginas de registro</h6>
						</div>
						<div class="page-btn">               											    
                			
                			<a href="javascript:void(0)" wire:click="Agregar()" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar </a>
						    
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
					        <ul class="nav nav-tabs  mb-3">
                            <li style="background:white; border: solid 1px #eee;" class="nav-item">
                                <a style="{{ $seccion == 2 ? 'color: #e95f2b;' : '' }}" class="nav-link  {{ $seccion == 2 ? 'active' : '' }} " href="javascript:void(0)"  wire:click="ElegirSeccion(2)"  > Pagina registro </a>
                            </li>
                            <li style="background:white; border: solid 1px #eee;" class="nav-item">
                                <a style="{{ $seccion == 3 ? 'color: #e95f2b;' : '' }}" class="nav-link  {{ $seccion == 3 ? 'active' : '' }} " href="javascript:void(0)"  wire:click="ElegirSeccion(3)"  > Landing precios</a>
                            </li>    
                            <li style="background:white; border: solid 1px #eee;" class="nav-item">
                                <a style="{{ $seccion == 1 ? 'color: #e95f2b;' : '' }}" class="nav-link  {{ $seccion == 1 ? 'active' : '' }} " href="javascript:void(0)"  wire:click="ElegirSeccion(1)"  > Planes de suscripcion </a>
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
							                											    
                			 @include('common.accion-lote')
							 
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th>ID origen</th>
											<th>Nombre</th>
											<th>URL registro</th>
											<th>URL registro</th>
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>
									    @foreach($datos_landing as $dato)
										<tr>
											<td>
												{{$dato->id}}
											</td>
											<td>
												<a href="javascript:void(0);" wire:click.prevent="Edit({{$dato->id}})">{{$dato->nombre}}</a>
											</td>
											
											<td>
											{{$dato->url_registro}}
											</td>
											<td>
											    @if($estado_filtro == 0 )
												<a class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$dato->id}})" >
													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
												</a>
												<a href="javascript:void(0)" onclick="Confirm('{{$dato->id}}')"  >
													<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
												</a>
												@else
            								    <a href="javascript:void(0)" onclick="RestaurarCategoria('{{$dato->id}}')" class="btn btn-light" title="Restaurar">
            										RESTAURAR
            									</a>
            								    
            								    @endif
            								    
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
								{{$datos->links()}}
							</div>
						</div>
					</div>
					
					<!-- /product list -->
					@endif 
					
					@if($agregar == 1)
					@include('livewire.planes_suscripcion.agregar-editar-land')
					@endif 
	                @endif
	                
	               @if($seccion == 3)
	               @if($agregar == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>Landings page</h4>
							<h6>Ver listado de las paginas con los precios para cada oferta</h6>
						</div>
						<div class="page-btn">               											    
                			
                			<a href="javascript:void(0)" wire:click="Agregar()" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar </a>
						    
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
					        <ul class="nav nav-tabs  mb-3">
                            <li style="background:white; border: solid 1px #eee;" class="nav-item">
                                <a style="{{ $seccion == 2 ? 'color: #e95f2b;' : '' }}" class="nav-link  {{ $seccion == 2 ? 'active' : '' }} " href="javascript:void(0)"  wire:click="ElegirSeccion(2)"  > Pagina registro </a>
                            </li>
                            <li style="background:white; border: solid 1px #eee;" class="nav-item">
                                <a style="{{ $seccion == 3 ? 'color: #e95f2b;' : '' }}" class="nav-link  {{ $seccion == 3 ? 'active' : '' }} " href="javascript:void(0)"  wire:click="ElegirSeccion(3)"  > Landing precios</a>
                            </li>    
                            <li style="background:white; border: solid 1px #eee;" class="nav-item">
                                <a style="{{ $seccion == 1 ? 'color: #e95f2b;' : '' }}" class="nav-link  {{ $seccion == 1 ? 'active' : '' }} " href="javascript:void(0)"  wire:click="ElegirSeccion(1)"  > Planes de suscripcion </a>
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
							                											    
                			 @include('common.accion-lote')
							 
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th>ID origen</th>
											<th>Nombre</th>
											<th>URL elegir plan</th>
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>
									    @foreach($datos_landing as $dato)
										<tr>
											<td>
												{{$dato->id}}
											</td>
											<td>
												<a href="javascript:void(0);" wire:click.prevent="Edit({{$dato->id}})">{{$dato->nombre}}</a>
											</td>
											
											<td>
											{{$dato->url}}
											</td>
											<td>
											    @if($estado_filtro == 0 )
												<a class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$dato->id}})" >
													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
												</a>
												<a href="javascript:void(0)" onclick="Confirm('{{$dato->id}}')"  >
													<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
												</a>
												@else
            								    <a href="javascript:void(0)" onclick="RestaurarCategoria('{{$dato->id}}')" class="btn btn-light" title="Restaurar">
            										RESTAURAR
            									</a>
            								    
            								    @endif
            								    
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
								{{$datos->links()}}
							</div>
						</div>
					</div>
					
					<!-- /product list -->
					@endif 
					
					@if($agregar == 1)
					@include('livewire.planes_suscripcion.agregar-editar-land')
					@endif 
	                @endif
					</div>
					
<script>
    
function copiarTexto(btn) {
    const inputTexto = btn.value; // Obtener el valor del botón
    const textoTemporal = document.createElement('textarea'); // Crear un elemento textarea temporal
    textoTemporal.value = inputTexto; // Asignar el valor del botón al textarea temporal

    document.body.appendChild(textoTemporal); // Agregar el textarea temporal al DOM
    textoTemporal.select(); // Seleccionar el contenido del textarea
    document.execCommand('copy'); // Copiar el contenido seleccionado

    document.body.removeChild(textoTemporal); // Eliminar el textarea temporal del DOM

    alert('Texto copiado al portapapeles');
}



</script>