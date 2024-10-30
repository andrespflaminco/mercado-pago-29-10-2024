
<div style="z-index: 99999 !important" wire:ignore.self class="modal fade" id="AgregarOrigen" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <b>ELEGIR SUCURSAL DE ORIGEN</b>

              </div>
              <div style="margin: 0 auto !important;" class="modal-body">


                  <button style="width:100%;" type="button" class="btn btn-dark" wire:click="ElegirSucursal(1)" name="button">Casa central</button> <br>
                  @foreach($sucursales as $s)
                  <button style="width:100%;" type="button" class="btn btn-dark" wire:click="ElegirSucursal({{$s->sucursal_id}})" name="button">{{$s->name}}</button> <br>
                  @endforeach
              <br>



              </div>

          </div>
      </div>
  </div>
