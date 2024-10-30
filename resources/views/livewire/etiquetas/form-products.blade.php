<div wire:ignore.self class="modal fade" id="ModalProductos" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header ">
        <h5 class="modal-title">
        	<b>AGREGAR PRODUCTOS A LA IMPRESION</b>
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body w-100">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>Filtrar por Categoria</label>
                <select class="form-control" wire:model.defer="filtro_categoria">
                <option value="0">TODOS</option>
                <option value="1">Sin categoria</option>
                @foreach($categorias as $c)
                <option value="{{$c->id}}">{{$c->name}}</option>    
                @endforeach
                </select>                    
                </div>

            </div>
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>Filtrar por Proveedor</label>
                <select class="form-control" wire:model.defer="filtro_proveedor">
                <option value="0">TODOS</option>
                <option value="1">Sin proveedor</option>
                @foreach($proveedores as $p)
                <option value="{{$p->id}}">{{$p->id_proveedor}} - {{$p->nombre}}</option>    
                @endforeach
                </select>                    
                </div>
            </div>
            
            
            <div class="col-12"><hr></div>
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>Cantidad de etiquetas</label>
                <input type="number" class="form-control" wire:model.defer="cantidad_filtro">
                </div>
            </div>
            
            
        </div>
     </div>
     <div class="modal-footer">

       <button type="button" wire:click.prevent="resetUIFiltroLote()" class="btn btn-cancel" data-dismiss="modal">CANCELAR</button>

       <button type="button" wire:click.prevent="AgregarEnLote()" class="btn btn-submit" >ACEPTAR</button>


     </div>
   </div>
 </div>
</div>
