

<script>
	function ConfirmConfiguracion(id) {
                        var accion = id;
                        if(accion == 1) {
                        var msg = "ESTO ACTUALIZARA LOS PRECIOS INTERNOS CON EL VALOR DE LOS COSTOS";    
                        } else {
                        var msg = "";    
                        }
                		swal({
                			title: 'CONFIRMAS ACTUALIZAR ',
                			text: msg,
                			type: 'warning',
                			showCancelButton: true,
                			cancelButtonText: 'Cerrar',
                			cancelButtonColor: '#fff',
                			confirmButtonColor: '#3B3F5C',
                			confirmButtonText: 'Aceptar'
                		}).then(function(result) {
                			if (result.value) {
                				window.livewire.emit('UpdateConfiguracion', id)
                				swal.close()
                			}
                
                		})
                	}    
                	
	function ConfirmEliminarProducto(id) {
                
                		swal({
                			title: 'CONFIRMAR',
                			text: '多CONFIRMAS ELIMINAR EL REGISTRO?',
                			type: 'warning',
                			showCancelButton: true,
                			cancelButtonText: 'Cerrar',
                			cancelButtonColor: '#fff',
                			confirmButtonColor: '#3B3F5C',
                			confirmButtonText: 'Aceptar'
                		}).then(function(result) {
                			if (result.value) {
                				window.livewire.emit('deleteRow', id)
                				swal.close()
                			}
                
                		})
                	}  
                	
    function ElegirTipoProducto(){
                	swal({
        				title: 'IMPORTANTE',
        				text: 'DEBE ELEGIR EL TIPO DE PRODUCTO',
        				type: 'warning',
        				confirmButtonColor: '#3B3F5C'
        			})
                	}     
                	
    function FaltaVariacion(){
                	swal({
        				title: 'IMPORTANTE',
        				text: 'NO PUEDE GUARDAR UN PRODUCTO VARIABLE SIN VARIACIONES ASOCIADAS',
        				type: 'warning',
        				confirmButtonColor: '#3B3F5C'
        			})
                	}
</script>
			
