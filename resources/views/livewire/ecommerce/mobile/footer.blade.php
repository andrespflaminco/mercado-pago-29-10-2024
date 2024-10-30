                @if($selected_id == 0 || $selected_id == null)
                <footer class="d-lg-none w-100" style="position: fixed; bottom: 0; background: f9f9f9 !important; border:none; padding:10px !important; ">
                    <a class="btn btn-dark w-100" id="ver-carrito"  href="{{ url('ecart/'.$slug)}}" style="color: {{$color}} !important; background-color: {{$background_color}} !important;">Ver el carrito  </a>
                </footer>
                @else 
                <footer class="d-lg-none w-100" style="position: fixed; bottom: 15px; padding:15px; white !important;">
                <div style="position:fixed: !important;" class="d-flex">
                      <div class="col-6 pl-0">
                          @if($estado_variacion == 1)
                            <div class="input-group mb-3" style="max-width:176px;">
                              <div class="input-group-prepend">
                                <button onclick="restar()" class="btn btn-dark" style="border: 1px solid #ced4da; color: {{$background_color}} !important; background-color: {{$color}} !important; padding: 9px 16px; border-radius: 7px;" type="button">-</button>
                              </div>
                              <input readonly wire:model="cantidad_modal"
                              style="background: white; margin-left: 15px; margin-right: 15px; border-radius: 7px;" type="text" id="cantidad" class="form-control text-center" value="1" min="1" aria-label="" aria-describedby="basic-addon1">
                              <div class="input-group-prepend">
                                <button onclick="Sumar()" class="btn btn-dark" style="border: 1px solid #ced4da; color: {{$background_color}} !important; background-color: {{$color}} !important; padding: 9px 16px; border-radius: 7px;" type="button">+</button>
                              </div>
        
                            <input hidden type="text" id="selected_id" value="{{$selected_id}}">
                           
        
                            <input hidden type="text" id="stock" value="{{$stock}}">
                            <input hidden type="text" id="stock_descubierto" value="{{$stock_descubierto}}">
                            <input hidden type="text" id="referencia_variacion" value="{{$referencia_variacion}}">
        
        
        
        
        
                          </div>
                          @endif                          
                      </div>
                      <div class="col-6 pl-0">
                      @if($estado_variacion == 1)
                      @if($stock < 1)
                      <button style="font-size: 13px; padding: 11px; width: 100%; color: {{$color}} !important; background-color: {{$background_color}} !important;" class="btn btn-dark  btn-sm disabled">Agregar</button>
                      @else
                      <button onclick="Agregar()" id="boton_add" style="font-size: 13px; padding: 11px; width: 100%; color: {{$color}} !important; background-color: {{$background_color}} !important;" class="btn btn-dark  btn-sm ">Agregar</button>
                      @endif
                      @endif                          
                      </div>
                  </div>  
                </footer>
                @endif