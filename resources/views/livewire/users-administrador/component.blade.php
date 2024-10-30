<div>	  

 @if($agregar == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>Usuarios</h4>
							<h6>Ver listado de usuarios</h6>
						</div>
						
						<div class="page-btn  d-lg-flex d-sm-block">
						    @if(Auth::user()->profile != "Cajero" )
						    
						    @if(Auth::user()->sucursal != 1)
    				        <a href="javascript:void(0)" class="btn btn-added" wire:click="Agregar"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar usuario</a>
                     	    @endif
                     	    
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
									<input type="text" autocomplete="off" wire:model="buscar" placeholder="Buscar nombre , mail , id" class="form-control"	>
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
											<a hidden href="{{ url('report/excel-clientes' . '/'. uniqid() ) }}" target="_blank"  title="excel"><img  src="{{ asset('assets/pos/img/icons/excel.svg') }}" alt="img"></a>
										</li>
										<li>
											<a hidden data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img  src="{{ asset('assets/pos/img/icons/printer.svg') }}" alt="img"></a>
										</li>
									</ul>
								</div>
							</div>
							<!-- /Filter -->
							
							<!-- /Filter -->
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
                                        <th wire:click="OrdenarColumna('id')">ID
                                              @if ($columnaOrden == 'id')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif</th>
                                        <th wire:click="OrdenarColumna('name')">USUARIO
                                              @if ($columnaOrden == 'name')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif</th>
                                        <th wire:click="OrdenarColumna('phone')">TELÉFONO
                                              @if ($columnaOrden == 'phone')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif</th>
                                        <th wire:click="OrdenarColumna('email')">EMAIL
                                              @if ($columnaOrden == 'email')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif</th>
                                      
                                        <th wire:click="OrdenarColumna('email_verified_at')">EMAIL VALIDADO
                                              @if ($columnaOrden == 'email_verified_at')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif</th>
                                        <th wire:click="OrdenarColumna('profile')">PERFIL
                                              @if ($columnaOrden == 'profile')
                                                @include('livewire.reports.flecha-' . ($direccionOrden === 'asc' ? 'arriba' : 'abajo'))
                                            @else 
                                                @include('livewire.reports.flecha-arriba-abajo')
                                            @endif</th>
                                        <th>ACCIONES</th>
										</tr>
									</thead>
									<tbody>
        							@foreach($data as $r)
        							<tr>
    	                                <td>{{$r->id}}</td>
    	                                <td>{{$r->name}}</td>
                                        <td>{{$r->phone}}</td>
                                        <td>{{$r->email}}</td>
                                        <td> {{$r->email_verified_at}}</td>
                                        <td>
                                        <p>{{$r->profile}}
                                        @if(Auth::user()->id == 1 )
                                        @if($r->sucursal  != 1)
                                        - Casa central
                                        @else 
                                        - Sucursal
                                        @endif
                                        @endif
                                        </p>
                                        </td>
                                      
                                  		<td>
                                  		   @if(Auth::user()->id == 1 && ($r->profile == "Comercio" ) )
                                          		
                                  		             <a class="me-3" href="javascript:void(0)" wire:click="AgregarMostrador({{$r->id}})" >
        												<img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img">
        											 </a>
        									@endif
        										    <a class="me-3" href="javascript:void(0)" wire:click="edit('{{$r->id}}')" >
        												<img src="{{ asset('assets/pos/img/icons/edit.svg') }}"  alt="img">
        											</a>
        								        	<a href="javascript:void(0)" onclick="Confirm('{{$r->id}}')"  >
        												<img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
        											</a>
        								    
										</td>
        							</tr>
        							@endforeach
									</tbody>
								</table>
								<br>
								{{$data->links()}}
								<br>
							</div>
						</div>
					</div>
					
					
@else
	@include('livewire.users-administrador.agregar-editar-usuario')
@endif




</div>


<script>
    document.addEventListener('DOMContentLoaded', function(){
        window.livewire.on('user-added', Msg => {
            $('#theModal').modal('hide')
            noty(Msg)
        })
        window.livewire.on('user-updated', Msg => {
            $('#theModal').modal('hide')
            noty(Msg)
        })
        window.livewire.on('user-deleted', Msg => {
            noty(Msg)
        })
        window.livewire.on('hide-modal', Msg => {
            $('#theModal').modal('hide')
        })
        window.livewire.on('show-modal', Msg => {
            $('#theModal').modal('show')
        })
        window.livewire.on('user-withsales', Msg => {
            noty(Msg)
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
                window.livewire.emit('deleteRow', id)
                swal.close()
            }

        })
    }
</script>