<script>
	document.addEventListener('DOMContentLoaded', function() {
	   
		window.livewire.on('product-added', msg => {
			noty(msg)
		});

        // Exportadores 
        
		window.livewire.on('modal-export-listas-show', msg => {
			$('#ExportarLista').modal('show')
		});
		
		window.livewire.on('modal-export-listas-hide', msg => {
			$('#ExportarLista').modal('hide')
		});

		window.livewire.on('modal-export-stocks-show', msg => {
			$('#ExportarStock').modal('show')
		});
		
		window.livewire.on('modal-export-stocks-hide', msg => {
			$('#ExportarStock').modal('hide')
		});

        //
		window.livewire.on('modal-stock', msg => {
			$('#theModalStock').modal('show')
		});
		
		window.livewire.on('modal-stock-hide', msg => {
			$('#theModalStock').modal('hide')
		});

		window.livewire.on('category-added', msg => {
			$('#Categoria').modal('hide')
			noty(msg)
		});

		// 6-6-2024
		
		window.livewire.on('modal-proveedor-show', msg => {
			$('#Proveedor').modal('show')
		});
		
				window.livewire.on('modal-categoria-show', msg => {
			$('#Categoria').modal('show')
		});
    
    	window.livewire.on('proveedor-added', msg => {
			$('#Proveedor').modal('hide')
			noty(msg)
		});
		
		window.livewire.on('proveedor-hide', msg => {
			$('#Proveedor').modal('hide')
		});
		
		window.livewire.on('modal-marca-show', msg => {
			$('#Marcas').modal('show')
		});
        //
        
		window.livewire.on('marca-added', msg => {
			$('#Marcas').modal('hide')
			noty(msg)
		});

		window.livewire.on('modal-descuento-en-lote', msg => {
		    $('#DescuentoEnLote').modal('show');
		});

		window.livewire.on('modal-descuento-en-lote-hide', msg => {
			$('#DescuentoEnLote').modal('hide')
		});
		
		// 22-9-2024
		
		window.livewire.on('modal-seleccion-en-lote', msg => {
			$('#SeleccionEnLote').modal('show')
		});
        
		window.livewire.on('modal-seleccion-en-lote-hide', msg => {
			$('#SeleccionEnLote').modal('hide')
		});

        
		window.livewire.on('modal-actualizacion-en-lote', msg => {
			$('#ActualizacionEnLote').modal('show')
		});

		window.livewire.on('modal-actualizacion-en-lote-hide', msg => {
			$('#ActualizacionEnLote').modal('hide')
		});
		
        document.getElementById('openModalButton').addEventListener('click', function() {
           $('#DescuentoEnLote').modal('show');
        });


        // fin de actualizacion 22-9-2024
        		
		window.livewire.on('almacen-added', msg => {
			$('#Almacen').modal('hide')
			noty(msg)
		});

		window.livewire.on('category-hide', msg => {
			$('#Categoria').modal('hide')
		});

		window.livewire.on('almacen-hide', msg => {
			$('#Almacen').modal('hide')
		});

		window.livewire.on('product-updated', msg => {
			$('#theModal').modal('hide')

			noty(msg)
		});
		
		window.livewire.on('mensajes', msg => {
		noty(msg, 2)
		});
		
		window.livewire.on('product-deleted', msg => {
			noty(msg)
		});

		window.livewire.on('exportar-lista', msg => {
			$('#ExportarLista').modal('show')
		});
				
		window.livewire.on('exportar-lista-hide', msg => {
			$('#ExportarLista').modal('hide')
		});
		
		window.livewire.on('modal-show', msg => {
			$('#theModal').modal('show')
		});

		window.livewire.on('modal-imagen-show', msg => {
			$('#Imagenes').modal('show')
		});

		window.livewire.on('modal-imagen-hide', msg => {
			$('#Imagenes').modal('hide')
		});
	
		window.livewire.on('modal-almacen-show', msg => {
			$('#Almacen').modal('show')
		});

		window.livewire.on('modal-cambio-sucursal', msg => {
			$('#ModalCambioSucursal').modal('show')
		});
		
		window.livewire.on('hide-cropp', msg => {
			$('#ModalCroppr').modal('hide')
		});
		window.livewire.on('hidden.bs.modal', msg => {
			$('.er').css('display', 'none')
		});
		$('#theModal').on('hidden.bs.modal', function(e) {
			$('.er').css('display', 'none')
		})
		$('#theModal').on('shown.bs.modal', function(e) {
			$('.product-name').focus()
		})

		window.livewire.on('credenciales-invalidas', id => {

			swal({
				title: 'ATENCION',
				text: 'LAS CREDENCIALES DE WOOCOMMERCE ASOCIADAS SON INVALIDAS.',
				type: 'warning',
				showCancelButton: true,
				cancelButtonText: 'Cerrar',
				cancelButtonColor: '#fff',
				confirmButtonColor: '#3B3F5C',
				confirmButtonText: 'IR A CONFIGURAR'
			}).then(function(result) {
			    window.location.href = '/woocommerce';
				swal.close()
			})

		});	
		
		window.livewire.on('producto-repetido', selected_id => {

                        
                		swal({
                			title: 'EXISTEN PRODUCTOS CON EL MISMO NOMBRE',
                			text: '¿DESEAS CONTINUAR DE TODOS MODOS?',
                			type: 'warning',
                			showCancelButton: true,
                			cancelButtonText: 'Cancelar',
                			cancelButtonColor: '#fff',
                			confirmButtonColor: '#3B3F5C',
                			confirmButtonText: 'Aceptar'
                		}).then(function(result) {
                			if (result.value) {
                				window.livewire.emit('Swicth', selected_id)
                				swal.close()
                			}
                
                		})

		});	
		
		
		window.livewire.on('msg-error', msg => {
			swal({
				title: 'ATENCION',
				type: 'warning',
				text: msg,
				confirmButtonColor: '#3B3F5C'
			})
		});	
		
		window.livewire.on('cambiar-tipo-producto', id => {

			swal({
				title: 'CONFIRMAR',
				text: 'CONFIRMAS CAMBIAR EL TIPO DE PRODUCTO? ESTO ELIMINARA LAS VARIACIONES ASOCIADAS',
				type: 'warning',
				showCancelButton: true,
				cancelButtonText: 'Cancelar',
				cancelButtonColor: '#fff',
				confirmButtonColor: '#3B3F5C',
				confirmButtonText: 'Aceptar'
			}).then(function(result) {
				if (result.value == true) {
				  //  alert(result.value)
					window.livewire.emit('CambiarProductoTipo', id)
					swal.close()
				} else {
				    var id = 1;
				    window.livewire.emit('VolverProductotipo', id)
				//	swal.close()
				}

			})

		});	
		
		window.livewire.on('confirm-eliminar', id => {

			swal({
				title: 'CONFIRMAR',
				text: '多CONFIRMAS ELIMINAR LOS REGISTROS?',
				type: 'warning',
				showCancelButton: true,
				cancelButtonText: 'Cerrar',
				cancelButtonColor: '#fff',
				confirmButtonColor: '#3B3F5C',
				confirmButtonText: 'Aceptar'
			}).then(function(result) {
				if (result.value) {
					window.livewire.emit('ConfirmCheck', id)
					swal.close()
				}

			})

		});



	});

	function RestaurarProducto(id) {

    swal({
      title: 'CONFIRMAR',
      text: 'QUIERE RESTAURAR EL PRODUCTO?',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      cancelButtonColor: '#fff',
      confirmButtonColor: '#3B3F5C',
      confirmButtonText: 'Aceptar'
    }).then(function(result) {
      if (result.value) {
        window.livewire.emit('RestaurarProducto', id)
        swal.close()
      } 

    })
  }
	function ConfirmVariacion(id) {

			swal({
				title: 'CONFIRMAR',
				text: '¿CONFIRMAS ELIMINAR LA VARIACION?',
				type: 'warning',
				showCancelButton: true,
				cancelButtonText: 'Cerrar',
				cancelButtonColor: '#fff',
				confirmButtonColor: '#3B3F5C',
				confirmButtonText: 'Aceptar'
			}).then(function(result) {
				if (result.value) {
					window.livewire.emit('deleteVariacion', id)
					swal.close()
				}

			})
		}

