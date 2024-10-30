

@if($caja_abierta == null)
<div class="row layout-top-spacing">
	<div class="col-sm-12 col-md-12">


    <!-- Modal -->
<div class="modal fade" id="MailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ingrese un mail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  x
                </button>
            </div>
            <div class="modal-body" style="width:100%;">
            <label>Mail</label>
            <input type="text" wire:model.defer="mail_ingresado" class="form-control" >
             </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                <button class="btn btn-dark" wire:click="EnviarMail()"> Enviar </button>

            </div>
        </div>
    </div>
</div>



		<!-- Modal -->
		<div class="modal" id="ModalFormulario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		    <div class="modal-dialog" role="document">
		        <div class="modal-content">
		            <div class="modal-header">
		                <h5 class="modal-title" id="exampleModalLabel">Ayudanos a conocerte mejor</h5>
		                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                  x
		                </button>
		            </div>
		            <div style="margin:0 !important;" class="modal-body">
		            <div class="col-12">
									<label for="">¿Cuantos empleados tiene tu empresa?</label>
									<select  wire:model.defer="cantidad_empleados" class="form-control">
										<option value="Elegir"> Elegir</option>
										<option value="1">1 empleado</option>
										<option value="2">2 empleados</option>
										<option value="3">3 empleados</option>
										<option value="4">4 empleados</option>
										<option value="5">5 empleados</option>
										<option value="6">+ de 5 empleados</option>
									</select>

									 @error('cantidad_empleados') <span class="error">{{ $message }}</span> @enderror

		            </div>
								<div class="col-12">
									<label for="">¿Cuenta con un sistema de administración?</label>
									<select wire:model="cuenta_con_sistema" wire:change='CuentaConSistema($event.target.value)'  class="form-control">
										<option value="Elegir">Elegir</option>
										<option value="Si">Si</option>
										<option value="No">No</option>
									</select>

									 @error('cuenta_con_sistema') <span class="error">{{ $message }}</span> @enderror
								</div>

								@if($cuenta_con_sistema == 'No')
								<br>
								<div class="col-12">
									<label for="">¿Que tan importante considera el uso de un sistema?</label> <br>
									<select wire:model.defer="importancia"  class="form-control">
										<option value="Elegir">Elegir</option>
										<option value="Mucho">Muy importante</option>
										<option value="Medio">Medianamente importante</option>
										<option value="Poco">Poco importante</option>
									</select>

									@error('importancia') <span class="error">{{ $message }}</span> @enderror

								</div>
								<br>
								<div class="col-12">
									<label for="">¿Porque motivo esta buscando un sistema?</label> <br>
									<input type="checkbox" wire:model.defer="motivo_no" value="stock"> Problemas de stock <br>
									<input type="checkbox" wire:model.defer="motivo_no" value="cajas"> Problemas de cajas <br>
									<input type="checkbox" wire:model.defer="motivo_no"  value="acceso remoto"> Controlar mi negocio sin estar presente todo el dia <br>
									<input type="checkbox"  wire:model.defer="motivo_no"  value="ventas"> Automatizar el registro de ventas <br>
									<input type="checkbox"  wire:model.defer="motivo_no"  value="facturacion"> Facturacion <br>
									<input type="checkbox"  wire:model.defer="motivo_no"  value="ecommerce"> E-commerce <br>

									@error('motivo_no') <span class="error">{{ $message }}</span> @enderror
								</div>
								@endif


								@if($cuenta_con_sistema == 'Si')
								<br>
								<div class="col-12">
									<label for="">¿Porque motivo esta buscando un nuevo sistema?</label> <br>
									<input type="checkbox" wire:model.defer='motivo_si' value="precio"> Precio <br>
									<input type="checkbox" wire:model.defer='motivo_si' value="funcionalidades"> Funcionalidades <br>
									<input type="checkbox" wire:model.defer='motivo_si' value="soporte"> Soporte <br>
									<input type="checkbox" wire:model.defer='motivo_si' value="suriosidad"> Curiosidad/Estoy viendo <br>
									<input type="checkbox" wire:model.defer='motivo_si' value="ns"> NS/NC <br>

								</div>
								@error('motivo_si') <span class="error">{{ $message }}</span> @enderror
								<br>


								<div class="col-12">
									<label for="">¿Con que urgencia usted considera que debe cambiarse de sistema?</label> <br>
									<select wire:model.defer="urgencia"  class="form-control">
										<option value="Elegir">Elegir</option>
										<option value="Mucha">Muy urgente</option>
										<option value="Media">Medianamente urgente</option>
										<option value="Poca">Poco urgente</option>
									</select>
									@error('urgencia') <span class="error">{{ $message }}</span> @enderror

								</div>

								@endif
		            </div>
		            <div class="modal-footer">
		                <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> OMITIR</button>
		                <button type="button" wire:click="GuardarInfoFormulario()" class="btn btn-primary">ACEPTAR</button>
		            </div>
		        </div>
		    </div>
		</div>


		<br><br>
