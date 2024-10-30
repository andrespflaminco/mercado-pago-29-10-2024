<div>
    
	                <div class="page-header">
					<div class="page-title">
							<h4>Roles</h4>
							<h6>Ver listado de Roles</h6>
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
											<th class="text-center">Rol</th>
											<th class="text-center">Creador</th>
											<th class="text-center">Acciones</th>
										</tr>
									</thead>
									<tbody>
					                @foreach($roles as $role)
                                        <tr>
                                                <td class="text-center">{{$role->id}}</td>
                                                <td class="text-center">
                                                     {{$role->name}}
                                                 </td>
                                                  <td class="text-center">
                                                      
                                                     @if($role->comercio_id != 1)
                                                    
                                                     @foreach($users as $u)
                                                     @if($role->comercio_id == $u->id)
                                                     {{$u->id}} - {{$u->name}}
                                                     @endif
                                                     @endforeach
                                                     
                                                     @else 
                                                     PREDETERMINADO
                                                     @endif
                                                 </td>
                    			                <td class="text-center">
                                                
                                                
                                                @if(auth()->user()->id != 1)
                                               
                                                @if($role->comercio_id != 1)
                                                    
                                                <a class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$role->id}})" >
													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
												</a>
												<a href="javascript:void(0)" onclick="Confirm('{{$role->id}}')"  >
													<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
												</a>
								                
                                                @endif
                                                
                                                @else
                                                <a class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$role->id}})" >
													<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
												</a>
												<a href="javascript:void(0)" onclick="Confirm('{{$role->id}}')"  >
													<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
												</a>
                                                @endif
                                                
                    			                </td>
                                    </tr>
                                    @endforeach
									</tbody>
								</table>
								{{$roles->links()}}
							</div>
						</div>
					</div>
					
					<!-- /product list -->


@include('livewire.roles.form')


</div>

<script>
    document.addEventListener('DOMContentLoaded', function(){
          


        window.livewire.on('role-added', Msg => {
            $('#theModal').modal('hide')
            noty(Msg)
        })
        window.livewire.on('role-updated', Msg => {
            $('#theModal').modal('hide')
            noty(Msg)
        })
        window.livewire.on('role-deleted', Msg => {           
            noty(Msg)
        })
        window.livewire.on('role-exists', Msg => {            
            noty(Msg)
        })
        window.livewire.on('role-error', Msg => {            
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