</script>

<script>
const $imagen = document.querySelector("#imagen_file");
const $calidad1 = document.querySelector("#calidad1");
const $calidad2 = document.querySelector("#calidad2");
const $imagenPrevisualizar = document.querySelector("#imagenPrevisualizar");
console.log($imagen);  // Verifica si selecciona el input correctamente

const comprimirImagen = (imagenComoArchivo, porcentajeCalidad) => {
    return new Promise((resolve, reject) => {
        const imagen = new Image();
        imagen.onload = () => {
            const $canvas = document.createElement("canvas");
            $canvas.width = imagen.width;
            $canvas.height = imagen.height;
            const ctx = $canvas.getContext("2d");
            ctx.drawImage(imagen, 0, 0);

            // Detectar el tipo MIME y usar el formato correspondiente
            let formato;
            switch (imagenComoArchivo.type) {
                case 'image/jpeg':
                case 'image/jpg':
                    formato = 'image/jpeg';
                    break;
                case 'image/png':
                    formato = 'image/png';
                    break;
                case 'image/gif':
                    formato = 'image/gif';
                    break;
                case 'image/bmp':
                    formato = 'image/bmp';
                    break;
                case 'image/webp':
                    formato = 'image/webp';
                    break;
                case 'image/tiff':
                    formato = 'image/tiff';
                    break;
                default:
                    console.error("Formato de imagen no soportado:", imagenComoArchivo.type);
                    reject("Formato no soportado");
                    return;
            }

            const base64 = $canvas.toDataURL(formato, formato === 'image/jpeg' || formato === 'image/webp' ? porcentajeCalidad / 100 : undefined);
            resolve(base64);
        };

        imagen.onerror = reject;  // Maneja errores de carga de la imagen
        imagen.src = URL.createObjectURL(imagenComoArchivo);
    });
};

