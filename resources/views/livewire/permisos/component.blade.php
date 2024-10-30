<div>
    
	                <div class="page-header">
					<div class="page-title">
							<h4>Permisos</h4>
							<h6>Ver listado de permisos</h6>
						</div>
						<div class="page-btn">
							<button onclick="BorrarMsg"  wire:click="Agregar()" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar</button>
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
								<div class="col-4">
						               <select class="form-control" wire:model="search_modulo">
                                        <option value="Elegir">Todos los modulos</option>
                                        <option value="Menu">Menu</option>
                                        <option value="Ventas">Ventas</option>
                                        <option value="Ventas Resumen">Ventas Resumen</option>
                                        <option value="Ventas Resumen por Producto">Ventas Resumen por Producto</option>
                                        <option value="Gastos">Gastos</option>
                                        <option value="Compra a un proveedor">Compra a un proveedor</option>  
                                        <option value="Compra a casa central">Compra a casa central</option>  
                                        <option value="Compras resumen">Compras resumen</option>  
                                        <option value="Productos">Productos</option>  
                                        <option value="Lista de precios">Lista de precios</option>
                                        <option value="Actualizaciones masivas de productos">Actualizaciones masivas de productos</option>  
                                        <option value="Atributos y variaciones">Atributos y variaciones</option> 
                                        <option value="Categorias">Categorias</option> 
                                        <option value="Almacenes">Almacenes</option>
                                        <option value="Movimientos de stock">Movimientos de stock</option> 
                                        <option value="Bancos">Bancos</option> 
                                        <option value="Metodos de cobro">Metodos de cobro</option> 
                                        <option value="Cajas">Cajas</option> 
                                        <option value="Clientes">Clientes</option>
                                        <option value="Proveedores">Proveedores</option>
                                        <option value="Usuarios">Usuarios</option>
                                        <option value="Mis sucursales">Mis sucursales</option>
                                        <option value="Tienda Flaminco">Tienda Flaminco</option>  
                                        <option value="Wocommerce">Wocommerce</option>
                                        <option value="Configuracion">Configuracion</option>
                                        <option value="Facturacion">Facturacion</option>
                                       </select>    
								</div>
							

								<div class="wordset">
									<ul>
							
									</ul>
								</div>
							</div>
							
							 
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
										
											<th class="text-center">Id</th>
											<th class="text-center">Modulo</th>
											<th class="text-center">Permiso</th>
											<th class="text-center">Acciones</th>
										</tr>
									</thead>
									<tbody>
									@foreach($permisos as $permiso)
                                            <tr>
                                                <td class="text-center">{{$permiso->id}}</td>
                                                <td class="text-center">
                                                 {{$permiso->modulo}}
                                                </td>
                                                <td class="text-center">
                                                 {{$permiso->name}}
                                             </td>
                                             <td class="text-center">
                                                                           
                    			                <a class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$permiso->id}})" >
													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
												</a>
												<a href="javascript:void(0)" onclick="Confirm('{{$permiso->id}}')"  >
													<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
												</a>
                                        </a>
                
                
                                    </td>
                                </tr>
                                @endforeach    
					  
									</tbody>
								</table>
								{{$permisos->links()}}
							</div>
						</div>
					</div>
					
					<!-- /product list -->


@include('livewire.permisos.form')

</div>



<script>
    document.addEventListener('DOMContentLoaded', function(){
          


        window.livewire.on('permiso-added', Msg => {
            $('#theModal').modal('hide')
            noty(Msg)
        })
        window.livewire.on('permiso-updated', Msg => {
            $('#theModal').modal('hide')
            noty(Msg)
        })
        window.livewire.on('permiso-deleted', Msg => {           
            noty(Msg)
        })
        window.livewire.on('permiso-exists', Msg => {            
            noty(Msg)
        })
        window.livewire.on('permiso-error', Msg => {            
            noty(Msg)
        })
        window.livewire.on('hide-modal', Msg => {
            $('#theModal').modal('hide')            
        })
        window.livewire.on('show-modal', Msg => {
            $('#theModal').modal('show')
        })
              

    });


    function Confirm(id)
    {   

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
            if(result.value){
                window.livewire.emit('destroy', id)
                swal.close()
            }

        })
    }
    
    

</script>