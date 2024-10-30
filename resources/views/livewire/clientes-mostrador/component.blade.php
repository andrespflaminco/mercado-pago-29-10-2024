<div>	  

 @if($agregar == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>Clientes</h4>
							<h6>Ver listado de clientes</h6>
						</div>
						<div class="page-btn  d-lg-flex d-sm-block">
					        <a href="javascript:void(0)" class="btn btn-added" wire:click="Agregar"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar cliente</a>
					        <a hidden href="javascript:void(0)" class="btn btn-added" wire:click="AjustarSaldosIniciales">-</a>
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
					    
				        <!--- Si tiene sucursales --->
				        @if(0 < $sucursales->count() )
				        <ul hidden class="nav nav-tabs  mb-3">
				        <li hidden style="background:white; border: solid 1px #eee;" class="nav-item">
                            <a style="{{ $sucursal_id == 0 ? 'color: #e95f2b;' : '' }}" class="nav-link" href="javascript:void(0)"  wire:click="ElegirSucursal('0')"  > Todos </a>
                        </li>
                        
                        <li style="background:white; border: solid 1px #eee;" class="nav-item">
                            <a style="{{ $sucursal_id == $comercio_id ? 'color: #e95f2b;' : '' }}" class="nav-link  {{ $sucursal_id == $comercio_id ? 'active' : '' }} " href="javascript:void(0)"  wire:click="ElegirSucursal({{$comercio_id}})"  > {{auth()->user()->name}} </a>
                        </li>
                        @foreach($sucursales as $item)
                        <li style="background:white; border: solid 1px #eee;"  class="nav-item">
                            <a style="{{ $sucursal_id == $item->sucursal_id ? 'color: #e95f2b;' : '' }}" class="nav-link {{ $sucursal_id == $item->sucursal_id ? 'active' : '' }}" href="javascript:void(0)"  wire:click="ElegirSucursal({{$item->sucursal_id}})"  >{{$item->name}}</a>
                        </li>
                        @endforeach
                    	</ul>
                    	@endif
                    	
				        
				        <!-----/ Si tiene sucursales ---->
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
								
								<div class="col-sm-12 col-md-2">
							
							    @if($solo_ver_clientes_propios != 1)
								<div class="form-group">
                                    <label>Sucursal:</label>
                                    <select class="form-control" wire:model="sucursal_id">
                                      <option value="0"> Todos </option>    
                                      <option value="{{$comercio_id}}"> {{auth()->user()->name}} </option>
                                      @foreach($sucursales as $item)
                                      <option value="{{$item->sucursal_id}}">{{$item->name}}</option>
                                      @endforeach
                                    </select>
                                </div>
                                @endif
                                
								</div>
								
								<div class="col-sm-12 col-md-4">

                                </div>

								<div class="wordset">
									<ul>
										<li>
											<a hidden data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img  src="{{ asset('assets/pos/img/icons/pdf.svg') }}"  alt="img"></a>
										</li>
										<li>
										<a style="font-size:12px !important; padding:5px !important; background: #198754 !important;" class="btn btn-cancel" wire:click="ExportarExcel()"  data-bs-placement="top" title="exportar excel"> 
										<svg style="margin-right: 5px;"  xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
										Exportar </a>
										</li>
										<li>
										<a style="font-size:12px !important; padding:5px !important;" class="btn btn-cancel" href="{{ url('import-clientes') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="importar"> 
									    <svg  style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-upload"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>		
									    Importar</a>
										</li>
									</ul>
								</div>
							</div>
							@if(auth()->user()->profile != "Cajero")
							<!-- /Filter -->
							@include('common.accion-lote')
							<!-- /Filter -->
							@endif
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
										<th wire:click="OrdenarColumna('id_cliente')" >COD CLIENTE
										    @if ($columnaOrden == 'id_cliente')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
										</th>
                						<th wire:click="OrdenarColumna('nombre')" >CLIENTE
 										    @if ($columnaOrden == 'nombre')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif               						
                						</th>
                						<th wire:click="OrdenarColumna('nombre_sucursal')" >SUCURSAL
                							@if ($columnaOrden == 'nombre_sucursal')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                						</th>
                                        <th wire:click="OrdenarColumna('email')" >EMAIL
                                            @if ($columnaOrden == 'email')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('telefono')" >TELÉFONO
                                            @if ($columnaOrden == 'telefono')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('direccion')" >DIRECCION
                                            @if ($columnaOrden == 'direccion')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('dias_desde_creacion')" >DIAS DESDE LA ULTIMA COMPRA
                                            @if ($columnaOrden == 'dias_desde_creacion')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th >ACCIONES</th>
										</tr>
									</thead>
									<tbody>
        							@foreach($data as $r)
        							<tr>
										<td>
										    @if (auth()->user()->sucursal != 1) 
											<label class="checkboxs">
											    <input type="checkbox" wire:model.defer="id_check" tu-attr-id="{{($r->id)}}"  class="mis-checkboxes" value="{{$r->id}}">
											    <span class="checkmarks"></span>
											</label>
											@else
											@if(auth()->user()->id == $r->creador_id)
											<label class="checkboxs">
											    <input type="checkbox" wire:model.defer="id_check" tu-attr-id="{{($r->id)}}"  class="mis-checkboxes" value="{{$r->id}}">
											    <span class="checkmarks"></span>
											</label>
											@endif
											@endif
										</td>
        							    <td>{{$r->id_cliente}}</td>
    	                                <td>{{$r->nombre}}
    	                                @if($r->wc_customer_id != null)
    	                                <i style="margin-right:4px;" class="fab fa-wordpress-simple"></i>
    	                                @endif
    	                                </td>
    	                                <td>{{$r->nombre_sucursal}}</td>
    	                                <td>{{$r->email}}</td>
                                        <td>{{$r->telefono}}</td>
                                        <td>{{$r->direccion}} {{$r->altura}} @if($r->piso != null)  {{$r->piso}}°{{$r->depto}} @endif {{$r->localidad}} @if($r->codigo_postal != null) (CP: {{$r->codigo_postal}}) @endif - {{$r->provincia}}  </td>
                                        <td>
                                        
                                        @if(!is_null($r->dias_desde_creacion)) 
                                        @if($r->recontacto < $r->dias_desde_creacion)
                                        <span class="badges bg-lightred">
                                        {{$r->dias_desde_creacion}}
                                        DIAS
                                        </span>
                                        @else
                                        <span class="badges bg-lightyellow" style="background: #8ea0af !important;">
                                        {{$r->dias_desde_creacion}}
                                        DIAS
                                        </span>
                                        @endif
                                        @endif
                                        
                                        
                                        </td>
                                       	<td>
                                  		    <!---- si NO es sucursal --------->
											
                                  		    @if (auth()->user()->sucursal != 1) 
                                  		    @if($estado_filtro == 0)
                                  		    
                                  		    @if($wc != null)
                                  			<a class="me-3 mb-1" href="javascript:void(0)" wire:click="SincronizarCliente({{$r->id}})" >
											<svg style="margin-bottom: 8px;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
											</a>
										    @endif
                                  		   
        								    <a class="me-3" href="javascript:void(0)" wire:click="Edit({{$r->id}})" >
												<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
											</a>
											<a href="javascript:void(0)" onclick="Confirm('{{$r->id}}')"  >
												<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
											</a>
											@else
											<a href="javascript:void(0)" class="btn btn-light" wire:click="RestaurarCliente('{{$r->id}}')"  >
											RESTAURAR
											</a>
											
											@endif
											
											@else
											
											<!---- si es sucursal --------->
											
											@if(auth()->user()->id == $r->creador_id)
											 @if($estado_filtro == 0)
                                  		    
                                  		   
        								    <a class="me-3" href="javascript:void(0)" wire:click="Edit({{$r->id}})" >
												<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
											</a>
											<a href="javascript:void(0)" onclick="Confirm('{{$r->id}}')"  >
												<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
											</a>
											@else
											<a href="javascript:void(0)" class="btn btn-light" wire:click="RestaurarCliente('{{$r->id}}')"  >
											RESTAURAR
											</a>
											
											@endif
											@endif
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
					
					
@else
	@include('livewire.clientes-mostrador/agregar-editar-cliente')
@endif




</div>


<script>
    document.addEventListener('DOMContentLoaded', function(){
        window.livewire.on('user-added', Msg => {
            $('#theModal').modal('hide')
            noty(Msg)
        })
        window.livewire.on('user-updated', Msg => {
            $('#theModal').modal('hide')
            noty(Msg)
        })
        window.livewire.on('user-deleted', Msg => {
            noty(Msg)
        })
        window.livewire.on('hide-modal', Msg => {
            $('#theModal').modal('hide')
        })
        window.livewire.on('show-modal', Msg => {
            $('#theModal').modal('show')
        })
        window.livewire.on('user-withsales', Msg => {
            noty(Msg)
        }) 
        window.livewire.on('msg', Msg => {
            noty(Msg)
        })

    });

    function Confirm(id)
    {

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
            if(result.value){
                window.livewire.emit('deleteRow', id)
                swal.close()
            }

        })
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1ZH0LqzqoejHd5ko36zMckdV0pt0xYqc&libraries=places&callback=initialize" async defer></script>

