<div>

    <div class="page-header">
        <div class="page-title">
            <h4>Suscripciones Control</h4>
            <h6>Ver listado</h6>
        </div>

    </div>


    <!-- /product list -->
    <div class="card">

        <div class="card-body">
            <div class="table-top">
                <div class="search-set">
                    <div class="search-path">
                        <a class="btn btn-filter" id="filter_search">
                            <img src="{{ asset('assets/pos/img/icons/filter.svg') }}" alt="img">
                            <span><img src="{{ asset('assets/pos/img/icons/closes.svg') }}" alt="img"></span>
                        </a>
                    </div>
                    <input type="text" autocomplete="off" wire:model="search" placeholder="Buscar.." class="form-control">
                    <div hidden class="search-input">
                        <a class="btn btn-searchset"><img src="{{ asset('assets/pos/img/icons/search-white.svg') }}" alt="img"></a>
                    </div>
                </div>

                <div class="wordset">
                    <ul>

                    </ul>
                </div>
            </div>


            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>

                            <th class="text-center">Id usuario</th>
                            <th class="text-center">Usuario</th>
                            <th class="text-center">Suscripcion / Plan MP</th>
                            <th class="text-center">Plan Flaminco</th>
                            <th class="text-center">Monto Mensual</th>
                            <th class="text-center">Users Adicionales</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suscripcionesControl as $suscripcionControl)
                        <tr>
                            <td hidden class="text-center">{{$suscripcionControl->id}}</td>
                            <td class="text-center">
                                {{ ($suscripcionControl->user?$suscripcionControl->user->id:'-') }}
                            </td>
                            <td class="text-center">
                                {{ ($suscripcionControl->user?$suscripcionControl->user->name:'-') }}
                            </td>
                            <td class="text-center">
                                {{$suscripcionControl->suscripcion_id}}
                                <br>
                                {{$suscripcionControl->plan_id}}
                            </td>
                            <td class="text-center">
                                {{ ($suscripcionControl->planFlaminco?$suscripcionControl->planFlaminco->nombre:'-') }}
                                <br>
                                ${{ number_format($suscripcionControl->monto_mensual, 0, ',', '.')  }}
                            </td>
                            <td class="text-center">
                                ${{number_format($suscripcionControl->monto_plan, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                {{$suscripcionControl->users_count }} user/s
                                <br>
                                ${{ number_format($suscripcionControl->users_amount, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                {{$suscripcionControl->action}}
                            </td>
                            <td class="text-center">
                                {{$suscripcionControl->created_at}}
                            </td>

                            <td class="text-center">

                                <a class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$suscripcionControl->id}})">
                                    <img src="{{ asset('assets/pos/img/icons/eye.svg') }}" alt="img">
                                </a>
                                <a href="javascript:void(0)" onclick="Confirm('{{$suscripcionControl->id}}')">
                                    <img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
                                </a>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$suscripcionesControl->links()}}
            </div>
        </div>
    </div>

    <!-- /product list -->


    @include('livewire.suscripciones-control.form')


</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        window.livewire.on('suscripcion-control-added', Msg => {
            $('#theModal').modal('hide')
            noty(Msg)
        })
        window.livewire.on('suscripcion-control-updated', Msg => {
            $('#theModal').modal('hide')
            noty(Msg)
        })
        window.livewire.on('suscripcion-control-deleted', Msg => {
            noty(Msg)
        })
        window.livewire.on('suscripcion-control-exists', Msg => {
            noty(Msg)
        })
        window.livewire.on('suscripcion-control-error', Msg => {
            noty(Msg)
        })
        window.livewire.on('hide-modal', Msg => {
            $('#theModal').modal('hide')
        })
        window.livewire.on('show-modal', Msg => {
            $('#theModal').modal('show')
        })


    });


    function Confirm(id) {

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
            if (result.value) {
                window.livewire.emit('destroy', id)
                swal.close()
            }

        })
    }
</script>