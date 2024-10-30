
<div>	                
	                @if($agregar == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>Insumos</h4>
							<h6>Ver listado de insumos</h6>
						</div>
						<div class="page-btn">    
						
                			@if(auth()->user()->sucursal != 1)
                			<a href="javascript:void(0)" wire:click="ExportarInsumos" class="btn btn-success">Exportar</a>
                			<a href="{{url('import-insumos')}}" class="btn btn-success">Importar</a>
                			<a href="javascript:void(0)" wire:click="Agregar()" class="btn btn-success" style="border-color: #FF9F43 !important; background: #FF9F43 !important;">+ Agregar</a>
						    @endif
						    
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
				
						<div class="card-body">
							<div class="table-top">
								<div class="search-set">
									<div class="search-path">
										<a class="btn btn-filter" id="filter_search">
											<img src="{{ asset('assets/pos/img/icons/filter.svg') }}"  alt="img">
											<span><img src="{{ asset('assets/pos/img/icons/closes.svg') }}" alt="img"></span>
										</a>
									</div>
									<input type="text" autocomplete="off" wire:model="search" placeholder="Buscar.." class="form-control"	>
									<div hidden class="search-input">
										<a class="btn btn-searchset"><img src="{{ asset('assets/pos/img/icons/search-white.svg') }}" alt="img"></a>
									</div>
								</div>
								<div hidden class="wordset">
									<ul>
										<li>
											<a data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img  src="{{ asset('assets/pos/img/icons/pdf.svg') }}"  alt="img"></a>
										</li>
										<li>
											<a data-bs-toggle="tooltip" data-bs-placement="top" title="excel"><img  src="{{ asset('assets/pos/img/icons/excel.svg') }}" alt="img"></a>
										</li>
										<li>
											<a data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img  src="{{ asset('assets/pos/img/icons/printer.svg') }}" alt="img"></a>
										</li>
									</ul>
								</div>
							</div>
							                											    
                		     @if(Auth::user()->sucursal != 1)				   
                			 @if(Auth::user()->profile != "Cajero" )
							 @include('common.accion-lote')
							 @endif
							 @endif

							 
							<div class="table-responsive mb-3">
                            <table class="table mb-3">
        <thead>
            <tr>
                <th colspan="5"></th>
                @foreach($sucursales_con_central as $s)
                    <th colspan="2" class="text-center" style="border-left: solid 1px #eee; border-right: solid 1px #eee;">{{$s->name}}</th>
                @endforeach
                <th></th>
            </tr>
            <tr>
                <th>
                    @if(Auth::user()->sucursal != 1)
                        @if(Auth::user()->profile != "Cajero" )
                            <label class="checkboxs">
                                <input name="Todos" type="checkbox" value="1" onclick="CheckTodosLote()" class="check_todos"/>
                                <span class="checkmarks"></span>
                            </label>
                        @endif
                    @endif
                </th>
                <th>Nombre del insumo</th>
                <th>Codigo</th>
                <th>Costo unitario</th>
                <th>Contenido por unidad</th>

                @foreach($sucursales_con_central as $s)
                    <th style="border-left: solid 1px #eee;">Unidades</th>
                    <th style="border-right: solid 1px #eee;">Stock total</th>
                @endforeach
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $product)
                <tr>
                    <td>
                        <div class="n-chk">
						<label class="checkboxs">
						    <input type="checkbox" wire:model.defer="id_check" tu-attr-id="{{($product->id)}}"  class="mis-checkboxes" value="{{$product->id}}">
							<span class="checkmarks"></span>
						</label>
                        </div>
                    </td>
                    <td>{{$product->name}}</td>
                    <td>{{$product->barcode}}</td>
                    <td>$ {{number_format($product->cost,2,",",".")}}</td>
                    <td> {{number_format($product->cantidad,3,",",".")}}  {{$product->unidad_medida}}</td>
                    
                    @foreach($sucursales_con_central as $s)
                        @php
                            $stockKey = 'stock_' . $s->sucursal_id;
                            $stock = $product->$stockKey ?? 0;
                            $contenidoTotal = $stock * $product->cantidad;
                        @endphp
                        
                        <td style="border-left: solid 1px #eee; ">{{ number_format($stock, 3, ",", ".") }}</td>
                        <td style="border-right: solid 1px #eee;">{{ number_format($contenidoTotal, 3, ",", ".") }} {{$product->unidad_medida}}</td>
                        
                        
                    @endforeach

                    <td>
                        
                        @if($estado_filtro == 0)
                        <a class="me-3" href="javascript:void(0)" wire:click="Edit({{$product->id}})">
                            <img src="{{ asset('assets/pos/img/icons/edit.svg') }}" alt="img">
                        </a>
                        
                        @if(auth()->user()->sucursal != 1)
                            <a href="javascript:void(0)" onclick="Confirm('{{$product->id}}')">
                                <img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
                            </a>
                        @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
                            </div>
   {{$data->links()}} 

							
							
						</div>
					</div>
					
					<!-- /product list -->
					@endif 
					
					@if($agregar == 1)
					@include('livewire.insumos.agregar-editar-insumo')
					@endif 
					
					
					</div>
					
	
<script>
					    
	function Confirm(id) {

		swal({
			title: 'CONFIRMAR',
			text: '多CONFIRMAS ELIMINAR EL REGISTRO?',
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

<script type="text/javascript">

	function RestaurarCategoria(id) {

    swal({
      title: 'CONFIRMAR',
      text: 'QUIERE RESTAURAR LA CATEGORIA?',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('RestaurarCategoria', id)
        swal.close()
      } 

    })
  }

</script>
<script>
    	document.addEventListener('DOMContentLoaded', function() {


		window.livewire.on('noty', msg => {
			noty(msg)
		});


	});

</script>
