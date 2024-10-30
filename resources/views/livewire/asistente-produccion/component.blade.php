<div class="row sales layout-top-spacing">

    <div class="col-sm-12">
        <div class="widget">
    			<ul class="nav nav-tabs  mb-3">
				<li class="nav-item">
						<a class="nav-link" href="{{ url('produccion')}}"  > PRODUCCION ACTUAL </a>
				</li>
				<li class="nav-item">
						<a class="nav-link active" href="{{ url('produccion')}}"  > ASISTENTE DE PRODUCCION </a>
				</li>

			</ul>


            <div class="widget-heading">
                <h4 class="card-title text-center"><b>{{$componentName}}</b></h4>

              <button type="button" class="btn btn-dark" onclick="showHtmlDiv()"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>  FILTROS</button>

              <a  class="btn btn-dark  {{count($data) <1 ? 'disabled' : '' }}"
              href="{{ url('report-produccion/excel')}}" target="_blank">Exportar a Excel</a>


                </div>
                  <div id="html-show" style="display:none;" class="card component-card_1">
                  <div class="card-body">
                    <div class="row">


                   </div>

                   </div>






                      </div>

            <div class="widget-content">
                <div class="row">


                    <div class="col-12 col-md-12">
                        <!--TABLAE-->
                        <div class="table-responsive">
        									<table  class="table table-bordered table-striped  mt-1 ">
        										<thead class="text-white" style="background: #3B3F5C">
        											<tr>
                                <th class="table-th text-center text-white">ID PEDIDO</th>
                                <th class="table-th text-center text-white">FECHA</th>
                                <th class="table-th text-center text-white">CODIGO PROD.</th>
                                <th class="table-th text-center text-white">PRODUCTO</th>
        												<th class="table-th text-center text-white">CANT FALTANTE</th>
                                <th class="table-th text-center text-white">ACCIONES</th>


        											</tr>
        										</thead>
        										<tbody>
        											@if(count($data)<1)
        											<tr><td class="text-center" colspan="10"><h5>Sin resultados</h5></td></tr>
        											@endif
        											@foreach($data as $d)
        											<tr>
                              	<td class="text-center"><strong>{{$d->sale_id}}</strong></td>
        												<td class="text-center"><strong>{{$d->created_at}}</strong></td>
                              	<td class="text-center"><strong>{{$d->product_barcode}}</strong></td>
        												<td class="text-center"><strong>{{$d->product_name}} {{$d->variacion}}</strong></td>
                                <td class="text-center"><strong>{{$d->cantidad}}</strong></td>
                                <td class="text-center">
                                    <button style="min-width:130px;" wire:click.prevent="IniciarProduccion({{$d->id}},{{$d->product_id}} , {{$d->referencia_variacion}}, {{$d->cantidad}})"
                                        class="btn btn-dark mb-2">
                                      INICIAR PRODUCCIÓN
                                    </button>
                                </td>
        										</tr>

        										@endforeach
        									</tbody>
        								</table>




        							</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function showHtmlDiv() {
  var htmlShow = document.getElementById("html-show");
  if (htmlShow.style.display === "none") {
    htmlShow.style.display = "block";
  } else {
    htmlShow.style.display = "none";
  }
}
</script>
<script>
    document.addEventListener('DOMContentLoaded', function(){


        flatpickr(document.getElementsByClassName('flatpickr'),{
            enableTime: false,
            dateFormat: 'Y-m-d',
            locale: {
                firstDayofWeek: 1,
                weekdays: {
                    shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                    longhand: [
                    "Domingo",
                    "Lunes",
                    "Martes",
                    "Miércoles",
                    "Jueves",
                    "Viernes",
                    "Sábado",
                    ],
                },
                months: {
                    shorthand: [
                    "Ene",
                    "Feb",
                    "Mar",
                    "Abr",
                    "May",
                    "Jun",
                    "Jul",
                    "Ago",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dic",
                    ],
                    longhand: [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Septiembre",
                    "Octubre",
                    "Noviembre",
                    "Diciembre",
                    ],
                },

            }

        })


        $('.tagging').select2({
                        tags: true
                    });

              //CLIENTES

                $('#select2-dropdown-cliente').on('change', function(e) {
                  var id = $('#select2-dropdown-cliente').select2('val');
                  var name = $('#select2-dropdown-cliente option:selected').text();
                  @this.set('clientesSelectedName', name);
                  @this.set('ClienteSeleccionado', ''+id);
                  @this.emit('locationUsersSelected', $('#select2-dropdown-cliente').select2('val'));
                });

                //USUARIOS


                $('#select2-dropdown-usuario').on('change', function(e) {
                  var id = $('#select2-dropdown-usuario').select2('val');
                  var name = $('#select2-dropdown-usuario option:selected').text();
                  @this.set('UsuarioSelectedName', name);
                  @this.set('usuarioSeleccionado', ''+id);
                  @this.emit('UsuarioSelected', $('#select2-dropdown-usuario').select2('val'));
                });


                //METODO DE PAGO


                $('#select2-dropdown-metodo-pago').on('change', function(e) {
                  var id = $('#select2-dropdown-metodo-pago').select2('val');
                  var name = $('#select2-dropdown-metodo-pago option:selected').text();
                  @this.set('metodopagoSelectedName', name);
                  @this.set('metodopagoSeleccionado', ''+id);
                  @this.emit('metodopagoSelected', $('#select2-dropdown-metodo-pago').select2('val'));
                });


                //PRODUCTOS


                $('#select2-dropdown-producto').on('change', function(e) {
                  var id = $('#select2-dropdown-producto').select2('val');
                  var name = $('#select2-dropdown-producto option:selected').text();
                  @this.set('productoSelectedName', name);
                  @this.set('productoSeleccionado', ''+id);
                  @this.emit('productoSelected', $('#select2-dropdown-producto').select2('val'));
                });

                //CATEGORIA


                $('#select2-dropdown-categoria').on('change', function(e) {
                  var id = $('#select2-dropdown-categoria').select2('val');
                  var name = $('#select2-dropdown-categoria option:selected').text();
                  @this.set('categoriaSelectedName', name);
                  @this.set('categoriaSeleccionado', ''+id);
                  @this.emit('categoriaSelected', $('#select2-dropdown-categoria').select2('val'));
                });


                //ALMACEN


                $('#select2-dropdown-seccion-almacen').on('change', function(e) {
                var id = $('#select2-dropdown-seccion-almacen').select2('val');
                var name = $('#select2-dropdown-seccion-almacen option:selected').text();
                @this.set('almacenSelectedName', name);
                @this.set('almacenSeleccionado', ''+id);
                @this.emit('almacenSelected', $('#select2-dropdown-seccion-almacen').select2('val'));
                });


                //ESTADOS


                $('#select2-dropdown-estados').on('change', function(e) {
                var id = $('#select2-dropdown-estados').select2('val');
                var name = $('#select2-dropdown-estados option:selected').text();
                @this.set('estadoSelectedName', name);
                @this.set('estadoSeleccionado', ''+id);
                @this.emit('estadoSelected', $('#select2-dropdown-estados').select2('val'));
                });




        //EVENTOS
        window.livewire.on('hide-modal', Msg =>{
            $('#modalDetails').modal('hide')
        })
        window.livewire.on('msg', Msg =>{
            noty(msg)
        })
        window.livewire.on('show-modal', Msg =>{
            $('#modalDetails').modal('show')
        })
        var total = $('#suma_totales').val();
        $('#ver_totales').html('Ventas: '+total);
    })
    window.livewire.on('modal-show', msg => {
      $('#theModal').modal('show')
    });


    function rePrint(saleId)
    {
        window.open("print://" + saleId,  '_self').close()
    }
</script>