$imagen.addEventListener("change", async () => {
    console.log("Evento 'change' disparado");

    if ($imagen.files.length <= 0) {
        console.log("No hay archivos seleccionados");
        return;
    }

    console.log("Archivo seleccionado:", $imagen.files[0]);

    // Muestra el loader antes de comenzar la compresión
    document.getElementById("loader").style.display = "block";

    const archivo = $imagen.files[0];
    const nombreArchivoOriginal = archivo.name;
    console.log("Nombre del archivo:", nombreArchivoOriginal);
    
    const maxSizeInBytes = 1024 * 1024; // 1 megabyte

    // Si el archivo es mayor a 1 MB
    if (archivo.size > maxSizeInBytes) {
        console.log("Archivo mayor a 1 MB, comprimiendo...");
        const base64Image = await comprimirImagen(archivo, parseInt($calidad2.value));
        console.log("Imagen comprimida base64:", base64Image);
        $imagenPrevisualizar.src = base64Image; // Mostrar la imagen Base64 en la etiqueta img
        document.querySelector("#imagenBase64").value = base64Image;
        document.querySelector("#imagennombreArchivoOriginal").value = nombreArchivoOriginal;
    //    window.livewire.emit('Base64', base64Image, nombreArchivoOriginal);
    }

    // Si el archivo es menor a 1 MB
    if (archivo.size < maxSizeInBytes) {
        console.log("Archivo menor a 1 MB, comprimiendo...");
        const base64Image = await comprimirImagen(archivo, parseInt($calidad1.value));
        console.log("Imagen comprimida base64:", base64Image);
        $imagenPrevisualizar.src = base64Image; // Mostrar la imagen Base64 en la etiqueta img
        document.querySelector("#imagenBase64").value = base64Image;
        document.querySelector("#imagennombreArchivoOriginal").value = nombreArchivoOriginal;
    //    window.livewire.emit('Base64', base64Image, nombreArchivoOriginal);
    }

    // Oculta el loader después de completar la compresión
    document.getElementById("loader").style.display = "none";
    console.log("Compresión completada y loader oculto");
    
    document.getElementById("imagenPrevisualizar").style.display = "block";
    document.getElementById("imagenPrevisualizarEdit").style.display = "none";
});

</script>

<script>
    // Variable para definir la regla de cada lista de precios (1: precio fijo, 2: % utilidad sobre costo)
    const reglasPrecio = {
        @foreach($lista_precios_reglas as $lp)
        '{{ $lp->lista_id }}': {{ $lp->regla ?? 1 }},
        @endforeach
    };

    // Función para calcular el costo después del descuento
    function calcularCostoDescuento(variacion) {
        console.log(variacion);
        const cost = parseFloat(document.getElementById('cost_' + variacion).value) || 0;
        const descuento = parseFloat(document.getElementById('descuento_costo_' + variacion).value) || 0;
        const costoDespuesDescuento = cost * (1 - descuento / 100);
        document.getElementById('costo_despues_descuento_' + variacion).value = costoDespuesDescuento.toFixed(2);
    }

    // Función para calcular el precio de una lista de precios específica
    function calcularPrecioEspecifico(listaId, variacion) {
        const costoDespuesDescuento = parseFloat(document.getElementById('costo_despues_descuento_' + variacion).value) || 0;
        const porcentajeUtilidad = parseFloat(document.getElementById(`porcentaje_regla_precio_${listaId}_${variacion}`).value) || 0;
        const precioLista = parseFloat(document.getElementById(`precio_lista_${listaId}_${variacion}`).value) || 0;
        let precio;

        if (reglasPrecio[listaId] === 2) {
            // Regla 2: % de utilidad sobre el costo
            precio = costoDespuesDescuento * (1 + porcentajeUtilidad / 100);
            document.getElementById(`tipo_regla_precio_${listaId}_${variacion}`).innerText = '% Utilidad sobre el costo';
        } else if (reglasPrecio[listaId] === 1) {
            // Regla 1: precio fijo
            //precio = precioLista;
            precio = costoDespuesDescuento * (1 + porcentajeUtilidad / 100);
            document.getElementById(`tipo_regla_precio_${listaId}_${variacion}`).innerText = 'Precio fijo';
        } else {
            precio = costoDespuesDescuento * (1 + porcentajeUtilidad / 100);
            document.getElementById(`tipo_regla_precio_${listaId}_${variacion}`).innerText = 'Precio fijo';
        }

        document.getElementById(`precio_lista_${listaId}_${variacion}`).value = precio.toFixed(2);
    }
    
    // Función para calcular el precio de una lista de precios específica
    function calcularPrecio(listaId, variacion) {
        const costoDespuesDescuento = parseFloat(document.getElementById('costo_despues_descuento_' + variacion).value) || 0;
        const porcentajeUtilidad = parseFloat(document.getElementById(`porcentaje_regla_precio_${listaId}_${variacion}`).value) || 0;
        const precioLista = parseFloat(document.getElementById(`precio_lista_${listaId}_${variacion}`).value) || 0;
        let precio;

        if (reglasPrecio[listaId] === 2) {
            // Regla 2: % de utilidad sobre el costo
            precio = costoDespuesDescuento * (1 + porcentajeUtilidad / 100);
            document.getElementById(`tipo_regla_precio_${listaId}_${variacion}`).innerText = '% Utilidad sobre el costo';
        } else if (reglasPrecio[listaId] === 1) {
            // Regla 1: precio fijo
            precio = precioLista;
            document.getElementById(`tipo_regla_precio_${listaId}_${variacion}`).innerText = 'Precio fijo';
        }

        document.getElementById(`precio_lista_${listaId}_${variacion}`).value = precio.toFixed(2);
    }
    

    function calcularPrecioFijo(listaId, variacion) {
        const costoDespuesDescuento = parseFloat(document.getElementById('costo_despues_descuento_' + variacion).value) || 0;
        const precio = parseFloat(document.getElementById(`precio_lista_${listaId}_${variacion}`).value) || 0;
        
        let porcentajeUtilidad;
        if (costoDespuesDescuento > 0) {
            porcentajeUtilidad = ((precio - costoDespuesDescuento) / costoDespuesDescuento) * 100;
        } else {
            porcentajeUtilidad = 0;
        }

        document.getElementById(`porcentaje_regla_precio_${listaId}_${variacion}`).value = porcentajeUtilidad.toFixed(2);
        SetNombreReglas(listaId, variacion);
    }
