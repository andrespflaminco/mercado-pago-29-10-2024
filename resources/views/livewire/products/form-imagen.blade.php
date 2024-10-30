<div wire:ignore.self class="modal fade" id="Imagenes" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>AGREGAR IMAGENES</b> | CREAR
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">

	<ul class="nav nav-tabs  mb-0">
				<li class="nav-item">
						<a class="nav-link {{$style_tipo_1}}" href="javascript:void(0)" wire:click="TipoCargaImagen(1)"> BIBLIOTECA DE IMAGENES  </a>
				</li>
				<li class="nav-item">
						<a class="nav-link {{$style_tipo_2}}" href="javascript:void(0)" wire:click="TipoCargaImagen(2)">  AGREGAR IMAGEN </a>
				</li>
				

			</ul>

<div class="row">
@if($tipo_carga_imagen == 1)    
 <div class="col-lg-6 col-md-12 col-sm-12">
	<div class="input-group mt-3 mb-3">
	
	<input type="text" autocomplete="off" wire:model="search_imagenes"  placeholder="Buscar" class="form-control">
	<div wire:click="Buscarimagen" class="input-group-prepend">
		<span style="height: 100% !important;" class="input-group-text input-gp">
			<i class="fas fa-search"></i>
		</span>
	</div>
	</div>

</div>
@endif


</div>			
<div class="row" style="overflow-y: auto !important; height: 400px !important;">



@if($tipo_carga_imagen == 1)

   @foreach($imagenes as $product)   
            <div wire:click="SeleccionarImagen({{$product->id}})"  style="padding: 0px 10px 0px 10px; border:solid 1px #eee; border-radius:5px;">
                <span>
                @if($imagen_seleccionada != null)
				@if($imagen_seleccionada == $product->id)
				<span style="position: absolute;  padding: 46px 51px;  margin-left: 0px;  margin-top: 0px; opacity: 0.4; background: #333;">
		    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#fcfcfc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>	</span>
				@endif
				@endif
			
               	<img src="{{ asset('storage/products/' . $product->url ) }}" alt="{{$product->name}}"  title="{{$product->name}}"  height="130" width="140" class="rounded">
				</span>
				
            </div>
            
                
            @endforeach

@endif

@if($tipo_carga_imagen == 2)


<div class="row">
    <div class="col-8">
            <input type="file" id="imagen" class="form-control">
        </div>
        <div class="col-4">
            <button class="btn text-white btn-success" type="button" id="btnComprimirBlob">ACEPTAR</button>
        </div>
</div>
        
<div>
    <label for="calidad">Calidad:</label>
	<input type="number" value="20" id="calidad">
	<button id="miBoton">Click</button>
	<button id="btnComprimirPrevisualizar">Comprimir y visualizar</button>
	<img style="max-width: 100%;" src="" alt="" id="imagenPrevisualizar">
	
	<button onclik="Comprimir">Comprimir</button>
	
</div>


@endif
</div>
</div>
		 <div class="modal-footer">
                <a href="javascript:void(0);" wire:click.prevent="resetUIImagen()" class="btn btn-cancel" data-dismiss="modal">Cerrar</a>
                <a wire:click.prevent="AceptarSeleccionarImagen()" href="javascript:void(0);" class="btn btn-submit me-2" >Aceptar</a>
 
		 </div>
	 </div>
 </div>
</div>

