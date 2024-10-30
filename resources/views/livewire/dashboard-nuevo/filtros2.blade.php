<div class="row mb-1">
    <div class="col-12">
    <div class="card mb-0" style="padding:15px;
    margin-top: -25px;
    margin-left: -25px;
    border: none;
    border-radius: 0px;
    border-bottom: 1px solid #e8ebed;">
    <div class="row">
    <div style="width: auto;">
        <input type="text" id="date-range-picker" name="date_range" />
    </div>
    
    
   <div style="width: auto;"> 
    <div style="width: 100% !important" class="dropdown">
    <button class="btn btn-white btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
       Filtros
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="min-width: 650px;">
        <div class="row" style="margin:0 !important;">

<div class="col-md-4 col-sm-12" style="color: #212B36; font-size: 13px; padding: 8px 15px; font-weight: 500;">
    <label class="form-label">Vendedor</label>
    <div class="col-12" style="max-height: 300px !important; overflow-y: auto;">
        <div class="form-check">
            <input class="form-check-input sucursal-checkbox" type="checkbox" data-sucursal-id="{{auth()->user()->id}}" wire:model.defer="selectedSucursalesCheckbox.{{auth()->user()->id}}">
            <label class="form-check-label">{{auth()->user()->name}}
            
            </label>
        </div>
        @foreach($usuarios as $usuario)
        @if($usuario->id == auth()->user()->id || $usuario->comercio_id == auth()->user()->id)
        <div style="margin-left: 15px !important;" class="form-check">
        <input id="usuario-{{ $usuario->id }}" class="form-check-input usuario-checkbox sucursal-{{  auth()->user()->id  }}" type="checkbox">
        <label class="form-check-label">{{$usuario->name}}</label>
        </div>
        @endif
        @endforeach

        @foreach($sucursales as $sucursal)
        <div class="form-check">
            <input class="form-check-input sucursal-checkbox" type="checkbox" data-sucursal-id="{{$sucursal->sucursal_id}}" wire:model.defer="selectedSucursalesCheckbox.{{$sucursal->sucursal_id}}">
            <label class="form-check-label">{{$sucursal->name}}</label>
        </div>
        @foreach($usuarios as $usuario)
        @if($usuario->id == $sucursal->sucursal_id || $usuario->comercio_id == $sucursal->sucursal_id)
        <div style="margin-left: 15px !important;" class="form-check">
        <input id="usuario-{{ $usuario->id }}" class="form-check-input usuario-checkbox sucursal-{{ $sucursal->sucursal_id }}" type="checkbox">
        <!--  <input class="form-check-input usuario-checkbox sucursal-{{$sucursal->sucursal_id}}" type="checkbox" wire:model.defer="selectedVendedoresCheckbox.{{$usuario->id}}"> -->
        <label class="form-check-label">{{$usuario->name}}</label>
        </div>
        @endif
        @endforeach
        @endforeach
    </div>
</div>            

            <div hidden class="col-md-4 col-sm-12" style=" color: #212B36;   font-size: 13px;   padding: 8px 15px;   font-weight: 500; max-height:300px !important; overflow-y: auto;" >
                <label class="form-label">Canal de ventas</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" wire:model.defer="selectedCanalCheckbox.1">
                    <label class="form-check-label">Mostrador</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" wire:model.defer="selectedCanalCheckbox.2">
                    <label class="form-check-label">Ventas online</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" wire:model.defer="selectedCanalCheckbox.3">
                    <label class="form-check-label">Ventas wocommerce</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" wire:model.defer="selectedCanalCheckbox.4">
                    <label class="form-check-label">Venta a sucursal</label>
                </div>
                <!-- Agrega más elementos de checkbox aquí -->
            </div>

            <div hidden class="col-md-4 col-sm-12" style=" color: #212B36;   font-size: 13px;   padding: 8px 15px;   font-weight: 500;" >
            <label class="form-label">Categoria</label>
            <div class="col-12" style="max-height:300px !important; overflow-y: auto;">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox">
                    <label class="form-check-label">Sin categoria</label>
                </div>
                 @foreach($categorias as $categoria)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" wire:model.defer="selectedCheckbox">
                    <label class="form-check-label">{{$categoria->name}}</label>
                </div>
                @endforeach
                <!-- Agrega más elementos de checkbox aquí -->                
            </div>

            </div>

        </div>
        <div class="row mt-3">
            <div class="col-md-6 col-sm-12">
                <!-- Espacio vacío para alinear los botones a la derecha -->
            </div>
            <div class="col-md-6 col-sm-12 text-end">
                <button class="applyBtn btn btn-sm btn-primary" wire:click="AplicarFiltro()">Aplicar</button>
            </div>
        </div>
    </ul>
</div>



            
            </div>
    </div>


    </div>    
    </div>
</div>