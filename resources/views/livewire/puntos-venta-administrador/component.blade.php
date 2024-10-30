<div>	  

 @if($agregar == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>Puntos de venta de los usuarios</h4>
							<h6>Ver listado de puntos de venta</h6>
						</div>
						
						<div class="page-btn  d-lg-flex d-sm-block">
						    <a hidden href="javascript:void(0)" class="btn btn-added" wire:click="Agregar"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar usuario</a>
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
									<input type="text" autocomplete="off" wire:model="buscar" placeholder="Buscar nombre , mail , id" class="form-control"	>
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
                                       <td>ID</td>
                                       <td>USUARIO</td>
                                       <td>CASA CENTRAL ID</td>
                                       <td>PERFIL</td>
                                       <th>CUIT</th>
                                       <th>IVA</th>
                                       <th>RELACION PRECIO IVA</th>
                                       <th>HABILITADO AFIP</th>
                                        <th>ACCIONES</th>
										</tr>
									</thead>
									<tbody>
        							@foreach($data as $r)
        							<tr>
    	                                <td>{{$r->id}}</td>
    	                                <td>{{$r->name}}</td>
    	                                <td>{{$r->casa_central_user_id}}</td>
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
                                        <td>{{$r->cuit}}</td>
                                        <td>{{$r->iva_defecto*100}} %</td>
                                        <td>
                                            @if($r->relacion_precio_iva == 0) Sin IVA @endif
                                            @if($r->relacion_precio_iva == 1) Precio + IVA @endif
                                            @if($r->relacion_precio_iva == 2) IVA incluido @endif
                                        </td>
                                        <td>
                                            @if($r->habilitado_afip == 0) no @endif
                                            @if($r->habilitado_afip == 1) si @endif
                                        </td>
                                        <td>
                                  	     		    <a class="me-3" href="javascript:void(0)" wire:click="edit({{$r->id_punto}})" >
        												<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
        											</a>
        											<a hidden href="javascript:void(0)" onclick="Confirm('{{$r->id}}')"  >
        												<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
        											</a>
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
	@include('livewire.puntos-venta-administrador.agregar-editar-usuario')
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
