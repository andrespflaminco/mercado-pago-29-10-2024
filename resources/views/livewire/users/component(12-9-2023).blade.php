<div class="row sales layout-top-spacing">

    <div class="col-sm-12">
        <div class="widget widget-chart-one">
            <div class="widget-heading">
                <h4 class="card-title">
                    <b>{{$componentName}} | {{ $pageTitle }}</b>
                </h4>
                <ul class="tabs tab-pills">
                    <li>

                      @if(Auth::user()->plan == 3)
                        <a href="javascript:void(0)" class="tabmenu bg-dark" data-toggle="modal" data-target="#theModal">Agregar</a>
                      @endif

                      @if(Auth::user()->plan == 2)

                      @if($cantidad_usuarios->cantidad_usuarios < 3)
                      <a href="javascript:void(0)" class="tabmenu bg-dark" data-toggle="modal" data-target="#theModal">Agregar</a>
                      @else
                      <p> Limite de 3 usuarios alcanzado</p>

                      @endif

                      @endif

                          <a hidden href="javascript:void(0)" class="tabmenu bg-dark" data-toggle="modal" wire:click="editfacturacion()">Facturacion</a>


                    </li>
                </ul>
            </div>
            		<div class="row">
            	    	<div class="col-lg-3 col-md-3 col-sm-3">

						<div class="input-group mb-4">
							<div class="input-group-prepend">
								<span class="input-group-text input-gp">
									<i class="fas fa-search"></i>
								</span>
							</div>
							<input type="text" wire:model.lazy="buscar" autocomplete="off" placeholder="Buscar" class="form-control">
						</div>

					</div>


                @if(auth()->user()->profile === "Admin")

					<div class="col-lg-3 col-md-3 col-sm-3">

						<div class="input-group mb-4">
							<div class="input-group-prepend">
								<span class="input-group-text input-gp">
									<i class="fas fa-user"></i>
								</span>
							</div>
						<select wire:model.lazy="categoria_profile" class="form-control">
                			<option value="Elegir" selected>Elegir</option>
                			@foreach($roles as $role)
                			<option value="{{$role->name}}" selected>{{$role->name}}</option>
                			@endforeach
                		</select>
						</div>

					</div>

					@endif

            		</div>

            <div class="widget-content">

                <div class="table-responsive">
                    <table class="table table-bordered table striped mt-1">
                        <thead class="text-white" style="background: #3B3F5C">
                            <tr>
                                <th class="table-th text-white">USUARIO</th>
                                <th class="table-th text-white text-center">TELÉFONO</th>
                                <th class="table-th text-white text-center">EMAIL</th>
                                <th class="table-th text-white text-center">ESTATUS</th>
                                <th class="table-th text-white text-center">PERFIL</th>
                                <th class="table-th text-white text-center">IMÁGEN</th>
                                <th class="table-th text-white text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $r)
                            <tr>
                                <td><h6>{{$r->name}}</h6></td>

                                <td class="text-center"><h6>{{$r->phone}}</h6></td>
                                <td class="text-center"><h6>{{$r->email}}</h6></td>
                                <td class="text-center">
                                    <span class="badge {{ $r->status == 'Active' ? 'badge-success' : 'badge-danger' }} text-uppercase">{{$r->status}}</span>
                                </td>
                                <td class="text-center text-uppercase">
                                    <h6>{{$r->profile}}</h6>
                                    <small><b>Roles:</b>{{implode(',',$r->getRoleNames()->toArray())}}</small>
                                </td>

                                <td class="text-center">
                                 @if($r->image != null)
                                 <img  width="100" class="rounded"
                                 src="{{ asset('storage/users/'.$r->image) }}"
                                 >
                                 @endif
                             </td>

                             <td class="text-center">

                               @can('user_create')

                                <a href="javascript:void(0)"
                                wire:click="edit({{$r->id}})"
                                class="btn btn-dark mtmobile" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            @if(Auth()->user()->id != $r->id)
                            <a href="javascript:void(0)"
                            onclick="Confirm('{{$r->id}}')"
                            class="btn btn-dark" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                        @endif

                        @endcan


                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{$data->links()}}
    </div>

</div>


</div>

						<div >

    <input style="color:none; background: transparent;" type="text" class="form-control">
						</div>


</div>

@include('livewire.users.form')
@include('livewire.users.form-facturacion')
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
        window.livewire.on('hide-modal-facturacion', Msg => {
            $('#theModalFacturacion').modal('hide')
        })
        window.livewire.on('show-modal-facturacion', Msg => {
            $('#theModalFacturacion').modal('show')
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
