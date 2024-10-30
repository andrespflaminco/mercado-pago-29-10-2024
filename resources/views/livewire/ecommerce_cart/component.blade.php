<main>

@inject('cart_ecommerce', 'App\Services\CartEcommerce')

@include('layouts.theme-ecommerce.header')



@if((new \Jenssegers\Agent\Agent())->isDesktop())
<section class="cart-area">
  <!-- BreadCrumb Start-->
  <section class="breadcrumb-area mt-15">
      <div class="container">
          <div class="row">
              <div class="col-lg-12">
                  <nav aria-label="breadcrumb">
                      <ol class="breadcrumb">
                          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                          <li class="breadcrumb-item active" aria-current="page">carrito</li>
                      </ol>
                  </nav>
                  <h5>Carrito</h5>
              </div>
          </div>
      </div>
  </section>

  <div class="">

  </div>
  <!-- BreadCrumb Start-->
    <div style="margin-top:3%" class="container">

        <div class="rows">


            <div class="cart-items">

                    @if ($cart_ecommerce->getContent()->count() > 0)


                        <div class="d-flex" style="width:100%">

                                <div class="col-3  d-none d-lg-block d-xl-block text-center" style="padding:20px;">Imagen</div>
                                <div class="col-2 text-center" style="padding:20px;">Producto</div>
                                <div class="d-none d-lg-block d-xl-block col-2 text-center" style="padding:20px;">Precio</div>
                                <div class="d-none d-lg-block d-xl-block col-3 text-center" style="padding:20px;">Cantidad</div>
                                <div class="d-none d-lg-block d-xl-block col-2 text-center" style="padding:20px;">Subtotal</div>

                        </div>


                          @foreach ($cart_ecommerce->getContent()->sortByDesc('orderby_id') as $product)
                          <div class="row" style="border-top: 2px solid #EFEFEF; border-bottom: 2px solid #EFEFEF; width:100%">
                              <div class="col-lg-3 col-sm-12 text-center" style="padding:10px;">
                              @if($product['image'])
                              <img src="{{ asset('storage/products/' . $product['image'] ) }}" style="height: 100px !important; border-radius: 10px;">
                              @else
                               <img style="border-radius: 10px; height: 100px !important; border: solid 1px #eee;" src="{{ asset('storage/products/noimg.png') }}" >
                               @endif
                              </div>
                              <div style="padding-top:6%" class="col-lg-2 col-sm-12 text-center"> {{$product['name']}}</div>
                              <div style="padding-top:6%" class="col-lg-2 col-sm-12 text-center">$ {{$product['price']}}</div>
                              <div style="padding-top:5%" class="col-lg-3 col-sm-12 text-center">

                               <div class="product-pricelist-selector-quantity">

                                <div style="max-width: 147px; margin: 0 auto;" class="wan-spinner wan-spinner-4">
                                    <a href="javascript:void(0)" wire:click="Decrecer({{$product['product_id']}} , '{{$product['referencia_variacion']}}')" class="minus">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11.98" height="6.69"
                                            viewBox="0 0 11.98 6.69">
                                            <path id="Arrow" d="M1474.286,26.4l5,5,5-5"
                                                transform="translate(-1473.296 -25.41)" fill="none"
                                                stroke="#989ba7" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="1.4" />
                                        </svg>
                                    </a>
                                    <input type="text" value="{{$product['qty']}}" min="1">
                                    <a href="javascript:void(0)" wire:click="Incrementar({{$product['product_id']}} , '{{$product['referencia_variacion']}}')" class="plus"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="11.98" height="6.69"
                                            viewBox="0 0 11.98 6.69">
                                            <g id="Arrow" transform="translate(10.99 5.7) rotate(180)">
                                                <path id="Arrow-2" data-name="Arrow" d="M1474.286,26.4l5,5,5-5"
                                                    transform="translate(-1474.286 -26.4)" fill="none"
                                                    stroke="#1a2224" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="1.4" />
                                            </g>
                                        </svg></a>
                                </div>
                            </div>

                              </div>
                              <div style="padding-top:6%" class="col-lg-2 col-sm-12 text-center">$ {{$product['price']*$product['qty']}}</div>
                          </div>

                            @endforeach
                        </tbody>

                    </table>



                    @else
                    <div class="item">
                      No hay productos en el carrito
                    </div>
                    @endif

            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div hidden class="apply-coupon">
                    <h6>Aplicar cupon</h6>
                    <form action="#">
                        <div class="form__div">
                            <input type="text" wire:model="cupon" class="form__input" placeholder=" ">
                            <label for="" class="form__label">Codigo del cupon</label>
                        </div>
                        <button class="btn bg-primary" wire:click="BuscarCupon()" type="button">Aplicar cupon</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card-price">
                    <h6>Checkout</h6>
                    <div class="card-price-list d-flex justify-content-between align-items-center">
                        <div class="item">
                            <p>{{ $cart_ecommerce->totalCantidad() }} item</p>
                        </div>
                        <div class="price">
                            <p>$ {{ $cart_ecommerce->totalAmount()  }}</p>
                        </div>

                    </div>
                    <!-- <div class="card-price-list d-flex justify-content-between align-items-center">
                        <div class="item">
                            <p>Shipping Cast</p>
                        </div>
                        <div class="price">
                            <p>$55</p>
                        </div>
                    </div> -->
                    @if($descuento > 0)

                    <div class="card-price-list d-flex justify-content-between align-items-center">
                        <div class="item">
                            <p> - Descuento</p>
                        </div>
                        <div class="price">
                            <p><p>$ {{ $descuento * $cart_ecommerce->totalAmount()  }}</p></p>
                        </div>
                    </div>

                    <div class="card-price-subtotal d-flex justify-content-between align-items-center">
                        <div class="total-text">
                            <p>Total</p>
                        </div>
                        <div class="total-price">
                            <p>$ {{ $cart_ecommerce->totalAmount() - ( $descuento * $cart_ecommerce->totalAmount() ) }}</p>
                        </div>

                    </div>

                    @else
                    <div class="card-price-subtotal d-flex justify-content-between align-items-center">
                        <div class="total-text">
                            <p>Total</p>
                        </div>
                        <div class="total-price">
                            <p>$ {{ $cart_ecommerce->totalAmount() - ( $descuento * $cart_ecommerce->totalAmount() ) }}</p>
                        </div>

                    </div>

                    @endif
                    <form action="#">
                    
                    @if($ecommerce->registro == 1)
                    
                    <!------ Si se requiere que el usuario se registre ---->
                    
                      @if (auth()->check())
                      <a href="{{ url('ecommerce-billing/' . $slug) }}" class="btn bg-primary" style="width: 100%;">ir a Finalizar compra</a>

                      @else
                      <a href="{{ url('ecommerce-login/' . $slug) }}" class="btn bg-primary" style="width: 100%;">ir a Finalizar compra</a>

                      @endif
                      
                    @else
                    
                    <!------ Si no se requiere que el usuario se registre ---->
                    
                    <a href="{{ url('ecommerce-billing/' . $slug) }}" class="btn bg-primary" style="width: 100%;">ir a Finalizar compra</a>
                    
                    @endif

                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
