<main style="height: 100%;">

@inject('cart_ecommerce', 'App\Services\CartEcommerce')

@include('layouts.theme-ecommerce.header')

@if(!(new \Jenssegers\Agent\Agent())->isMobile())

@include('livewire.ecommerce.form-variacion')
@include('livewire.ecommerce.form')

@endif


        <!-- Populer Product Strat -->
        
        <input type="hidden" value="{{$background_color}}" id="background_color">
        <input type="hidden" value="{{$color}}" id="color">

            <!-- Verificar autenticación del usuario y cliente_id -->
            @if(Auth::check() && is_null(Auth::user()->cliente_id))

                    <div class="pricing-plan mb-lg-5 mb-sm-3 mt-sm-3 text-center" >
                       <br>    
                       <br>    
                       <img style="width:200px !important;" src="../assets/pos/img/logo.png"   alt="">
                       <br>
                       <br>
                       <br>    
                       <h2><b>ATENCION</b></h2>
                       <br>
                       <b>Debe continuar como cliente para comprar en una tienda Flaminco, se deslogueara su sesion de Administrador de comercio.</b> 
                       <br><br><br>
                       <br>
                       <button class="btn btn-dark" wire:click="logout()">Continuar ></button>
                    </div>  

            
            @else

                @if($tipo == "1")
                
                @include('livewire.ecommerce.forma-cuadricula')
                @include('livewire.ecommerce.mobile.footer')
                <!-- MODO LISTA -->
                
                @endif

                @if($tipo == "2")
                
                @include('livewire.ecommerce.forma-lista')
                @include('livewire.ecommerce.mobile.footer')
        
                
                @endif            
            
            @endif


        <script type="text/javascript">



        function   Agregar() {

            var selected_id = $('#selected_id').val();
            var referencia_variacion = $('#referencia_variacion').val();

            window.livewire.emit('Agregar', selected_id, referencia_variacion)

            $('#cantidad').val() = "";
            $('#selected_id').val() = "";
            $('#referencia_variacion').val() = "";

        }
        
        function   Sumar() {

            var cantidad_agregar = $('#cantidad').val();
            var selected_id = $('#selected_id').val();
            var referencia_variacion = $('#referencia_variacion').val();

            window.livewire.emit('Sumar', selected_id, referencia_variacion)

            $('#cantidad').val() = "";
            $('#selected_id').val() = "";
            $('#referencia_variacion').val() = "";

        }
        
        function   Restar() {

            var cantidad_agregar = $('#cantidad').val();
            var selected_id = $('#selected_id').val();
            var referencia_variacion = $('#referencia_variacion').val();

            window.livewire.emit('Sumar', selected_id, referencia_variacion)

            $('#cantidad').val() = "";
            $('#selected_id').val() = "";
            $('#referencia_variacion').val() = "";

        }

        </script>

        <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            
            
          window.livewire.on('add-hide', msg => {
            $('#product-modal').modal('hide')

          });
          
          window.livewire.on('add-new', msg => {
            $('#product-modal').modal('show')
            $('#modal-body').modal('show')
          });
            

          window.livewire.on('add-variacion', msg => {
            $('#variaciones').modal('show')

            var valor = $('#cantidad').val();
            var stock = $('#stock').val();
            var stock_descubierto = $('#stock_descubierto').val();
            var agregado = $('#agregado').val();

            if(agregado != undefined) {

            document.getElementById("added").innerHTML = " <b> Agregado al carrito: </b>"+agregado;

            if((stock_descubierto == "si") && (stock - agregado) < 1) {

                $('#boton_add').prop('disabled', true);

            }

          } else {
              document.getElementById("added").innerHTML = " <b> Agregado al carrito: </b> 0";
          }

          });
          

          window.livewire.on('add', msg => {
            $('#product-modal').modal('show')

            var valor = $('#cantidad').val();
            var stock = $('#stock').val();
            var stock_descubierto = $('#stock_descubierto').val();
            var agregado = $('#agregado').val();

            if(agregado != undefined) {

            document.getElementById("added").innerHTML = " <b> Agregado al carrito: </b>"+agregado;

            if((stock_descubierto == "si") && (stock - agregado) < 1) {

                $('#boton_add').prop('disabled', true);

            }

          } else {
              document.getElementById("added").innerHTML = " <b> Agregado al carrito: </b> 0";
          }

          });
          
          

   

          window.livewire.on('product-added', msg => {
            $('#product-modal').modal('hide')
            var reset = 1;
            $('#cantidad').val(reset);

            const toast = swal.mixin({
             toast: true,
             position: 'bottom',
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
          
          
          window.livewire.on('alerta-stock', msg => {
         
            const toast = swal.mixin({
             toast: true,
             position: 'top',
             showConfirmButton: false,
             timer: 3000,
             padding: '2em'
           });

           toast({
             type: 'warning',
             title: 'Stock insuficiente',
             padding: '2em',
           })



          });

        });

        function sumar() {
            
          var valor = $('#cantidad').val();
          var stock = $('#stock').val();
          var stock_descubierto = $('#stock_descubierto').val();
          var agregado = $('#agregado').val();


          if(agregado != undefined) {

          var valor_nuevo = (parseFloat( $('#cantidad').val()) + parseFloat(1) );

          if((stock_descubierto == "si") && (valor_nuevo > (stock - agregado))) {

            alert('Cantidad maxima en stock: '+stock+' unidades.');
            var valor_agregado = stock - agregado;
              $('#cantidad').val(valor_agregado);

          } else {
            $('#cantidad').val(valor_nuevo);
          }
          


        } else {

          var valor_nuevo = (parseFloat( $('#cantidad').val()) + parseFloat(1) );

          if((stock_descubierto == "si") && (valor_nuevo > stock)) {

            alert('Cantidad maxima en stock: '+stock+' unidades.');
              $('#cantidad').val(stock);

          } else {
            $('#cantidad').val(valor_nuevo);
          }

        }


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
        
        

        