<div class="row">
<div class="col-sm-12 col-md-2">

 </div>
<div class="col-sm-12 col-md-8">
<div class="form-group">
	<h5>Abrir Caja</h5>
 <div style="margin-bottom: 0 !important;" class="input-group mb-4">
	 <div class="input-group-prepend">
		 <span class="input-group-text input-gp">
			 Monto inicial: $
		 </span>
	 </div>
	 <input type="text" wire:model.lazy="monto_inicial" required class="form-control" placeholder="Ej: 10" >

		 </div>
</div>
</div>
<div class="col-sm-12 col-md-2">
 </div>
 <div class="col-sm-12 col-md-2">

  </div>
 <div class="col-sm-12 col-md-8">
	 <button style="float:right;" type="button"  wire:loading.attr="disabled" id="caja-abrir" wire:click.prevent="AbrirCaja()" class="btn btn-dark close-modal" >GUARDAR</button>

 </div>
 <div class="col-sm-12 col-md-2">

  </div>


<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>



</div>
</div>
</div>
@else
<div>

	<div class="row layout-top-spacing">
		<div class="col-sm-12 col-md-12">



    <!-- Modal -->
<div class="modal fade" id="MailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ingrese un mail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  x
                </button>
            </div>
            <div class="modal-body" style="width:100%;">
            <label>Mail</label>
            <input type="text" wire:model.defer="mail_ingresado" class="form-control" >
             </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                <button class="btn btn-dark" wire:click="EnviarMail()"> Enviar </button>

            </div>
        </div>
    </div>
