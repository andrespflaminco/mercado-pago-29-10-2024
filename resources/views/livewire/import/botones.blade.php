  @if(auth()->user()->sucursal != 1)      
            <!----- Botones ------>
           <div class="row">
           
            <div class="col-4 mt-4"  style="text-align: left;">
            @if($nro_paso == 2)
                <button class="btn btn-cancel" wire:click="Paso1()">< Anterior </button>
            @endif
            @if($nro_paso == 3)
                <button class="btn btn-cancel" wire:click="Paso2()">< Anterior </button>
            @endif
            </div>
            <div class="col-4 mt-4"></div>
            <div class="col-4 mt-4" style="text-align: right;">
            @if($nro_paso == 1)
            <button class="btn btn-submit" wire:click="Paso2()">Siguiente > </button>
            @endif
            
            @if($nro_paso == 2)
            <button class="btn btn-submit" wire:click="Paso3()">Siguiente > </button>
            @endif
            
            </div>         
            
            </div>
    @endif
            