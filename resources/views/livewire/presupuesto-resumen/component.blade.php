<div>
    @include('common.form-cliente')

    @if($accion == 0)
        <div class="page-header mt-2" style="width: 100% !important;">
            <div class="page-title">
                <h4>PRESUPUESTO</h4>
                <h6>Listado de presupuestos</h6>
            </div>
            <div class="page-btn">
                <a href="{{ url('presupuesto') }}" class="btn btn-added">
                    <img src="{{ asset('assets/pos/img/icons/plus.svg') }}" alt="img">Agregar nuevo presupuesto
                </a>

            </div>
        </div>


        <!-- /product list -->
        <div class="card">
            <div class="card-body">
                <div class="table-top">
                    <div class="search-set">
                        <div class="search-path">
                            @include('common.boton-filtros')
                        </div>
                        <input type="text" autocomplete="off" wire:model="search" placeholder="Buscar.."
                               class="form-control">
                        <div hidden class="search-input">
                            <a class="btn btn-searchset"><img src="{{ asset('assets/pos/img/icons/search-white.svg') }}"
                                                              alt="img"></a>
                        </div>
                    </div>
                    <div class="wordset">
                        <ul>
                            <li hidden>
                                <a data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img
                                        src="{{ asset('assets/pos/img/icons/pdf.svg') }}" alt="img"></a>
                            </li>
                            <li hidden>
                                <a data-bs-toggle="tooltip"
                                   wire:click="ExportarReporte('{{ ( ($search == '' ? '0' : $search) . '/' . ($proveedor_elegido == '' ? '0' : $proveedor_elegido)  .  '/' . ($estado_pago == '' ? '0' : ($estado_pago == 'Pago' ? '1' : '2')) . '/'  . $dateFrom . '/' . $dateTo) }}')"
                                   data-bs-placement="top" title="excel"><img
                                        src="{{ asset('assets/pos/img/icons/excel.svg') }}" alt="img"></a>
                            </li>
                            <li hidden>
                                <a data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img
                                        src="{{ asset('assets/pos/img/icons/printer.svg') }}" alt="img"></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- /Filter -->
                <div class="card" @if(!$mostrarFiltros) hidden @endif >
                    <div class="card-body pb-0">
                        <div class="row">

                            <div class="col-lg col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Proveedor</label>
                                    <select wire:model='proveedor_elegido' class="form-control">
                                        <option value="Elegir" disabled>Elegir</option>
                                        <option value="0">Todos</option>
                                        <option value="2">Casa central</option>
                                        @foreach($prov as $pr)
                                            <option value="{{$pr->id}}">{{$pr->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Estado de pago</label>

                                    <select wire:model="estado_pago" class="form-control">
                                        <option value="">Todos</option>
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Pago">Pagos</option>

                                    </select>
                                </div>
                            </div>

                            <div class="col-lg col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Rango de fechas</label>
                                    <input type="text" id="date-range-picker" name="date_range"/>
                                </div>
                            </div>


                            <div hidden class="col-lg col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Etiquetas</label>

                                </div>
                            </div>


                            <div class="col-lg-2 col-sm-6 col-12">
                                <div class="form-group">
                                    <label style="margin-top: 28px !important;"></label>
                                    <a class="btn btn-light" wire:click="LimpiarFiltros">LIMPIAR</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Filter -->
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>

                            <th @if(!$columns['id']) style="display: none;" @endif>Nro</th>
                            <th @if(!$columns['created_at']) style="display: none;" @endif>Fecha</th>
                            <th @if(!$columns['nombre_cliente']) style="display: none;" @endif>Cliente</th>
                            <th @if(!$columns['total']) style="display: none;" @endif>Total</th>
                            <th @if(!$columns['items']) style="display: none;" @endif>Cant Items</th>
                            <th @if(!$columns['created_at']) style="display: none;" @endif>Vigencia</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tbody>
                        @foreach($data as $compra)
                            <tr>

                                <td>
                                    {{$compra->id}}
                                </td>
                                <td>
                                    {{\Carbon\Carbon::parse($compra->created_at)->format('d-m-Y')}}
                                </td>

                                <td>
                                    {{$compra->nombre_cliente}}
                                </td>

                                <td>
                                    $ {{$compra->total}}
                                </td>
                                <td>
                                    {{$compra->items}}
                                </td>

                                <td>
                                    {{\Carbon\Carbon::parse($compra->created_at)->add($compra->vigencia, 'days')->format('d/m/Y')}}
                                </td>
                                <td>
                                    @switch($compra->estado)
                                        @case(0)
                                            <span style="cursor:pointer;" class="badges bg-lightgreen">Vigente</span>
                                            @break
                                        @case(1)
                                            <span style="cursor:pointer;" class="badges bg-danger">Vencido</span>
                                            @break
                                        @case(2)
                                            <span style="cursor:pointer;" class="badges bg-info">Concretado</span>
                                    @endswitch
                                </td>
                                <td class="text-center">

                                    <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                    </button>
                                    <div style="z-index:99999999999999999 !important;" class="dropdown-menu">
                                        <a href="javascript:void(0);"
                                           wire:click.prevent="RenderFactura({{$compra->id}})" class="dropdown-item"><i
                                                class="flaticon-dots mr-1"></i> Ver </a>
                                    </div>

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
                <br>
                {{$data->links()}}
            </div>
        </div>
        <!-- /product list -->
    @endif


    @if($accion == 1)
        @include('livewire.presupuesto-resumen.ver-presupuesto')
    @endif


</div>

<script src="{{ asset('js/keypress.js') }}"></script>
<script src="{{ asset('js/onscan.js') }}"></script>
<script>

    try {

        onScan.attachTo(document, {
            suffixKeyCodes: [13],
            onScan: function (barcode) {
                console.log(barcode)
                window.livewire.emit('scan-code', barcode)
            },
            onScanError: function (e) {
                //console.log(e)
            }
        })

        console.log('Scanner ready!')


    } catch (e) {
        console.log('Error de lectura: ', e)
    }


</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        livewire.on('scan-code', action => {
            $('#code').val('')
        })
        window.livewire.on('sale-ok', Msg => {
            $('#theModal2').modal('hide')
            noty(Msg)
        })

        window.livewire.on('variacion-elegir', Msg => {
            $('#Variaciones').modal('show')
        })

        window.livewire.on('variacion-elegir-hide', Msg => {
            $('#Variaciones').modal('hide')
        })


        window.livewire.on('agregar-cliente', Msg => {
            $('#theModal-cliente').modal('show')
        })

        window.livewire.on('cerrar-venta', Msg => {
            $('#theModal-venta').modal('show')
        })

        window.livewire.on('agregar-pago', Msg => {
            $('#AgregarPago').modal('show')
        })


        window.livewire.on('agregar-pago-hide', Msg => {
            $('#AgregarPago').modal('hide')
        })

        window.livewire.on('pago-dividido', Msg => {
            $('#PagoDividido').modal('show')
        })

        window.livewire.on('pago-dividido-hide', Msg => {
            $('#PagoDividido').modal('hide')
        })


        window.livewire.on('hide-modal2', Msg => {
            $('#modalDetails2').modal('hide')
        })

        window.livewire.on('cerrar-factura', Msg => {
            $('#theModal1').modal('hide')
        })

        window.livewire.on('modal-show', msg => {
            $('#theModal1').modal('show')
        })


        window.livewire.on('abrir-hr-nueva', msg => {
            $('#theModal').modal('show')
        })

        window.livewire.on('hide-modal3', Msg => {
            $('#modalDetails3').modal('hide')
        })


        window.livewire.on('modal-hr-hide', Msg => {
            $('#theModal').modal('hide')
        })

        window.livewire.on('hr-added', Msg => {
            noty(Msg)
        })

        window.livewire.on('modal-estado', Msg => {
            $('#modalDetails-estado-pedido').modal('show')
        })

        window.livewire.on('modal-estado-hide', Msg => {
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
        //events
        window.livewire.on('product-added', Msg => {
            $('#theModal').modal('hide')
            noty(Msg)
        })

        window.livewire.on('no-stock', Msg => {
            noty(Msg, 2)
        })

        //eventos
        window.livewire.on('show-modal', Msg => {
            $('#modalDetails').modal('show')
        })

        var total = $('#suma_totales').val();
        $('#ver_totales').html('Ventas: ' + total);


    });
</script>

<script type="text/javascript">


    function ConfirmEliminar(id) {

        swal({
            title: 'CONFIRMAR',
            text: '¿QUIERE ELIMINAR EL PRODUCTO?',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            cancelButtonColor: '#fff',
            confirmButtonColor: '#3B3F5C',
            confirmButtonText: 'Aceptar'
        }).then(function (result) {
            if (result.value) {
                window.livewire.emit('deleteRow', id)
                swal.close()
            }

        })
    }


    function muestra_oculta(id) {

        if (document.getElementById) {
            //se obtiene el id
            var el = document.getElementById(id); //se define la variable "el" igual a nuestro div
            el.style.display = (el.style.display == 'none') ? 'block' : 'none'; //damos un atributo display:none que oculta el div
        }

    }

    window.onload = function () {
        /*hace que se cargue la funci贸n lo que predetermina que div estar谩 oculto hasta llamar a la
        funci贸n nuevamente*/
        muestra_oculta('contenido');/* "contenido_a_mostrar" es el nombre que le dimos al DIV */

    }


</script>

@include('livewire.pos.scripts.shortcuts')
@include('livewire.pos.scripts.events')
@include('livewire.pos.scripts.general')
