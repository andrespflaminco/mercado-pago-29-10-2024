<div class="row d-lg-none" style="padding: 10px 0px; color: {{$color}} !important; background-color: {{$background_color}} !important;">
    <div style="padding-left: 30px;" class="col-1">
    <a style="color: {{$color}} !important;" type="button" data-dismiss="modal" aria-label="Close" wire:click="resetUI()">
    <svg  style="color: {{$color}} !important;"  xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    </a>
    </div>
    <div class="col" style="font-size: 18px; font-weight: 500; margin-left: 10px; ">{{$name}}</div>
</div>
            
<div class="row">
              <div style="width:100% !important;" class="col-md-6 col-sm-6 col-xs-12">
                <div class="modal-image">
                    @if($image)
                  <img style="width:100%;" class="img-responsive" src="{{ asset('storage/products/' . $image ) }}" alt="{{$name}}" />
                  @else
                    <img src="{{ asset('storage/products/noimg.png') }}" alt="{{$name}}" class="img-responsive">
                    @endif
                </div>
              </div>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <div style="padding: 15px;" class="product-short-details">
                  <br>
                   <p class="product-short-description">


                      <b>Codigo:</b> {{$barcode}}
                    </p>
                    @if(0 < count($atributos_form) )
                  <div style="border:solid 1px #eee;">
                      
                        @foreach($atributos_form as $a)
                   
                   <label>{{$a->nombre_atributo}}</label>
                   <select class="form-control" wire:model.defer="variacion_elegida.{{$a->atributo_id}}" >
                       <option value="Elegir" >Elegir</option>
                       @foreach($variaciones_form as $v)
                       
                       @if($v->atributo_id == $a->atributo_id)
                       
                       <option value="{{$v->variacion_id}}">{{$v->nombre_variacion}}</option>
                       @endif
                       
                       @endforeach
                       
                   </select>
                   @endforeach
                 
                    @if($atributos_form)
                    <br>
                     <button style="font-size: 13px;" class="btn btn-dark  btn-sm" wire:click="BuscarVariacion">Buscar variacion</button>
                     @endif
                 
                  </div>
                  
                  @endif
                  @if($encontrado == 1)
                    
                  <div>
                   @if($estado_variacion == 1)
                  <h5 class="product-price">Precio: $ {{$price}}</h5>
                  @endif
                  <br>
                    @if($descripcion != null)
                  <p class="product-short-description">
                    {{$descripcion}}
                  </p>
                    @endif
                   
                  <p class="product-short-description">
                 
                  
                   
                  @if($estado_variacion == 1)
                    <b>Disponibles:</b> {{ $stock }} <br>
                    <p id="added"></p>
                    @endif
                     @foreach ($cart_ecommerce->getContent() as $product)
                    @if($barcode == $product['barcode'])
                    <input hidden type="text" id="agregado" value="{{$product['qty']}}">
                   <p class="product-short-description">
                       <b> Agregados: </b> {{$product['qty']}}  <br>
                   </p> 
                    @endif
                    @endforeach
                   
                
                  </p>

                    


                  
                 

                  </div>
                   @else
                  
                  <h5> No existe la variacion seleccionada. </h5>
                  @endif
               
               
                 

                </div>
              </div>
            </div>    