</div>


		<!-- Modal -->
		<div class="modal" id="ModalFormulario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		    <div class="modal-dialog" role="document">
		        <div class="modal-content">
		            <div class="modal-header">
		                <h5 class="modal-title" id="exampleModalLabel">Ayudanos a conocerte mejor</h5>
		                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                  x
		                </button>
		            </div>
		            <div style="margin:0 !important;" class="modal-body">
		            <div class="col-12">
									<label for="">¿Cuantos empleados tiene tu empresa?</label>
									<select  wire:model.defer="cantidad_empleados" class="form-control">
										<option value="Elegir"> Elegir</option>
										<option value="1">1 empleado</option>
										<option value="2">2 empleados</option>
										<option value="3">3 empleados</option>
										<option value="4">4 empleados</option>
										<option value="5">5 empleados</option>
										<option value="6">+ de 5 empleados</option>
									</select>

									 @error('cantidad_empleados') <span class="error">{{ $message }}</span> @enderror

		            </div>
								<div class="col-12">
									<label for="">¿Cuenta con un sistema de administración?</label>
									<select wire:model="cuenta_con_sistema" wire:change='CuentaConSistema($event.target.value)'  class="form-control">
										<option value="Elegir">Elegir</option>
										<option value="Si">Si</option>
										<option value="No">No</option>
									</select>

									 @error('cuenta_con_sistema') <span class="error">{{ $message }}</span> @enderror
								</div>

								@if($cuenta_con_sistema == 'No')
								<br>
								<div class="col-12">
									<label for="">¿Que tan importante considera el uso de un sistema?</label> <br>
									<select wire:model.defer="importancia"  class="form-control">
										<option value="Elegir">Elegir</option>
										<option value="Mucho">Muy importante</option>
										<option value="Medio">Medianamente importante</option>
										<option value="Poco">Poco importante</option>
									</select>

									@error('importancia') <span class="error">{{ $message }}</span> @enderror

								</div>
								<br>
								<div class="col-12">
									<label for="">¿Porque motivo esta buscando un sistema?</label> <br>
									<input type="checkbox" wire:model.defer="motivo_no" value="stock"> Problemas de stock <br>
									<input type="checkbox" wire:model.defer="motivo_no" value="cajas"> Problemas de cajas <br>
									<input type="checkbox" wire:model.defer="motivo_no"  value="acceso remoto"> Controlar mi negocio sin estar presente todo el dia <br>
									<input type="checkbox"  wire:model.defer="motivo_no"  value="ventas"> Automatizar el registro de ventas <br>
									<input type="checkbox"  wire:model.defer="motivo_no"  value="facturacion"> Facturacion <br>
									<input type="checkbox"  wire:model.defer="motivo_no"  value="ecommerce"> E-commerce <br>

									@error('motivo_no') <span class="error">{{ $message }}</span> @enderror
								</div>
								@endif


								@if($cuenta_con_sistema == 'Si')
								<br>
								<div class="col-12">
									<label for="">¿Porque motivo esta buscando un nuevo sistema?</label> <br>
									<input type="checkbox" wire:model.defer='motivo_si' value="precio"> Precio <br>
									<input type="checkbox" wire:model.defer='motivo_si' value="funcionalidades"> Funcionalidades <br>
									<input type="checkbox" wire:model.defer='motivo_si' value="soporte"> Soporte <br>
									<input type="checkbox" wire:model.defer='motivo_si' value="suriosidad"> Curiosidad/Estoy viendo <br>
									<input type="checkbox" wire:model.defer='motivo_si' value="ns"> NS/NC <br>

								</div>
								@error('motivo_si') <span class="error">{{ $message }}</span> @enderror
								<br>


								<div class="col-12">
									<label for="">¿Con que urgencia usted considera que debe cambiarse de sistema?</label> <br>
									<select wire:model.defer="urgencia"  class="form-control">
										<option value="Elegir">Elegir</option>
										<option value="Mucha">Muy urgente</option>
										<option value="Media">Medianamente urgente</option>
										<option value="Poca">Poco urgente</option>
									</select>
									@error('urgencia') <span class="error">{{ $message }}</span> @enderror

								</div>

								@endif
		            </div>
		            <div class="modal-footer">
		                <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> OMITIR</button>
		                <button type="button" wire:click="GuardarInfoFormulario()" class="btn btn-primary">ACEPTAR</button>
		            </div>
		        </div>
		    </div>
		</div>



			<div class="d-flex">
  		<div class="mr-auto p-2">
				<div class="page-title">
				<h3>Nueva venta</h3>
				<a hidden href="{{ url('ticket' . '/' . '1103') }}"  target="_blank">Imprimir.</a>

				</div>
				</div>
				<div  class="p-2 d-none d-sm-block">
				<select  wire:model='comprobante' wire:change='MetodoComprobante($event.target.value)' class="form-control">
									<option value="1">No facturar</option>
									<option value="2">Facturar</option>
				</select>
				</div>
				<div  class="p-2 d-none d-sm-block">

				 <select  wire:model.lazy='canal_venta' 	class="form-control">
					 <option value="Mostrador">Mostrador</option>
					 <option value="E-commerce">E-commerce</option>
					 <option value="Instragram">Instragram</option>
					 <option value="Mercado libre">Mercado libre</option>

					 </select>

			 </div>
				<div  class="p-2 d-none d-sm-block">
					<div wire:ignore>
						<select  wire:model.lazy='tipo_comprobante'  style="height: calc(1.2em + 1.4rem + 2px) !important;" class="form-control" >
							<option value="" disabled> Tipo de comprobante</option>
							<option value="CF">Consumidor final</option>
							<option value="A">Factura A</option>
							<option value="B">Factura B</option>
							<option value="C">Factura C</option>

						</select>
					</div>

				</div>
  		<div class="p-2 d-none d-sm-block">
				@if($estado_pedido == '')
									<button wire:click="selectEstado()" type="button" style=" width: 130px; margin-top: 3px; margin-bottom: 0 !important; margin-right: 15px; margin-bottom: 0 !important; " class="btn btn-dark" > Estado </button>

										@endif

										@if($estado_pedido == 'Pendiente')
										<button wire:click="selectEstado()" type="button" style=" width: 130px; margin-top: 3px; margin-bottom: 0 !important; margin-right: 15px; margin-bottom: 0 !important; " class="btn btn-warning" > Pendiente </button>

										@endif


											@if($estado_pedido == 'En proceso')
										<button wire:click="selectEstado()" type="button" style=" width: 130px; margin-top: 3px; margin-bottom: 0 !important; margin-right: 15px; margin-bottom: 0 !important; " class="btn btn-secondary" > En proceso </button>

												@endif


												@if($estado_pedido == 'Entregado')

												<button type="button" wire:click="selectEstado()" style=" width: 130px; margin-top: 3px; margin-bottom: 0 !important; margin-right: 15px; margin-bottom: 0 !important; " class="btn btn-success" > Entregado </button>
													@endif

			</div>

			</div>


			</div>
		<div class="col-sm-12 col-md-12">
			<div id="connect-sorting" class="connect-sorting">


				<div class="col-lg-2 col-md-4 col-sm-12 d-none d-sm-block">
            	<div class="input-group mb-0">
				
						<input id="code" type="text"
							wire:keydown.enter.prevent="$emit('scan-code', $('#code').val())"
							 class="form-control search-form-control  ml-lg-auto"
			placeholder="Cod." style="place">
			
						<div class="input-group-prepend">
								<span wire:click="$emit('scan-code', $('#code').val())" class="input-group-text input-gp">
									<i class="fas fa-search"></i>
								</span> 
							</div>
							</div>

				 </div>

				<div class="col-lg-3 col-md-4 col-sm-12">
				<div style="margin-bottom: 0 !important;" class="input-group mb-4">
					<div class="input-group-prepend">
						<span class="input-group-text input-gp">
							<i class="fas fa-clipboard-list"></i>
						</span>
					</div>
						<input
								style="font-size:14px !important;"
								type="text"
								class="form-control"
								placeholder="Seleccione un producto"
								wire:model="query_product"
								wire:keydown.escape="resetProduct"
								wire:keydown.tab="resetProduct"
								wire:keydown.enter="selectProduct"
						/>
						</div>


						@if(!empty($query_product))
								<div class="fixed top-0 bottom-0 left-0 right-0" wire:click="reset"></div>

								<div style="position:absolute; z-index: 999 !important; height: 250px; width: 300px; overflow: auto;"  class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
										@if(!empty($products_s))
												@foreach($products_s as $i => $product)

												<div class="btn-group" role="group" aria-label="Basic example">
													<button value="{{$product['barcode']}}" id="code{{$product['barcode']}}"  wire:click.prevent="$emit('scan-code', $('#code{{$product['barcode']}}').val())" wire:click.lazy="selectProduct"
													class="btn btn-light" title="Click en el producto">{{ $product['barcode'] }} - {{ $product['name'] }}
											</button>
									    <button hidden value="{{$product['barcode']}}" id="info{{$product['barcode']}}"  wire:click="$emit('info-producto', $('#info{{$product['barcode']}}').val())" style="max-width:50px;" type="button" class="btn btn-dark">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
											 </button>
									</div>



												@endforeach


										@else
										<div style="  padding: 10px;  text-align: center;"  class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
										No hay resultados
										</div>
										@endif
										@can('product_create')
										<a href="javascript:void(0)"   style=" position: fixed;   width: 300px;  margin-top: 250px;" class="btn btn-dark" data-toggle="modal" data-target="#ModalProductos" >Agregar otro producto</a>
										@endcan

								</div>
						@endif

				</div>


				<div class="col-lg-3 col-md-4 col-sm-12">
				<div style="margin-bottom: 0 !important;" class="input-group mb-4">
					<div class="input-group-prepend">
						<span class="input-group-text input-gp">
							<i class="fas fa-users"></i>
						</span>
					</div>
						<input
								style="font-size:14px !important;"
								type="text"
								class="form-control"
								placeholder="Seleccione un cliente"
								wire:model="query"
								wire:keydown.escape="resetCliente"
								wire:keydown.tab="resetCliente"
								wire:keydown.enter="selectContact"
						/>
						</div>



						@if(!empty($query))
								<div class="fixed top-0 bottom-0 left-0 right-0" wire:click="reset"></div>

								<div style="position:absolute; z-index: 999 !important;"  class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
										@if(!empty($contacts))
												@foreach($contacts as $i => $contact)
												<a href="javascript:void(0)"
												wire:click="selectContact({{$contact['id']}})"
												class="btn btn-light" title="Edit">{{ $contact['nombre'] }}
										</a>


												@endforeach
												<a href="javascript:void(0)"   class="btn btn-dark" data-toggle="modal" data-target="#ModalCliente" >Agregar otro cliente</a>

										@else

										@endif
								</div>
						@endif

				</div>




