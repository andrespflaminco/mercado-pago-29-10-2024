        <thead>
            <tr>
                <th>
                    @if(Auth::user()->profile != "Cajero" )
                    @can('accion en lote productos')  
                    <label class="checkboxs">
                        <input type="checkbox" id="select-all">
                        <span class="checkmarks"></span>
                    </label>
                    @endcan
                    @endif
                </th>
                <th>Nombre del producto</th>
                <th>SKU</th>
                <th>Stock minimo</th>
                <th {{ ($muestra_stock_casa_central == 0) && (Auth::user()->casa_central_user_id != $comercio_id) ? 'hidden' : ''}}>Stock casa central</th>
                @foreach($sucursales as $suc)
                
                
                <th wire:click="sort('stock')" {{ ($muestra_stock_otras_sucursales == 0) && ($suc->sucursal_id != $comercio_id) && (Auth::user()->casa_central_user_id != $comercio_id) ? 'hidden' : ''}}>
                    STOCK {{$suc->name}} 
                </th>
                @endforeach
                <th>Acciones</th>
            </tr>
        </thead>
