<div>

<div class="col-lg-12">
	<div class="form-group">
		<label>	Imagen</label>
			<div class="image-upload" style="height: 200px; padding:5px;">
			    
			    @if(0 < $selected_id)
			    <!---------- si existe el producto ---------->
			     @if($base_64_archivo == null)
				<input type="file" id="imagen">
				<div class="image-uploads">
				<img src="{{ asset('assets/pos/img/icons/upload.svg') }}" alt="img">
				<h4>Agregar imagen</h4>
				<div id="loader" style="display: none;">
            	Cargando imagen...
                 <!-- Agrega aquí tu imagen o animación de carga -->
                </div>
				</div>
				@else
				<input type="file" id="imagen">
				<div class="image-uploads">
				<img src="{{$base_64_archivo}}" style="float:left; max-height: 180px;">
				<div id="loader" style="display: none;">
            	Cargando imagen...
                 <!-- Agrega aquí tu imagen o animación de carga -->
                </div>
				</div>
				@endif
			    <!---------- // si existe el producto ---------->
			
			    @else
			    
			    <!---------- si no existe el producto ---------->
			    @if($base_64_archivo == null)
				<input type="file" id="imagen">
				<div class="image-uploads">
				<img src="{{ asset('assets/pos/img/icons/upload.svg') }}" alt="img">
				<h4>Agregar imagen</h4>
				<div id="loader" style="display: none;">
            	Cargando imagen...
                 <!-- Agrega aquí tu imagen o animación de carga -->
                </div>
				</div>
				@else
				<input type="file" id="imagen">
				<div class="image-uploads">
				<img src="{{$base_64_archivo}}" style="float:left; max-height: 180px;">
	     		<div id="loader" style="display: none;">
            	Cargando imagen...
                 <!-- Agrega aquí tu imagen o animación de carga -->
                </div>
				</div>
				@endif
				
			    <!---------- // si no existe el producto ---------->
			
			 
				@endif
			    
			   	
			</div>
		
			</div>
		</div>
								


	<input hidden type="text" value="25" id="calidad1">
	<input hidden type="text" value="40" id="calidad2">
	<input hidden type="text" value="80" id="calidad3">
	<hr>

	<img hidden style="max-width: 250px;" src="" alt="" id="imagenPrevisualizar">
	<br>
	<input hidden type="text" id="imagenBase64" wire:model="" />




</div>