 @include('common.modalHead')


 <div class="row">
  <div class="col-sm-12 col-md-4">
   <div class="form-group">
    <label>Nombre</label>
      <input type="text" wire:model.lazy="nombre" class="form-control" placeholder="Ej: Alquiler octubre 2021" >
    @error('nombre') <span class="text-danger er">{{ $message }}</span> @enderror
  </div>
</div>

  <div class="col-sm-12 col-md-4">
   <div class="form-group">
    <label>Fecha</label>
      <input type="date" wire:model.lazy="fecha_gasto" class="form-control" >
    @error('fecha_gasto') <span class="text-danger er">{{ $message }}</span> @enderror
  </div>
</div>


<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Categoria</label>
  <select wire:model='categoria' wire:change.defer='ModalCategoria($event.target.value)'  class="form-control">
    <option value="Elegir" disabled >Elegir</option>
    <option value="1" >Sin categoria</option>
    @foreach($gastos_categoria as $gc)
      <option value="{{$gc->id}}" >{{$gc->nombre}}</option>
    @endforeach
    <option value="AGREGAR" style="padding:20px !important; " class="btn btn-dark">+ NUEVA CATEGORIA</option>
  </select>
  @error('categoria') <span class="text-danger err">{{ $message }}</span> @enderror


</div>

</div>

<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Monto</label>
  <div style="margin-bottom: 0 !important;" class="input-group mb-4">
    <div class="input-group-prepend">
      <span class="input-group-text input-gp">
        $
      </span>
    </div>
    <input type="text" wire:model.lazy="monto" class="form-control" placeholder="Ej: 10" >
      </div>
      @error('monto') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Etiquetas</label>
  <select wire:model='etiqueta_form' wire:change.defer='ModalEtiqueta($event.target.value)'  class="form-control">
    <option value="1" >Sin etiqueta</option>
    @foreach($etiquetas as $et)
      <option value="{{$et->id}}" >{{$et->nombre}}</option>
    @endforeach
     <option value="AGREGAR" style="padding:20px !important; " class="btn btn-dark">+ NUEVA ETIQUETA</option>
  </select>
  @error('etiqueta_form') <span class="text-danger err">{{ $message }}</span> @enderror


</div>
</div>
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Metodo de pago</label>

  <select wire:model='metodo_pago_elegido' wire:change.defer='ModalFormaPago($event.target.value)'  class="form-control">
    <option value="Elegir" disabled>Elegir</option>
    <option value="1">Efectivo</option>
    @foreach($metodo_pago as $mp)
      <option value="{{$mp->id}}" >{{$mp->nombre}}</option>
    @endforeach
    <option hidden value="AGREGAR" style="padding:20px !important; " class="btn btn-dark">+ NUEVA FORMA DE PAGO</option>
  </select>
  @error('metodo_pago_elegido') <span class="text-danger err">{{ $message }}</span> @enderror


</div>
</div>

<div class="col-sm-12 col-md-4">
 <div class="form-group">
    @if($caja == null)

    <b style="color:red;"> Sin caja seleccionada. </b>
   
    @else
    <b style="color:green;"> Caja seleccionada: # {{$caja_seleccionada->nro_caja}} </b>
    @endif

</div>         <div style="width:100%;" class="btn-group  mb-4 mr-2">
                   <button type="button" class="btn btn-dark dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                   Seleccionar otra caja
                   <span class="sr-only"><span>
                   </button>
                   <div class="dropdown-menu">
                    @if($caja == null)
                    Abrir caja
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="javascript:void(0);" wire:click.prevent="ModalAbrirCaja()">+ NUEVA CAJA </a>

                    @endif
                      Ultimas cajas
                     <div class="dropdown-divider"></div>
                     @foreach($ultimas_cajas as $uc)
                   <a class="dropdown-item" href="javascript:void(0);" wire:click.prevent="ElegirCaja({{$uc->id}})">Caja # {{$uc->nro_caja}} ( {{\Carbon\Carbon::parse($uc->created_at)->format('d/m/Y')}} )</a>
                    @endforeach
                     <a class="dropdown-item" href="javascript:void(0);" wire:click.prevent="SinCaja()"> SIN CAJA </a>


                   <div class="dropdown-divider"></div>
                   Elegir caja por fecha
                   <div class="dropdown-divider"></div>
                      <input type="date" wire:change="CambioCaja()" wire:model="fecha_ap"  class="form-control " >
                   
                   </div>
                   </div>

                  </div>




</div>



@include('common.modalFooter')
