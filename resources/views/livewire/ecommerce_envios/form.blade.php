<div wire:ignore.self class="modal fade" id="theModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>{{$nombre_metodo}}</b>
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">
 <div class="row">

   @if($metodo == 1)

   <div class="col-sm-12 col-md-12">
    <div class="form-group">
     <label>Mensaje</label>
       <textarea  class="form-control" style="width:100%;" wire:model.prevent="mensaje_efectivo" rows="8" cols="80">
         Paga en efectivo en el momento de la entrega.
       </textarea>
       @error('banco') <span class="text-danger err">{{ $message }}</span> @enderror
   </div>
   </div>

   @endif


@if($metodo == 2)

<div class="col-sm-12 col-md-12">
 <div class="form-group">
  <label>Elija un banco</label>
    <select wire:model='banco' class="form-control">
      <option value="Elegir" disabled >Elegir</option>
      @foreach($bancos as $b)
        <option value="{{$b->id}}" >{{$b->nombre}}</option>
      @endforeach
    </select>
    @error('banco') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>

<div class="col-sm-12 col-md-12">
 <div class="form-group">
  <label>Mensaje</label>
    <textarea  class="form-control" style="width:100%;" wire:model="mensaje_transferencia" rows="8" cols="80">
    </textarea>
    @error('banco') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>

@endif


@if($metodo == 3)

<div class="col-sm-12 col-md-12">
 <div class="form-group">
  <label>MP Primary key</label>
    <input type="text" wire:model="mp_key" class="form-control">
    @error('mp_key') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>

<div class="col-sm-12 col-md-12">
 <div class="form-group">
  <label>MP Secret key</label>
    <input type="text" wire:model="mp_secret" class="form-control">
    @error('mp_secret') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>

<div class="col-sm-12 col-md-12">
 <div class="form-group">
  <label>Mensaje</label>
    <textarea  class="form-control" style="width:100%;" wire:model="mensaje_transferencia" rows="8" cols="80">
    </textarea>
    @error('banco') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>

@endif



</div>

</div>
     <div class="modal-footer">

       <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

       <button type="button" wire:click.prevent="Update('{{$metodo}}')" class="btn btn-dark close-modal" >GUARDAR</button>



     </div>
   </div>
 </div>
</div>
