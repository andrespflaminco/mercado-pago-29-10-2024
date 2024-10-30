<style media="screen">
.table-hover:not(.table-dark) tbody tr:hover {
    background-color: transparent !important;
}
	.boton-etiqueta:hover {
		font-size: 1rem!important;
		width: auto;
		background-color:
		transparent;
		border-left: none;
		border-top: none;
		border-right: none;
		text-align: center;
		border-bottom: 1px solid #bfc9d4;
	}
	.boton-etiqueta:focus {
		font-size: 1rem!important;
		width: auto;
		background-color:
		transparent;
		border-left: none;
		border-top: none;
		border-right: none;
		text-align: center;
		border-bottom: 1px solid #bfc9d4;
	}
	.boton-etiqueta {
		font-size: 1rem!important;
		width: auto;
		background-color:
		transparent;
		border: none;
		text-align: center;
	}
</style>
<div wire:ignore.self class="modal fade" id="catalogo" tabindex="-1" role="dialog" aria-labelledby="tabsModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-lg" style="max-width: 1200px !important;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tabsModalLabel">Catalogo</h5>
        <button type="button" class="close" wire:click.prevent="CloseCatalogo()"  data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="width:100%;">
    <div class="row">
        <div class="col-6">
            <div class="form-group">
            <label for="exampleInputEmail1">Buscar producto por nombre o codigo</label>

             <div class="input-group mb-4">

              <input autocomplete="off" type="text" id="title" wire:model="search_lista_productos" wire:change="VerCatalogo" wire:keyup="VerCatalogo" class="form-control" required="">
              
             </div>
            </div>
        </div>
        <div class="col-6">
            
            <div class="form-group">
            <label for="exampleInputEmail1">Buscar por categoria</label>

             <div class="input-group mb-4">

             <select wire:model="search_categorias_lista_productos" wire:change="VerCatalogo" class="form-control">
                 <option value="0">Todas las categorias</option>
                 <option value="1">Sin categoria</option>
                @foreach($categories as $c)
                <option value="{{$c->id}}">{{$c->name}}</option>
                @endforeach
              </select>
              
             </div>
            </div>
            

        </div>
    </div>
    <div style="margin-bottom: 0 !important; max-height:500px !important;" class="table-responsive mb-4 mt-4">
                    <table class="multi-table table table-hover">
                        <thead>
                            <tr>
                                <th class="text-left">Nombre</th>
                                <th>SKU</th>
								@if($cliente != null)
								@if($cliente->sucursal_id != 0)
								<th>Precio interno</th>
								@endif
								@endif	
                                <th>Precio publico</th>
                                <th>Categoria</th>
                                <th class="text-left"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lista_productos as $lp)
                            <tr>
                                <td class="productimgname">
									<a href="javascript:void(0);" class="product-img">
								    @if($lp->image != null)
            						<img src="{{ asset('storage/products/' . $lp->image ) }}" alt="{{$lp->name}}" height="70" width="80" class="rounded">
            						@else
            						<img src="{{ asset('storage/products/noimg.png') }}" alt="{{$lp->name}}" height="70" width="80" class="rounded">
            						@endif
									</a>
									<a href="javascript:void(0);">{{$lp->name}}</a>
								</td>
								<td>{{$lp->barcode}}</td>

								@if($cliente != null)
								@if($cliente->sucursal_id != 0)
								<td>
								$ {{ number_format($lp->precio_interno,2,",",".") }}
								</td>
								@endif
								@endif								    

								<td>
								@if($lp->producto_tipo == "s")
								$ {{ number_format($lp->precio_lista,2,",",".") }}
								@endif
								</td>
								<td>{{$lp->categoria}}</td>
                                <td class="text-left">
                                    	<button value="{{$lp->barcode}}" 
											id="code{{$lp->barcode}}"  
											wire:click="AgregarProductoCatalogo('{{$lp->barcode}}')" 
											class="btn btn-dark"
											title="Click en el producto">Agregar
										</button>
										
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
    </div>
    
    <div class="modal-footer">
        <a wire:click.prevent="CloseCatalogo()" href="javascript:void(0);" class="btn btn-cancel">CERRAR</a>
      </div>
    </div>
  </div>
</div>
