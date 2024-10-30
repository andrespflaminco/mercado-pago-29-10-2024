<div>

    
@include('livewire.products.form-marcas')	
@include('livewire.products.form-categoria')
@include('livewire.products.form-almacen')
@include('livewire.products.form-proveedor')
@include('livewire.products.form-imagen')	

<div style="display:{{$ver_configuracion == 1? 'block' : 'none';}};">

<div>
<div class="page-header">

						<div class="page-title">
						    
						    
							<h4>CONFIGURACIONES </h4>
							<h6>Setea las configuraciones relacionada a los productos. </h6>
						
						</div>
					</div>
                    <!-- /add -->

            			
                <div class="card">
                <ul class="nav nav-tabs  mb-0">
            				<li class="nav-item">
            						<a class="nav-link {{$configuracion_ver == 'codigos' ? 'active' : '' }}" href="javascript:void(0)" wire:click="CambiarConfiguracionVer('codigos')" > CODIGOS  </a>
            				</li>
            				<li class="nav-item">
            						<a class="nav-link {{$configuracion_ver == 'precios' ? 'active' : '' }}" href="javascript:void(0)" wire:click="CambiarConfiguracionVer('precios')" > PRECIOS </a>
            				</li>
            				<li class="nav-item">
            						<a class="nav-link {{$configuracion_ver == 'stock' ? 'active' : '' }}" href="javascript:void(0)" wire:click="CambiarConfiguracionVer('stock')" > STOCK </a>
            				</li>
            				<li class="nav-item">
            						<a class="nav-link {{$configuracion_ver == 'acciones_masivas' ? 'active' : '' }}" href="javascript:void(0)" wire:click="CambiarConfiguracionVer('acciones_masivas')" > ACCIONES MASIVAS </a>
            				</li>
            	</ul>                
                @include('livewire.products.form-configuracion')    
				</div>
				
				

				
</div>





</div>
<!------- AGREGAR ES IGUAL A CERO --------------->
<div style="display:{{ ($agregar == 0) && ($ver_configuracion == 0) ? 'block' : 'none';}};">
	                
@include('livewire.products.catalogo')   
				
<!-- /product list -->
</div>
<!------- / AGREGAR ES IGUAL A CERO --------------->
					
<!------- AGREGAR ES IGUAL A UNO --------------->
<div style="display:{{ ($agregar == 1) && ($ver_configuracion == 0) ? 'block' : 'none';}};">
@include('livewire.products.agregar-editar-producto')


</div>
<!------- / AGREGAR ES IGUAL A UNO --------------->

@include('livewire.products.exportar-stock')
@include('livewire.products.exportar-lista')


  
</div>

					
@include('common.script-productos') 

@include('common.script-etiquetas') 
<script>


const $imagen = document.querySelector("#imagen");
const $calidad1 = document.querySelector("#calidad1");
const $calidad2 = document.querySelector("#calidad2");
const $imagenPrevisualizar = document.querySelector("#imagenPrevisualizar");

const comprimirImagen = (imagenComoArchivo, porcentajeCalidad) => {
    return new Promise((resolve, reject) => {
        const imagen = new Image();
        imagen.onload = () => {
            const $canvas = document.createElement("canvas");
            $canvas.width = imagen.width;
            $canvas.height = imagen.height;
            const ctx = $canvas.getContext("2d");
            ctx.drawImage(imagen, 0, 0);
            const base64 = $canvas.toDataURL("image/jpeg", porcentajeCalidad / 100);
            resolve(base64);
        };
        imagen.src = URL.createObjectURL(imagenComoArchivo);
    });
};

$imagen.addEventListener("change", async () => {
    if ($imagen.files.length <= 0) {
        return;
    }
    
    // Muestra el loader antes de comenzar la compresión
    document.getElementById("loader").style.display = "block";
    
    const archivo = $imagen.files[0];
    const nombreArchivoOriginal = archivo.name;
    
    // Verificar si el tamaño del archivo supera 1 megabyte (en bytes)
    const maxSizeInBytes = 1024 * 1024; // 1 megabyte
    
    //SI EL ARCHIVO TIENE MAS DE 1 MB
    
    if (archivo.size > maxSizeInBytes) {
    const base64Image = await comprimirImagen(archivo, parseInt($calidad2.value));
    $imagenPrevisualizar.src = base64Image; // Mostrar la imagen Base64 en la etiqueta img
    
    // Asignar la cadena Base64 al input de texto
    const imagenBase64Input = document.querySelector("#imagenBase64");
    imagenBase64Input.value = base64Image;     
    
    
    window.livewire.emit('Base64', base64Image, nombreArchivoOriginal);
    }
    
    // SI EL ARCHIVO TIENE MENOS DE 1 MB
    
    if (archivo.size < maxSizeInBytes) {
    const base64Image = await comprimirImagen(archivo, parseInt($calidad1.value));
    $imagenPrevisualizar.src = base64Image; // Mostrar la imagen Base64 en la etiqueta img
    
    // Asignar la cadena Base64 al input de texto
    const imagenBase64Input = document.querySelector("#imagenBase64");
    imagenBase64Input.value = base64Image;     
    
    
    window.livewire.emit('Base64', base64Image, nombreArchivoOriginal);
    
    // Después de completar la compresión, oculta el loader
    document.getElementById("loader").style.display = "none";

    }
        

});


</script>