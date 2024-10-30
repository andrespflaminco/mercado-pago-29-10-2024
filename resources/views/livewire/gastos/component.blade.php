<div>	  

 
 <div @if($agregar == 1) hidden  @endif>
	                <div class="page-header">
					<div class="page-title">
							<h4>Gastos</h4> 
							<h6>Ver listado de gastos</h6>
						</div>
						<div class="page-btn  d-lg-flex d-sm-block">
						    <a href="javascript:void(0)" class="btn btn-dark"  wire:click.prevent="GetEtiqueta()">Etiquetas</a>
						    <a href="javascript:void(0)" class="btn btn-dark" wire:click.prevent="GetCategorias()">Categorias</a>
					        <a href="javascript:void(0)" class="btn btn-added" wire:click="Agregar"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar gasto</a>
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
				
						<div class="card-body">
							<div class="table-top">
								<div class="search-set">
									<div class="search-path">
									    
									    @include('common.boton-filtros')

									</div>
									<input type="text" autocomplete="off" wire:model="search" placeholder="Buscar.." class="form-control"	>
									<div hidden class="search-input">
										<a class="btn btn-searchset"><img src="{{ asset('assets/pos/img/icons/search-white.svg') }}" alt="img"></a>
									</div>
								</div>
								<div class="wordset">
									<ul>
										<li>
											<a hidden data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img  src="{{ asset('assets/pos/img/icons/pdf.svg') }}"  alt="img"></a>
										</li>
										<li>
										    
										    <a 
											style="font-size:12px !important; padding:5px !important; background: #198754 !important;" 
											class="btn btn-cancel" 
											wire:click="ExportarReporte('{{ ( ($search == '' ? '0' : $search) . '/' . ($categoria_filtro == '' ? '0' : $categoria_filtro)  .  '/' . ($etiquetas_filtro_excel == '' ? '0' : $etiquetas_filtro_excel) . '/' . ($metodo_pago_filtro == '' ? '0' : $metodo_pago_filtro) . '/' . ($forma_pago_filtro == '' ? '0' : $forma_pago_filtro) . '/'  . $dateFrom . '/' . $dateTo) }}')"   title="Descargar Excel"  data-bs-placement="top" title="exportar excel"> 
											<svg style="margin-right: 5px;"  xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
											Exportar </a>
											
											<a hidden wire:click="ExportarReporte('{{ ( ($search == '' ? '0' : $search) . '/' . ($categoria_filtro == '' ? '0' : $categoria_filtro)  .  '/' . ($etiquetas_filtro_excel == '' ? '0' : $etiquetas_filtro_excel) . '/' . ($metodo_pago_filtro == '' ? '0' : $metodo_pago_filtro) . '/' . ($forma_pago_filtro == '' ? '0' : $forma_pago_filtro) . '/'  . $dateFrom . '/' . $dateTo) }}')"  title="excel"><img  src="{{ asset('assets/pos/img/icons/excel.svg') }}" alt="img"></a>
										</li>
										<li>
											<a hidden data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img  src="{{ asset('assets/pos/img/icons/printer.svg') }}" alt="img"></a>
										</li>
									</ul>
								</div>
							</div>
							
							
							<!-- /Filter -->
							    
								<div class="card mb-0"  @if(!$mostrarFiltros) hidden @endif >
								<div class="card-body pb-0">
									<div class="row">
										<div class="col-lg-12 col-sm-12">
											<div class="row">
										         <div class="row">
										             
												<div class="col-lg col-sm-6 col-12">
													<div class="form-group">
								                    <label>Categoria</label>
													<select wire:model='categoria_filtro' class="form-control">
                                						<option value="" >Todos</option>
                                						<option value="1" >Sin categoria</option>
                                						@foreach($gastos_categoria as $gc)
                                							<option value="{{$gc->id}}" >{{$gc->nombre}}</option>
                                						@endforeach
                                					</select>
													</div>
												</div>
												<div class="col-lg col-sm-6 col-12">
													<div class="form-group">
														<label>Etiquetas</label>
                                    					    <div wire:ignore>
                                                            <select class="form-control tagging"  multiple="multiple" id="select2-buscar-etiquetas">
                                                                <option value="" >Sin etiqueta</option>
                                                         
                                                            </select>
                                                            </div>
                                  
													</div>
												</div>
												<div class="col-lg col-sm-6 col-12">
													<div class="form-group">
														<label>Proveedor</label>
                                    					<select wire:model='proveedor_filtro' class="form-control">
                                    						<option value="1">Sin proveedor</option>
                                    						@foreach($gastos_proveedor as $pr)
                                    							<option value="{{$pr->id}}" >{{$pr->nombre}}</option>
                                    						@endforeach
                                    					</select>
													</div>
												</div>   

												
										         </div>
										         
										         <div class="row">
												
												<div class="col-lg col-sm-6 col-12">
												<div class="form-group">
												<label>Fecha</label>
                                                <input type="text" id="date-range-picker" name="date_range" />
        
												</div>
												</div>										          
												<div class="col-lg col-sm-6 col-12">
													<div class="form-group">
														<label>Metodo de pago</label>
                                    					<select wire:model='metodo_pago_filtro' class="form-control">
                                    						<option value="" >Todas</option>
                                    						<option value="1" >Efectivo</option>
                                    						@foreach($metodo_pago as $met)
                                    							<option value="{{$met['id']}}" >{{$met['nombre']}}</option>
                                    						@endforeach
                                    					</select>
													</div>
												</div>

												<div class="col-lg col-sm-6 col-12">
													<div class="form-group">
													    <label style="margin-top: 28px !important;"></label>
													    <button style="background: white !important; " class="btn btn-light ms-auto" wire:click="LimpiarFiltros()" >
													     LIMPIAR
													    </button>
													</div>
												</div>
										         </div>
												

												

											</div>
										</div>
									</div>
								</div>
							</div>
							    
							    
							    
							@include('common.accion-lote') 
							<!-- /Filter -->
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
        								<th>
                                        <input name="Todos" type="checkbox" value="1" onclick="CheckTodosLote()" class="check_todos"/>    
                                        </th>
        								<th>FECHA</th>
        								<th>NOMBRE</th>
        								<th>METODO DE PAGO</th>
        								<th>PROVEEDOR</th>
        								<th>CATEGORIA</th>
        								<th>ETIQUETA</th>
        								<th>DEUDA</th>
        								<th>SUBTOTAL</th>
        								<th>IVA</th>
        								<th>TOTAL</th>
        								<th>ACCIONES</th>
										</tr>
									</thead>
									<tbody>
        							@foreach($data as $metodo)
        							<tr>
        							    <td>
        							        <input type="checkbox" wire:model.defer="id_check" tu-attr-id="{{($metodo->id)}}"  class="mis-checkboxes" value="{{$metodo->id}}">
        							    </td>
        								<td>
        								{{\Carbon\Carbon::parse( $metodo->created_at)->format('d-m-Y')}}
        								</td>
        								<td>
        								{{$metodo->nombre}}
        								</td>
        								<td>
        		                        @php
        		                        if($metodo->cuenta != "" || $metodo->cuenta != null){
            		                      $cuentas = explode(",",$metodo->cuenta);
            		                        
            		                      foreach($cuentas as $cuenta){
            		                      if($cuenta != null){
                                            $registro = \App\Models\bancos::find($cuenta);
                                            if($registro != null){
                                            echo $registro->nombre ." - ";      
                                            }
            		                      }
            		                      }
            		                      
            		                      } 
                                        @endphp
                                        
        								</td>
        								<td>
        							    {{$metodo->nombre_proveedor}}
        								</td>
        								<td>
        							    {{$metodo->nombre_categoria}}
        								</td>
        								<td>
        								@foreach(explode('|', trim($metodo->nombre_etiqueta)) as $etiqueta)
        							
        							   	@foreach($etiquetas as $e)
        								@if($e->nombre == trim($etiqueta))
        								
        								<span class="badge bg-{{$e->color}}"> {{trim($etiqueta)}}  </span>
        							   
        							    
        							    @endif
        							    @endforeach
        							
        							
        							    @endforeach
        							
        								</td>
        								<td>
        							    $ {{number_format($metodo->deuda,2)}}
        								</td>
        								<td>
        							    $ {{number_format($metodo->monto_sin_iva,2)}}
        								</td>
        								<td>
        							    $ {{number_format($metodo->iva,2)}}
        								</td>
        								<td>
        							    $ {{number_format($metodo->monto,2)}}
        								</td>

        
        
        								<td>
        								    @if($estado_filtro == 0 )
        								    <a class="me-3" href="javascript:void(0)" wire:click="Edit({{$metodo->id}})" >
												<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
											</a>
											<a href="javascript:void(0)" onclick="Confirm('{{$metodo->id}}')"  >
												<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
											</a>
											@else
        								    <a href="javascript:void(0)" onclick="RestaurarGasto('{{$metodo->id}}')" class="btn btn-dark text-white" title="Restaurar">
        										RESTAURAR
        									</a>
        								    
        								    @endif
										</td>
        							</tr>
        							@endforeach
									</tbody>
									<tfoot style="border-top: solid 1.5px #dcd9d9;">
        							<td><b>Total</b> </td>
        							<td></td>
        							<td></td>
        							<td></td>
        							<td></td>
        							<td></td>
        							<td></td>
        							<td></td>
        							<td></td>
        							<td></td>
        							<td>  <b>$ {{number_format($gastos_total,2)}}</b>
        							</td>
        							<td class="text-center"></td>
        						</tfoot>
								</table>
								
								<a hidden href="javascript:void(0)" wire:click="NuevoGasto">Nuevo gasto</a>
								{{$data->links()}}
							</div>
						</div>
					</div>
					     
 </div>

