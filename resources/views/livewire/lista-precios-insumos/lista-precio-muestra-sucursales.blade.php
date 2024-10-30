<div>
	                <div class="page-header">
					<div class="page-title">
							<h4>Configuracion visibilidad de lista de precios</h4>
							<h6>Agregar quitar lista de precios de una sucursal</h6>
						</div>
						<div class="page-btn">
						    @if(Auth::user()->sucursal != 1)
						    @if(Auth::user()->profile != "Cajero" )
							<a href="javascript:void(0)" wire:click="Agregar()" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar Lista</a>
						    @endif
						    @endif
						</div>
					</div>    
    

<div class="card">
<div class="card-body">

<label>Forma de mostrar las listas de precios </label>
    <select class="form-control" wire:model="forma_mostrar" wire:change="UpdateConfiguracionListaPrecios">
        <option value="0">Mostrar/Ocultar listas por sucursal</option>
        <option value="1">Mostrar todas las listas en todas las sucursales</option>
    </select>
    
    
@if($forma_mostrar == 0)
<div class="table-responsive mt-5">
        <table class="table">
        <thead>
            <tr>
                <th>Sucursales / Listas de Precios</th>
                @foreach($lista_precios as $lista)
                    <th>{{ $lista->nombre }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>

        <tr>
            <td>
            @php
                       $nombre_casa_central = \App\Models\User::find(Auth::user()->casa_central_user_id)->name;
                    @endphp
                    {{ $nombre_casa_central }}
                    </td>
                    @foreach($lista_precios as $lista)
                        @php
                            $registro = \App\Models\lista_precios_muestra_sucursales::where('sucursal_id', Auth::user()->casa_central_user_id)
                                ->where('lista_id', $lista->id)
                                ->first();
                        @endphp
                        <td>
                            <input type="checkbox"
                                   wire:click="toggleMuestra({{ Auth::user()->casa_central_user_id }}, {{ $lista->id }}) " 
                                   {{ $registro && $registro->muestra ? 'checked' : '' }}>
                        </td>
                    @endforeach
                </tr>
                
            @foreach($sucursales as $sucursal)
                <tr>
                    <td>{{ $sucursal->name }}</td>
                    @foreach($lista_precios as $lista)
                        @php
                            $registro = \App\Models\lista_precios_muestra_sucursales::where('sucursal_id', $sucursal->sucursal_id)
                                ->where('lista_id', $lista->id)
                                ->first();
                        @endphp
                        <td>
                            <input type="checkbox" 
                                   wire:click="toggleMuestra({{ $sucursal->sucursal_id }}, {{ $lista->id }})" 
                                   {{ $registro && $registro->muestra ? 'checked' : '' }}>
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endif
    <br><br>
    <button class="btn btn-submit" wire:click="CerrarVerVisibilidad">Volver</button>
</div>
</div>    
</div>

