@if($ver_opciones_pantalla == 1)
<div style="margin-bottom: 0 !important; margin-top: -25px !important; margin-left: -25px !important; margin-right: -25px !important;" class="card" id="miDiv">
    <div class="card-body" style="border:none !important;">
    <p style="font-weight: 700; color: #212B36; font-size: 18px;"><b>Columnas</b></p>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('created_at')" wire:model="columns.created_at" /> FECHA</label>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('nro_venta')" wire:model="columns.nro_venta" /> NRO VENTA</label>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('barcode')" wire:model="columns.barcode" /> CODIGO</label>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('product')" wire:model="columns.product" /> NOMBRE PRODUCTO</label>
     <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('nombre_cliente')" wire:model="columns.nombre_cliente" /> CLIENTE</label>
     <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('nombre_categoria')" wire:model="columns.nombre_categoria" /> CATEGORIA</label>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('nombre_usuario')" wire:model="columns.nombre_usuario" /> VENDEDOR</label>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('price')" wire:model="columns.price" /> PRECIO</label>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('quantity')" wire:model="columns.quantity" /> CANTIDAD</label>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('iva')" wire:model="columns.iva" /> IVA</label>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('recargo')" wire:model="columns.recargo" /> RECARGO</label>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('descuento')" wire:model="columns.descuento" /> DESCUENTO</label>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('costo')" wire:model="columns.costo" /> COSTO</label>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('total')" wire:model="columns.total" /> TOTAL</label>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('almacen')" wire:model="columns.almacen" /> ALMACEN</label>
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('nombre_banco')" wire:model="columns.nombre_banco" /> BANCO </label>        
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('nombre_metodo_pago')" wire:model="columns.nombre_metodo_pago" /> METODO DE PAGO</label>        
    <label><input style="margin-left:15px; margin-right:5px;" type="checkbox" wire:click="toggleColumnVisibility('entrega')" wire:model="columns.entrega" /> ENTREGA </label>        
    
    </div>
</div>
@endif
<button id="boton" style=" {{$ver_opciones_pantalla == 1 ? 'margin-top: 0px;' : 'margin-top: -25px;'}} float:right; border: 1px solid #E8EBED; border-top: none; height: auto; margin-bottom: 0;  padding: 3px 6px 3px 16px; background: #fff; border-radius: 0 0 4px 4px;
    color: #646970; line-height: 1.7;  box-shadow: 0 0 0 transparent;  transition: box-shadow .1s linear;" wire:click="VerOpcionesPantalla({{$ver_opciones_pantalla}})">Opciones de pantalla</button>