<div @if($agregar == 0)  hidden  @endif>
	@include('livewire.gastos.agregar-editar-gastos')    
</div>					



	@include('livewire.gastos.form-abrir')
	@include('livewire.gastos.etiquetas')
	@include('livewire.gastos.categorias')
	@include('livewire.gastos.estado-pedido-pos')

</div>
	
@include('common.script-etiquetas') 


<script>
    document.addEventListener('DOMContentLoaded', function(){

		window.livewire.on('product-added', msg => {
			$('#theModal').modal('hide')
		});
		window.livewire.on('product-updated', msg => {
			$('#theModal').modal('hide')
		});

		window.livewire.on('modal-estado', Msg =>{
				$('#modalDetails-estado-pedido').modal('show')
		})

		window.livewire.on('modal-estado-hide', Msg =>{
				$('#modalDetails-estado-pedido').modal('hide')
		})


		window.livewire.on('product-deleted', msg => {
			// noty
		});
		window.livewire.on('abrir-caja', msg => {
			$('#AbrirCaja').modal('show')
		});
		window.livewire.on('abrir-caja-hide', msg => {
			$('#AbrirCaja').modal('hide')
		});
		window.livewire.on('modal-show', msg => {
			$('#theModal').modal('show')
		});
		window.livewire.on('modal-hide', msg => {
			$('#theModal').modal('hide')
		});
		window.livewire.on('modal-show2', msg => {
			$('#theModal2').modal('show')
		});
		window.livewire.on('modal-hide2', msg => {
			$('#theModal2').modal('hide')
		});
		window.livewire.on('tabs-show', msg => {
			$('#tabsModal').modal('show')
		});
		window.livewire.on('tabs-hide', msg => {
			$('#tabsModal').modal('hide')
		});

		window.livewire.on('categorias-show', msg => {
			$('#categorias').modal('show')
		});
		window.livewire.on('categorias-hide', msg => {
			$('#categorias').modal('hide')
		});

		
        

		window.livewire.on('hidden.bs.modal', msg => {
			$('.er').css('display', 'none')
		});
		$('#theModal').on('hidden.bs.modal', function(e) {
			$('.er').css('display', 'none')
		})
		$('#tabsModal').on('hidden.bs.modal', function(e) {
			$('.er').css('display', 'none')
		})
		$('#theModal').on('shown.bs.modal', function(e) {
			$('.product-name').focus()
		})
		
		window.livewire.on('msg', msg => {
			noty(msg)
		});
		


	});

	function Confirm(id) {

		swal({
			title: 'CONFIRMAR',
			text: '¿CONFIRMAS ELIMINAR EL REGISTRO?',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if (result.value) {
				window.livewire.emit('deleteRow', id)
				swal.close()
			}

		})
	}