<script type="text/javascript">

function initialize() {

  $('form').on('keyup keypress', function(e) {
      var keyCode = e.keyCode || e.which;
      if (keyCode === 13) {
          e.preventDefault();
          return false;
      }
  });
  const locationInputs = document.getElementsByClassName("map-input");

  const autocompletes = [];
  const geocoder = new google.maps.Geocoder;
  for (let i = 0; i < locationInputs.length; i++) {

      const input = locationInputs[i];
      const fieldKey = input.id.replace("-input", "");
      const isEdit = document.getElementById(fieldKey + "-latitude").value != '' && document.getElementById(fieldKey + "-longitude").value != '';

      const latitude = parseFloat(document.getElementById(fieldKey + "-latitude").value) || -33.8688;
      const longitude = parseFloat(document.getElementById(fieldKey + "-longitude").value) || 151.2195;

      const map = new google.maps.Map(document.getElementById(fieldKey + '-map'), {
          center: {lat: latitude, lng: longitude},
          zoom: 13
      });
      const marker = new google.maps.Marker({
          map: map,
          position: {lat: latitude, lng: longitude},
      });

      marker.setVisible(isEdit);

      const autocomplete = new google.maps.places.Autocomplete(input);

    }
  }
</script>
 <script>
    document.addEventListener('DOMContentLoaded', function(){

        $('.tagging').select2({
                        tags: true
                    });

                $('#select2-dropdown').on('change', function(e) {
                  var id = $('#select2-dropdown').select2('val');
                  var name = $('#select2-dropdown option:selected').text();
                  @this.set('clientesSelectedName', name);
                  @this.set('ClienteSeleccionado', ''+id);
                  @this.emit('locationUsersSelected', $('#select2-dropdown').select2('val'));
                });
                
                
                
                });
                
</script>