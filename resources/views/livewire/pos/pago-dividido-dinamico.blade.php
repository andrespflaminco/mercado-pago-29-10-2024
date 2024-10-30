<div style="{{$style_pago_dividido}}" class="row mb-4">
@if(0 < count($metodos_pago_dividido))
@foreach($metodos_pago_dividido as $index => $metodo_pago)
               
               @if($relacion_precio_iva == 1)
               <div class="row">
                <br>
                <div class="col-sm-12 col-md-3">
                      <label>Monto </label>
                 <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>
                   <input autocomplete="off" type="text" id="title"   min="0" type="number" id="cash"
                    onchange="cambio();"
                    onkeyup="cambio();"
                    wire:change='cambioDiv({{$index}},$event.target.value)'
                    wire:keyup.enter='cambioDiv({{$index}},$event.target.value)'
                    class="form-control text-center" value="${{number_format($metodo_pago['efectivo'],2)}}"
                    wire:model.lazy="monto_ap_div.{{$index}}" class="form-control" required="">

               </div>
               </div>
               <br>
                <div class="col-sm-12 col-md-4">
                  <div class="form-group">
                    <label class="mb-0">Metodo de pago</label>
                   <select wire:model='metodo_pago_ap_div.{{$index}}'  wire:change='cambioMetodoDiv({{$index}},$event.target.value)' class="form-control">
                     <option value="Elegir" disabled >Elegir</option>
                     <option value="1" >Efectivo</option>
                     @foreach($metodos_todos as $mp)
                     <option value="{{$mp->id}}">
                       
                       {{$mp->nombre_banco}} -
                       
                       {{$mp->nombre}}
                     </option>
                     @endforeach
                   </select>
                   @if(0 < $recargo_div[$index]) <text style="color:red;">Recargo {{$recargo_div[$index]}} % @if($relacion_precio_iva == 1) + IVA @endif</text> @endif
                 </div>


                 </div>
                <div class="col-sm-12 col-md-2">
                      <label>IVA</label>
                 <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>
                  <input class="form-control text-center" readonly wire:model="iva_total_dividido.{{$index}}">
               </div>
               
               </div>
                <div class="col-sm-12 col-md-3">
                      <label>A cobrar</label>
                 <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>
                  <input class="form-control text-center" readonly wire:model="a_cobrar.{{$index}}">
                    <div  class="input-group-append">
                     <span style="margin-left: 12px !important;  background-color: white !important; border: none; padding: 0.375rem 0px !important; " class="input-group-text input-gp">
                     <a style="color: #212529 !important; " href="javascript:void(0)" wire:click="quitarMetodoPago({{$index}})"> <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg> </a>
                     </span>
                  </div>
               </div>
               
               </div>
              </div>
             
              
              <div {{$es_sucursal != 1 ? 'hidden' : '' }} class="row">
                  <div class="col-sm-12 col-md-4">
                  <div class="form-group">
                    <label class="mb-0">Metodo de pago sucursal</label>
                   <select wire:model='metodo_pago_sucursal.{{$index}}' wire:change='cambioMetodoDiv({{$index}},$event.target.value)' class="form-control">
                     <option value="Elegir" disabled >Elegir</option>
                     <option value="1" >Efectivo</option>
                     @foreach($metodos_todos as $mp)
                     <option value="{{$mp->id}}">{{$mp->nombre_banco}} - {{$mp->nombre}} </option>
                     @endforeach
                   </select>
                 </div>
                </div>
              </div>
              @else
              <div class="row">
                <br>
                <div class="col-sm-12 col-md-4">
                      <label>Monto </label>
                 <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>
                   <input autocomplete="off" type="text" id="title"   min="0" type="number" id="cash"
                    onchange="cambio();"
                    onkeyup="cambio();"
                    wire:change='cambioDiv({{$index}},$event.target.value)'
                    wire:keyup.enter='cambioDiv({{$index}},$event.target.value)'
                    class="form-control text-center" value="${{number_format($metodo_pago['efectivo'],2)}}"
                    wire:model.lazy="monto_ap_div.{{$index}}" class="form-control" required="">

               </div>
               </div>
               <br>
                <div class="col-sm-12 col-md-4">
                  <div class="form-group">
                    <label class="mb-0">Metodo de pago   </label>
                    <a style="color: black !important;  display: block;  width: 100%;  white-space: nowrap;  overflow: hidden;  text-overflow: ellipsis;
                  padding: .375rem .75rem;
                  font-size: 1rem;
                  font-weight: 400;
                  line-height: 1.5;
                  background-color: #fff;
                  background-clip: padding-box;
                  border: 1px solid #ced4da;
                  -webkit-appearance: none;
                  -moz-appearance: none;
                  appearance: none;
                  border-radius: .25rem;
                  transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;"  href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> {{ $metodo_pago_ap_div_nombre[$index] }}  </a>
                    <div  class="dropdown-menu" style="max-height: 300px; overflow-y: auto;">
                 	<button style="{{ $metodo_pago_ap_div[$index] == 1 ? 'background: #eee;' : '' }}"  wire:click="cambioMetodoDiv('Efectivo', {{$index}}, 1)"   class="dropdown-item">Efectivo</button>
                    @foreach($metodos_todos as $mp)
                	<button style="{{ $metodo_pago_ap_div[$index] == $mp->id ? 'background: #eee;' : '' }}"   wire:click="cambioMetodoDiv('{{$mp->nombre_banco}} - {{$mp->nombre}}',{{$index}},{{$mp->id}})" class="dropdown-item">{{$mp->nombre_banco}} - {{$mp->nombre}}</button>
                	@endforeach
                    </div>
                
                   @if(0 < $recargo_div[$index]) <text style="color:red;">Recargo {{$recargo_div[$index]}} % @if($relacion_precio_iva == 1) + IVA @endif</text> @endif
                 </div>


                 </div>

                <div class="col-sm-12 col-md-4">
                 <label>A cobrar</label>
                 <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>
                  <input class="form-control text-center" readonly wire:model="a_cobrar.{{$index}}">
                   <div  class="input-group-append">
                     <span style="margin-left: 12px !important;  background-color: white !important; border: none; padding: 0.375rem 0px !important; " class="input-group-text input-gp">
                     <a style="color: #212529 !important; " href="javascript:void(0)" wire:click="quitarMetodoPago({{$index}})"> <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg> </a>
                     </span>
                   </div>
                  
               </div>
               </div>

              </div>
              
              <div {{$es_sucursal != 1 ? 'hidden' : '' }} class="row">
                  <div class="col-sm-12 col-md-4">
                  <div class="form-group">
                    <label class="mb-0">Metodo de pago sucursal</label>
                   <select wire:model='metodo_pago_sucursal.{{$index}}' wire:change='cambioMetodoSucursalDiv({{$index}},$event.target.value)' class="form-control">
                     <option value="Elegir" disabled >Elegir</option>
                     <option value="1" >Efectivo</option>
                     @foreach($bancos_sucursal as $mp)
                     <option value="{{$mp->id}}">Cuenta origen: {{$mp->nombre}} </option>
                     @endforeach
                   </select>
                 </div>
                </div>
              </div>
              @endif
