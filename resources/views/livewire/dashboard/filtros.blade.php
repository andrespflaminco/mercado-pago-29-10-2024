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
    
    <div style="width: 90%; !important" class="dropdown">
        <button class="btn btn-white btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
           Sucursales
        </button>
        <ul class="dropdown-menu" style="max-height: 300px !important; overflow-y: auto !important;" aria-labelledby="dropdownMenuButton">
            <li>
                <label style="text-align: inherit;  text-decoration: none;  white-space: nowrap;   background-color: transparent;    border: 0;   width: 100%;   color: #212B36;   font-size: 13px;   padding: 8px 15px;   font-weight: 500;">
                    <input type="checkbox" style="margin-right: 10px;"> <text> Seleccionar todas </text>
                </label>
            </li>
            <div class="dropdown-divider"></div>
            <li>
                <label style="text-align: inherit;  text-decoration: none;  white-space: nowrap;   background-color: transparent;    border: 0;   width: 100%;   color: #212B36;   font-size: 13px;   padding: 8px 15px;   font-weight: 500;">
                    <input type="checkbox" wire:model.defer="selectedSucursalesCheckbox.{{auth()->user()->id}}" style="margin-right: 10px;">  <text> {{auth()->user()->name}} </text>
                </label>
            </li>
            @foreach($sucursales as $sucursal)
            <li>
                <label style="text-align: inherit;  text-decoration: none;  white-space: nowrap;   background-color: transparent;    border: 0;   width: 100%;   color: #212B36;   font-size: 13px;   padding: 8px 15px;   font-weight: 500;">
                    <input type="checkbox" wire:model.defer="selectedSucursalesCheckbox.{{$sucursal->sucursal_id}}" style="margin-right: 10px;"> <text> {{$sucursal->name}} </text>
                </label>
            </li>
            @endforeach
             <div class="dropdown-divider"></div>
             <div style="text-align: inherit;  text-decoration: none;  white-space: nowrap;   background-color: transparent;    border: 0;   width: 100%;   color: #212B36;   font-size: 13px;   padding: 8px 15px;   font-weight: 500;">
                 <button class="applyBtn btn btn-sm btn-primary" wire:click="AplicarElegirSucursal()">Aplicar</button>
             </div>
        </ul>
    </div>

	
    </div>        
    </div>

    

    </div>    
    </div>
</div>