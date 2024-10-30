<script src="{{ asset('assets/js/libs/jquery-3.1.1.min.js') }}"></script>
<script src="{{ asset('bootstrap/js/popper.min.js') }}"></script>
<script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('plugins/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>


<script>
    $(document).ready(function() {
        App.init();
    });
</script>

<!-- <script src="plugins/tagInput/tags-input.js"></script> -->
<script src="{{ asset('assets/js/users/account-settings.js') }}"></script>
<!--  END CUSTOM SCRIPTS FILE  -->

<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('plugins/table/datatable/datatables.js') }}"></script>

<script src="{{ asset('assets/js/custom.js') }}"></script>
<script src="{{ asset('plugins/sweetalerts/sweetalert2.min.js')}}"></script>
<script src="{{ asset('plugins/notification/snackbar/snackbar.min.js')}}"></script>
<script src="{{ asset('plugins/nicescroll/nicescroll.js')}}"></script>
<script src="{{ asset('plugins/currency/currency.js')}}"></script>

<script src="{{ asset('plugins/apex/apexcharts.min.js')}}"></script>
<script src="{{ asset('assets/js/dashboard/dash_2.js')}}"></script>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{ asset('assets/js/scrollspyNav.js')}}"></script>
<script src="{{ asset('plugins/input-mask/jquery.inputmask.bundle.min.js')}}"></script>
<script src="{{ asset('plugins/input-mask/input-mask.js')}}"></script>
<!-- BEGIN PAGE LEVEL PLUGINS -->

<!--  BEGIN CUSTOM SCRIPTS FILE  -->
<script src="{{ asset('assets/js/scrollspyNav.js')}} "></script>
<script src="{{ asset('plugins/select2/select2.min.js')}} "></script>
<script src="{{ asset('plugins/select2/custom-select2.js')}} "></script>
<!--  BEGIN CUSTOM SCRIPTS FILE  -->

<script src="{{ asset('assets/js/ie11fix/fn.fix-padStart.js')}}"></script>
<script src="{{ asset('assets/js/apps/notes.js')}}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


   <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
   <script>

   // Enable pusher logging - don't include this in production
   Pusher.logToConsole = true;

   var pusher = new Pusher('93abd037e37ed5513af5', {
    cluster: 'sa1'
   });

   var channel = pusher.subscribe('channel');
   channel.bind('event', function(data) {
    alert(JSON.stringify(data));
   });
   </script>



<script type="text/javascript">
var ss = $(".basic").select2({
    tags: true,
});
</script>