<div class="col-lg-2 col-md-4 col-sm-12 d-none d-sm-block  ">
	<div style="margin-bottom: 0 !important;" class="input-group mb-4">
		<div class="input-group-prepend">
			<span class="input-group-text input-gp">
				<i class="fas fa-user"></i>
			</span>
		</div>

				<select  style="font-size:14px !important;" wire:model.lazy='usuario_activo' class="form-control">

						@foreach($user as $usuario)
						<option style="font-size:14px !important;" value="{{$usuario->id}}">{{$usuario->name}}</option>
						@endforeach
					</select>

				</div>

		</div>

		<div class="col-lg-2 col-md-4 col-sm-12 d-none d-sm-block">


												<input autocomplete="off" type="date" wire:change="CambioCaja()" wire:model.lazy="fecha_pedido" class="form-control" required="">


				</div>


		</div>

		</div>

		<div class="col-sm-12 col-md-8">
			<!-- DETALLES -->
			<button hidden type="button" name="button" wire:click="enviarNotificacion()">Notificacion</button>


			@include('livewire.pos.partials.detail')


		</div>

		<div class="col-sm-12 col-md-4">
		    
			<!-- TOTAL -->
			@include('livewire.pos.partials.total')


			<!-- DENOMINATIONS
			@include('livewire.pos.partials.coins')
