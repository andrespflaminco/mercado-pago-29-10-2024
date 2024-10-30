                <div>	                
	                <div class="page-header">
					<div class="page-title">
							<h4>Ventas rapidas</h4>
							<h6>Ver listado de ventas rapidas</h6>
						</div>
						<div class="page-btn">               											    
                            <a class="btn btn-added" href="{{ url('venta-rapida') }}">+ Agregar nueva</a>
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
					    <ul class="nav nav-tabs  mb-3">
            				<li class="nav-item">
            						<a class="nav-link  {{ $sucursal_id == $comercio_id ? 'active' : '' }} " href="javascript:void(0)"  wire:click="ElegirSucursal({{$comercio_id}})"  > {{auth()->user()->name}} </a>
            				</li>
            				@foreach($sucursales as $item)
            				<li class="nav-item">
            						<a class="nav-link {{ $sucursal_id == $item->sucursal_id ? 'active' : '' }}" href="javascript:void(0)"  wire:click="ElegirSucursal({{$item->sucursal_id}})"  >{{$item->name}}</a>
            				</li>
            				@endforeach
            			</ul>
				
				<div class="card-body">
					<div class="row">
					     @if(session('status'))
                         <strong style="padding: 5px 5px 5px 5px !important; border-radius: 3px; margin-right: 15px!important; color:#e2a03f !important" >{{ session('status') }}</strong>
                         @endif
                         
                        <div class="col-lg-3 col-md-3 col-sm-3">
                        
                         <div class="form-group">
                        	<label>Buscar</label>
                            <div class="input-group">
                            <div class="input-group-append">
                            <span>
                            </span>
                            </div>                        	
                              <input type="text" wire:model="search" class="form-control" placeholder="Buscar...">
                            </div>
                        	
                          </div>
                         </div>
                        <div class="col-lg-3 col-md-3 col-sm-3">
                         <div class="form-group">
                        	<label>Fecha desde</label>
                        	<input type="date" wire:model="dateFrom" class="form-control" placeholder="Click para elegir">
                          </div>
                         </div>
                         <div class="col-lg-3 col-md-3 col-sm-3">
                         <div class="form-group">
                         	 <label>Fecha hasta</label>
                         	 <input type="date" wire:model="dateTo" class="form-control" placeholder="Click para elegir">
                         </div>
                         </div>
                		</div>
                
							 
				<div class="table-responsive">
					<table id="default-ordering" class="table">
						<thead>
							<tr>
								<th wire:click="sort('')">FECHA</th>
								<th wire:click="sort('')">NRO FACTURA</th>
								<th wire:click="sort('')">MONTO</th>
								<th>ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($data as $cobro)
							<tr>
								<td>{{\Carbon\Carbon::parse($cobro->created_at)->format('d/m/Y H:i')}}</td>
								<td>
								    @if($cobro->nota_credito == null) 
										@if($cobro->nro_factura)
										<?php
										$porciones = explode("-", $cobro->nro_factura);
										$tipo_factura = $porciones[0]; // porción1
										$pto_venta = $porciones[1]; // porción2
										$nro_factura_ = $porciones[2]; // porción2
										echo $tipo_factura."-".str_pad($pto_venta, 3, "0", STR_PAD_LEFT)."-".str_pad($nro_factura_, 5, "0", STR_PAD_LEFT); ?>
										@else
										-
										@endif
                                    @else
                                    <?php
                                    $porciones = explode("-", $cobro->nro_factura);
									$tipo_factura = $porciones[0]; // porción1
									$pto_venta = $porciones[1]; // porción2
									$nro_factura_ = $porciones[2]; // porción2
									echo '<span style="color: red; text-decoration: line-through;">';
                                    echo $tipo_factura."-".str_pad($pto_venta, 3, "0", STR_PAD_LEFT)."-".str_pad($nro_factura_, 5, "0", STR_PAD_LEFT);
                                    echo '</span>';

									 ?> 
									
									||
										
                                    {{$cobro->nota_credito}}
                                    @endif
								</td>
								<td>$ {{$cobro->total}}</td>

								<td>
    								<a href="{{ url('ticket-rapido' . '/' . $cobro->id ) }}"  target="_blank" class="btn btn-light" title="Imprimir ticket">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bookmark"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
									</a>
									<a href="{{ url('report-factura-rapido/pdf' . '/' . $cobro->id) }}"  target="_blank" class="btn btn-light" title="Imprimir A4">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
									</a>
									<a class="btn btn-light" href="javascript:void(0)" wire:click="MailModal({{$cobro->id}})" >
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
									</a>
									@if($cobro->nro_factura != null && $cobro->nota_credito == null)
									<a class="btn btn-light" href="javascript:void(0)" wire:click="AnularFactura({{$cobro->id}})" >
									NC
									</a>
									@endif
									
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
					{{$data->links()}}
				</div>
				</div>
				</div>

				@include('livewire.reports-venta-rapida.modal-email')	
					
				</div>
				