</script>
<script>
    document.addEventListener('DOMContentLoaded', function(){


        flatpickr(document.getElementsByClassName('flatpickr'),{
            enableTime: false,
            dateFormat: 'Y-m-d',
            locale: {
                firstDayofWeek: 1,
                weekdays: {
                    shorthand: ["Dom", "Lun", "Mar", "Mi���", "Jue", "Vie", "S���b"],
                    longhand: [
                    "Domingo",
                    "Lunes",
                    "Martes",
                    "Mi���rcoles",
                    "Jueves",
                    "Viernes",
                    "S���bado",
                    ],
                },
                months: {
                    shorthand: [
                    "Ene",
                    "Feb",
                    "Mar",
                    "Abr",
                    "May",
                    "Jun",
                    "Jul",
                    "Ago",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dic",
                    ],
                    longhand: [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Septiembre",
                    "Octubre",
                    "Noviembre",
                    "Diciembre",
                    ],
                },

            }

        })

			});

</script>
<script type="text/javascript">
function Confirm2(id_etiqueta) {

  swal({
    title: 'CONFIRMAR',
    text: '¿CONFIRMAS ELIMINAR LA ETIQUETA?',
    type: 'warning',
    showCancelButton: true,
    cancelButtonText: 'Cerrar',
    cancelButtonColor: '#fff',
    confirmButtonColor: '#3B3F5C',
    confirmButtonText: 'Aceptar'
  }).then(function(result) {
    if (result.value) {
      window.livewire.emit('deleteRow2', id_etiqueta)
      swal.close()
    }

  })
}

function ConfirmCategoria(id_etiqueta) {

  swal({
    title: 'CONFIRMAR',
    text: '¿CONFIRMAS ELIMINAR LA CATEGORIA?',
    type: 'warning',
    showCancelButton: true,
    cancelButtonText: 'Cerrar',
    cancelButtonColor: '#fff',
    confirmButtonColor: '#3B3F5C',
    confirmButtonText: 'Aceptar'
  }).then(function(result) {
    if (result.value) {
      window.livewire.emit('deleteRowCategoria', id_etiqueta)
      swal.close()
    }

  })
}


</script>
<script type="text/javascript">

	function RestaurarGasto(id) {

    swal({
      title: 'CONFIRMAR',
      text: 'QUIERE RESTAURAR EL GASTO?',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('RestaurarGasto', id)
        swal.close()
      } 

    })
  }

</script>