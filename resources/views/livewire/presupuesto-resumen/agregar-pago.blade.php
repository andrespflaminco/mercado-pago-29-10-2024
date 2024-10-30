
<div style="z-index: 99999 !important" wire:ignore.self class="modal fade" id="theModal-venta" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <b>STOCK INSUFICIENTE</b>

              </div>
              <div style="margin: 0 auto !important;" class="modal-body">

                    <div class="table-responsive">
                        <p>Hemos detectado que tiene stock insuficiente en los siguientes productos:</p>
                        <table class="table table-bordered mb-4">
                            <thead>
                                <tr>
                                    <th  class="text-center">PRODUCTO</th>
                                    <th  class="text-center">CANT. PRESUP</th>
                                    <th  class="text-center">STOCK</th>
                                    <th  class="text-center">FALTANTE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detalle_productos as $dp)
                                
                                @if($dp->stock < $dp->cantidad)
                                <tr>
                                    <td class="text-center">{{$dp->nombre}}</td>
                                    <td class="text-center">{{$dp->cantidad}}</td>
                                    <td class="text-center">{{$dp->stock}}</td>
                                    <td class="text-center">{{$dp->cantidad-$dp->stock}}</td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

              </div>
              <div class="modal-footer">
                <br>
                <button type="button" wire:click.prevent="CerrarAgregarPago({{$NroVenta}})" class="btn btn-dark close-btn text-info" data-dismiss="modal">ACEPTAR</button>
                <a hidden class="btn btn-dark close-modal">IMPRIMIR</a>
                <button hidden type="button" wire:click.prevent="CreatePago({{$id_pago}})" class="btn btn-dark close-modal" >GUARDAR</button>
               
              </div>
          </div>
      </div>
  </div>
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

        });
        </script>