/*
    function SetNombreReglas(listaId, variacion) {
        const element = document.getElementById(`tipo_regla_precio_${listaId}_${variacion}`);
        
        if(element){
        if (reglasPrecio[listaId] === 2) {
            document.getElementById(element).innerText = '% Utilidad sobre el costo';
        } else if (reglasPrecio[listaId] === 1) {
            document.getElementById(element).innerText = 'Precio fijo';
        } else {
            document.getElementById(element).innerText = 'Precio fijo';
        }
        }
    }
*/
function SetNombreReglas(listaId, variacion) {
    const element = document.getElementById(`tipo_regla_precio_${listaId}_${variacion}`);
    
    if (element) {
        if (reglasPrecio[listaId] === 2) {
            element.innerText = '% Utilidad sobre el costo';
        } else if (reglasPrecio[listaId] === 1) {
            element.innerText = 'Precio fijo';
        } else {
            element.innerText = 'Precio fijo';
        }
    } else {
        console.warn(`Elemento con id tipo_regla_precio_${listaId}_${variacion} no encontrado`);
    }
}

    function SetearNombreTodasReglas(variacion) {
        SetNombreReglas(0, variacion);
        SetNombreReglas(1, variacion);
        @foreach($lista_precios as $lp)
        SetNombreReglas('{{ $lp->id }}', variacion);
        @endforeach
    }

    function calcularTodosLosPrecios(variacion) {
        calcularPrecio(1, variacion);
        calcularPrecioFijo(1, variacion);
        calcularPrecio(0, variacion);
        calcularPrecioFijo(0, variacion);

        @foreach($lista_precios as $lp)
        calcularPrecio('{{ $lp->id }}', variacion);
        calcularPrecioFijo('{{ $lp->id }}', variacion);
        @endforeach
    }
    // Array global para almacenar las variaciones actualizadas
    var variacionesActualizadas = [];
    
    function CambiarStockDisponible(sucursalId, variacion) {
        // Obtener los elementos por sus IDs
        let stockRealInput = document.getElementById(`stock_real_${sucursalId}_${variacion}`);
        let stockComprometidoInput = document.getElementById(`stock_comprometido_${sucursalId}_${variacion}`);
        let stockDisponibleInput = document.getElementById(`stock_disponible_${sucursalId}_${variacion}`);
        
        // Convertir los valores de los inputs a números (asegurarse de manejar valores nulos)
        let stockReal = parseFloat(stockRealInput.value) || 0;
        let stockComprometido = parseFloat(stockComprometidoInput.value) || 0;
        
        // Calcular el stock disponible
        let stockDisponible = stockReal - stockComprometido;
        
        // Asignar el valor al campo stock_disponible
        stockDisponibleInput.value = stockDisponible;
    }

    function SetValuesStocks(variacion, stock_sucursales) {
        stock_sucursales.forEach(s => {
            const almacenElement = document.getElementById(`almacen_id_${s.sucursal_id}_${variacion}`);
            const stockRealElement = document.getElementById(`stock_real_${s.sucursal_id}_${variacion}`);
            const stockDisponibleElement = document.getElementById(`stock_disponible_${s.sucursal_id}_${variacion}`);
            const stockComprometidoElement = document.getElementById(`stock_comprometido_${s.sucursal_id}_${variacion}`);
    
            if (almacenElement) {
                almacenElement.value = s.almacen_id || 0;
            }
            if (stockRealElement) {
                stockRealElement.value = s.stock_real || 0;
            }
            if (stockDisponibleElement) {
                stockDisponibleElement.value = s.stock_disponible || 0;
            }
            if (stockComprometidoElement) {
                stockComprometidoElement.value = s.stock_comprometido || 0;
            }
        });
    }
    
        
    function SetValuesPrecios(variacion, costos, lista_precios, regla_precio_interno) {
        const setValueIfElementExists = (id, value) => {
            const element = document.getElementById(id);
            if (element) {
                element.value = value;
            } else {
                console.warn(`Elemento con id ${id} no encontrado`);
            }
        };
    
        // Actualizamos los valores de las variaciones
        costos.forEach(costo => {
            setValueIfElementExists('cost_' + variacion, costo.cost);
            setValueIfElementExists('descuento_costo_' + variacion, costo.descuento_costo);
            setValueIfElementExists('costo_despues_descuento_' + variacion, costo.costo_despues_descuento);
            setValueIfElementExists('precio_lista_1_' + variacion, costo.precio_interno);
            setValueIfElementExists('porcentaje_regla_precio_1_' + variacion, costo.porcentaje_regla_precio_interno);
    
            const msg_interno = regla_precio_interno == "1" ? "Precio interno" : "% Utilidad sobre el costo";
            setValueIfElementExists('nombre_regla_1_' + variacion, msg_interno);
    
            SetNombreReglas(1, variacion);
        });
    
        // Actualizamos los valores de la lista de precios
        lista_precios.forEach(lp => {
            if(lp.lista_id != 1){
            setValueIfElementExists(`porcentaje_regla_precio_${lp.lista_id}_${variacion}`, lp.porcentaje_regla_precio);
            setValueIfElementExists(`precio_lista_${lp.lista_id}_${variacion}`, lp.precio_lista);
    
            const msg = lp.regla == "1" ? "Precio fijo" : "% Utilidad sobre el costo";
            setValueIfElementExists(`nombre_regla_${lp.lista_id}_${variacion}`, msg);
    
            SetNombreReglas(lp.lista_id, variacion);
            }
        });
    }
    
    document.addEventListener('livewire:load', function () {
        Livewire.on('valuesUpdated', function (variaciones,costos, lista_precios, regla_precio_interno, stock_sucursales,base64Image, ImageNombre) {
            
            console.log(lista_precios);
            
            variaciones.forEach(v => {
            var variacion = v.variacion;
            
            // Filtrar los costos correspondientes a la variación actual
            var costosFiltrados = costos.filter(costo => costo.variacion_id == variacion);
            
            // Filtrar los precios de la lista para la variación actual
            var preciosFiltrados = lista_precios.filter(lp => lp.variacion_id == variacion);
            
            // Filtrar los stocks correspondientes a la variación actual
            //var stocksFiltrados = stock_sucursales.filter(stock => stock.variacion_id == variacion);
            
            SetValuesPrecios(variacion,costosFiltrados, preciosFiltrados, regla_precio_interno);
            //SetValuesStocks(variacion, stocksFiltrados);

            SetValuesStocks(variacion,stock_sucursales);
            
            // Guardamos la variación en el array si no existe ya
            if (!variacionesActualizadas.includes(variacion)) {
                variacionesActualizadas.push(variacion);
            }
            
            });
           
            if (base64Image) {
                console.log(base64Image);  // Asegúrate de que estás recibiendo correctamente la imagen Base64
        
                // Seleccionamos el elemento img donde mostraremos la previsualización
                const imageBase64 = document.getElementById('imagenBase64');
                const $imagennombreArchivoOriginal = document.querySelector("#imagennombreArchivoOriginal");
                const $base64Input = document.querySelector("#base64Input"); // Campo de entrada para base64
                
                imageBase64.value = base64Image;
                $base64Input.value = base64Image;
                $imagennombreArchivoOriginal.value = ImageNombre;
                const base64 = $base64Input.value;
                $imagenPrevisualizar.src = base64; // Mostrar la imagen desde el base64 ingresado


            }
        });
        
        // Ocultar el mensaje de "Cargando..." al terminar
        Livewire.on('datosGuardados', function() {
                document.getElementById('loadingOverlayProducts').classList.remove('show');
        });
        
        Livewire.on('actualizarDescuentoProveedor', function (nuevoDescuento, variacion) {
            document.getElementById('descuento_costo_' + variacion).value = nuevoDescuento;
            calcularCostoDescuento(variacion);
            calcularTodosLosPrecios(variacion);
        });
    
        Livewire.on('SetNombreTodasReglas', function (variacion) {
            SetearNombreTodasReglas(variacion);
    
            // Aseguramos que la variación esté en el array
            if (!variacionesActualizadas.includes(variacion)) {
                variacionesActualizadas.push(variacion);
            }
        });
        
    });

