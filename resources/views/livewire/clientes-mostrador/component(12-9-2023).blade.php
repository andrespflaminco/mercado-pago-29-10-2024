<div class="row sales layout-top-spacing">

    <div class="col-sm-12">
        <div class="widget widget-chart-one">
            <div class="widget-heading">
                <h4 class="card-title">
                    <b>{{$componentName}} | {{ $pageTitle }}</b>
                </h4>
                <ul class="tabs tab-pills">
                    <li>
                      <a  class="tabmenu bg-dark"  href="{{ url('import-clientes') }}">Importar clientes</a>
                      <a  class="tabmenu bg-dark"
                      href="{{ url('report/excel-clientes' . '/'. uniqid() ) }}" target="_blank">Exportar a Excel</a>

                        <a href="javascript:void(0)" class="tabmenu bg-dark" data-toggle="modal" data-target="#theModal">Agregar</a>
                    </li>
                </ul>
            </div>
            @include('common.searchbox')


            <div class="widget-content">

                <div class="table-responsive">
                    <table class="table table-bordered table striped mt-1">
                        <thead class="text-white" style="background: #3B3F5C">
                            <tr>
                                <th class="table-th text-white">CLIENTE</th>
                                <th class="table-th text-white text-center">EMAIL</th>
                                <th class="table-th text-white text-center">TELÉFONO</th>
                                <th class="table-th text-white text-center">DIRECCION</th>
                                <th class="table-th text-white text-center">ESTADO</th>
                                <th class="table-th text-white text-center">OBSERVACIONES</th>
                                <th class="table-th text-white text-center">ULTIMA COMPRA</th>
                                <th class="table-th text-white text-center">HISTORIAL DE COMPRA</th>
                                <th class="table-th text-white text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $r)
                            <tr>
                                <td><h6>{{$r->nombre}}</h6></td>
                                <td class="text-center"><h6>{{$r->email}}</h6></td>
                                <td class="text-center"><h6>{{$r->telefono}}</h6></td>
                                <td class="text-center"><h6>{{$r->provincia}},{{$r->localidad}},{{$r->direccion}}</h6></td>
                                <td class="text-center">
                                    <span class="badge {{ $r->status == 'Active' ? 'badge-success' : 'badge-danger' }} text-uppercase">{{$r->status}}</span>
                                </td>
                                <td class="text-center"><h6>{{$r->observaciones}}</h6></td>
                                <td class="text-center"><h6>{{$r->last_sale}}</h6></td>
                                
                                <td class="text-center">
                                <a href="{{ url('historial-cliente/'.$r->id) }}"
                                class="btn btn-dark mtmobile" title="Historial">
                                VER
                                </a>
                                </td>

                                <td hidden class="text-center">
                                 @if($r->image != null)
                                 <img class="card-img-top img-fluid"
                                 src="{{ asset('storage/users/'.$r->image) }}"
                                 >
                                 @endif
                             </td>

                             <td class="text-center">
                                <a href="javascript:void(0)"
                                wire:click="edit({{$r->id}})"
                                class="btn btn-dark mtmobile" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <a href="javascript:void(0)"
                            onclick="Confirm('{{$r->id}}')"
                            class="btn btn-dark" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>


                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{$data->links()}}
    </div>

    <!----- GOOGLE MAPS ----->

    <div hidden class="form-group">
    		<label for="address_address">Address</label>
    		<input type="text" id="address-input" name="address_address" class="form-control map-input">
    		<input type="text" name="address_latitude" id="address-latitude" value="0" />
    		<input type="text" name="address_longitude" id="address-longitude" value="0" />
    </div>
    <div hidden id="address-map-container" style="width:100%;height:400px; ">
    		<div style="width: 100%; height: 100%" id="address-map"></div>
    </div>

</div>


</div>


</div>

@include('livewire.clientes-mostrador.form')
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