-->

		</div>
		@include('livewire.pos.form-hoja-ruta')
		@include('livewire.pos.form-hoja-ruta-nueva')
		@include('livewire.factura.form-pagos')
		@include('livewire.pos.agregar-pago')
		@include('livewire.pos.partials.comentarios')
		@include('livewire.pos.pago-dividido')
		@include('livewire.pos.cheque')

		@include('livewire.reports.sales-detail2')
		@include('livewire.reports.sales-detail3')

		@include('livewire.pos.partials.form-alto')
		@include('livewire.pos.estado-pedido-pos')
		@include('livewire.pos.form')
		@include('livewire.pos.form-products')
		@include('livewire.pos.form-banco')
		@include('livewire.pos.form-metodo-pago')


		@include('livewire.pos.partials.imprimir')


		@include('livewire.pos.ver-factura')

		@include('livewire.pos.info')

		@include('livewire.pos.variaciones')





	</div>




@endif



<input type="hidden" id="completo_formulario" value="{{$completa_formulario->completo_formulario}}" >

<script src="{{ asset('js/keypress.js') }}"></script>
<script src="{{ asset('js/onscan.js') }}"></script>
<script>

try{

    onScan.attachTo(document, {
    suffixKeyCodes: [13],
    onScan: function(barcode) {
        console.log(barcode)
        window.livewire.emit('scan-code', barcode)
    },
    onScanError: function(e){
        //console.log(e)
    }
})

    console.log('Scanner ready!')


} catch(e){
    console.log('Error de lectura: ', e)
}


