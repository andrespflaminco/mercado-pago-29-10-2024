
    <!-- Modal -->
<div wire:ignore.self class="modal fade" id="theModalIngresoRetiro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ingreso o retiro de dinero</h5>
                <button type="button" class="close" wire:click.prevent="CerrarModalIngresoRetiro()" aria-label="Close">
                  x
                </button>
            </div>
            <div style="width: 100% !important;" class="modal-body">

            <div class="row">
                 <div style="padding-top: 10px;" class="col-sm-12 col-md-6">
                 @if($caja_seleccionada == null)

                  <b style="color:red;"> Sin caja asignada </b>
                  <br>
                  <br>
                  @else
                  <b style="color:green;"> Caja seleccionada: # 
                  @if($caja_seleccionada)
                  {{$caja_seleccionada->nro_caja}} 
                  @endif
                  </b>
                  <br>
                  <br>
                  @endif

                  </div>
                  <div class="col-sm-12 col-md-6">

                   <div style="width:100%;" class="btn-group  mb-4 mr-2">
                   <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Seleccionar otra caja</button>
                   <div class="dropdown-menu">

                     <p style="margin-bottom: 0; padding: 8px 8px 0px 8px;">Elegir caja</p>
                     <div class="dropdown-divider"></div>
                     @foreach($ultimas_cajas as $uc)
                   <a class="dropdown-item" href="javascript:void(0);" wire:click.prevent="ElegirCaja({{$uc->id}})">Caja # {{$uc->nro_caja}} ( {{\Carbon\Carbon::parse($uc->fecha_inicio)->format('d/m/Y')}} )</a>
                    @endforeach

                   </div>
                   </div>
                  <br>
                  <br>

                  </div>
                  </div>
                  
            <div class="col-12">
            <label>Tipo de movimiento</label>
            <select class="form-control" wire:model.defer="tipo_ingreso_retiro">
                <option value="Elegir">Elegir</option>
                <option value="Ingreso">Ingreso</option>
                <option value="Retiro">Retiro</option>
            </select>  
             @error('tipo_ingreso_retiro') <span style="color: #dc3545 !important;" class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="col-12">
            <label>Forma de ingreso / retiro del dinero</label>
            <select class="form-control" wire:model="metodo_ingreso_retiro">
                <option value="Elegir">Elegir</option>
                <option value="1">Efectivo</option>
                @foreach($listado_bancos as $lb)
                <option value="{{$lb->id}}">{{$lb->nombre}}</option>
                @endforeach
            </select>  
             @error('metodo_ingreso_retiro') <span style="color: #dc3545 !important;"  class="error">{{ $message }}</span> @enderror
            </div>
           
            <div class="col-12">
            <label>Monto</label>
            <input type="text" autocomplete="off" wire:model.defer="monto_ingreso_retiro" class="form-control" >    
             @error('monto_ingreso_retiro') <span style="color: #dc3545 !important;"  class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="col-12">
            <label>Descripcion</label>
               <textarea style="width: 100%;" rows="3" cols="50" wire:model.defer="descripcion_ingreso_retiro" class="form-control"></textarea>
            @error('descripcion_ingreso_retiro') <span style="color: #dc3545 !important;"  class="error">{{ $message }}</span> @enderror
            </div>
            
            @if($tipo_ingreso_retiro == "Retiro")
           
            <div class="col-12">
            <label>Contraseña del usuario *</label>
            <input type="password" autocomplete="off" wire:model.defer="password_ingreso_retiro" class="form-control" >    
             @error('password_ingreso_retiro') <span style="color: #dc3545 !important;"  class="error">{{ $message }}</span> @enderror
            </div>
           
            @endif
            
            </div>
            
            <div class="modal-footer">
                 <a href="javascript:void(0);" wire:click.prevent="CerrarModalIngresoRetiro()" class="btn btn-cancel">Cerrar</a>
                 @if(0 < $selected_ingreso_retiro)
                 <a wire:click.prevent="UpdateIngresoRetiro()" href="javascript:void(0);" class="btn btn-submit me-2" >Actualizar</a>
                 @else
                 <a wire:click.prevent="StoreIngresoRetiro()" href="javascript:void(0);" class="btn btn-submit me-2" >Aceptar</a> 
                 @endif
            </div>
        </div>
    </div>
</div>