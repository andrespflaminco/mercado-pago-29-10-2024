<div class="row sales layout-top-spacing">

    <div class="col-sm-12">
        <div class="widget">
            <div class="widget-heading">
                <h4 class="card-title text-center"><b>{{$componentName}}</b></h4>


              <button type="button" class="btn btn-dark" onclick="showHtmlDiv()"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>  FILTROS</button>

              <a  class="btn btn-dark  {{count($data_reportes) <1 ? 'disabled' : '' }}"
              href="{{ url('report/excel' . '/' . ($usuarioSeleccionado == '' ? '0' : $usuarioSeleccionado) . '/' . ($ClienteSeleccionado == '' ? '0' : $ClienteSeleccionado)  .  '/' . ($estado_pago == '' ? '0' : ($estado_pago == 'Pago' ? '1' : '2')) . '/'. ($EstadoSeleccionado == '' ? '0' : $EstadoSeleccionado) . '/' . ($MetodoPagoSeleccionado == '' ? '0' : $MetodoPagoSeleccionado) . '/'. uniqid() .'/'  . $dateFrom . '/' . $dateTo) }}" target="_blank">Exportar a Excel</a>
                </div>
                  @include('livewire.gastos.estado-pedido-pos')
                  @include('livewire.reports.agregar-pago')
                  @include('livewire.reports.form-hoja-ruta')
                  @include('livewire.reports.form-hoja-ruta-nueva')
                  @include('livewire.factura.form-pagos')

                  @include('livewire.reports.sales-detail2')
                  @include('livewire.reports.sales-detail3')



                <input hidden type="text" id="input" value="1">
                  <div id="html-show" style="{{$estado}}" class="card component-card_1">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-sm-12 col-md-4">
                       <div class="form-group">
                        <label>Id de pedido</label>


                          <input class="form-control" type="text" wire:model="search">
                      </div>
                      </div>
                      <div class="col-sm-12 col-md-4">
                       <div class="form-group">
                        <label>Cliente</label>
                        <div wire:ignore>

                            <select class="form-control tagging" multiple="multiple" id="select2-dropdown">
                              <option value="1">Consumidor final</option>
                              @foreach($clientes as $client)
                              <option value="{{$client->id}}">{{$client->nombre}}</option>
                              @endforeach
                          </select>
                      </div>
                      </div>
                      </div>

                      <div class="col-sm-12 col-md-4">
                       <div class="form-group">
                        <label>Vendedor</label>
                        <div wire:ignore>

                            <select class="form-control tagging" multiple="multiple" id="select2-dropdown2">
                              @foreach($users as $u)
                              <option value="{{$u->id}}">{{$u->name}}</option>
                              @endforeach
                          </select>
                      </div>
                      </div>
                      </div>

                      <div class="col-sm-12 col-md-4">
                       <div class="form-group">
                        <label>Estado de pedido</label>
                        <div wire:ignore>

                            <select class="form-control tagging" multiple="multiple" id="select2-dropdown3">
                              <option value="Pendiente">Pendiente</option>
                              <option value="En proceso">En proceso</option>
                              <option value="Entregado">Entregado</option>
                              <option value="Cancelado">Cancelado</option>
                          </select>
                      </div>
                      </div>
                      </div>

                      <div class="col-sm-12 col-md-4">
                         <div class="form-group">
                          <label>Metodo de pago</label>
                          <div wire:ignore>

                              <select class="form-control tagging" multiple="multiple" id="select2-dropdown-metodo-pago">
                                <option value="1">Efectivo</option>
                                @foreach($metodo_pago as $mp)
                                <option value="{{$mp->id}}">{{$mp->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        </div>
                        </div>

                      <div class="col-sm-12 col-md-4">
                       <div class="form-group">
                        <label>Estado de pago</label>

                            <select wire:model.lazy="estado_pago" class="form-control" >
                              <option value="">Todos</option>
                              <option value="Pendiente">Pendiente</option>
                              <option value="Pago">Pago</option>

                          </select>
                      </div>
                      </div>





                      <div class="col-sm-12 col-md-4">
                       <div class="form-group">
                        <label>Fecha desde</label>
                        <input type="text" wire:model="dateFrom" class="form-control flatpickr" placeholder="Click para elegir">

                      </div>
                      </div>

                      <div class="col-sm-12 col-md-4">
                       <div class="form-group">
                        <label>Fecha hasta</label>
                        <input type="text" wire:model="dateTo" class="form-control flatpickr" placeholder="Click para elegir">

                      </div>
                      </div>

                   </div>

                   </div>






                      </div>
                      <div class="card component-card_1">
                      <div class="card-body">
                        <div class="row">
              <div class="col-sm-12 col-md-3">
                 <div class="form-group">

                     <h5>Ventas totales: $ {{number_format($this->suma_totales+$this->recargos_totales,2)}}</h5>


                  </div>
              </div>


              <div class="col-sm-12 col-md-3">
               <div class="form-group">

                 <h5>Cant. tickets: {{$this->cantidad_tickets}}</h5>


               </div>
             </div>

              <div class="col-sm-12 col-md-3">
               <div class="form-group">

                 <h5>Ticket prom: $ {{number_format($this->ticket_promedio,2)}}</h5>


               </div>
             </div>
             <div class="col-sm-12 col-md-3">
              <div class="form-group">

                <?php $sum = 0; ?>
                @foreach($data_reportes as $d)
                @if ($d->deuda > 0)
                <?php $sum += $d->deuda; ?>
                @endif
                @endforeach
                <h5>A cobrar: $ {{number_format($sum,2)}}</h5>




              </div>
            </div>
                  </div>
                    </div>
                    </div>

            <div class="widget-content">
                <div class="row">


                    <div class="col-12 col-md-12">
                        <!--TABLAE-->
                         @if(session('status'))
                                            <strong style="padding: 5px 5px 5px 5px !important; border-radius: 3px; margin-right: 15px!important; color:#e2a03f !important" >{{ session('status') }}</strong>
                                            @endif
                        <div class="table-responsive">
                            <table class="table table-bordered table striped mt-1">
                                <thead class="text-white" style="background: #3B3F5C">
                                    <tr>
                                        <th class="table-th text-white text-center">ID PEDIDO</th>
                                        <th class="table-th text-white text-center">FECHA</th>
                                        <th class="table-th text-white text-center">CLIENTE</th>
                                        <th class="table-th text-white text-center">TOTAL</th>
                                        <th class="table-th text-white text-center">FORMA DE PAGO</th>
                                        <th class="table-th text-white text-center">FACTURA</th>
                                        <th style="width: 10%;" class="table-th text-white text-center">A COBRAR</th>
                                        <th class="table-th text-white text-center">ESTADO DEL PEDIDO</th>
                                        <th class="table-th text-white text-center" >ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($data_reportes) <1)
                                    <tr><td colspan="7"><h5>Sin Resultados</h5></td></tr>
                                    @endif
                                    @foreach($data_reportes as $d)
                                    <?php $sum += $d->total; ?>
                                    <tr>

                                        <td class="text-center"><h6>{{$d->id}}</h6></td>
                                        <td class="text-center">
                                            <h6>
                                                {{\Carbon\Carbon::parse($d->created_at)->format('d-m-Y H:i')}}
                                            </h6>
                                        </td>
                                        <td class="text-center"><h6>{{$d->nombre_cliente}} </h6></td>
                                        <td class="text-center"><h6>${{number_format($d->total+$d->recargo-$d->descuento,2)}}</h6></td>
                                        <td class="text-center"><h6>{{$d->nombre_metodo_pago}}</h6></td>
                                        <td class="text-center"><h6>
                                          @if($d->nro_factura)
                                          <?php
                                          $porciones = explode("-", $d->nro_factura);
                                          $tipo_factura = $porciones[0]; // porción1
                                          $pto_venta = $porciones[1]; // porción2
                                          $nro_factura_ = $porciones[2]; // porción2
                                          echo $tipo_factura."-".str_pad($pto_venta, 3, "0", STR_PAD_LEFT)."-".str_pad($nro_factura_, 5, "0", STR_PAD_LEFT); ?>
                                          @else
                                          -
                                          @endif
                                        </h6></td>

                                        <td class="text-center"><h6>
                                        @if($d->deuda > 0)
                                        $ {{$d->deuda}}
                                        @else
                                        -
                                        @endif
                                        </h6>
                                        </td>
                                        <td class="text-center">
                                        @if($d->status == 'Pendiente')
                                          <button onclick="cambiar()" style="   min-width: 130px; margin-bottom: 0 !important;  margin-top: -2px !important;  margin-right: 15px;  padding: 3px !important;" wire:click.prevent="getDetails2({{$d->id}})"
                                            class="btn btn-warning mb-2">
                                            {{$d->status}}
                                        </button>
                                        @endif
                                        @if($d->status == 'Entregado')
                                          <button onclick="cambiar()"  style="    min-width: 130px; margin-bottom: 0 !important;  margin-top: -2px !important;  margin-right: 15px;  padding: 3px !important;" wire:click.prevent="getDetails2({{$d->id}})"
                                            class="btn btn-success mb-2">
                                            {{$d->status}}
                                        </button>
                                        @endif
                                        @if($d->status == 'Cancelado')
                                          <button onclick="cambiar()"  style="    min-width: 130px; margin-bottom: 0 !important;  margin-top: -2px !important;  margin-right: 15px;  padding: 3px !important;" wire:click.prevent="getDetails2({{$d->id}})"
                                            class="btn btn-danger mb-2">
                                            {{$d->status}}
                                        </button>
                                        @endif
                                        @if($d->status == 'En proceso')
                                          <button onclick="cambiar()"  style="    min-width: 130px; margin-bottom: 0 !important;  margin-top: -2px !important;  margin-right: 15px;  padding: 3px !important;" wire:click.prevent="getDetails2({{$d->id}})"
                                            class="btn btn-secondary mb-2">
                                            {{$d->status}}
                                        </button>
                                        @endif
                                       </td>




                                        <td class="text-center" >
                                              <div class="btn-group mb-1 mr-1" role="group">
                                                  <button id="btndefault" type="button" class="btn btn-dark btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></button>
                                                  <div class="dropdown-menu" aria-labelledby="btndefault">
                                                    <a href="javascript:void(0);" wire:click.prevent="RenderFactura({{$d->id}})" class="dropdown-item"><i class="flaticon-dots mr-1"></i>  Ver </a>
                                                      @if($d->nro_factura > 0)

                                                      @else
                                                      <a href="javascript:void(0);" class="dropdown-item" onclick="ConfirmFactura('{{$d->id}}')"><i class="flaticon-home-fill-1 mr-1"></i>Facturar</a>
                                                      @endif

                                                      <a href="javascript:void(0);" href="{{ url('report-email/pdf' . '/' . $d->id  . '/' . $d->email) }}" target="_blank" class="dropdown-item"><i class="flaticon-dots mr-1"></i>Enviar por mail</a>
                                                      <a href="javascript:void(0);" href="{{ url('report-factura/pdf' . '/' . $d->id) }}" target="_blank"  class="dropdown-item"><i class="flaticon-dots mr-1"></i>  Imprimir </a>
                                                  </div>
                                              </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{$data_reportes->links()}}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('livewire.reports.form')


    @include('livewire.reports.sales-detail')





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

                $('#select2-dropdown').on('change', function(e) {
                  var id = $('#select2-dropdown').select2('val');
                  var name = $('#select2-dropdown option:selected').text();
                  @this.set('clientesSelectedName', name);
                  @this.set('ClienteSeleccionado', ''+id);
                  @this.emit('locationUsersSelected', $('#select2-dropdown').select2('val'));
                });

                $('#select2-dropdown2').on('change', function(e) {
                  var id = $('#select2-dropdown2').select2('val');
                  var name = $('#select2-dropdown2 option:selected').text();
                  @this.set('UsuarioSelectedName', name);
                  @this.set('usuarioSeleccionado', ''+id);
                  @this.emit('UsuarioSelected', $('#select2-dropdown2').select2('val'));
                });

                $('#select2-dropdown3').on('change', function(e) {
                  var id = $('#select2-dropdown3').select2('val');
                  var name = $('#select2-dropdown3 option:selected').text();
                  @this.set('EstadoSelectedName', name);
                  @this.set('EstadoSeleccionado', ''+id);
                  @this.emit('EstadoSelected', $('#select2-dropdown3').select2('val'));
                });


                $('#select2-dropdown-metodo-pago').on('change', function(e) {
                  var id = $('#select2-dropdown-metodo-pago').select2('val');
                  var name = $('#select2-dropdown-metodo-pago option:selected').text();
                  @this.set('MetodoPagoSelectedName', name);
                  @this.set('MetodoPagoSeleccionado', ''+id);
                  @this.emit('MetodoPagoSelected', $('#select2-dropdown-metodo-pago').select2('val'));
                });




        //eventos
        window.livewire.on('show-modal', Msg =>{
            $('#modalDetails').modal('show')
        })

        window.livewire.on('agregar-pago', Msg =>{
            $('#AgregarPago').modal('show')
        })

        window.livewire.on('agregar-pago-hide', Msg =>{
            $('#AgregarPago').modal('hide')
        })

        window.livewire.on('show-modal2', Msg =>{
            $('#modalDetails2').modal('show')
        })

        window.livewire.on('hide-modal2', Msg =>{
            $('#modalDetails2').modal('hide')
        })

        window.livewire.on('show-modal3', Msg =>{
            $('#modalDetails3').modal('show')
        })

        window.livewire.on('hide-modal3', Msg =>{
            $('#modalDetails3').modal('hide')
        })

        window.livewire.on('cerrar-factura', Msg =>{
            $('#theModal1').modal('hide')
        })

        window.livewire.on('abrir-hr-nueva', Msg =>{
            $('#theModal').modal('show')
        })

        window.livewire.on('modal-hr-hide', Msg =>{
            $('#theModal').modal('hide')
        })

        window.livewire.on('hr-added', Msg => {
          noty(Msg)
        })

        window.livewire.on('modal-estado', Msg =>{
            $('#modalDetails-estado-pedido').modal('show')
        })

        window.livewire.on('modal-estado-hide', Msg =>{
            $('#modalDetails-estado-pedido').modal('hide')
        })

        window.livewire.on('hr-asignada', Msg => {
          noty(Msg)
        })

        window.livewire.on('pago-agregado', Msg => {
          noty(Msg)
        })

        window.livewire.on('pago-actualizado', Msg => {
          noty(Msg)
        })

        window.livewire.on('pago-eliminado', Msg => {
          noty(Msg)
        })

        window.livewire.on('no-stock', Msg => {
    			noty(Msg, 2)
    		})

        var total = $('#suma_totales').val();
        $('#ver_totales').html('Ventas: '+total);

    window.livewire.on('modal-show', msg => {
      $('#theModal1').modal('show')
    });



    function rePrint(saleId)
    {
        window.open("print://" + saleId,  '_self').close()
    }
  });

  function ConfirmFactura(id) {

    swal({
      title: 'CONFIRMAR',
      text: '¿QUIERE FACTURAR LA VENTA #'+id+' ?',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('FacturarVenta', id)
        swal.close()
      }

    })
  }

</script>
