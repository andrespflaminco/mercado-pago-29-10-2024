<main>

@inject('cart_ecommerce', 'App\Services\CartEcommerce')


@include('layouts.theme-ecommerce.header')

<section class="cart-area">


      <!-- BreadCrumb Start-->
      <section id="gracias_ecommerce" class="breadcrumb-area mt-15">
          <div class="container">
              <div class="row">
                  <div  class="col-lg-12">
                      <nav aria-label="breadcrumb">
                        <h5 style="color: #eee;">Gracias por su compra</h5>
                        <br><br><br>
                        <a class="btn btn-light" href="{{url('tienda/'.$slug)}}">Volver a la tienda</a>
                      </nav>
                  </div>
              </div>
          </div>
      </section>
      <!-- BreadCrumb Start-->


</section>


        <script type="text/javascript">
        function   SaveSale() {
            window.livewire.emit('SaveSale')
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
