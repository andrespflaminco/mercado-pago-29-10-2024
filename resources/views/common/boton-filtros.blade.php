<a style="font-size:14px !important; padding:5px !important; background: #FF9F43 !important; width: auto !important; color: white;" wire:click="MostrarFiltro()"  class="btn btn-filter" >
	<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
	<span><img src="{{ asset('assets/pos/img/icons/closes.svg') }}" alt="img"></span>
	<div style="margin-left: 5px; margin-right: 5px; font-size: 14px !important;">
	<b>Filtros</b> 
	</div>
</a>

@if($mostrarFiltros)
    <!-- Mostrar con filtros -->
    <a hidden class="btn btn-filter" wire:click="MostrarFiltro()" style="background: #EA5455 !important;">
    <img src="{{ asset('assets/pos/img/icons/closes.svg') }}" alt="img">
    </a>
@else
    <!-- Mostrar sin filtros -->
    <a hidden class="btn btn-filter" wire:click="MostrarFiltro()">
    <img src="{{ asset('assets/pos/img/icons/filter.svg') }}"  alt="img">
    </a>
@endif