@endforeach
@endif

            <div class="row">
                  <br>
                <a href="javascript:void(0)" wire:click="agregarMetodoPago"> + Agregar Método de Pago</a>
                </div>
                
            <div style="border-top: solid 1px #eee; border-bottom: solid 1px #eee; padding:15px;" class="row">
            @if($relacion_precio_iva == 1)
                <div class="col-sm-12 col-md-3">
                 <label> Monto Total </label>
                 <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>
                   <input readonly class="form-control text-center" value="{{$efectivo_pago_dividido}}">
               </div>
               <br>
               <p>Deuda: $ {{number_format($subtotal-$sum_descuento_promo-$sum_descuento - $efectivo_pago_dividido,2,",",".")}}</p>
               </div>
                <div class="col-sm-12 col-md-4">
                    <label> Recargo Total </label>
                <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>
                   <input readonly class="form-control text-center" value="{{$recargo_total}}">
               </div>
                </div>
                <div class="col-sm-12 col-md-2">
                <label> IVA Total </label>
                <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>
                   <input readonly class="form-control text-center" value="{{$iva_pago_dividido_total}}">
               </div>
                </div>
                <div class="col-sm-12 col-md-3">
                 <label> A cobrar Total </label>
                 <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>
                  <input readonly class="form-control text-center" value="{{$a_cobrar_total}}">
               </div>
               
               </div>
            @else
            
                <div class="col-sm-12 col-md-4">
              
                      <label> Monto Total </label>
                 <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>
                   <input readonly class="form-control text-center" value="{{$efectivo_pago_dividido}}">
               </div>
               
                </div>
                <div class="col-sm-12 col-md-4">
              
                </div>
                <div class="col-sm-12 col-md-4">
              
                      <label>A cobrar total</label>
                 <div style="margin-bottom: 0 !important;" class="input-group mb-12">
                   <div class="input-group-append">
                     <span class="input-group-text input-gp">
                       $
                     </span>
                   </div>
                  <input readonly class="form-control text-center" value="{{$a_cobrar_total}}">
               </div>
               <br>
               @if(0 < ($total - $a_cobrar_total))
               <p>Deuda: $ {{number_format($total - $a_cobrar_total,2,",",".")}}</p>
               @else
               <p>Saldo a favor: $ {{number_format( ($total - $a_cobrar_total) * -1,2,",",".")}}</p>
               @endif
               
               
                </div>
            @endif
            </div>
            

</div>