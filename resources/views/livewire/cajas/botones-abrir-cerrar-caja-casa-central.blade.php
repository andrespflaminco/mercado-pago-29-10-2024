
    <!---- Si la sucursal es igual a comercio ----->
@if($sucursal_id == $comercio_id)

	@if($configuracion_caja == 0)
	
	
	@if(count($cajas_activas_comercio) < 1)
	@can('abrir caja')
    
	    <a href="javascript:void(0)" class="btn btn-added" style="background: #637381 !important;" wire:click.prevent="AbrirModal({{$sucursal_id}})" wire:loading.attr="disabled">ABRIR CAJA</a>
	
	@endcan
	@endif
	
	@endif

	<!----------- ABRIR CAJA ------------------>		 

    @if($configuracion_caja == 1)	
    
    @if((0 < count($cajas_inactivas_comercio ?? [])) || (0 < count($cajas_inactivas_usuario ?? [])))
        @can('abrir caja otros usuarios')
            <a href="javascript:void(0)" class="btn btn-added dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="background: #637381 !important;">ABRIR CAJA</a>
            <div class="dropdown-menu">
                @foreach($cajas_inactivas_comercio as $cic)
                    <a href="javascript:void(0);" wire:click.prevent="AbrirModal({{$cic['id']}})" class="dropdown-item"> {{$cic['id']}} - {{$cic['nombre_comercio']}} </a>
                @endforeach

            </div>
        @else
        
        @can('abrir caja')
            <a href="javascript:void(0)" class="btn btn-added dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="background: #637381 !important;">ABRIR CAJA</a>
            <div class="dropdown-menu">
                @foreach($cajas_inactivas_usuario as $ciu)
                    <a href="javascript:void(0);" wire:click.prevent="AbrirModal({{$ciu['id']}})" class="dropdown-item"> {{$ciu['id']}} - {{$ciu['nombre_comercio']}} </a>
                @endforeach

            </div>        
        @endcan
        
        @endcan
    @endif

    <!----------- /ABRIR  CAJA ------------------>	
	@endif

							
	@if(Auth::user()->profile != "Cajero" )
	@can('agregar caja anterior')
	<a href="javascript:void(0)" class="btn btn-added" wire:click.prevent="AgregarCajaAnteriorModal()"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">AGREGAR CAJA ANTERIOR</a>
	@endcan 
	@endif
	
	@endif		