</script>
<script>
    document.addEventListener('DOMContentLoaded', function(){
            livewire.on('scan-code', action => {
                $('#code').val('')
            })

						window.livewire.on('modal-formulario', Msg => {
							$('#ModalFormulario').modal('show')
							document.getElementById('ModalFormulario').style.display = 'block';

						})

						window.livewire.on('cliente-agregado', Msg => {
								$('#theModal-cliente').modal('hide')
								noty(Msg)
						})

								window.livewire.on('agregar-cliente', Msg => {
										$('#theModal-cliente').modal('show')
								})


								window.livewire.on('variacion-elegir', Msg => {
										$('#Variaciones').modal('show')
								})

								window.livewire.on('variacion-elegir-hide', Msg => {
										$('#Variaciones').modal('hide')
								})

								window.livewire.on('formulario', Msg => {
									$('#ModalFormulario').modal('show');
									document.getElementById('ModalFormulario').style.display = 'block';
								})

								window.livewire.on('formulario-hide', Msg => {
										$('#ModalFormulario').modal('hide');
										document.getElementById('ModalFormulario').style.display = 'none';
										noty(Msg);
								})

								window.livewire.on('agregar-pago', Msg =>{
										$('#AgregarPago').modal('show')
								})

								window.livewire.on('agregar-pago-hide', Msg =>{
										$('#AgregarPago').modal('hide')
								})

								window.livewire.on('show-modal', Msg =>{
										$('#modalDetails').modal('show')
								})

								window.livewire.on('show-modal-alto', Msg =>{
										$('#modalDetailsAlto').modal('show')
								})

								window.livewire.on('show-modal2', Msg =>{
										$('#modalDetails2').modal('show')
								})

								window.livewire.on('pago-dividido', Msg =>{
										$('#PagoDividido').modal('show')
								})

								window.livewire.on('pago-dividido-hide', Msg =>{
										$('#PagoDividido').modal('hide')
								})

								window.livewire.on('cheque', Msg =>{
										$('#Cheque').modal('show')
								})

								window.livewire.on('cheque-hide', Msg =>{
										$('#Cheque').modal('hide')
								})

								window.livewire.on('tipo-pago-nuevo-show', Msg =>{
										$('#ModalBanco').modal('show')
								})

								window.livewire.on('tipo-pago-nuevo-hide', Msg =>{
										$('#ModalBanco').modal('hide')
								})

								window.livewire.on('metodo-pago-nuevo-show', Msg =>{
										$('#ModalMetodoPago').modal('show')
								})

								window.livewire.on('metodo-pago-nuevo-hide', Msg =>{
										$('#ModalMetodoPago').modal('hide')
								})

								window.livewire.on('hide-modal2', Msg =>{
										$('#modalDetails2').modal('hide')
								})

								window.livewire.on('cerrar-factura', Msg =>{
										$('#theModal1').modal('hide')
								})

								window.livewire.on('modal-show', msg => {
									$('#theModal1').modal('show')
								})

								window.livewire.on('abrir-hr-nueva', msg => {
									$('#theModal').modal('show')
								})

								window.livewire.on('show-modal3', Msg =>{
										$('#modalDetails3').modal('show')
								})

								window.livewire.on('hide-modal3', Msg =>{
										$('#modalDetails3').modal('hide')
								})

								window.livewire.on('modal-hr-hide', Msg =>{
										$('#theModal').modal('hide')
								})

								window.livewire.on('productos-hide', Msg =>{
										$('#ModalProductos').modal('hide')
								})

								window.livewire.on('metodo-pago-hide', Msg =>{
										$('#ModalMetodoPago').modal('hide')
								})

								window.livewire.on('cliente-hide', Msg =>{
										$('#ModalCliente').modal('hide')
								})

								window.livewire.on('banco-hide', Msg =>{
										$('#ModalBanco').modal('hide')
								})

								window.livewire.on('info-prod', Msg =>{
										$('#InfoProducto').modal('show')
								})

								window.livewire.on('hide-info-prod', Msg =>{
										$('#InfoProducto').modal('hide')
								})


								window.livewire.on('hr-added', Msg => {
									noty(Msg)
								})

								window.livewire.on('modal-estado', Msg =>{
										$('#modalDetails-estado-pedido').modal('show')

								})

								window.livewire.on('modal-estado-hide', Msg =>{
										$('#modalDetails-estado-pedido').modal('hide')
								})

								window.livewire.on('hr-asignada', Msg => {
									noty(Msg)
								})

								window.livewire.on('pago-agregado', Msg => {
									noty(Msg)
								})

								window.livewire.on('pago-actualizado', Msg => {
									noty(Msg)
								})

                                window.livewire.on('mail-modal', Msg =>{
                                $('#modalImprimir').modal('hide')
                                $('#MailModal').modal('show')
                                })


								window.livewire.on('pago-eliminado', Msg => {
									noty(Msg)
								})




								window.livewire.on('buscar-stock', id => {
									swal({
										title: 'BUSCAR STOCK EN OTRA SUCURSAL',
										text: '¿DESEA BUSCAR EN OTRAS SUCURSALES ESTE PRODUCTO?',
										showCancelButton: true,
										cancelButtonText: 'Cerrar',
										cancelButtonColor: '#fff',
										confirmButtonColor: '#3B3F5C',
										confirmButtonText: 'Aceptar'
									}).then(function(result) {
										if (result.value) {
											window.livewire.emit('info-producto', id)
											swal.close()
										}

									})

								})


								window.livewire.on('volver-stock', id => {
								var stock = $("#q"+id).val();
								$("#r"+id).val(stock);
								})

								window.livewire.on('no-stock', Msg => {
									noty(Msg, 2)
								})



								window.livewire.on('imprimir-show', Msg => {
									$('#modalImprimir').modal('show')
								})

    			    	window.livewire.on('no-factura', id => {
                swal({
                title: 'IMPORTATE',
                text: 'DEBE CONFIGURAR SUS DATOS FISCALES ANTES DE FACTURAR',
                showCancelButton: true,
                cancelButtonText: 'CERRAR',
                cancelButtonColor: '#fff',
                confirmButtonColor: '#3B3F5C',
                confirmButtonText: 'IR A CONFIGURAR'
                }).then(function(result) {
                if (result.value) {
                window.location.href = '/mi-comercio';
                swal.close()
                }

                })

                })





								window.livewire.on('mensaje-facturar', id => {
									swal({
										title: 'FACTURAR',
										text: '¿DESEA EMITIR LA FACTURA DE AFIP?',
										showCancelButton: true,
										cancelButtonText: 'Cerrar',
										cancelButtonColor: '#fff',
										confirmButtonColor: '#3B3F5C',
										confirmButtonText: 'Aceptar'
									}).then(function(result) {
									    if (result.value === true) {
											window.livewire.emit('emitir-factura', id)
											swal.close()
										} else {
										    window.livewire.emit('print', id)
											swal.close()
										}

									})

								})
						


								window.livewire.on('update-cliente-modal', query_id => {
									swal({
										title: 'CAMBIO DE PRECIOS',
										text: 'HEMOS DETECTADO QUE EL CLIENTE TIENE UNA LISTA DE PRECIOS DIFERENCIAL , ¿DESEA ADECUAR LOS PRECIOS DEL CARRITO A LA LISTA DE PRECIOS DEL CLIENTE?',
										showCancelButton: true,
										cancelButtonText: 'NO',
										cancelButtonColor: '#fff',
										confirmButtonColor: '#3B3F5C',
										confirmButtonText: 'SI'
									}).then(function(result) {
										if (result.value) {
											window.livewire.emit('update-cliente', query_id)
											swal.close()
										}

									})

								})

								var total = $('#suma_totales').val();
								$('#ver_totales').html('Ventas: '+total);


    });
</script>
<script type="text/javascript">
function mostrar()  {

var viewed =	localStorage.getItem("viewed");
var completo_formulario = document.getElementById('completo_formulario').value;


if (completo_formulario != 1) {

if (viewed != 'true') {

$('#ModalFormulario').modal('show');
localStorage.setItem("viewed", 'true');

}

}

}
		 window.onload = mostrar;
</script>
<script type="text/javascript">
	function mostrar_empleador() {
		$('#ModalFormulario').modal('show');
		document.getElementById('ModalFormulario').style.display = 'block';
	}
</script>

@include('livewire.pos.scripts.shortcuts')
@include('livewire.pos.scripts.events')
@include('livewire.pos.scripts.general')
