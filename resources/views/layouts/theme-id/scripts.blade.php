<script src="{{ asset('assets/js/libs/jquery-3.1.1.min.js') }}"></script>
<script src="{{ asset('bootstrap/js/popper.min.js') }}"></script>
<script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('plugins/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>


<script>
    $(document).ready(function() {
        App.init();
    });
</script>

<!-- <script src="plugins/tagInput/tags-input.js"></script> -->
<script src="{{ asset('assets/js/users/account-settings.js') }}"></script>
<!--  END CUSTOM SCRIPTS FILE  -->

<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('plugins/table/datatable/datatables.js') }}"></script>

<script src="{{ asset('assets/js/custom.js') }}"></script>
<script src="{{ asset('plugins/sweetalerts/sweetalert2.min.js')}}"></script>
<script src="{{ asset('plugins/notification/snackbar/snackbar.min.js')}}"></script>
<script src="{{ asset('plugins/nicescroll/nicescroll.js')}}"></script>
<script src="{{ asset('plugins/currency/currency.js')}}"></script>

<script src="{{ asset('plugins/apex/apexcharts.min.js')}}"></script>
<script src="{{ asset('assets/js/dashboard/dash_2.js')}}"></script>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{ asset('assets/js/scrollspyNav.js')}}"></script>
<script src="{{ asset('plugins/input-mask/jquery.inputmask.bundle.min.js')}}"></script>
<script src="{{ asset('plugins/input-mask/input-mask.js')}}"></script>
<!-- BEGIN PAGE LEVEL PLUGINS -->

<!--  BEGIN CUSTOM SCRIPTS FILE  -->
<script src="{{ asset('assets/js/scrollspyNav.js')}} "></script>
<script src="{{ asset('plugins/select2/select2.min.js')}} "></script>
<script src="{{ asset('plugins/select2/custom-select2.js')}} "></script>
<!--  BEGIN CUSTOM SCRIPTS FILE  -->

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


   <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
   <script>

   // Enable pusher logging - don't include this in production
   Pusher.logToConsole = true;

   var pusher = new Pusher('93abd037e37ed5513af5', {
    cluster: 'sa1'
   });

   var channel = pusher.subscribe('channel');
   channel.bind('event', function(data) {
    alert(JSON.stringify(data));
   });
   </script>



<script type="text/javascript">
var ss = $(".basic").select2({
    tags: true,
});
</script>

<script>
    function noty(msg, option = 1)
    {
        Snackbar.show({
            text: msg.toUpperCase(),
            actionText: 'CERRAR',
            actionTextColor: '#fff',
            backgroundColor: option == 1 ? '#3b3f5c' : '#e7515a',
            pos: 'top-right'
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        window.livewire.on('global-msg', msg => {
            noty(msg)
        });
    })


</script>


<script src="{{ asset('plugins/flatpickr/flatpickr.js')}}"></script>

  <script src="{{ asset('plugins/file-upload/file-upload-with-preview.min.js')}}"></script>
  <script>
      //First upload
      var firstUpload = new FileUploadWithPreview('myFirstImage')
      //Second upload
      var secondUpload = new FileUploadWithPreview('mySecondImage')
  </script>

@livewireScripts
