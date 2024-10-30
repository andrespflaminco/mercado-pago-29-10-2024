
              <div style="{{$style_pago_dividido}}" class="row">
                <br>
                <div class="row">
                  <br>
                  <div class="col-sm-12 col-md-4">
                      <label>Monto $</label>
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
                    wire:keyup.enter='cambioDiv1($event.target.value)'
                    class="form-control text-center" value="${{number_format($efectivo1,2)}}"
                    wire:model.lazy="monto_ap_div" class="form-control" required="">

                     

               </div>
                @error('monto_ap_div') <span class="text-danger er">{{ $message }}</span> @enderror
               </div>
               <br>
                 <div class="col-sm-12 col-md-4">
                  <div class="form-group">
                    <label class="mb-0">Metodo de pago</label>
                   <select wire:model='metodo_pago_ap_div1'  wire:change='cambioMetodoDiv1($event.target.value)' class="form-control">
                     <option value="Elegir" disabled >Elegir</option>
                     <option value="1" >Efectivo</option>
                     @foreach($metodos_todos as $mp)
                     <option value="{{$mp->id}}">
                       
                       {{$mp->nombre_banco}} -
                       
                       {{$mp->nombre}}
                     </option>
                     @endforeach
                   </select>
                   @error('metodo_pago_ap_div1') <span class="text-danger er">{{ $message }}</span> @enderror
                   
                   @if(0 < $metodo_pago_div1) <text style="color:red;">Recargo {{$metodo_pago_div1}} % @if($relacion_precio_iva == 1) + IVA @endif</text> @endif
                 </div>


                 </div>
                 
                <div class="col-sm-12 col-md-4">
                      <label>A cobrar en este metodo de pago $</label>
                 <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>
                  <input class="form-control text-center" readonly wire:model="a_cobrar_1">
               </div>
               
               </div>


              </div>
               <br>
               <div class="row">

                 <div class="col-sm-12 col-md-4">
                     <label class="mb-0">Monto $</label>
                   
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
                 wire:keyup.enter='cambioDiv2($event.target.value)'
                 class="form-control text-center" value="${{number_format($efectivo2,2)}}"
                  wire:model.lazy="monto_ap_div2" class="form-control" required="">



              </div>
                @error('monto_ap_div2') <span class="text-danger er">{{ $message }}</span> @enderror
              </div>
                <div class="col-sm-12 col-md-4">
                    <label class="mb-0">Metodo de pago</label>
                   
                 <div class="form-group">
                  <select wire:model.lazy='metodo_pago_ap_div2'  wire:change='cambioMetodoDiv2($event.target.value)'  class="form-control">
                    <option value="Elegir" disabled >Elegir</option>
                    <option value="1" >Efectivo</option>
                    @foreach($metodos_todos as $mp)
                    <option value="{{$mp->id}}">
                           
                       {{$mp->nombre_banco}} -
                       
                       {{$mp->nombre}}
                    </option>
                    @endforeach
                  </select>
                  @error('metodo_pago_ap_div2') <span class="text-danger er">{{ $message }}</span> @enderror
                  
                   @if(0 < $metodo_pago_div2) <text style="color:red;">Recargo {{$metodo_pago_div2}} % @if($relacion_precio_iva == 1) + IVA @endif</text> @endif
                </div>
                </div>
                                 
                <div class="col-sm-12 col-md-4">
                <label class="mb-0">A cobrar en este metodo de pago $</label>
                   
                 <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>
                   <input class="form-control text-center" readonly wire:model="a_cobrar_2">
               </div>
               </div>
                
                <div class="col-sm-12 col-md-4"></div>
                <div class="col-sm-12 col-md-4"></div>
                <div class="col-sm-12 col-md-4">Total a cobrar: {{$a_cobrar_total}}</div>
                
             </div>
             
            
             
            </div>
              