function mostrarOverlay() {
//    document.getElementById('global-loader').style.display = 'flex'; // Muestra el overlay
}

function ocultarOverlay() {
//    document.getElementById('global-loader').style.display = 'none'; // Oculta el overlay
}

  

function GuardarDescuento() {
    mostrarOverlay(); // Mostrar el overlay cuando comienza la carga
    
    Livewire.emit('GuardarDescuento', "");

    // Escuchar el evento de finalización de Livewire
    Livewire.on('descuentoGuardado', function() {
        ocultarOverlay(); // Ocultar el overlay cuando termine la acción
    });
}    

function GuardarDescuentosEnLotes() {
    const ids = [];
    
    $('.mis-checkboxes').each(function() {
        if($(this).hasClass('mis-checkboxes')) {
            if($(this).is(':checked')) {
                ids.push($(this).attr('tu-attr-id'));    
            }
        }
    });

    // Verificar si el array ids está vacío
    if (ids.length === 0) {
        alert('Debe agregar productos a la selección múltiple');
        return; // Salir de la función si no hay IDs
    }

    // Mostrar el overlay cuando inicia la carga
    mostrarOverlay();
    
    // Desmarcar los checkbox
    document.querySelectorAll('.mis-check_todos').forEach(function(checkElement) {
        checkElement.checked = false;
    });
    
    Livewire.emit('GuardarDescuentosEnLote', ids);

    // Escuchar el evento de finalización de Livewire
    Livewire.on('descuentosGuardados', function() {
        ocultarOverlay(); // Ocultar el overlay cuando termine la acción
    });
}

