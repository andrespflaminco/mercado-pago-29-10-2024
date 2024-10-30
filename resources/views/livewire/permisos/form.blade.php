<div wire:ignore.self class="modal fade" id="theModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>{{$componentName}}</b> | {{ $selected_id > 0 ? 'EDITAR' : 'CREAR' }}
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">

        <div class="row">
          <div class="col-sm-12 mb-3">
              <label>Nombre del permiso</label><br>
            <div class="input-group">
                
              <input type="text" wire:model.lazy="permissionName" class="form-control" placeholder="ej: Category_Index" maxlength="255">
            </div>
            @error('permissionName') <span class="text-danger er">{{ $message }}</span> @enderror
          </div>
        
        <div class="col-sm-12">
             <label>Modulo en el que aparece</label><br>
            <div class="input-group">
               <select class="form-control" wire:model="modulo">
                <option value="">Elegir</option>
                <option value="Menu">Menu</option>
                <option value="Ventas">Ventas</option>
                <option value="Ventas Resumen">Ventas Resumen</option>
                <option value="Ventas Resumen por Producto">Ventas Resumen por Producto</option>
                <option value="Gastos">Gastos</option>
                <option value="Compra a un proveedor">Compra a un proveedor</option>  
                <option value="Compra a casa central">Compra a casa central</option>  
                <option value="Compras resumen">Compras resumen</option>  
                <option value="Productos">Productos</option>  
                <option value="Lista de precios">Lista de precios</option>
                <option value="Actualizaciones masivas de productos">Actualizaciones masivas de productos</option>  
                <option value="Atributos y variaciones">Atributos y variaciones</option> 
                <option value="Categorias">Categorias</option> 
                <option value="Almacenes">Almacenes</option>
                <option value="Movimientos de stock">Movimientos de stock</option> 
                <option value="Bancos">Bancos</option> 
                <option value="Metodos de cobro">Metodos de cobro</option> 
                <option value="Cajas">Cajas</option> 
                <option value="Clientes">Clientes</option>
                <option value="Proveedores">Proveedores</option>
                <option value="Usuarios">Usuarios</option>
                <option value="Mis sucursales">Mis sucursales</option>
                <option value="Tienda Flaminco">Tienda Flaminco</option>  
                <option value="Wocommerce">Wocommerce</option>
                <option value="Configuracion">Configuracion</option>
                <option value="Facturacion">Facturacion</option>
               </select>
            </div>
            @error('modulo') <span class="text-danger er">{{ $message }}</span> @enderror
          </div>
        </div>


      </div>
      <div class="modal-footer">

        <button type="button" wire:click.prevent="Close()" class="btn btn-cancel" >CERRAR</button>

        @if($selected_id < 1)
        <button type="button" wire:click.prevent="CreatePermission()" class="btn btn-submit" >GUARDAR</button>
        @else
        <button type="button" wire:click.prevent="UpdatePermission()" class="btn btn-submit" >ACTUALIZAR</button>
        @endif


      </div>
    </div>
  </div>
</div>