<script>
    function noty(msg, option = 1)
    {
        Snackbar.show({
            text: msg.toUpperCase(),
            actionText: 'CERRAR',
            actionTextColor: '#fff',
            backgroundColor: option == 1 ? '#3b3f5c' : '#e7515a',
            pos: 'top-right'
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        window.livewire.on('global-msg', msg => {
            noty(msg)
        });
    })


</script>


<script src="{{ asset('plugins/flatpickr/flatpickr.js')}}"></script>

  <script src="{{ asset('plugins/file-upload/file-upload-with-preview.min.js')}}"></script>
  <script>
      //First upload
      var firstUpload = new FileUploadWithPreview('myFirstImage')
      //Second upload
      var secondUpload = new FileUploadWithPreview('mySecondImage')
  </script>
  
  
<script>
         document.addEventListener('DOMContentLoaded', () => {

             // Input File
             const inputImage = document.querySelector('#file-input');
             // Nodo donde estará el editor
             const editor = document.querySelector('#editor');
             // El canvas donde se mostrará la previa
             const miCanvas = document.querySelector('#preview');
             // Contexto del canvas
             const contexto = miCanvas.getContext('2d');
             // Ruta de la imagen seleccionada
             let urlImage = undefined;
             // Evento disparado cuando se adjunte una imagen
             inputImage.addEventListener('change', abrirEditor, false);

             /**
              * Método que abre el editor con la imagen seleccionada
              */
             function abrirEditor(e) {
                 
                  $('#ModalCroppr').modal('show');

                 // Obtiene la imagen
                 urlImage = URL.createObjectURL(e.target.files[0]);

                 // Borra editor en caso que existiera una imagen previa
                 editor.innerHTML = '';
                 let cropprImg = document.createElement('img');
                 cropprImg.setAttribute('id', 'croppr');
                 editor.appendChild(cropprImg);

                 // Limpia la previa en caso que existiera algún elemento previo
                 contexto.clearRect(0, 0, miCanvas.width, miCanvas.height);

                 // Envia la imagen al editor para su recorte
                 document.querySelector('#croppr').setAttribute('src', urlImage);

                 // Crea el editor
                 new Croppr('#croppr', {
                     aspectRatio: 0.9,
                     startSize: [300, 300],
                     onCropEnd: recortarImagen
                 })
             }

             /**
              * Método que recorta la imagen con las coordenadas proporcionadas con croppr.js
              */
             function recortarImagen(data) {
                 // Variables
                 const inicioX = data.x;
                 const inicioY = data.y;
                 const nuevoAncho = data.width;
                 const nuevaAltura = data.height;
                 const zoom = 1;
                 let imagenEn64 = '';
                 // La imprimo
                 miCanvas.width = nuevoAncho;
                 miCanvas.height = nuevaAltura;
                 // La declaro
                 let miNuevaImagenTemp = new Image();
                 // Cuando la imagen se carge se procederá al recorte
                 miNuevaImagenTemp.onload = function() {
                     // Se recorta
                     contexto.drawImage(miNuevaImagenTemp, inicioX, inicioY, nuevoAncho * zoom, nuevaAltura * zoom, 0, 0, nuevoAncho, nuevaAltura);
                     // Se transforma a base64
                     imagenEn64 = miCanvas.toDataURL("image/jpeg");
                     // Mostramos el código generado
                     document.querySelector('#base64').textContent = imagenEn64;
                 }
                 // Proporciona la imagen cruda, sin editarla por ahora
                 miNuevaImagenTemp.src = urlImage;
             }
         });
        </script>
        <script>

	function ConfirmAccionEnLote() {

    var id_accion = $('#id_accion').val();
    if(id_accion == 0) { var msg = 'ELIMINAR';} else { var msg = 'RESTAURAR';}
  
		swal({
			title: 'CONFIRMAR',
			text: '¿CONFIRMAS '+msg+' LOS ITEMS SELECCIONADOS?',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if (result.value) {
				AccionProductos();
				swal.close()
			}

		})
	}
	
function AccionProductos() {

  const ids = [];

  $('.mis-checkboxes').each(function() {
    if($(this).hasClass('mis-checkboxes')) {
      
      if($(this).is(':checked')) {
      ids.push($(this).attr('tu-attr-id'));    
      }
       
    }
  });
  
  var id_accion = $('#id_accion').val();
  if(id_accion == 0) { var msg = 'Eliminar';} else { var msg = 'Restaurar';}
     
  window.livewire.emit('accion-lote', ids , id_accion);

document.querySelectorAll('.mis-check_todos').forEach(function(checkElement) {
checkElement.checked = false;
});
  
}    

function CheckTodosLote() {

	     if($(".check_todos").is(":checked")) {
		 	document.querySelectorAll('.mis-checkboxes').forEach(function(checkElement) {
                checkElement.checked = true;
            });
          }else{
			document.querySelectorAll('.mis-checkboxes').forEach(function(checkElement) {
                checkElement.checked = false;
            });
         }
         
}
</script>
<script>
    document.getElementById("caja-abrir").addEventListener("click", btnDisable);

    function btnDisable() {
      document.getElementById("caja-abrir").disabled = true;
    }
</script>

<script>
    function FuncionMobile() {
    var a = document.getElementById("div-mobile");
    if (a.style.display === "none") {
        a.style.display = "block";
    } else {
        a.style.display = "none";
    }
}
</script>

<script>
  function  MejorarPlan() {
    
    $miCheckbox = document.querySelector("#checkbox"),
    
     		swal({
			title: 'CAMBIO DE PLAN',
			text: 'ESTA FUNCIONALIDAD NO SE ENCUENTRA HABILITADA EN ESTE PLAN',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'CERRAR',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'MEJORAR PLAN'
		}).then(function(result) {
			if (result.value) {
				$miCheckbox.checked = false;
				swal.close()
			}

		}) 
  }
</script>



<script>
    function noty(msg, option = 1)
    {
        Snackbar.show({
            text: msg.toUpperCase(),
            actionText: 'CERRAR',
            actionTextColor: '#fff',
            backgroundColor: option == 1 ? '#3b3f5c' : '#e7515a',
            pos: 'top-right'
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        window.livewire.on('global-msg', msg => {
            noty(msg)
        });
    })


</script>



<script>

	const $imagen = document.querySelector("#imagen"),
		$calidad = document.querySelector("#calidad");
		
	const comprimirImagen = (imagenComoArchivo, porcentajeCalidad) => {
		/*
			https://parzibyte.me/blog
		*/
		return new Promise((resolve, reject) => {
			const $canvas = document.createElement("canvas");
			const imagen = new Image();
			imagen.onload = () => {
				$canvas.width = imagen.width;
				$canvas.height = imagen.height;
				$canvas.getContext("2d").drawImage(imagen, 0, 0);
				$canvas.toBlob(
					(blob) => {
						if (blob === null) {
							return reject(blob);
						} else {
							resolve(blob);
						}
					},
					"image/jpeg",
					porcentajeCalidad / 100
				);
			};
			imagen.src = URL.createObjectURL(imagenComoArchivo);
		});
	};

	document.querySelector("#btnComprimirBlob").addEventListener("click", async () => {
	    
	    
		if ($imagen.files.length <= 0) {
			return;
		}
		const archivo = $imagen.files[0];
	    // Obtener el nombre del archivo
        const nombreArchivo = archivo.name;
        
        
		const blob = await comprimirImagen(archivo, parseInt($calidad.value));
		// Ya puedes subir este archivo con FormData por ejemplo:
		//https://parzibyte.me/blog/2018/11/06/cargar-archivo-php-javascript-formdata/ 
		//console.log(nombreArchivo);
	    
        // Crear una instancia de FileReader
        const reader = new FileReader();
        
        // Definir el evento 'load' para obtener los datos en Base64 una vez que se complete la lectura del Blob
        reader.onload = function(event) {
          // Obtener los datos en Base64 desde el resultado del FileReader
          const base64Data = event.target.result;
        console.log(nombreArchivo);
          window.livewire.emit('StoreImagen', base64Data, nombreArchivo);
        };
        
        // Leer el Blob como datos en formato Base64
        reader.readAsDataURL(blob);
	});
	
	
	document.querySelector("#imagen").addEventListener("change", async () => {
	    
	    
		if ($imagen.files.length <= 0) {
			return;
		}
		const archivo = $imagen.files[0];
	    // Obtener el nombre del archivo
        const nombreArchivo = archivo.name;
        
        
		const blob = await comprimirImagen(archivo, parseInt($calidad.value));
		// Ya puedes subir este archivo con FormData por ejemplo:
		//https://parzibyte.me/blog/2018/11/06/cargar-archivo-php-javascript-formdata/ 
		//console.log(nombreArchivo);
	    
        // Crear una instancia de FileReader
        const reader = new FileReader();
        
        // Definir el evento 'load' para obtener los datos en Base64 una vez que se complete la lectura del Blob
        reader.onload = function(event) {
          // Obtener los datos en Base64 desde el resultado del FileReader
          const base64Data = event.target.result;
        console.log(nombreArchivo);
          window.livewire.emit('StoreImagen', base64Data, nombreArchivo);
        };
        
        // Leer el Blob como datos en formato Base64
        reader.readAsDataURL(blob);
	});


	document.querySelector("#btnComprimirPrevisualizar").addEventListener("click", async () => {
		if ($imagen.files.length <= 0) {
			return;
		}
		const archivo = $imagen.files[0];
		const blob = await comprimirImagen(archivo, parseInt($calidad.value));
		
	});

</script>

@livewireScripts