function GuardarDatos() {
    
    //document.getElementById('loadingOverlayProducts').style.display = 'block';
    document.getElementById('loadingOverlayProducts').classList.add('show');
    
    var producto_tipo = document.getElementById('producto_tipo').value;
    
    // Arrays para acumular los datos de todas las variaciones
    var todosCostos = [];
    var todasVariaciones = [];
    var todosPreciosYPorcentajesListas = [];
    var todosStocks = [];
    
    if (producto_tipo == "s") {
        // Si es un producto simple, solo enviamos una variación vacía
        todasVariaciones.push(0);
    }

    if (producto_tipo == "v") {
        // Acumulamos todas las variaciones
        variacionesActualizadas.forEach(function (variacion) {
            if (variacion != 0) {
                todasVariaciones.push(variacion);
            }
        });
    }
    
    // Recorremos todas las variaciones para acumular los datos
    todasVariaciones.forEach(function (variacion) {
        var Costos = [];
        var preciosYPorcentajesListas = [];
        var Stocks = [];
        
        // Costos
        var cost = parseFloat(document.getElementById('cost_' + variacion).value) || 0;
        var descuento = parseFloat(document.getElementById('descuento_costo_' + variacion).value) || 0;
        var costoDespuesDescuento = parseFloat(document.getElementById('costo_despues_descuento_' + variacion).value) || 0;

        
        // Precio interno
        var PrecioInterno = parseFloat(document.getElementById('precio_lista_1_' + variacion).value) || 0;
        var PorcentajePrecioInterno = parseFloat(document.getElementById('porcentaje_regla_precio_1_' + variacion).value) || 0;
        
        Costos.push({
            variacion_id: variacion,
            cost: cost,
            descuento: descuento,
            costo_despues_descuento: costoDespuesDescuento,
            precio_interno: PrecioInterno,
            porcentaje_precio_interno: PorcentajePrecioInterno
        });

        // Precio base
        var precio_base = parseFloat(document.getElementById('precio_lista_0_' + variacion).value) || 0;
        var porcentaje_regla_precio_base = parseFloat(document.getElementById('porcentaje_regla_precio_0_' + variacion).value) || 0;
        
        // Listas precios
        preciosYPorcentajesListas.push({
            lista_id: 0,
            variacion_id: variacion,
            precio: precio_base,
            porcentaje: porcentaje_regla_precio_base
        });
        
        @foreach($lista_precios as $lp) 
        var precio = parseFloat(document.getElementById(`precio_lista_{{ $lp->id }}_${variacion}`).value) || 0;
        var porcentaje_regla_precio = parseFloat(document.getElementById(`porcentaje_regla_precio_{{ $lp->id }}_${variacion}`).value) || 0;

        preciosYPorcentajesListas.push({
            lista_id: '{{$lp->id}}',
            variacion_id: variacion,
            precio: precio,
            porcentaje: porcentaje_regla_precio
        });
        @endforeach
        
        // Reglas de precios
        const reglasPrecio = {
            @foreach($lista_precios_reglas as $lp)
            '{{ $lp->lista_id }}': {{ $lp->regla ?? 1 }},
            @endforeach
        };
        
        // Stocks
        var almacen_id_base = parseFloat(document.getElementById('almacen_id_0_' + variacion).value) || 1;
        var stock_real_base = parseFloat(document.getElementById('stock_real_0_' + variacion).value) || 0;
        var stock_disponible_base = parseFloat(document.getElementById('stock_disponible_0_' + variacion).value) || 0;

        Stocks.push({
            sucursal_id: 0,
            almacen_id : almacen_id_base,
            variacion_id: variacion,
            stock_real: stock_real_base,
            stock_disponible: stock_disponible_base
        });
        
        @foreach($sucursales as $sucursal)
        var almacen_id = parseFloat(document.getElementById(`almacen_id_{{ $sucursal->sucursal_id }}_${variacion}`).value) || 1;
        var stock_real = parseFloat(document.getElementById(`stock_real_{{ $sucursal->sucursal_id }}_${variacion}`).value) || 0;
        var stock_disponible = parseFloat(document.getElementById(`stock_disponible_{{ $sucursal->sucursal_id }}_${variacion}`).value) || 0;
        
        Stocks.push({
            sucursal_id: '{{$sucursal->sucursal_id}}',
            almacen_id: almacen_id,
            variacion_id: variacion,
            stock_real: stock_real,
            stock_disponible: stock_disponible
        });
        @endforeach

        // Agregamos los datos de esta variación al array global
        todosCostos.push(...Costos);
        todosPreciosYPorcentajesListas.push(...preciosYPorcentajesListas);
        todosStocks.push(...Stocks);
    });

    var imagen_base_64 = document.querySelector("#imagenBase64").value;
    var imagen_nombre = document.querySelector("#imagennombreArchivoOriginal").value;
        
    // Finalmente, enviamos todos los datos al backend en un solo emit
    Livewire.emit('guardarDatos', todasVariaciones, todosPreciosYPorcentajesListas, todosStocks,todosCostos,imagen_base_64,imagen_nombre);
}
    

</script>

<script>

document.addEventListener('DOMContentLoaded', function () {
    Livewire.on('completarValoresSimples', (data) => {

        // Asignar los valores estáticos
        document.getElementById('cost').value = data.cost;
        document.getElementById('porcentaje_sucursal').value = data.porcentaje_sucursal;
        document.getElementById('precio_sucursal').value = data.precio_sucursal;

    });
});


</script>