<script>
	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('product-added', msg => {
			$('#theModal').modal('hide')
			noty(msg)
		});

		window.livewire.on('category-added', msg => {
			$('#Categoria').modal('hide')
			$('#theModal').modal('show')
			noty(msg)
		});

		window.livewire.on('almacen-added', msg => {
			$('#Almacen').modal('hide')
			$('#theModal').modal('show')
			noty(msg)
		});


		window.livewire.on('product-updated', msg => {
			$('#theModal').modal('hide')

			noty(msg)
		});
		window.livewire.on('product-deleted', msg => {
			// noty
		});
		window.livewire.on('modal-show', msg => {
			$('#theModal').modal('show')
		});

        window.livewire.on('mail-modal', Msg =>{
        $('#MailModal').modal('show')
        })

		window.livewire.on('modal-categoria-show', msg => {
			$('#Categoria').modal('show')
			$('#theModal').modal('hide')
		});

		window.livewire.on('modal-almacen-show', msg => {
			$('#Almacen').modal('show')
			$('#theModal').modal('hide')
		});

		window.livewire.on('modal-hide', msg => {
			$('#theModal').modal('hide')
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



	});

	function Confirm(id) {

		swal({
			title: 'CONFIRMAR',
			text: '¿CONFIRMAS ELIMINAR EL REGISTRO?',
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

	function ConfirmCheck(id) {

		swal({
			title: 'CONFIRMAR',
			text: '¿CONFIRMAS ELIMINAR LOS REGISTROS?',
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
	}
</script>
<script>
		$('#default-ordering').DataTable( {
				"stripeClasses": [],
				drawCallback: function () { $('.dataTables_paginate > .pagination').addClass(' pagination-style-13 pagination-bordered mb-5'); }
	} );
</script>
<script type="text/javascript">

function getEditar(item)
{

    var id =item.value;

		var x = document.getElementById("id"+id);
		var y = document.getElementById("id2-"+id);

		if (x.style.display === "block") {
			x.style.display = "none";
			y.style.display = "block";
		} else {
			x.style.display = "block";
			y.style.display = "none";
		}


}

function getCerrarEditar(item)
{

    var id =item.value;

		var y = document.getElementById("id"+id);
		var x = document.getElementById("id2-"+id);

		if (x.style.display === "block") {
			x.style.display = "none";
			y.style.display = "flex";
		} else {
			x.style.display = "flex";
			y.style.display = "none";
		}


}

//
</script>
<script type="text/javascript">

function getEditarPrice(item)
{

    var id =item.value;

		var a = document.getElementById("idprice"+id);
		var b = document.getElementById("idprice2-"+id);

		if (a.style.display === "block") {
			a.style.display = "none";
			b.style.display = "block";
		} else {
			a.style.display = "block";
			b.style.display = "none";
		}


}

function getCerrarEditarPrice(item)
{

    var id =item.value;

		var b = document.getElementById("idprice"+id);
		var a = document.getElementById("idprice2-"+id);

		if (a.style.display === "block") {
			a.style.display = "none";
			b.style.display = "flex";
		} else {
			a.style.display = "flex";
			b.style.display = "none";
		}


}

//
</script>
<script type="text/javascript">
document.getElementById("file-input").onchange = function(e) {
// Creamos el objeto de la clase FileReader
let reader = new FileReader();

// Leemos el archivo subido y se lo pasamos a nuestro fileReader
reader.readAsDataURL(e.target.files[0]);

// Le decimos que cuando este listo ejecute el código interno
reader.onload = function(){
	let preview = document.getElementById('image-upload'),
					image = document.createElement('img');

	image.src = reader.result;

	preview.innerHTML = '';
	preview.append(image);
};
}
</script>
<script>
    document.addEventListener('DOMContentLoaded', function(){




        flatpickr(document.getElementsByClassName('flatpickr'),{
            enableTime: false,
            dateFormat: 'Y-m-d',
            locale: {
                firstDayofWeek: 1,
                weekdays: {
                    shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                    longhand: [
                    "Domingo",
                    "Lunes",
                    "Martes",
                    "Miércoles",
                    "Jueves",
                    "Viernes",
                    "Sábado",
                    ],
                },
                months: {
                    shorthand: [
                    "Ene",
                    "Feb",
                    "Mar",
                    "Abr",
                    "May",
                    "Jun",
                    "Jul",
                    "Ago",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dic",
                    ],
                    longhand: [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Septiembre",
                    "Octubre",
                    "Noviembre",
                    "Diciembre",
                    ],
                },

            }

        })


							})

</script>





@include('livewire.reports-venta-rapida.scripts.ConectorPlugin')


<script type="text/javascript">

	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('imprimir-ticket', items => {
		    var item = items;
		    document.getElementById("items").value = item;
            var codigo_qr = document.getElementById("codigo_qr").value;
            var condicion_iva = document.getElementById("condicion_iva").value;
            var pto_venta = document.getElementById("pto_venta").value;
            var tipo_comprobante = document.getElementById("tipo_comprobante").value;
            var total = document.getElementById("total").value;
            var subtotal = document.getElementById("subtotal").value;
            var iva = document.getElementById("iva").value;

            var detalle_items = JSON.parse(item)

            // Iteracion de la lista de detalles.

                function pasarLista(detalle_items, indice) {
                    console.log(` ${detalle_items.product_name} `);
                }

                detalle_items.forEach((detalle_items, indice) => pasarLista(detalle_items, indice));

		});

		});


const $estado = document.querySelector("#estado"),
    $listaDeImpresoras = document.querySelector("#listaDeImpresoras"),
    $btnLimpiarLog = document.querySelector("#btnLimpiarLog"),
    $btnImprimir = document.querySelector("#btnImprimir");



const loguear = texto => $estado.textContent += (new Date()).toLocaleString() + " " + texto + "\n";
const limpiarLog = () => $estado.textContent = "";

$btnLimpiarLog.addEventListener("click", limpiarLog);


const obtenerListaDeImpresoras = () => {
    loguear("Cargando lista...");
    ConectorPlugin.obtenerImpresoras()
        .then(listaDeImpresoras => {
            loguear("Lista cargada");
            listaDeImpresoras.forEach(nombreImpresora => {
                const option = document.createElement('option');
                option.value = option.text = nombreImpresora;
                $listaDeImpresoras.appendChild(option);
            })
        })
        .catch(() => {
            loguear("Error obteniendo impresoras. Asegúrese de que el plugin se está ejecutando");
        });
}


$btnImprimir.addEventListener("click", () => {

    var codigo_qr = document.getElementById("codigo_qr").value;
    var razon_social = document.getElementById("razon_social").value;
    var condicion_iva = document.getElementById("condicion_iva").value;
    var pto_venta = document.getElementById("pto_venta").value;
    var iibb = document.getElementById("iibb").value;
    var inicio_actividades = document.getElementById("inicio_actividades").value;
    var tipo_comprobante = document.getElementById("tipo_comprobante").value;
    var total = document.getElementById("total").value;
    var subtotal = document.getElementById("subtotal").value;
    var iva = document.getElementById("iva").value;
    var cae = document.getElementById("cae").value;
    var fecha = document.getElementById("fecha").value;
    var item = document.getElementById("items").value;
    var usuario = document.getElementById("usuario").value;
    var direccion = document.getElementById("direccion").value;
    var cuit = document.getElementById("cuit").value;
    var cuit_cliente = document.getElementById("cuit_cliente").value;
    var nombre_cliente = document.getElementById("nombre_cliente").value;
    var nro_factura = document.getElementById("nro_factura").value;

    var detalle_items = JSON.parse(item);


    let nombreImpresora = $listaDeImpresoras.value;
    if (!nombreImpresora) return loguear("Selecciona una impresora");
    let conector = new ConectorPlugin();
    conector.establecerTamanioFuente(1, 1);
    conector.establecerEnfatizado(0);
    conector.establecerJustificacion(ConectorPlugin.Constantes.AlineacionCentro);
    conector.feed(3);
    conector.establecerTamanioFuente(2, 1);
    conector.texto("" + usuario + "\n");
    conector.feed(2);
    conector.establecerTamanioFuente(1, 1);
    conector.establecerEnfatizado(0);
    conector.establecerJustificacion(ConectorPlugin.Constantes.AlineacionIzquierda);
    conector.texto("CUIT: " + cuit + "\n");
    conector.texto("COND. IVA: \n");
    conector.texto("" + condicion_iva + "\n");
    conector.texto("ESTABLECIMIENTO: " + pto_venta + "\n");
    conector.texto("INICIO ACT: " + inicio_actividades + "\n");
    conector.texto("IIBB: " + iibb + "\n");
    conector.texto("DIRECCION: \n");
    conector.texto("" + direccion + "\n");
    conector.texto("" + razon_social + "\n");

    conector.texto("--------------------------------\n");

    if(nro_factura != 0) {
    conector.texto("TICKET:" + nro_factura + "\n");
    }
    conector.texto("Fecha/Hora:" + fecha + "\n");

    conector.texto("--------------------------------\n");


    if(cuit_cliente != 0) {
    conector.texto("" + nombre_cliente + "\n");
    conector.texto("CUIT:" + cuit_cliente + "\n");
    } else {
     conector.texto("CONSUMIDOR FINAL \n");
    conector.texto("DNI: 00000000 \n");
    }


    conector.texto("--------------------------------\n");

    function pasarLista(detalle_items, indice) {
    conector.establecerJustificacion(ConectorPlugin.Constantes.AlineacionIzquierda);

    if(tipo_comprobante == "A") {
      conector.texto(` ${detalle_items.product_name}(${detalle_items.quantity}x $${detalle_items.price})`);
    } else {
          conector.texto(` ${detalle_items.product_name}(${detalle_items.quantity}x $${detalle_items.price_iva})`);
    }


    conector.establecerJustificacion(ConectorPlugin.Constantes.AlineacionDerecha);
     if(tipo_comprobante == "A") {
    conector.texto(`$ ${detalle_items.price*detalle_items.quantity}  \n`);
     } else {
    conector.texto(`$ ${detalle_items.price_iva*detalle_items.quantity}  \n`);
     }
    }

    detalle_items.forEach((detalle_items, indice) => pasarLista(detalle_items, indice));

    if(tipo_comprobante == "A") {

    conector.texto("--------------------------------\n");
    conector.texto("SUBTOTAL: $ " + subtotal + "\n");
    conector.texto("IVA: $ " + iva + "\n");
    conector.texto("TOTAL: $ " + total + "\n");
    conector.texto("--------------------------------\n");

    } else {
       conector.texto("--------------------------------\n");
    conector.texto("TOTAL: $ " + total + "\n");
    conector.texto("--------------------------------\n");
    }

    conector.establecerJustificacion(ConectorPlugin.Constantes.AlineacionCentro);
    conector.feed(3);
    if(codigo_qr != 0) {
    conector.establecerJustificacion(ConectorPlugin.Constantes.AlineacionCentro).qrComoImagen(codigo_qr);
    conector.texto("\n");
    conector.texto("CAE:" + cae + "\n \n");
    }
    conector.texto("***Gracias por su compra***");
    conector.feed(3);
    conector.cortar();
    conector.cortarParcialmente();
    conector.imprimirEn(nombreImpresora)
        .then(respuestaAlImprimir => {
            if (respuestaAlImprimir === true) {
                loguear("Impreso correctamente");
            } else {
                loguear("Error. La respuesta es: " + respuestaAlImprimir);
            }
        });
});

// En el init, obtenemos la lista
obtenerListaDeImpresoras();


</script>

<script>
    	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('imprimir-ticket', msg => {
			$('#exampleModal').modal('show')
		});

    	});

</script>
