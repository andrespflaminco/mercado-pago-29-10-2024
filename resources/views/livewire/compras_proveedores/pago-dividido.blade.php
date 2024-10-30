<<head>
  <style media="screen">
  @media (min-width: 576px) {
.modal-body {
  margin: 0 auto !important;
}
}
  </style>
</head>
<div wire:ignore.self class="modal fade" id="PagoDividido" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Pago dividido</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div class="modal-body">
                <br>
                <label>Metodo de pago nro 1</label>
                <div class="row">
                  <br>
                  <div class="col-sm-12 col-md-6">
                 <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>
                   <input autocomplete="off" type="text" id="title"   min="0" type="number" id="cash"
                    onchange="cambio();"
                    onkeyup="cambio();"
                    wire:change='cambioDiv1($event.target.value)'
                    wire:keydown.enter="saveSale"
                    class="form-control text-center" value="${{number_format($efectivo1,2)}}"
                    wire:model.lazy="monto_ap_div" class="form-control" required="">

                      @error('monto_ap_div') <span class="text-danger er">{{ $message }}</span> @enderror

               </div>
               </div>
               <br>
                 <div class="col-sm-12 col-md-6">
                  <div class="form-group">

                   <select wire:model='metodo_pago_ap_div1'  wire:change='cambioMetodoDiv1($event.target.value)' class="form-control">
                     <option value="Elegir" disabled >Elegir</option>
                     <option value="1" >Efectivo</option>
                     @foreach($metodos as $mp)
                     <option value="{{$mp->id}}">
                            @if($mp->nombre_banco)
                       {{$mp->nombre_banco}} -
                       @endif
                       {{$mp->nombre}}
                     </option>
                     @endforeach
                   </select>
                   @error('metodo_pago_ap_div1') <span class="text-danger er">{{ $message }}</span> @enderror
                 </div>


                 </div>


              </div>
               <br>
               <label>Metodo de pago nro 2</label><br>
               <div class="row">

                 <div class="col-sm-12 col-md-6">
                <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                  <div class="input-group-append">
                    <span class="input-group-text input-gp">
                      $
                    </span>
                  </div>
                  <input autocomplete="off" type="text" id="title"
                  min="0" type="number" id="cash"
                 onchange="cambio();"
                 onkeyup="cambio();"
                 wire:change='cambioDiv2($event.target.value)'
                 wire:keydown.enter="saveSale"
                 class="form-control text-center" value="${{number_format($efectivo2,2)}}"
                  wire:model.lazy="monto_ap_div2" class="form-control" required="">



              </div>
                @error('monto_ap_div2') <span class="text-danger er">{{ $message }}</span> @enderror
              </div>
                <div class="col-sm-12 col-md-6">
                 <div class="form-group">
                  <select wire:model.lazy='metodo_pago_ap_div2'  wire:change='cambioMetodoDiv2($event.target.value)'  class="form-control">
                    <option value="Elegir" disabled >Elegir</option>
                    <option value="1" >Efectivo</option>
                    @foreach($metodos as $mp)
                    <option value="{{$mp->id}}">
                           @if($mp->nombre_banco)
                       {{$mp->nombre_banco}} -
                       @endif
                       {{$mp->nombre}}
                    </option>
                    @endforeach
                  </select>
                  @error('metodo_pago_ap_div2') <span class="text-danger er">{{ $message }}</span> @enderror
                  
                </div>
                </div>


             </div>
             <h6 class="text-muted">Subtotal: ${{number_format($efectivo1+$efectivo2,2)}}</h6>
             <p>Recargo: $ {{$recargo_div1 + $recargo_div2}}</p>

             <h6 class="text-muted">A cobrar: ${{number_format($efectivo1 + $recargo_div1 + $efectivo2 + $recargo_div2 ,2)}}</h6>


              </div>
              <div class="modal-footer">
                <br>
                <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

                <button type="button" wire:click.prevent="guardarPagoDividido()" class="btn btn-dark close-modal" >GUARDAR</button>
              </div>
          </div>
      </div>
  </div>
