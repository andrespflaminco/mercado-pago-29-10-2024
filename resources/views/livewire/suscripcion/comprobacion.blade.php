                     
                    <div style="margin: 0 auto;" class="row mb-2 text-center">
                        <div class="pricing-plan mb-lg-5 mb-sm-3 mt-sm-3">
                            <br>
                            <br>
                            <img style="width:200px !important;" src="assets/pos/img/logo.png" alt="">
                            <br>
                            <br>
                            <br>
                            <h2><b>GRACIAS POR ELEGIRNOS</b></h2>
                            <b>Estamos corroborando el pago</b>
                            <br><br><br>
                            <br>
                    
                            <!-- Barra de carga -->
                            <div class="progress progress-md">
								<div class="progress-bar" id="progress" style="width: 10%">
							</div>

                            <div id="progress-bar">
                                <div id="progress"></div>
                            </div>
                    
                            <script>
                                // Espera a que la página se cargue completamente
                                document.addEventListener('DOMContentLoaded', function() {
                                    var progressBar = document.getElementById('progress');
                                    var width = 0;
                                    var interval = setInterval(function() {
                                        width += 20; // Aumenta la anchura en 10% cada 2 segundos
                                        progressBar.style.width = width + '%';
                                        if (width >= 100) {
                                            clearInterval(interval);
                                            // Espera 4 segundos antes de redirigir
                                            setTimeout(function() {
                                                window.location.href = "https://app.flamincoapp.com.ar/regist";
                                            }, 1000); // 2000 milisegundos = 2 segundos
                                        }
                                    }, 1000); // 2000 milisegundos = 2 segundos
                                });
                                
                            </script>
                            <!-- Facebook Pixel Code -->
                            
                            
                            <script>
                                   !function(f,b,e,v,n,t,s)
                                    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                                    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                                    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                                    n.queue=[];t=b.createElement(e);t.async=!0;
                                    t.src=v;s=b.getElementsByTagName(e)[0];
                                    s.parentNode.insertBefore(t,s)}(window, document,'script',
                                    'https://connect.facebook.net/en_US/fbevents.js');
                                    fbq('init', '946528043924786');
                                    fbq('track', 'Purchase', {value: {{$suscripcion->monto ?? 0.00}} , currency: 'ARS'});
                          
                            </script>
                    		<noscript><img height="1" width="1" style="display:none"
                            src="https://www.facebook.com/tr?id=946528043924786&ev=PageView&noscript=1"
                            /></noscript>
                            <!-- End Facebook Pixel Code -->
                        </div>
                    </div>
                     </div>