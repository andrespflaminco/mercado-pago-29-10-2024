
    <!-- Modal -->
<div wire:ignore.self class="modal fade" id="theModalResumenIngresoRetiro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 600px !important;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Resumen de ingresos y retiros de la caja</h5>
                <button type="button" class="close" wire:click.prevent="CerrarModalResumenIngresoRetiro()" aria-label="Close">
                  x
                </button>
            </div>
            <div style="width: 100% !important;" class="modal-body">
            <div class="col-12">
            <div class="table-responsive">
			<table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Medio</th>
                            <th>Monto</th>
                            <th>Descripcion</th>
                            <th></th>
                        </tr>
                    </thead>
                        <tbody>
                        @foreach($detalle_ingresos_egresos as $die)
                        <tr>
                            <td>{{\Carbon\Carbon::parse( $die->fecha_pago)->format('d-m-Y')}}</td>
                            <td>{{$die->tipo}}</td>
                            <td>{{$die->nombre_banco}}</td>
                            <td>
                                $ {{$die->monto_ingreso_retiro}}
                            </td>
                            <td>
                            {{$die->descripcion}} 
                            </td>
                            
                            <td>
                            <a href="javascript:void(0)" wire:click="EditIngresoRetiro({{$die->id}})" >
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </a>
                
                            <a href="javascript:void(0)" onclick="ConfirmIngresoRetiro({{$die->id}})" >
                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                            </a>
                                                
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            </div>
            <div class="col-12">
            <a href="javascript:void(0);" wire:click="ModalIngresoRetiro()" >Agregar ingreso o retiro</a>     
            </div>
           
            </div>
            
            <div class="modal-footer">
                 <a href="javascript:void(0);" wire:click.prevent="CerrarModalResumenIngresoRetiro()" class="btn btn-cancel">Cerrar</a>
            </div>
        </div>
    </div>
</div>