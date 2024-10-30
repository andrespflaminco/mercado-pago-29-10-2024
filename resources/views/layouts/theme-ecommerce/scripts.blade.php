

   <script src="{{ asset('assets/ecommerce/js/jquery.min.js') }}"></script>
   <script src="{{ asset('assets/ecommerce/js/bootstrap.min.js') }}"></script>
   <script src="{{ asset('assets/ecommerce/scss/vendors/plugin/js/slick.min.js') }}"></script>
   <script src="{{ asset('assets/ecommerce/main.js') }}"></script>
   <script src="{{ asset('assets/js/app.js') }}"></script>

   <script src="{{ asset('plugins/sweetalerts/sweetalert2.min.js')}}"></script>


<script type="text/javascript">
$(document).ready(function(){
$("#lista").click(function(){
  $("#product-item").toggleClass("product-item-list");
  $("#cart-icon").toggleClass("cart-icon-list");
  $("#product-item-image").toggleClass("product-item-image-list");
  $("#productos").toggleClass("productos-list");
  $("#descripcion").toggleClass("descripcion-list");
});
});
</script>
   <script>
       function openNav() {

           document.getElementById("mySidenav").style.width = "350px";
           $('#overlayy').addClass("active");
       }

       function closeNav() {
           document.getElementById("mySidenav").style.width = "0";
           $('#overlayy').removeClass("active");
       }
   </script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("form-id");
        const svgIcon = document.getElementById("your-id");
        const searchInput = document.querySelector("input[name='search']");

        svgIcon.addEventListener("click", function() {
            form.submit();
        });

        searchInput.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                form.submit();
            }
        });
    });
</script>

@livewireScripts
