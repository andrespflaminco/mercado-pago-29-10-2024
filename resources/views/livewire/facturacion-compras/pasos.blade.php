<div style="padding:30px !important;" class="card mt-3 mb-3">
           
           <div class="row">
            
            <!-- Agrega contenido de paso a tu archivo HTML con IDs únicos -->
            <div id="paso1" class="contenido-paso">
            <h2 style="font-weight:700 !important;">PASO 1:</h2>    
            <br>
            <h4><b>Bienvenido al paso a paso para configurar el facturador de AFIP.</b></h4><br>
            
            <h6>1) Ingresar a la <a style="color: orange; font-size:16px; font-weight:600;" href="https://www.afip.gob.ar/" target="_blank">pagina de AFIP > </a> con tu usuario y contraseña</h6>
            <br>
            <img style="width:70% !important;" src="assets/pos/img/imagenes_facturacion/PASO_1.jpg"  class="card-img-top">
            <br>
            <h6>2) Buscar el  "Administrador de Relaciones de Clave Fiscal"</h6>
            <br>
            <img style="width:70% !important;" src="assets/pos/img/imagenes_facturacion/PASO_2.jpg"  class="card-img-top">
            <br>
            <h6>3) Click en "Nueva relacion"</h6>
            <br>
            <img style="width:70% !important;" src="assets/pos/img/imagenes_facturacion/PASO_3.jpg"  class="card-img-top">
            <br>
            
            </div>
            
            <div id="paso2" class="contenido-paso" style="display: none;">
             <h2 style="font-weight:700 !important;">PASO 2:</h2>    
            <br>
            <h6>4) Entrar en Afip --> Web services y buscar el web service “Facturacion Electronica”. </h6>
            <br>
            <img style="width:70% !important;" src="assets/pos/img/imagenes_facturacion/PASO_4.jpg"  class="card-img-top">
            <br>
            <h6>5) En donde dice "Representante" click en buscar </h6>
            <br>
            <img style="width:70% !important;" src="assets/pos/img/imagenes_facturacion/PASO_5.jpg"  class="card-img-top">
            <br>
            <h6>6) Ingresar el cuit 20358072101 y click en "Buscar" </h6>
            <br>
            <img style="width:70% !important;" src="assets/pos/img/imagenes_facturacion/PASO_6.jpg"  class="card-img-top">
            <br>
            <br>
            
            </div>
            
            <div id="paso3" class="contenido-paso" style="display: none;">
            <h2 style="font-weight:700 !important;">PASO 3:</h2>
            <!-- Contenido del paso 3 va aquí -->
                
            <br>
            <h4><b>Felicitaciones ya pudiste configurar la relacion de facturacion en AFIP.</b></h4><br>
            <br>
            <h4>Ahora ingresa tus datos de facturacion a continuacion para finalizar la configuracion</h4>
            <br>
            <br>
            <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                                    <div  class="section contact">
                                        <div class="info">
                                            <h5 class="">Datos de facturacion</h5>
                                            <br>
                                            <div class="row">
                                                <form wire:submit.prevent="StoreDatosFacturacion" id="miFormulario">
                                                <div class="col-md-12 mx-auto">
                                                    <div class="row">

                                                      <div class="col-sm-12 col-md-6">
                                                      	<div class="form-group">
                                                      		<label >Razon social</label>
                                                      		<input type="text" wire:model.defer="razon_social_form" id="razon_social_form"
                                                      		class="form-control" placeholder=""  >
                                                      		@error('razon_social') <span class="text-danger er">{{ $message}}</span>@enderror
                                                      	</div>
                                                      </div>

                                                      <div class="col-sm-12 col-md-6">
                                                      	<div class="form-group">
                                                      		<label >CUIT</label>
                                                      		<input type="text" wire:model.defer="cuit_form" id="cuit_form"
                                                      		class="form-control" placeholder=""  >
                                                      		@error('cuit') <span class="text-danger er">{{ $message}}</span>@enderror
                                                      	</div>
                                                      </div>

                                                      <div class="col-sm-12 col-md-6">
                                                      	<div class="form-group">
                                                      		<label >Condicion ante el IVA</label>
                                                      		<select wire:model.defer="condicion_iva_form" id="condicion_iva_form" class="form-control">
                                                      			<option value="Elegir" selected>Elegir</option>
                                                      			<option value="IVA Responsable inscripto" >IVA Responsable inscripto</option>
                                                      			<option value="IVA exento" >IVA exento</option>
                                                      			<option value="Monotributo" >Monotributo</option>

                                                      		</select>
                                                      		@error('condicion_iva') <span class="text-danger er">{{ $message}}</span>@enderror
                                                      	</div>
                                                      </div>

                                                      <div class="col-sm-12 col-md-6">
                                                      	<div class="form-group">
                                                      		<label >Ingresos brutos</label>
                                                      		<input type="text" wire:model.defer="iibb_form" id="iibb_form"
                                                      		class="form-control" placeholder=""  >
                                                      		@error('iibb') <span class="text-danger er">{{ $message}}</span>@enderror
                                                      	</div>
                                                      </div>

                                                      <div class="col-sm-12 col-md-6">
                                                        <div class="form-group">
                                                          <label >Punto de venta</label>
                                                          <input type="text" wire:model.defer="pto_venta_form" id="pto_venta_form"
                                                          class="form-control" placeholder=""  >
                                                          @error('pto_venta') <span class="text-danger er">{{ $message}}</span>@enderror
                                                        </div>
                                                      </div>

                                                      <div class="col-sm-12 col-md-6">
                                                        <div class="form-group">
                                                          <label >Fecha de inicio de actividades</label>
                                                          <input type="date" wire:model.defer="fecha_inicio_actividades_form" id="fecha_inicio_actividades_form"
                                                          class="form-control" >
                                                          @error('fecha_inicio_actividades') <span class="text-danger er">{{ $message}}</span>@enderror
                                                        </div>
                                                      </div>

                                                      <div class="col-sm-12 col-md-6">
                                                      	<div class="form-group">
                                                      		<label >IVA por defecto</label>
                                                      		<select wire:model.defer="iva_defecto_form" id="iva_defecto_form"
                                                      		class="form-control">
                                                              <option value="Elegir" selected>Elegir</option>
                                                              <option value="0">Sin IVA</option>
                                                              <option value="0.105">10,5%</option>
                                                              <option value="0.21">21%</option>
                                                              <option value="0.27">27%</option>
                                                          </select>
                                                      		@error('iva_defecto') <span class="text-danger er">{{ $message}}</span>@enderror
                                                      	</div>
                                                      </div>
                                                      
                                                       <div class="col-sm-12 col-md-6">
                                                      	<div class="form-group">
                                                      		<label >Relacion Precio -> Iva</label>
                                                      		<select wire:model.defer="relacion_precio_iva_form" id="relacion_precio_iva_form"
                                                      		class="form-control">
                                                      		  <option value="Elegir" selected>Elegir</option>
                                                      		  <option value="0">Sin IVA</option>
                                                              <option value="1">IVA + Precio</option>
                                                              <option value="2">IVA incluido en el precio</option>
                                                          </select>
                                                      		@error('relacion_precio_iva') <span class="text-danger er">{{ $message}}</span>@enderror
                                                      	</div>
                                                      </div>

                                                    </div>
                                                </div>    
                                                </form>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
            
            </div>
       
            
            </div>
            
            <div class="row">
            <!----- Botones ------>
                        
            <!-- Agrega botones de paso a tu archivo HTML -->
            <div class="row">
                <div class="col-4 mt-4" style="text-align: left;">
                    <button id="paso1BtnAnterior" class="btn btn-cancel">Anterior</button>
                    <button id="paso2BtnAnterior" class="btn btn-cancel">Anterior</button>
                </div>
                <div class="col-4 mt-4"></div>
                <div class="col-4 mt-4" style="text-align: right;">
                    <button id="paso2Btn" class="btn btn-submit">Siguiente</button>
                    <button id="paso3Btn" class="btn btn-submit">Siguiente</button>
                    <button type="button" id="paso4Btn" onclick="validarFormulario()" class="btn btn-submit">Guardar</button>
                </div>
            </div>
            </div>




