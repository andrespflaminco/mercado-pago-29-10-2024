<div>

	<input hidden type="text" value="25" id="calidad1">
	<input hidden type="text" value="40" id="calidad2">
	<input hidden type="text" value="80" id="calidad3">
    
    Imagen
	<input type="file" id="imagen_file">

	<hr>
	
	@if(0 < $selected_id)
	@if($base_64_archivo)
	 <img style="max-width: 250px;" src="{{$base_64_archivo}}"  id="imagenPrevisualizarEdit">
	@else
	<img style="max-width: 250px;" src="https://app.flamincoapp.com.ar/storage/products/noimg.png"  id="imagenPrevisualizarEdit">
	@endif
	@endif
	
	
    <img style="max-width: 250px;" src="" style="display: none;"  id="imagenPrevisualizar">
	
	<div id="loader" style="display: none;">	</div>
	<input hidden type="text" id="imagenBase64" />
	<input hidden type="text" id="imagennombreArchivoOriginal" />
	
<input  hidden type="text" id="base64Input" placeholder="Introduce aquí el base64...">
<button hidden  id="renderizarBase64Btn">Renderizar imagen base64</button>	








</div>