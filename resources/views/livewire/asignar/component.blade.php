<div>
    
	                <div class="page-header">
					<div class="page-title">
							<h4>Permisos</h4>
							<h6>Ver listado de permisos</h6>
						</div>
						<div class="page-btn">
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
	<div class="card-body">	
	<div hidden class="row mb-3">
    <div class="col col-lg-3 col-sm-12">
   	<label>Buscar</label><br>
	<input class="form-control" wire:model="search" placeholder="Buscar permiso">    
     </div>	    
	</div>
<div class="table-responsive">
	<table class="table">
	   <thead>
        <tr>
            <th>Modulo</th>
            <th>Permisos/Roles</th>
             
            @php
            $x = 0;
            @endphp
            
            @foreach($roles_unicos as $rol)
                @php
                    $cadena = $rol;
                    $partes = explode('|', $cadena);
                    $nombreRol = $partes[0]; // Contiene "Admin"
                    $idRol = $partes[1]; // Contiene "1"
                    $rol_comercio_id = DB::table('roles')->where('id', $idRol)->value('comercio_id');
                @endphp

                @php
                $x++;
                @endphp
            
                <th {{ (auth()->user()->profile != "Admin") && ($x == 1) ? 'hidden' : '' }} class="text-center">
                    {{ $nombreRol }}
  
                    @if(auth()->user()->sucursal != 1)
                    <input type="checkbox"  
                           wire:model="checkboxValues.{{ $idRol }}"
                           wire:click="syncAll('{{ $idRol }}')"  
                           {{ (auth()->user()->profile != "Admin") && ($rol_comercio_id == 1) ? 'hidden' : '' }} >
                           
                    @else    
                            
                    <input type="checkbox"  
                           wire:model="checkboxValues.{{ $idRol }}"
                           wire:click="syncAll('{{ $idRol }}')"  
                           {{ (auth()->user()->profile != "Admin") && ($rol_comercio_id != auth()->user()->id) ? 'hidden' : '' }} >
                    @endif

                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($permisos_unicos as $permiso)
            @php
                $id_permiso = $permisos_ids[$permiso];
            @endphp

            @if($id_permiso != null)
                <tr>
                    @foreach($permisos_modulos as $pm)
                        @if($pm->name == $permiso)
                            <td>{{$pm->modulo}}</td>
                        @endif
                    @endforeach 

                    <td>{{ $permiso }}</td>

                    @php
                    $i = 0;
                    @endphp
                    @foreach ($roles_unicos as $rol)
                        @php
                            $cadena = $rol;
                            $partes = explode('|', $cadena);
                            $nombreRol = $partes[0]; // Contiene "Admin"
                            $idRol = $partes[1]; // Contiene "1"
                            $rol_comercio_id = DB::table('roles')->where('id', $idRol)->value('comercio_id');
                        @endphp

                        @php
                        $i++;
                        @endphp
            
                        <td {{ (auth()->user()->profile != "Admin") && ($idRol == 1) ? 'hidden' : '' }} class="text-center">
                            
                            @if(auth()->user()->id == 1)
                            
                            <input type="checkbox" 
                            wire:model="matriz.{{ $permiso }}.{{ $rol }}"
                            wire:click="actualizarMatriz('{{ $permiso }}', '{{ $rol }}', $event.target.checked, {{ $id_permiso }})"
                            >  
                                                        
                            @else
                            <!---- si es sucursal  -------->
                            @if(auth()->user()->sucursal != 1)
                            <input type="checkbox" 
                            wire:model="matriz.{{ $permiso }}.{{ $rol }}"
                            wire:click="actualizarMatriz('{{ $permiso }}', '{{ $rol }}', $event.target.checked, {{ $id_permiso }})"
                            {{ $rol_comercio_id == 1 ? 'disabled' : '' }}>  
                            
                            @else    
                            
                            <input type="checkbox" 
                            wire:model="matriz.{{ $permiso }}.{{ $rol }}"
                            wire:click="actualizarMatriz('{{ $permiso }}', '{{ $rol }}', $event.target.checked, {{ $id_permiso }})"
                            {{ $rol_comercio_id != auth()->user()->id ? 'disabled' : '' }}>  
                            
                            @endif
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
</div>


						</div>
					</div>
					</div>	
					

<script>
    document.addEventListener('DOMContentLoaded', function(){
        
        window.livewire.on('sync-error', Msg => {
            noty(Msg)
        })
        window.livewire.on('permi', Msg => {
            noty(Msg)
        })
        window.livewire.on('syncall', Msg => {
            noty(Msg)
        })
        window.livewire.on('removeall', Msg => {
            noty(Msg)
        })


    });


    function Revocar()
    {   

        swal({
            title: 'CONFIRMAR',
            text: '¿CONFIRMAS REVOCAR TODOS LOS PERMISOS?',
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Cerrar',
            cancelButtonColor: '#fff',
            confirmButtonColor: '#3B3F5C',
            confirmButtonText: 'Aceptar'
        }).then(function(result) {
            if(result.value){
                window.livewire.emit('revokeall')
                swal.close()
            }

        })
    }

</script>