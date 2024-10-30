@if($ver_opciones_pantalla == 1)
<div style="margin-bottom: 0 !important; margin-top: -25px !important; margin-left: -25px !important; margin-right: -25px !important;" class="card" id="miDiv">
    <div class="card-body" style="border:none !important;">
    <p style="font-weight: 700; color: #212B36; font-size: 18px;"><b>Columnas</b></p>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('nro_compra')" wire:model="columns.nro_compra" /> NRO COMPRA</label>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('nombre_proveedor')" wire:model="columns.nombre_proveedor" /> NOMBRE PROVEEDOR</label>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('created_at')" wire:model="columns.created_at" /> FECHA</label>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('status')" wire:model="columns.status" /> ESTADO </label>  
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('subtotal')" wire:model="columns.subtotal" /> SUBTOTAL</label>  
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('descuento')" wire:model="columns.descuento" /> DESCUENTO </label>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('iva')" wire:model="columns.iva" /> IVA </label>        
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('total')" wire:model="columns.total" /> TOTAL</label> 
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('etiquetas')" wire:model="columns.etiquetas" /> ETIQUETAS </label>   
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('pago')" wire:model="columns.pago" /> PAGO </label>        
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('deuda')" wire:model="columns.deuda" /> DEUDA</label>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('estado_pago')" wire:model="columns.estado_pago" /> ESTADO DE PAGO</label> 
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('entrega_parcial')" wire:model="columns.entrega_parcial" /> ENTREGA PARCIAL</label> 
    
    </div>
</div>
@endif
<button id="boton" style=" {{$ver_opciones_pantalla == 1 ? 'margin-top: 0px;' : 'margin-top: -25px;'}} float:right; border: 1px solid #E8EBED; border-top: none; height: auto; margin-bottom: 0;  padding: 3px 6px 3px 16px; background: #fff; border-radius: 0 0 4px 4px;
    color: #646970; line-height: 1.7;  box-shadow: 0 0 0 transparent;  transition: box-shadow .1s linear;" wire:click="VerOpcionesPantalla({{$ver_opciones_pantalla}})">Opciones de pantalla</button>


