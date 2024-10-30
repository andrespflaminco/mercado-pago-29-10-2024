<div>	  

 @if($agregar == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>CRM</h4>
							<h6>Ver listado de clientes potenciales</h6>
						</div>
						
						<div class="page-btn  d-lg-flex d-sm-block">
						    
						    <a href="javascript:void(0)" class="btn btn-success" wire:click="ObtenerLeads()">Obtener Leads ZOHO</a>
					        <a href="javascript:void(0)" class="btn btn-success" wire:click="Exportar()">Exportar Excel</a>
					        <a href="javascript:void(0)" class="btn btn-success" wire:click="ActualizarLeadsEnlote()">Actualizar Leads paginados</a>
					        
                     	</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
				
						<div class="card-body">
							
							<!-- /Filter -->
							<div class="row">
							<div class="col">
							    <label>Buscar por mail, nombre o id</label>
							    <input type="text" autocomplete="off" wire:model="buscar" placeholder="Buscar nombre , mail , id" class="form-control"	>
							</div>
							<div class="col">
							    <label>Valida mail</label>
							    <select class="form-control" wire:model="filtro_valida_mail">
							        <option value="">Todos</option>
							        <option value="si">si</option>
							        <option value="no">no</option>
							    </select>
							</div>	
							<div class="col">
							    <label>Contrato servicio</label>
							    <select class="form-control" wire:model="contrato">
							        <option value="">Todos</option>
							        <option value="pago">Contrato servicio</option>
							        <option value="no">No contrato</option>
							    </select>
							</div>	
							<div class="col mb-3">
							    <label>Dias desde creacion</label>
							    <select class="form-control" wire:model="dias_desde_creacion">
							        <option value="">Todos</option>
							        <option value="1">1 a 7 dias</option>
							        <option value="2">8 a 15 dias </option>
							        <option value="3">Mas de 15 dias</option>
							        
							    </select>
							</div>	
		
	    					</div>

							<!-- /Filter -->
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
										<th></th>
                                        <th wire:click="OrdenarColumna('id')">ID
                                              @if ($columnaOrden == 'id')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif</th>
                                        <th wire:click="OrdenarColumna('name')">USUARIO
                                              @if ($columnaOrden == 'name')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif</th>
                                        <th wire:click="OrdenarColumna('phone')">TELÉFONO
                                              @if ($columnaOrden == 'phone')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif</th>
                                         <th wire:click="OrdenarColumna('intento_pago')"> INTENTO PAGO
                                              @if ($columnaOrden == 'intento_pago')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif</th>
                                        <th wire:click="OrdenarColumna('email')">EMAIL
                                              @if ($columnaOrden == 'email')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif</th>
                                            
                                        <th wire:click="OrdenarColumna('email')">TELEFONO
                                              @if ($columnaOrden == 'phone')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif</th>
                                      
                                        <th wire:click="OrdenarColumna('email_verified_at')">EMAIL VALIDADO
                                              @if ($columnaOrden == 'email_verified_at')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif</th>
                                        
                                        <th wire:click="OrdenarColumna('email_verified_at')">FECHA EMAIL VALIDADO
                                              @if ($columnaOrden == 'email_verified_at')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif</th>
                                        <th wire:click="OrdenarColumna('email_verified_at')">HORA EMAIL VALIDADO
                                              @if ($columnaOrden == 'email_verified_at')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('created_at')">FECHA CREACION
                                              @if ($columnaOrden == 'created_at')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        <th wire:click="OrdenarColumna('created_at')">HORA CREACION
                                              @if ($columnaOrden == 'created_at')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        
                                        <th wire:click="OrdenarColumna('confirmed_at')"> FECHA DE PAGO
                                              @if ($columnaOrden == 'confirmed_at')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        
                                        <th wire:click="OrdenarColumna('profile')">PERFIL
                                              @if ($columnaOrden == 'profile')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        
                                        <th wire:click="OrdenarColumna('cantidad_login')"> CANT LOGUEOS
                                              @if ($columnaOrden == 'profile')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>
                                        
                                        <th wire:click="OrdenarColumna('last_login')">ULTIMO LOGUEO
                                              @if ($columnaOrden == 'profile')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif
                                        </th>

                                        <th>ACCIONES</th>
										</tr>
									</thead>
									<tbody>
        							@foreach($data as $r)
        							<tr>
        							    <td>
        							        <a style="color: black !important; background: #FAFBFE !important; padding: 1px 8px; border-radius: 8px; border: 1px solid #E9ECEF;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"></a>
                                         	<div class="dropdown-menu">
                                        	<button wire:click="EstablecerComoMailPrueba({{$r->id}})" class="dropdown-item">Establecer como mail de pruebas</button>
                                        	<button wire:click="createLeadFromUser({{$r->id}})" class="dropdown-item">CREAR LEAD EN CRM</button>
											<button wire:click="actualizarLead({{$r->id}})" class="dropdown-item">ACTUALIZAR LEAD EN CRM</button>
											<button hidden wire:click="actualizarContacto({{$r->id}})" class="dropdown-item">ACTUALIZAR CONTACTO POR ID FLAMINCO</button>
											
											</div>
        							    </td>
    	                                <td>{{$r->id}}</td>
    	                                <td>{{$r->name}}</td>
                                        <td>{{$r->phone}}</td>
                                        <td>
                                        {{$r->intento_pago}}
                                        </td>
                                        <td>{{$r->email}}</td>
                                        <td>{{$r->phone}}</td>
                                        <td> 
                                        @if($r->email_verified_at == null)
                                        NO
                                        @else
                                        SI
                                        @endif
                                        </td>
                                        <td>
                                        @if($r->created_at == null)
                                        -
                                        @else
                                        {{\Carbon\Carbon::parse($r->created_at)->format('d-m-Y')}}
                                        @endif
                                        </td>
                                        
                                        <td>
                                        @if($r->created_at == null)
                                        -
                                        @else
                                        {{\Carbon\Carbon::parse($r->created_at)->format('H:i')}}
                                        @endif
                                        </td>
                                        
                                        <td>
                                        @if($r->created_at == null)
                                        -
                                        @else
                                        {{\Carbon\Carbon::parse($r->created_at)->format('d-m-Y')}}
                                        @endif
                                        </td>
                                        
                                        <td>
                                        @if($r->created_at == null)
                                        -
                                        @else
                                        {{\Carbon\Carbon::parse($r->created_at)->format('H:i')}}
                                        @endif
                                        </td>
                                         <td>
                                        @if($r->confirmed_at == null)
                                        -
                                        @else
                                        {{\Carbon\Carbon::parse($r->confirmed_at)->format('d-m-Y H:i')}}
                                        @endif
                                        </td>
                                        <td>{{$r->profile}}</td>
                                        <td> {{$r->cantidad_login}}</td>
                                        <td> {{\Carbon\Carbon::parse($r->last_login)->format('d-m-Y H:i')}} </td>
                                        <td>
                                        <p>{{$r->profile}}
                                        @if(Auth::user()->id == 1 )
                                        @if($r->sucursal  != 1)
                                        - Casa central
                                        @else 
                                        - Sucursal
                                        @endif
                                        @endif
                                        </p>
                                        </td>
                                      
                                  		<td>
                                  		    
                                  		</td>
        							</tr>
        							@endforeach
									</tbody>
								</table>
								<br>
								{{$data->links()}}
								<br>
							</div>
						</div>
					</div>
					
					
@else
	@include('livewire.users-administrador.agregar-editar-usuario')
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
