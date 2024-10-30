<!-- Modal -->
<div class="modal product-modal  fade" id="variaciones">

    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
          <div class="modal-body" id="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
            <div class="row">
              <div style="width:100% !important;" class="col-md-6 col-sm-6 col-xs-12">
                <div class="modal-image">
                  <img style="width:100%;" class="img-responsive" src="{{ asset('storage/products/' . $image ) }}" alt="{{$name}}" />
                </div>
              </div>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="product-short-details">
                  <h2 class="product-title">Nombre: {{$name}}</h2>
                  <br>
                  @if($estado_variacion == 1)
                  <h5 class="product-price">$ {{$price}}</h5>
                  @endif
                  <br>
                    @if($descripcion != null)
                  <p class="product-short-description">
                    {{$descripcion}}
                  </p>
                    @endif
                    <p class="product-short-description">


                      <b>Codigo:</b> {{$barcode}}
                    </p>
                  <p class="product-short-description">
                   
                   @foreach($atributos_form as $a)
                   
                   <label>{{$a->nombre_atributo}}</label>
                   <select class="form-control" wire:model.defer="variacion_elegida.{{$a->atributo_id}}">
                       <option value="Elegir" >Elegir</option>
                       @foreach($variaciones_form as $v)
                       
                       @if($v->atributo_id == $a->atributo_id)
                       
                       <option value="{{$v->variacion_id}}">{{$v->nombre_variacion}}</option>
                       @endif
                       
                       @endforeach
                       
                   </select>
                   @endforeach
                   
                   @if($estado_variacion == 1)
                    <b>Disponibles:</b> {{ $stock }} <br>
                    <p id="added"></p>
                    @endif
                   
                    @foreach ($cart_ecommerce->getContent() as $product)
                    @if($barcode == $product['barcode'])
                    <input hidden type="text" id="agregado" value="{{$product['qty']}}">
                    @endif
                    @endforeach

                    <input hidden type="text" id="stock" value="{{$stock}}">
                    <input type="hidden" id="stock_descubierto" value="{{$stock_descubierto}}">

                  </p>

                     @if($estado_variacion == 1)
                    <div class="input-group mb-3" style="max-width:176px;">
                      <div class="input-group-prepend">
                        <button onclick="restar()" class="btn btn-dark" style="padding: 9px 16px;" type="button">-</button>
                      </div>
                      <input readonly
                      style="background:white;" type="text" id="cantidad" class="form-control text-center" value="1" min="1" aria-label="" aria-describedby="basic-addon1">
                      <div class="input-group-prepend">
                        <button onclick="sumar()" class="btn btn-dark" style="padding: 9px 16px;" type="button">+</button>
                      </div>

                        <input hidden type="text" id="selected_id" value="{{$selected_id}}">




                  </div>
                  @endif

                @if($estado_variacion == 1)
                  @if($stock < 1)
                  <button style="font-size: 13px;" class="btn btn-dark  btn-sm disabled">Agregar al carrito</button>
                  @else
                  <button onclick="Agregar()" id="boton_add" style="font-size: 13px;" class="btn btn-dark  btn-sm ">Agregar al carrito</button>
                  @endif
                  @else
                   <button style="font-size: 13px;" class="btn btn-dark  btn-sm" wire:click="BuscarVariacion">Buscar variacion</button>
                  @endif



                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
</div><!-- /.modal -->
