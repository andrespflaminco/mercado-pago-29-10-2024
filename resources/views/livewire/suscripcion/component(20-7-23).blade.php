<main class="h-100">

    @include('layouts.theme-finapp.top')
              <!-- push-->
          <script src="{{ asset('assets/js/push.min.js')}}"></script>
            <script src="{{ asset('assets/js/serviceWorker.min.js')}}"></script>

         <div class="row">
             <div class="col position-relative align-self-center">
                 <input hidden type="text" id="daterange" />

             </div>
             <div class="col-auto align-self-center">

                 <canvas style="display: none;" id="areachart" class="mb-4"></canvas>
             </div>
         </div>
             <!-- balance -->

                     <!-- main page content -->
                     <div class="main-container container">
                         <!-- select contacts -->
                         <!-- select Amount -->

                         <!-- coupon code-->
                         <!-- Amount breakdown -->

                         @if($suscripcion != null)

                         <br>
                         <div class="row my-4 text-center">
                             <div class="col-12">
                                 @if($suscripcion->estado == 1)
                                  <h1 style="color: green;" class="fw-light mb-2"> SUSCRIPCION RECIBIDA. <br> </h1>
                                  @endif

                                  @if($suscripcion->estado == 2)
                                  <h1 style="color: green;" class="fw-light mb-2"> SUSCRIPCION RECIBIDA. <br> APROBADA </h1>
                                   @endif

                                   @if($suscripcion->estado == 3)
                                   <h1 style="color: red;" class="fw-light mb-2"> SUSCRIPCION RECHAZADA. <br> SUSCRIBASE DE NUEVO. </h1>
                                    @endif
                             </div>
                         </div>

                         @else
                         <!--div class="card mb-4">
                             <div class="card-body">
                                 <div class="row mb-3">
                                     <h6>
                                       <b>PASOS PARA SUSCRIBIRME:</b><br><br>
                                       <p>1- Suscribase con Mercado Pago en el boton amarillo a continuacion. </p><br>
                                       <p>2- Adjunte el comprobante de pago y apriete en el boton ENVIAR COMPROBANTE DE PAGO . </p>
                                     </h6>
                                 </div>
                                 <div class="row fw-medium">
                                     <div class="col-12">
                                         <div class="dashed-line mb-3"></div>
                                     </div>
                                     <div class="col">
                                         <p>MONTO MENSUAL A DEBITAR</p>
                                     </div>
                                     <div class="col-auto text-end">
                                         <p class="text-muted">$720.00</p>
                                     </div>
                                 </div>
                             </div>
                         </div-->
                         <!-- Saving targets -->
                         <div class="row mb-2">

                   @if($user->profile === 'Indirecto' &&  $user->confirmed_at === null)                      
                             
                                <a class='btn btn-default btn-lg shadow-sm w-100' style="width:100% margin:0 auto;" mp-mode="dftl" href="{{ $this->initPoint }}" name="MP-payButton" class='blue-ar-l-rn-none'>Suscribirme</a>
                        
                    @endif

                    @if($user->profile === 'Indirecto' && $suscripcionStatus === true)        
                       <b>El ultimo pago se ha realiado con exito</b>                            
                    @endif
                        <!--script type="text/javascript">
                        (function() {
                            function $MPC_load() {
                                window.$MPC_loaded !== true && (function() {
                                var s = document.createElement("script");
                                s.type = "text/javascript";
                                s.async = true;
                                s.src = document.location.protocol + "//secure.mlstatic.com/mptools/render.js";
                                var x = document.getElementsByTagName('script')[0];
                                x.parentNode.insertBefore(s, x);
                                window.$MPC_loaded = true;
                            })();
                        }
                        window.$MPC_loaded !== true ? (window.attachEvent ? window.attachEvent('onload', $MPC_load) : window.addEventListener('load', $MPC_load, false)) : null;
                        })();
                        /*
                                // to receive event with message when closing modal from congrants back to site
                                function $MPC_message(event) {
                                // onclose modal ->CALLBACK FUNCTION
                                // !!!!!!!!FUNCTION_CALLBACK HERE Received message: {event.data} preapproval_id !!!!!!!!
                                }
                                window.$MPC_loaded !== true ? (window.addEventListener("message", $MPC_message)) : null; 
                                */
                        </script-->


                         </div>
                         <br><br>
                         <!-- swiper credit cards -->
                         <!--div class="card mb-4">
                             <div class="card-body">
                                 <div class="row mb-3">
                                     <h6>
                                       <b>ADJUNTE EL COMPROBANTE DE PAGO:</b><br><br><br>
                                      <div style="vertical-align:middle !important;" class="form-group custom-file">

                                       <input type="file" class="custom-file-input" wire:model="archivo" accept="image/x-png, image/gif, image/jpeg, application/pdf"  class="form-control">
                                       <br><br>
                                       @error('archivo') <span class="error">
                                         <b style="color: red;">{{ $message }}</b>
                                       </span> @enderror

                                     </h6>
                                 </div>
                             </div>
                         </div>

                         <div class="row mb-4">
                             <div class="col-12 ">
                                 <a style="    background: #03a903fc !important;   border-color: #01b701fc !important;   color: white !important;" class="btn btn-success btn-lg shadow-sm w-100" wire:click="Store">
                                     ENVIAR COMPROBANTE DE PAGO
                                 </a>
                             </div>
                         </div>
                     </div-->
                     <!-- main page content ends -->

 <br><br><br><br>
     <!-- Footer -->
     <!-- Menu Modal -->
     <!-- Footer ends-->

     @endif
 </div>
 <!--
  <script>
     const showNotification = document.querySelector('.show-push').addEventListener("click", showPush)

     window.onload = function() {
       Push.Permission.has()
     }

     function showPush() {
       Push.create('Via bana!', {
           body: 'Prueba de notificacion push',
           icon: './assets/img/logo.png',
             onClick: function () {
                window.focus();
                this.close();
            }
           //timeout: 5000
         })
     }

             document.addEventListener('DOMContentLoaded', function(){
                                   window.livewire.on('notification-view', msg => {
                                             $('#Notifications').modal('show')
                                     })

                                         window.livewire.on('cerrar', msg => {
                                             $('#Notifications').modal('hide')
                                     })

             });
         </script>

          main page content ends -->

          <script>
              document.addEventListener('DOMContentLoaded', function(){

                      window.livewire.on('envio-ok', Msg => {
                        const toast = swal.mixin({
                              toast: true,
                              position: 'top',
                              width: '500px',
                              showConfirmButton: false,
                              timer: 1000,
                              padding: '2em'
                            });

                            toast({
                              type: 'success',
                              title: 'SUSCRIPCION ENVIADA CON EXITO',
                              padding: '1em',
                            })

                      })



              });
          </script>


 </main>
 <!-- daterange picker script -->
