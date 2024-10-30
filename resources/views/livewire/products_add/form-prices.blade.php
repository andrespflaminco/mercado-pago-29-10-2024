<div wire:ignore.self class="modal fade" id="theModalPrices" tabindex="-1" role="dialog" style="overflow-y: auto !important;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>PRECIOS EN LA SUCURSAL</b>
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">


   <div class="row">
<div class="col-sm-12 col-md-12">
  <label>Sucursal</label>
     <select class="form-control" wire:model="sucursal_selected">
       <option value="Elegir" selected>Elegir</option>
       @foreach($sucursales as $s)
      <option value="{{$s->id}}">{{$s->name}}</option>
      @endforeach
    </select>


</div>

@if($sucursal_selected > 0)

<hr>

     <div class="col-sm-12 col-md-4">
      <label>Precio</label>
        <input type="text" wire:model.lazy="price" class="form-control" placeholder="ej: 0.00" >
      @error('price') <span class="text-danger er">{{ $message }}</span> @enderror
     </div>

     <div class="col-sm-12 col-md-4">
      <div class="form-group">
       <label>Stock</label>
         <input type="number" wire:model.lazy="stock" class="form-control" placeholder="ej: 0" >
       @error('stock') <span class="text-danger er">{{ $message }}</span> @enderror
     </div>
     </div>

     <div class="col-sm-12 col-md-4">
      <div class="form-group">
       <label>Inv. minimo</label>
         <input type="number" wire:model.lazy="alerts" class="form-control" placeholder="ej: 10" >
       @error('alerts') <span class="text-danger er">{{ $message }}</span> @enderror
     </div>
     </div>
     <div class="col-sm-12 col-md-4">
      <div class="form-group">
       <label>Maneja stock?</label>
         <select wire:model='stock_descubierto' class="form-control">
           <option value="Elegir" disabled >Elegir</option>
           <option value="si" > Activo </option>
           <option value="no" > Inactivo </option>

         </select>
         @error('stock_descubierto') <span class="text-danger err">{{ $stock_descubierto }}</span> @enderror
     </div>
     </div>
     <div class="col-sm-12 col-md-4">
      <div class="form-group">
       <label>Seccion del almacen</label>
         <select wire:model='almacen' wire:change='ModalAlmacen($event.target.value)' class="form-control">
           <option value="Elegir" disabled >Elegir</option>
           <option value="1" >Sin almacen</option>
           @foreach($almacenes as $a)
           <option value="{{$a->id}}">{{$a->nombre}}</option>
           @endforeach
           <option value="AGREGAR" style="padding:20px !important;" class="btn btn-dark">Agregar Almacen</option>
         </select>
         @error('almacen') <span class="text-danger err">{{ $message }}</span> @enderror
     </div>
     </div>

     @endif


</div>
</div>
     <div class="modal-footer">

       <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

      <button type="button" wire:click.prevent="StorePrecios()" class="btn btn-dark close-modal" >GUARDAR</button>

     </div>
   </div>
 </div>
</div>
