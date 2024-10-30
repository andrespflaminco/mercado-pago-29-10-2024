<div>	  

 @if($agregar == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>Metodos de cobro</h4>
							<h6>Ver el listado de sus metodos de cobro</h6>
						</div>
						<div class="page-btn  d-lg-flex d-sm-block">
					        <a href="javascript:void(0)" class="btn btn-added" wire:click="Agregar"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar metodo</a>
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
									<div hidden class="search-input">
										<a class="btn btn-searchset"><img src="{{ asset('assets/pos/img/icons/search-white.svg') }}" alt="img"></a>
									</div>
								</div>
								<div class="wordset">
									<ul>
									</ul>
								</div>
							</div>
							<!-- /Filter -->
							
							<!-- /Filter -->
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
										<th>METODO DE PAGO</th>
        								<th>TIPO DE CUENTA</th>
        								<th> CREADOR </th>
        								<th> SE MUESTRA EN SUCURSALES </th>
        								<th>RECARGO</th>
        								<th>ACCIONES</th>
										</tr>
									</thead>
									<tbody>
        							@foreach($data as $metodo)
        							<tr>
    	                                	<td>
    	                                	@if($metodo->nombre_banco != null)
    										{{$metodo->nombre_banco}} -
    										@endif
    										{{$metodo->nombre}}
        	                                </td>
            								<td>
    										@if($metodo->categoria == 0)
    										Sin asignar
    										@endif
    
    										@if($metodo->categoria == 1)
    										Efectivo
    										@endif
    
    										@if($metodo->categoria == 2)
    										Bancos
    										@endif
    
    										@if($metodo->categoria == 3)
    										Plataforma de pago
    										@endif
    
    										@if($metodo->categoria == 4)
    										A cobrar
    										@endif    
            								</td>
            								<td>
            								    {{$metodo->creador}}
            								</td>
            								<td>
            							    
										    @foreach($metodos_muestra_sucursales as $ms)
										    
										    @if($ms->metodo_id == $metodo->id)
										    {{$ms->nombre_sucursal}}
										    @endif
										    
										    @endforeach
										
											</td>
            								<td>
            								{{number_format($metodo->recargo,2)}} %    
            								</td>
                                  		<td>
                                  		    @if(auth()->user()->sucursal != 1)
                                  		    <a class="me-3" href="javascript:void(0)" wire:click="Edit({{$metodo->id}})" >
												<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
											</a>
											<a href="javascript:void(0)" onclick="Confirm('{{$metodo->id}}')"  >
												<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
											</a> 
                                  		    @else
                                  		    
                                  		    @if(auth()->user()->id == $metodo->creador_id)
        								    <a class="me-3" href="javascript:void(0)" wire:click="Edit({{$metodo->id}})" >
												<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
											</a>
											<a href="javascript:void(0)" onclick="Confirm('{{$metodo->id}}')"  >
												<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
											</a>
											@else
											Heredado de casa central
											@endif
											
											@endif
										</td
        							</tr>
        							@endforeach
									</tbody>
								</table>
								{{$data->links()}}
							</div>
						</div>
					</div>
					
					
@else
	@include('livewire.metodo-pago.agregar-editar-metodo')
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
        window.livewire.on('msg', Msg => {
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
