@if($accion == 0)
<div class="row">
                
                @if(auth()->user()->sucursal != 1)
                <div  class="col-6">
                    <div style="cursor:pointer; {{$accion == 1 ? 'background:#f9f9f9;' : '' }}" wire:click="SetAccion(1)" class="card">
                    <div class="card-body">
                        <h3>Crear y actualizar productos</h3>
                    </div>
                </div>
                </div>
                @if(0 < $importaciones->count() )
                <div hidden class="col-6">
                  <div style="cursor:pointer; {{$accion == 3 ? 'background:#f9f9f9;' : '' }} " wire:click="SetAccion(3)" class="card">
                    <div class="card-body">
                        <h3>Actualizar stock</h3>
                    </div>
                </div>  
                </div>
                <div hidden class="col-6">
                <div style="cursor:pointer; {{$accion == 2 ? 'background:#f9f9f9;' : '' }}" wire:click="SetAccion(2)" class="card">
                    <div class="card-body">
                        <h3>Actualizar precios y costos</h3>
                    </div>
                </div>    
                </div>
                <div  class="col-6">
                  <div style="cursor:pointer; {{$accion == 4 ? 'background:#f9f9f9;' : '' }} " wire:click="SetAccion(4)" class="card">
                    <div class="card-body">
                        <h3>Importar nueva compra</h3>
                    </div>
                </div>  
                </div>
                @endif
                
                @else
                @if(0 < $product_count->count() )
                <div  class="col-6">
                  <div style="cursor:pointer; {{$accion == 4 ? 'background:#f9f9f9;' : '' }} " wire:click="SetAccion(4)" class="card">
                    <div class="card-body">
                        <h3>Importar nueva compra</h3>
                    </div>
                </div>  
                </div>
                @endif
                @endif
</div> 
@else
<div class="row">
            <h4>Accion elegida: {{$accion == 1 ? 'Crear y actualizar productos' : ''}} {{$accion == 2 ? 'Actualizar precios y costos' : ''}} {{$accion == 3 ? 'Actualizar stock' : ''}} {{$accion == 4 ? 'Importar compra' : ''}}   </h4>
 </div>
@endif