<!-- Modal -->
<div class="modal product-modal  fade" id="product-modal">

    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
          <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <i class="tf-ion-close"></i>
            </button>
            <div class="row">
              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="modal-image">
                  <img class="img-responsive" src="{{ asset('storage/products/' . $image ) }}" alt="{{$name}}" />
                </div>
              </div>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="product-short-details">
                  <h2 class="product-title">{{$name}}</h2>
                  <br>
                  <h5 class="product-price">$ {{$price}}</h5>
                  <br>
                  <p class="product-short-description">

                    Codigo: {{$barcode}}
                  </p>
                  <p class="product-short-description">

                    Disponibles: {{$stock}}
                  </p>

                    <div class="input-group mb-3" style="max-width:176px;">
                      <div class="input-group-prepend">
                        <button onclick="restar()" class="btn btn-dark" style="padding: 9px 16px;" type="button">-</button>
                      </div>
                      <input type="text" id="cantidad" class="form-control text-center" value="1" min="1" aria-label="" aria-describedby="basic-addon1">
                      <div class="input-group-prepend">
                        <button onclick="sumar()" class="btn btn-dark" style="padding: 9px 16px;" type="button">+</button>
                      </div>

                        <input type="text" id="selected_id" value="{{$selected_id}}">




                  </div>
                  <button onclick="Agregar()" style="font-size: 13px;" class="btn btn-dark  btn-sm">Agregar al carrito</button>



                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
</div><!-- /.modal -->
