<div>	  

 @if($agregar == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>Usuarios</h4>
							<h6>Ver listado de usuarios</h6>
						</div>
						
						<div class="page-btn  d-lg-flex d-sm-block">
						    @if(Auth::user()->profile != "Cajero" )
                            
                            @if(Auth::user()->sucursal != 1)
                            <a href="{{ url('sucursales') }}" class="btn btn-added" style="background: #212529 !important;">
                                <img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar Sucursal
                            </a>
                            @endif
                            
                            @if(Auth::user()->plan == 1 && $count_usuarios >= (1 + Auth::user()->usuarios_extra) )
                            <a href="javascript:void(0)" class="btn btn-added" onclick="MejorarPlan()">
                                <img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar Usuario
                            </a>
                            @elseif(Auth::user()->plan == 2 && $count_usuarios >= (10 + Auth::user()->usuarios_extra) )
                            <a href="javascript:void(0)" class="btn btn-added" onclick="MejorarPlan()">
                                <img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar Usuario
                            </a>
                            @elseif(Auth::user()->plan == 3 && $count_usuarios >= (20 + Auth::user()->usuarios_extra) )
                            <a href="javascript:void(0)" class="btn btn-added" onclick="MejorarPlan()">
                                <img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar Usuario
                            </a>
                            @elseif(Auth::user()->plan == 4 && $count_usuarios >= (50 + Auth::user()->usuarios_extra) )
                            <a href="javascript:void(0)" class="btn btn-added" onclick="MejorarPlan()">
                                <img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar Usuario
                            </a>
                            @else
                            <a href="javascript:void(0)" class="btn btn-added" wire:click="Agregar">
                                <img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar Usuario
                            </a>
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

									<input type="text" autocomplete="off" wire:model="buscar" placeholder="Buscar.." class="form-control"	>

								
								@if(auth()->user()->sucursal != 1)	
								<div style="margin-left: 15px !important;" class="col-lg-4 col-sm-6 col-12">
                            	<div class="form-group">
                            		<label style="margin-bottom: 4px !important;">Sucursal</label>
                            		<select wire:model.lazy="sucursal_filtro" class="form-control">
                            			<option value="all" selected>Todas</option>
                            			<option value="{{auth()->user()->id}}" selected>{{auth()->user()->name}}</option>
                            			@foreach($sucursales as $s)
                            			<option value="{{$s->sucursal_id}}">{{$s->name}}</option>
                            			@endforeach
                            		</select>
                            		@error('sucursal_id') <span class="text-danger er">{{ $message}}</span>@enderror
                            	</div>
								</div>
								@endif
								
									<div hidden class="search-input">
										<a class="btn btn-searchset"><img src="{{ asset('assets/pos/img/icons/search-white.svg') }}" alt="img"></a>
									</div>
								</div>
								<div class="wordset">
									<ul>
										<li>
											<a hidden data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img  src="{{ asset('assets/pos/img/icons/pdf.svg') }}"  alt="img"></a>
										</li>
										<li>
											<a hidden href="{{ url('report/excel-clientes' . '/'. uniqid() ) }}" target="_blank"  title="excel"><img  src="{{ asset('assets/pos/img/icons/excel.svg') }}" alt="img"></a>
										</li>
										<li>
											<a hidden data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img  src="{{ asset('assets/pos/img/icons/printer.svg') }}" alt="img"></a>
										</li>
									</ul>
								</div>
							</div>
							<!-- /Filter -->
							
							<!-- /Filter -->
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
                                        <th>USUARIO</th>
                                        <th>TELÉFONO</th>
                                        <th>EMAIL</th>
                                        <th>ESTATUS</th>
                                        <th>PERFIL</th>
                                        <th>ACCIONES</th>
										</tr>
									</thead>
									<tbody>
        							@foreach($data as $r)
        							<tr>
    	                                <td>{{$r->name}}</td>
                                        <td>{{$r->phone}}</td>
                                        <td>{{$r->email}}</td>
                                        <td>{{$r->status}}</td>
                                        <td>
                                        <p>{{$r->profile}}</p>
                                        </td>
                                      
                                  		<td>
                                  		     @if(Auth::user()->profile != "Cajero" )
                                  		     
                                  		    
                                  		    <a class="me-3" href="javascript:void(0)" wire:click="edit({{$r->id}})" >
												<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
											</a>
											<a href="javascript:void(0)" onclick="Confirm('{{$r->id}}')"  >
												<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
											</a>
									
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
	@include('livewire.users.agregar-editar-usuario')
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
