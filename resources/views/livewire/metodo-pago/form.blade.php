 @include('common.modalHead')


 <div class="row">
  <div class="col-sm-12 col-md-8">
   <div class="form-group">
    <label>Nombre</label>
      <input type="text" wire:model.lazy="nombre" class="form-control" placeholder="Ej: Cuenta corriente a 60 dias." >
    @error('nombre') <span class="text-danger er">{{ $message }}</span> @enderror
  </div>
</div>
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Categoría</label>
    <select wire:model='categoria' class="form-control">
      <option value="Elegir" disabled >Elegir</option>
      <option value="1">Efectivo</option>
      <option value="2">Bancos</option>
      <option value="3">Plataformas de pago</option>
    </select>
    @error('categoria') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-8">
 <div class="form-group">
  <label>Recargo</label>
  <div style="margin-bottom: 0 !important;" class="input-group mb-4">

    <input type="text" wire:model.lazy="recargo" class="form-control" placeholder="Ej: 10" >
    <div class="input-group-append">
      <span class="input-group-text input-gp">
        %
      </span>
    </div>
      </div>

  @error('recargo') <span class="text-danger er">{{ $message }}</span> @enderror
</div>
</div>


@if($categoria == 2)
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Banco</label>
    <select wire:model='cuenta' class="form-control">
      <option value="Elegir" disabled >Elegir</option>
      @foreach($bancos as $b)
        <option value="{{$b->id}}" >{{$b->nombre}}</option>
      @endforeach
    </select>
    @error('cuenta') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>
@else

@if($categoria == 3)
<div class="col-sm-12 col-md-4">
 <div class="d-flex form-group">
  <label>Platadorma</label>
    <select wire:model='cuenta' class="form-control">
      <option value="Elegir" disabled >Elegir</option>
      @foreach($plataformas as $p)
        <option value="{{$p->id}}" >{{$p->nombre}}</option>
      @endforeach
    </select>
    @error('cuenta') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>
@else

@endif

@endif

@if(count($sucursales))

<div class="col-sm-12 col-md-12">
  <label for="">Se muestra en las sucursales:</label>
  <div style="border: solid 1px #c8c8c8; padding:10px; border-radius:5px;">
    @foreach($sucursales as $s)
   <div class="form-group">
    <input type="checkbox" wire:model="muestra_sucursales.{{ $s->id }}" ><label style="margin-left: 10px;">{{$s->nombre_sucursal}}</label>
  </div>
  @endforeach
  </div>

</div>

@endif

</div>



@include('common.modalFooter')
