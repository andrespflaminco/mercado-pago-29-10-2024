@if(auth()->user()->sucursal != 1)
<div class="mt-3 mb-3">
            <div class="row">
            <div id="progrss-wizard" class="twitter-bs-wizard">
                <ul class="twitter-bs-wizard-nav nav nav-pills nav-justified">
                    <li class="nav-item">
                        <a class="nav-link">
                            <div class="step-icon" style="{{$logo_paso1}}" data-bs-toggle="tooltip" data-bs-placement="top" title="User Details">
                            1
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link">
                            <div class="step-icon" style="{{$logo_paso2}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Address Detail">
                                2
                            </div>
                        </a>
                    </li>
                                            
                    <li class="nav-item">
                        <a class="nav-link">
                            <div class="step-icon" style="{{$logo_paso3}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Payment Details">
                                3
                            </div>
                        </a>
                    </li>
                    </ul>
                    <!-- wizard-nav -->

            </div>
            </div> 
            <!----- Paso 1------>
            <div class="row">
            <div class="col-12">
            
            <br><br><br>
            <div style="{{$paso1}}">
            <h4><b>Bienvenido al importador de Flaminco.</b></h4><br>
            <h6>Como primer paso te recomendamos que agregues las sucursales, variaciones y las listas de precios que vas a usar.</h6>
            <br>
            <h6>Esto es importante para poder importar correctamente los precios y stocks.</h6>
            <br>
            <a style="color: orange; font-size:16px; font-weight:600;" href="{{url('sucursales')}}" target="_blank">Agregar mis sucursales > </a>
            <br><br>
            <a style="color: orange; font-size:16px;  font-weight:600;" href="{{url('lista-precios')}}" target="_blank">Agregar mis listas de precios > </a>
            <br><br>
            <a style="color: orange; font-size:16px;  font-weight:600;" href="{{url('atributos')}}" target="_blank">Agregar atributos y variaciones > </a>
            <br><br>
            <a class="btn btn-light mt-3 mb-3" style="border-color: #000 !important;" title="Ayuda" target="_blank" href="https://academia.flaminco.com.ar/importar-productos/">
			    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
			Ayuda online
			</a>

			<a class="btn btn-success mt-3 mb-3" title="Ayuda" target="_blank"  href="https://wa.me/+5493516824493">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                  <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
            </svg>

			Consultar con un asesor
			</a>
			
            <h6>En caso de no tener sucursales, no usar productos variables o manejar solo la lista de precios base apreta el boton siguiente</h6>
            </div>                
            </div>
            <!----- Paso 1------>

            <!----- Paso 2 ------>
            <div style="{{$paso2}}">
            <br>
            <h6>Descarga el excel de ejemplo y completalo con los datos de tus productos:</h6>
            <br><br>
            <a class="btn btn-success" wire:click="ExportarEjemplo">Descargar Excel</a>  
            <br><br>
            
            <a class="btn btn-light mt-3 mb-3" style="border-color: #000 !important;" title="Ayuda" target="_blank" href="https://academia.flaminco.com.ar/importar-productos/">
			    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
			Ayuda online
			</a>

			<a class="btn btn-success mt-3 mb-3" title="Ayuda" target="_blank"  href="https://wa.me/+5493516824493">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                  <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
            </svg>

			Consultar con un asesor
			</a>
            
              
            </div>
            <!----- / Paso 2 ------>
          
            </div>

            
            </div>
@endif