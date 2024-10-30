
<div wire:ignore.self class="modal fade" id="modalDetails2" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="margin: 1.75rem auto; max-width: 300px !important;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Hojas de ruta</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div style="    margin: auto;" class="modal-body">
                <br>
                <input hidden type="text" wire:model.lazy="id_pedido_hr" value="{{$id_pedido_hr}}">

                @foreach ($listado_hojas_ruta as $lh)
                  <button wire:click.prevent="HojaRutaElegida({{$lh->id}})" style="min-width:200px;" class="btn btn-warning mb-2">{{\Carbon\Carbon::parse($lh->fecha)->format('d-m-Y')}}
                    @if($lh->turno)
                     ({{$lh->turno}})
                   @else

                 @endif

               </button><br>

                @endforeach
                <button wire:click.prevent="HojaRutaElegida(0)" style="min-width:200px;" class="btn btn-light mb-2"> Sin asignar </button><br>
              <!-- Warning -->




              </div>
              <div class="modal-footer">
                <br>
              </div>
          </div>
      </div>
  </div>
