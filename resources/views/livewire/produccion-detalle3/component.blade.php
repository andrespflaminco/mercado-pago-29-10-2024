<div class="row sales layout-top-spacing">

    <div class="col-sm-12">
        <div class="widget">
            <div class="widget-heading">
                <h4 class="card-title text-center"><b>Productos a fabricar</b></h4>
            </div>

            <div class="widget-content">
                <div class="row">
                    <div class="col-sm-12 col-md-3">
                        <div class="row">
                            <div class="col-sm-12">
                                <h6>Estado del producto</h6>
                                <div class="form-group">
                                    <select wire:model="EstadoId" class="form-control">
                                        <option value="0">Todos</option>
                                        @foreach($estados as $est)
                                        <option value="{{$est->id}}">{{$est->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <h6>Elige el tipo de reporte</h6>
                                <div class="form-group">
                                    <select wire:model="reportType" class="form-control">
                                        <option value="0">Ventas del día</option>
                                        <option value="1">Ventas por fecha</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 mt-2">
                                <h6>Fecha desde</h6>
                                <div class="form-group">
                                    <input type="text" wire:model="dateFrom" class="form-control flatpickr" placeholder="Click para elegir">
                                </div>
                            </div>
                            <div class="col-sm-12 mt-2">
                                <h6>Fecha hasta</h6>
                                <div class="form-group">
                                    <input type="text" wire:model="dateTo" class="form-control flatpickr" placeholder="Click para elegir">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <button wire:click="$refresh" class="btn btn-dark btn-block">
                                    Consultar
                                </button>

                                <a  class="btn btn-dark btn-block {{count($data) <1 ? 'disabled' : '' }}"
                                href="{{ url('report-produccion/excel' . '/' . $EstadoId . '/' . $reportType . '/' . $dateFrom . '/' . $dateTo) }}" target="_blank">Exportar a Excel</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-9">
                        <!--TABLAE-->
                        <div class="table-responsive">
                            <table class="table table-bordered table striped mt-1">
                                <thead class="text-white" style="background: #3B3F5C">
                                    <tr>
                                        <th class="table-th text-white text-center">ID VENTA</th>
                                        <th class="table-th text-white text-center">PRODUCTO</th>
                                        <th class="table-th text-white text-center">CANTIDAD</th>
																				<th class="table-th text-white text-center">ALMACEN</th>
                                        <th class="table-th text-white text-center">FECHA DE VENTA</th>
                                        <th class="table-th text-white text-center">FECHA DE ENTREGA</th>
                                        <th class="table-th text-white text-center" >ESTADO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($data) <1)
                                    <tr><td colspan="7"><h5>Sin Resultados</h5></td></tr>
                                    @endif
                                    @foreach($data as $d)
                                    <tr>
                                        <td class="text-center"><h6>{{$d->sale_id}}</h6></td>
																				<td class="text-center"><h6>{{$d->product}}</h6></td>
																				<td class="text-center"><h6>{{$d->quantity}}</h6></td>
                                        <td class="text-center"><h6>{{$d->almacen}}</h6></td>
                                        <td class="text-center">
                                            <h6>
                                                {{\Carbon\Carbon::parse($d->created_at)->format('d-m-Y')}}
                                            </h6>
                                        </td>
                                        <td class="text-center">
                                            <h6>
                                                {{\Carbon\Carbon::parse($d->fecha_entrega)->format('d-m-Y')}}
                                            </h6>
                                        </td>
                                        <td class="text-center">
																					@if($d->id_estado == '1')
                                            <button style="min-width:130px;" wire:click.prevent="getDetails({{$d->id}})"
                                                class="btn btn-warning mb-2">
                                                {{$d->nombre_estado}}
                                            </button>
																						@elseif($d->id_estado == '2')
																						<button style="min-width:130px;" wire:click.prevent="getDetails({{$d->id}})"
																								class="btn btn-secondary  mb-2">
																								{{$d->nombre_estado}}
																						</button>
																						@elseif($d->id_estado == '3')
																						<button style="min-width:130px;" wire:click.prevent="getDetails({{$d->id}})"
																								class="btn btn-success mb-2">
																								{{$d->nombre_estado}}
																						</button>
																						@elseif($d->id_estado == '4')
																						<button style="min-width:130px;" wire:click.prevent="getDetails({{$d->id}})"
																								class="btn btn-dark  mb-2">
																								{{$d->nombre_estado}}
																						</button>
																						@endif

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
    @include('livewire.produccion-detalle.sales-detail')
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


        //eventos
        window.livewire.on('show-modal', Msg =>{
            $('#modalDetails').modal('show')
        })
				window.livewire.on('hide-modal', Msg =>{
						$('#modalDetails').modal('hide')
				})
    })

    function rePrint(saleId)
    {
        window.open("print://" + saleId,  '_self').close()
    }
</script>
