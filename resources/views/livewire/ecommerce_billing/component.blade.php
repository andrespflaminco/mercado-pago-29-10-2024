

<main>

@inject('cart_ecommerce', 'App\Services\CartEcommerce')

@php

if($tk != null)

{
require base_path('vendor/autoload.php');

MercadoPago\SDK::setAccessToken($tk);


  $preference = new MercadoPago\Preference();


 foreach ($cart_ecommerce->getContent() as $product) {
   $item = new MercadoPago\Item();
   $item->title = $product['name'];
   $item->quantity = $product['qty'];
   $item->currency_id = "ARS";
   $item->unit_price = $product['price'];

   $products[] = $item;

 }



$preference->back_urls = array(
"success" => route('webhooks.pay', $slug),
"failure" => route('webhooks.pay', $slug),
"pending" => route('webhooks.pay', $slug)

);
$preference->auto_return = "approved";

$preference->items = $products;
$preference->save();

}

if($preference != null) {
    $preferencia = $preference->id;
} else {
$preferencia = "";

}


@endphp





@include('layouts.theme-ecommerce.header')

@if ($cart_ecommerce->getContent()->count() > 0)
<section class="cart-area">

                      
      @if(((new \Jenssegers\Agent\Agent())->isMobile()))
      <div style="color: {{$color}} !important; background-color: {{$background_color}} !important; width: 100%;  padding: 10px 0px;">
      <p style="padding-left:30px; color: {{$color}} !important; background-color: {{$background_color}} !important; text-rendering: optimizeSpeed; line-height: 1.5; font-family: Anton, Impact, 'sans-serif'; font-size: 22px;">Datos de facturacion</p>
      </div>
      @else          
      <!-- BreadCrumb Start-->
      <section class="breadcrumb-area mt-15">
          <div class="container">
              <div class="row">
                  <div class="col-lg-12">
                      <nav aria-label="breadcrumb">
                          <ol class="breadcrumb">
                              <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                              <li class="breadcrumb-item active" aria-current="page">Datos de facturacion </li>
                          </ol>
                      </nav>
                      <h5>Datos de facturacion</h5>
                  </div>
              </div>
          </div>
      </section>
      <!-- BreadCrumb Start-->
      @endif
      
      <!--Deliver Info Start-->
      <section class="deliver-info">

          <div style="max-width: 1250px !important; " class="container">
                <form name="add-blog-post-form"  id="form" enctype="multipart/form-data" method="post" action="{{url('save-sale-e')}}">
                @csrf
              <div class="row">
                  <div class="col-lg-12">
                <div style="padding: 26px;  border: 2px solid #EFEFEF; border-radius: 4px; margin-bottom:25px;">
                    @if(session()->has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                    @endif
                    
                    @if($registro == 2)
                            
                            
                    <div>

                    <h6>Datos del cliente</h6>

                            <div class="row">
                                <div class="col-lg-8 col-sm-12">
                                    <div class="form__div">
                                        <input name="nombre_destinatario"  type="text" required class="form__input" placeholder="
                                        ">
                                        <label for="" class="form__label">Nombre y apellido</label>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12">
                                    <div class="form__div">
                                        <input name="telefono"  required type="text" class="form__input" placeholder="
                                        ">
                                        <label for="" class="form__label">Telefono</label>
                                    </div>
                                </div>
                            </div>


                    </div>
                         
                    @endif
                    
                    <!--------------------------------------->
                                
                    <a hidden style="color: #1696e7;" href="javascript:void(0)">Ver detalle de venta</a>
                </div>
                  
                  </div>
                  <br>
                  <div class="col-lg-8">
                      <div style="margin-top:0; max-width: 791px;" class="apply-coupon">
                          <h6>Metodo de entrega</h6>
                            <select class="form-control" name="metodo_entrega" id="entrega" onchange="ShowSelectedEntrega();">

                              <option value="Elegir" selected>Elegir</option>
                               
                                
                              @if($ecommerce->retiro_habilitado == 1)
                              <option value="1">Retiro por el local</option>
                              @endif

                              @if($ecommerce->envio_habilitado == 1)
                              <option value="2"> Entrega a domicilio </option>

                              @endif



                            </select>
                            <br><br><br>

                            <div style="display:none;" id="datos_envio">

                            <h6>Datos de envio</h6>

                              <form action="#">

                            @if($registro == 1)
                            <div class="row">
                                <div class="col-8">
                                    <div class="form__div">
                                        <input name="nombre_destinatario" type="text" class="form__input" placeholder="
                                        ">
                                        <label for="" class="form__label">Nombre del destinatario</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form__div">
                                        <input name="dni" type="text" class="form__input" placeholder="
                                        ">
                                        <label for="" class="form__label">DNI</label>
                                    </div>
                                </div>
                            </div>
                            @endif

                              <div class="row">
                                  <div class="col-12">
                                      <div class="form__div">
                                          <input name="direccion" type="text" class="form__input" placeholder="
                                          ">
                                          <label for="" class="form__label">Direccion</label>
                                      </div>
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-12">
                                      <div class="form__div">
                                          <input type="text" name="departamento" class="form__input" placeholder="
                                          ">
                                          <label for="" class="form__label">Departamento, Casa</label>
                                      </div>
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-12">
                                      <div class="form__div mb-0">
                                          <input type="text"  name="ciudad" class="form__input" placeholder=" ">
                                          <input type="hidden" name="slug" class="form__input" value="{{$slug}}">
                                          <label for="" class="form__label">Ciudad</label>
                                      </div>
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-lg-4 col-md-4 col-12 mt-30">
                                      <select class="form-control" name="pais" id="">
                                          <option value="01">Pais</option>
                                          <option value="02">Argentina</option>
                                      </select>
                                  </div>
                                  <div class="col-lg-4 col-md-4 col-12 mt-30">
                                      <select class="form-control" name="provincia" id="">
                                          <option value="01">Provincia</option>
                                          @foreach( $provincias as $p)
                                          <option value="{{$p->id}}">{{$p->provincia}}</option>

                                          @endforeach
                                      </select>
                                  </div>
                                  <div class="col-lg-4 col-md-4 col-12 mt-30">
                                      <div class="form__div">
                                          <input type="text" name="codigo_postal" class="form__input" placeholder="
                                          ">
                                          <label for="" class="form__label">Codigo postal</label>
                                      </div>
                                  </div>

                              </div>
                          </form>

                          </div>

                          <div class="mensaje" style="display:none;" id="datos_retira">
                            @if($datos_retiro->domicilio_fiscal != null)

                            Puede retirar la mercaderia por: <br>

                            {{$datos_retiro->domicilio_fiscal}} , {{$datos_retiro->localidad}} , {{$datos_retiro->provincia}} {{$datos_retiro->pais}}

                            @else
                            Consulte por donde retirar el pedido al siguiente email {{$datos_retiro->email}} o al siguiente telefono {{$datos_retiro->phone}}
                            @endif
                          </div>

                        @if((new \Jenssegers\Agent\Agent())->isDesktop())
                            <h6>Nota al comercio</h6>
                            <div class="form__div">
                              <textarea name="observaciones" style="position: absolute;  top: 0; left: 0; width: 100%; height: 130px; font-size: 14px; border: 1px solid rgba(152, 155, 167, 0.5) !important; border-radius: 4px;  outline: none; padding: 1rem; background: transparent; z-index: 1;" name="name" class="form_control" rows="8" cols="80"></textarea>
                            </div>
                        @endif
                            <br><br>
    

                      </div>
                  </div>




                  <div class="col-lg-4">
                      <div style="margin-top:0;" class="apply-coupon">
                          <h6>Forma de pago</h6>
                          <select class="form-control" name="metodo_pago" id="producto" name="producto" onchange="ShowSelected();">

                            <option value="Elegir" selected>Elegir</option>

                            @if($ecommerce->efectivo_habilitado == 1)
                            <option value="1">Contrareembolso</option>
                            @endif

                            @if($ecommerce->transferencia_habilitado == 1)
                            <option value="{{$ecommerce->banco_id}}"> Transferencia </option>

                            @endif

                            @if($ecommerce->mp_habilitado == 1)
                            <option value="3">Mercado Pago</option>
                            @endif


                          </select>
                        <!--  <form action="#">
                            <div class="row">
                              <div class="col-1">
                                <input type="checkbox" wire:model="efectivo" value="">
                              </div>
                              <div class="col-10">
                                Efectivo
                              </div>

                            </div>
                            <div class="row">
                              <div class="col-1">
                                <input type="checkbox" wire:model="transferencia" value="">
                              </div>
                              <div class="col-10">
                                Transferencia bancaria
                              </div>

                            </div>
                            <div class="row">
                              <div class="col-1">
                                <input type="checkbox" wire:model="tarjeta" value="">
                              </div>
                              <div class="col-10">
                                Pago con tarjeta
                              </div>

                            </div>
                            <br><br>

                              <button class="btn bg-primary" type="submit">FINALIZAR COMPRA</button>
                          </form>

                        -->
                          <br>
                          <div style="display:none;" id="mensaje_efectivo2">
                             <label>Total: $ {{$total}}</label> <br>
                            <label>Paga con:</label>  <input type="text" class="form-control" id="paga_con" onchange="CalcularTotal()" name="paga_con">
                            <input hidden type="text" class="form-control" id="total" value="{{$total}}">
                          </div>
                          <div style="color:red;" id="msg"></div>
                          <br>
                          <div class="mensaje" style="display:none;" id="mensaje_efectivo">
                              
                            {{$ecommerce->mensaje_efectivo}}
                          </div>

                          <div class="mensaje" style="display:none;" id="mensaje_transferencia">
                            {{$ecommerce->mensaje_transferencia}}
                            <br><br>
                            @foreach($banco as $b)
                            <b>Banco:</b> {{$b->nombre_banco}}
                            <br>
                            <b>CBU:</b> {{$b->CBU}}
                            <br>
                            <b>CUIT:</b> {{$b->cuit}}
                            @endforeach
                          </div>


                          <div class="mensaje" style="display:none;" id="mensaje_mp">
                            {{$ecommerce->mensaje_mp}}



                          </div>

                          <div id="boton_mp" style="display:none;" class="cho-container"></div>
                            @if((new \Jenssegers\Agent\Agent())->isDesktop())
                            <input type="submit" class="btn bg-primary"  value="GUARDAR">
                            @endif
                      <!---    <button class="btn bg-primary" id="boton_save" type="button" onclick="SaveSale()" >FINALIZAR COMPRA.</button> --->
                          <br><br>
                      </div>
                  </div>
                  
                   @if((new \Jenssegers\Agent\Agent())->isMobile())
                   <div class="col-lg-8">
                       <div style="margin-top:0; max-width: 791px; height: 290px;" class="apply-coupon">
                            <h6>Nota al comercio</h6>
                            <div class="form__div">
                              <textarea name="observaciones" style="position: absolute;  top: 0; left: 0; width: 100%; height: 130px; font-size: 14px; border: 1px solid rgba(152, 155, 167, 0.5) !important; border-radius: 4px;  outline: none; padding: 1rem; background: transparent; z-index: 1;" name="name" class="form_control" rows="8" cols="80"></textarea>
                            </div>
                            </div>
                    </div>
                    <br><br>
                    <div class="col-lg-12">
                        <br><br>
                         <input type="submit" class="btn btn-dark w-100" style="color: {{$color}} !important; background-color: {{$background_color}} !important; width: 100%;" value="GUARDAR">
                    </div>  
                    @endif
              </div>
          </div>
      </section>
      <!--Deliver Info End-->

</section>

@else
@php 

return \Redirect::to('tienda/'.$slug);

@endphp
@endif


<script src="https://sdk.mercadopago.com/js/v2"></script>

<script>
			//Adicione as credenciais de sua conta Mercado Pago junto ao SDK
			const mp = new MercadoPago('<?php echo $ky; ?>', {
			    locale: 'es-AR'
			});
			const checkout = mp.checkout({
			   preference: {
			       id: '{{ $preferencia }}' // Indica el ID de la preferencia
			   },
			   render: {
			       container: '.cho-container', // Clase CSS para renderizar el botón de pago
			       label: 'FINALIZAR COMPRA', // Cambiar el texto del botón de pago (opcional)
			    }
			});
	</script>



<script type="text/javascript">
function ShowSelected()
{
/* Para obtener el valor */
var cod = document.getElementById("producto").value;

/* Para obtener el texto */
var combo = document.getElementById("producto");
var selected = combo.options[combo.selectedIndex].text;

/*Tomamos los id de las cajas*/


var mensaje_efectivo = document.getElementById("mensaje_efectivo");
var mensaje_efectivo2 = document.getElementById("mensaje_efectivo2");
var mensaje_transferencia = document.getElementById("mensaje_transferencia");
var mensaje_mp = document.getElementById("mensaje_mp");



if(selected == "Contrareembolso") {
mensaje_efectivo.style.display = "block";
mensaje_efectivo2.style.display = "block";
mensaje_transferencia.style.display = "none";
mensaje_mp.style.display = "none";
boton_mp.style.display = "none";
boton_save.style.display = "block";

}
if(selected == "Transferencia") {
  const div = document.querySelector("#msg");  // <div></div>
  div.textContent = "";   
  mensaje_efectivo.style.display = "none";
  mensaje_efectivo2.style.display = "none";
  mensaje_transferencia.style.display = "block";
  mensaje_mp.style.display = "none";
  boton_mp.style.display = "none";
  boton_save.style.display = "block";

}

if(selected == "Mercado Pago") {
const div = document.querySelector("#msg");  // <div></div>
  div.textContent = "";  
  mensaje_efectivo.style.display = "none";
  mensaje_efectivo2.style.display = "none";
  mensaje_transferencia.style.display = "none";
  mensaje_mp.style.display = "block";
  boton_mp.style.display = "block";
  boton_save.style.display = "none";

}

if(selected == "Elegir") {
    const div = document.querySelector("#msg");  // <div></div>
  div.textContent = "";  
  mensaje_efectivo.style.display = "none";
  mensaje_efectivo2.style.display = "none";
  mensaje_transferencia.style.display = "none";
  mensaje_mp.style.display = "none";
  boton_mp.style.display = "none";
  boton_save.style.display = "block";

}


}
</script>



<script type="text/javascript">
function ShowSelectedEntrega()
{
/* Para obtener el valor */
var cod_entrega = document.getElementById("entrega").value;

/* Para obtener el texto */
var combo_entrega = document.getElementById("entrega");
var selected_entrega = combo_entrega.options[combo_entrega.selectedIndex].text;

/*Tomamos los id de las cajas*/


var datos_envio = document.getElementById("datos_envio");
var datos_retira = document.getElementById("datos_retira");


if(selected_entrega == "Entrega a domicilio") {
datos_envio.style.display = "block";
datos_retira.style.display = "none";
}
if(selected_entrega == "Retiro por el local") {
  datos_envio.style.display = "none";
  datos_retira.style.display = "block";
}

if(selected_entrega == "Elegir") {
  datos_envio.style.display = "none";
  datos_retira.style.display = "none";
}


}
</script>

<script>
    
        function   SaveSale() {
            
            alert('save');
            window.livewire.emit('Save')
        }
        
</script>
        <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            
            window.livewire.on("load", url => {
                var window = window.open(url);
                window.focus();
            });


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

        });

        function sumar() {
          var valor = $('#cantidad').val();
          var valor_nuevo = (parseFloat( $('#cantidad').val()) + parseFloat(1) );
          $('#cantidad').val(valor_nuevo);
        }
        
        function CalcularTotal() {
          var total = $('#total').val();
          var paga_con = $('#paga_con').val();
          
          if(paga_con < total) {
           const div = document.querySelector("#msg");  // <div></div>
            div.textContent = "El monto con el que paga debe ser mayor al total";   
          }
          
        }
        
        document.getElementById("form").addEventListener("submit", function(event){
        var total = $('#total').val();
        var paga_con = $('#paga_con').val();
       
        /* Para obtener el texto */
        var combo = document.getElementById("producto");
        var selected = combo.options[combo.selectedIndex].text;
        
        if(selected == "Contrareembolso") {
         if(paga_con < total) {
          event.preventDefault()
          alert("El monto con el que paga no puede ser menor que el total")
         }
        }
        
        if(selected == "Elegir") {
        
          event.preventDefault()
          alert("Debe elegir una forma de pago")
         
        }
        
        
        
        });
        
        
    

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
