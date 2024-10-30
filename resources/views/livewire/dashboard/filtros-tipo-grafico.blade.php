<div class="col-md-3 mb-3">
    <ul class="nav nav-tabs-solid nav-tabs-rounded nav-justified">
        <li class="nav-item mr-2" style="padding-right:4px !important;">
            
            @if($ver == 'ventas')
                <a href="https://app.flamincoapp.com.ar/dashboard?v=1&suc={{$sucursales_elegidas}}&dateFrom={{$dateFrom}}&dateTo={{$dateTo}}" class="nav-link" style="border-radius: 25px !important; background-color: #e7fce3 !important; color: #008069 !important;">Ventas</a>
            @else
                <a href="https://app.flamincoapp.com.ar/dashboard?v=1&suc={{$sucursales_elegidas}}&dateFrom={{$dateFrom}}&dateTo={{$dateTo}}"  class="nav-link" style="border-radius: 25px !important; background-color: #f0f2f5 !important; color: #54656F !important;">Ventas</a>
            @endif
        </li>
        <li class="nav-item mr-2" style="padding-right:4px !important;">
            @if($ver == 'ingresos-gastos')
                <a href="https://app.flamincoapp.com.ar/dashboard?v=2&suc={{$sucursales_elegidas}}&dateFrom={{$dateFrom}}&dateTo={{$dateTo}}"  class="nav-link" style="border-radius: 25px !important; background-color: #e7fce3 !important; color: #008069 !important;">Finanzas</a>
            @else
                <a href="https://app.flamincoapp.com.ar/dashboard?v=2&suc={{$sucursales_elegidas}}&dateFrom={{$dateFrom}}&dateTo={{$dateTo}}"  class="nav-link" style="border-radius: 25px !important; background-color: #f0f2f5 !important; color: #54656F !important;">Finanzas</a>
            @endif
        </li>
        <li class="nav-item mr-2" style="padding-right:4px !important;">
            @if($ver == 'stock')
                <a href="https://app.flamincoapp.com.ar/dashboard?v=3&suc={{$sucursales_elegidas}}&dateFrom={{$dateFrom}}&dateTo={{$dateTo}}" class="nav-link" style="border-radius: 25px !important; background-color: #e7fce3 !important; color: #008069 !important;">Stock</a>
            @else
                <a href="https://app.flamincoapp.com.ar/dashboard?v=3&suc={{$sucursales_elegidas}}&dateFrom={{$dateFrom}}&dateTo={{$dateTo}}" class="nav-link" style="border-radius: 25px !important; background-color: #f0f2f5 !important; color: #54656F !important;">Stock</a>
            @endif
        </li>
    </ul>
</div>