<!---- SI ES MOBILE ---->


@if((new \Jenssegers\Agent\Agent())->isMobile())


<section class="cart-area">
  <!-- BreadCrumb Start-->
<div class="row" style="padding: 10px 0px; color: {{$color}} !important; background-color: {{$background_color}} !important;">
    <div style="padding-left: 30px;" class="col-1">
    <a style="color: {{$color}} !important;" href="{{ url('tienda/' . $slug) }}">
    <svg  style="color: {{$color}} !important;"  xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    </a>
    </div>
    <div class="col" style="padding-left:30px; color: {{$color}} !important; background-color: {{$background_color}} !important; text-rendering: optimizeSpeed; line-height: 1.5; font-family: Anton, Impact, 'sans-serif'; font-size: 22px; "> Carrito </div>
</div>
        
  <div class="">

  </div>
  <!-- BreadCrumb Start-->
    <div style="margin-top:3%" class="container">

        <div class="rows">


            <div class="cart-items">

                    @if ($cart_ecommerce->getContent()->count() > 0)


                        <div class="d-flex" style="width:100%">

                        </div>


                          @foreach ($cart_ecommerce->getContent()->sortByDesc('orderby_id') as $product)
                          
                        <div style="padding:0px;" class="col-md-4 productos-list" >
                        <div class="product-item-list">
                            <div class="product-item-image-list">
                               
                                @if($product['image'])
                               <img style="width: 121px !important; height: 100px; border-top-left-radius: 7px; border-bottom-left-radius: 7px;" src="{{ asset('storage/products/' . $product['image'] ) }}" alt="{{$product['name']}}" class="img-fluid">
                                @else
                               <img style="width: 121px !important; height: 100px; border-top-left-radius: 7px; border-bottom-left-radius: 7px;" src="{{ asset('storage/products/noimg.png') }}" alt="{{$product['name']}}" class="img-fluid">
                                @endif
                                </a>
                                <div id="cart-icon" class="cart-icon-list">
                                </div>
                               </div>
                            <div class="product-item-info-list">
                                <div style="padding: 3px 0px 0px 10px;">
                                <a style="cursor:pointer; color: #1A2224 !important; font-weight: 600 !important;" href="javascript:void(0)">{{$product['name']}}</a>
                                <div>
                                <span  >$ {{$product['price']}}</span> <del hidden>$999</del>
                                 </div>
                                
                              <div class="input-group mb-0" style="max-width:120px; margin-top:3%;">
                              <div class="input-group-prepend">
                                <button wire:click="Decrecer({{$product['product_id']}} , '{{$product['referencia_variacion']}}')" class="btn btn-dark" style="font-size:14px; border: 1px solid #ced4da; color: {{$background_color}} !important; background-color: {{$color}} !important; padding: 9px 12px; border-radius: 7px;" type="button">-</button>
                              </div>
                              <input readonly value="{{$product['qty']}}" 
                              style="font-size:14px; background: white; border-radius: 7px;" type="text" id="cantidad" class="form-control text-center" value="1" min="1" aria-label="" aria-describedby="basic-addon1">
                              <div class="input-group-prepend">
                                <button wire:click="Incrementar({{$product['product_id']}} , '{{$product['referencia_variacion']}}')" class="btn btn-dark" style="font-size:14px; border: 1px solid #ced4da; color: {{$background_color}} !important; background-color: {{$color}} !important; padding: 9px 12px; border-radius: 7px;" type="button">+</button>
                              </div>
                              </div>

                                </div>
                               
                                 
                                
                            </div>
                        </div>
                    </div>

                   @endforeach
                </tbody>

                    </table>



                    @else
                    <div class="item">
                      No hay productos en el carrito
                    </div>
                    @endif

            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div hidden class="apply-coupon">
                    <h6>Aplicar cupon</h6>
                    <form action="#">
                        <div class="form__div">
                            <input type="text" wire:model="cupon" class="form__input" placeholder=" ">
                            <label for="" class="form__label">Codigo del cupon</label>
                        </div>
                        <button class="btn bg-primary" wire:click="BuscarCupon()" type="button">Aplicar cupon</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card-price">
                    <h6>Checkout</h6>
                    <div class="card-price-list d-flex justify-content-between align-items-center">
                        <div class="item">
                            <p>{{ $cart_ecommerce->totalCantidad() }} item</p>
                        </div>
                        <div class="price">
                            <p>$ {{ $cart_ecommerce->totalAmount()  }}</p>
                        </div>

                    </div>
                    <!-- <div class="card-price-list d-flex justify-content-between align-items-center">
                        <div class="item">
                            <p>Shipping Cast</p>
                        </div>
                        <div class="price">
                            <p>$55</p>
                        </div>
                    </div> -->
                    @if($descuento > 0)

                    <div class="card-price-list d-flex justify-content-between align-items-center">
                        <div class="item">
                            <p> - Descuento</p>
                        </div>
                        <div class="price">
                            <p><p>$ {{ $descuento * $cart_ecommerce->totalAmount()  }}</p></p>
                        </div>
                    </div>

                    <div class="card-price-subtotal d-flex justify-content-between align-items-center">
                        <div class="total-text">
                            <p>Total</p>
                        </div>
                        <div class="total-price">
                            <p>$ {{ $cart_ecommerce->totalAmount() - ( $descuento * $cart_ecommerce->totalAmount() ) }}</p>
                        </div>

                    </div>

                    @else
                    <div class="card-price-subtotal d-flex justify-content-between align-items-center">
                        <div class="total-text">
                            <p>Total</p>
                        </div>
                        <div class="total-price">
                            <p>$ {{ $cart_ecommerce->totalAmount() - ( $descuento * $cart_ecommerce->totalAmount() ) }}</p>
                        </div>

                    </div>

                    @endif
                    <form action="#">
                     @if($ecommerce->registro == 1)
                     
                      @if (auth()->check())
                      <a href="{{ url('ecommerce-billing/' . $slug) }}" style="color: {{$color}} !important; background-color: {{$background_color}} !important;" class="btn btn-dark w-100" >ir a Finalizar compra</a>

                      @else
                      <a href="{{ url('ecommerce-login/' . $slug) }}" style="color: {{$color}} !important; background-color: {{$background_color}} !important;" class="btn btn-dark w-100" >ir a Finalizar compra</a>

                      @endif
                      
                      @else
                      
                       <a href="{{ url('ecommerce-billing/' . $slug) }}" style="color: {{$color}} !important; background-color: {{$background_color}} !important;" class="btn btn-dark w-100" >ir a Finalizar compra</a>
                       
                       @endif

                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endif

        <script type="text/javascript">
        function   Agregar() {

            var cantidad_agregar = $('#cantidad').val();
            var selected_id = $('#selected_id').val();

            window.livewire.emit('Agregar', cantidad_agregar, selected_id);

            $('#cantidad').val() = 1;
            $('#selected_id').val() = "";

        }

        </script>


        <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {

          window.livewire.on('add', msg => {
            $('#product-modal').modal('show')
          });

          window.livewire.on('product-added', msg => {
            $('#product-modal').modal('hide')

            const toast = swal.mixin({
             toast: true,
             position: 'top-end',
             showConfirmButton: false,
             timer: 3000,
             padding: '2em'
           });

           toast({
             type: 'success',
             title: 'Producto agregado',
             padding: '2em',
           })


          });

          window.livewire.on('cupon-added', msg => {
            $('#product-modal').modal('hide')

            const toast = swal.mixin({
             toast: true,
             position: 'top-end',
             showConfirmButton: false,
             timer: 3000,
             padding: '2em'
           });

           toast({
             type: 'success',
             title: 'Cupon agregado',
             padding: '2em',
           })


          });

        });

        function sumar() {
          var valor = $('#cantidad').val();
          var valor_nuevo = (parseFloat( $('#cantidad').val()) + parseFloat(1) );
          $('#cantidad').val(valor_nuevo);
        }

        function restar() {
          var valor = $('#cantidad').val();
          if(valor != 1) {
          var valor_nuevo = (parseFloat( $('#cantidad').val()) - parseFloat(1) );
        } else {
          var valor_nuevo = 1;
        }
          $('#cantidad').val(valor_nuevo);
        }


        </script>
        </main>
