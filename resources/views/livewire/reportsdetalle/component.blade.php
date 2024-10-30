<div >	                

    @include('livewire.reportsdetalle.configurar-columnas')
    
    <div class="page-header">
					<div class="page-title">
							<h4>Ventas por producto</h4>
							<h6>Vea los productos que se vendieron en cada venta</h6>
						</div>
						<div class="page-btn">
							<a hidden href="javascript:void(0)" wire:click="Agregar()" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar producto</a>
						</div>
					</div>
					
                    
	<!-- /product list -->
	<div class="card">
	        <ul class="nav nav-tabs  mb-3">
            <li style="background:white; border: solid 1px #eee;" class="nav-item">
                <a style="{{ $sucursal_id == $comercio_id ? 'color: #e95f2b;' : '' }}" class="nav-link  {{ $sucursal_id == $comercio_id ? 'active' : '' }} " href="javascript:void(0)"  wire:click="ElegirSucursal({{$comercio_id}})"  > {{auth()->user()->name}} </a>
            </li>
            @foreach($sucursales as $item)
            <li style="background:white; border: solid 1px #eee;"  class="nav-item">
                <a style="{{ $sucursal_id == $item->sucursal_id ? 'color: #e95f2b;' : '' }}" class="nav-link {{ $sucursal_id == $item->sucursal_id ? 'active' : '' }}" href="javascript:void(0)"  wire:click="ElegirSucursal({{$item->sucursal_id}})"  >{{$item->name}}</a>
            </li>
            @endforeach
        	</ul>
			
						<div class="card-body">
							<div class="table-top">
								<div class="search-set">
									<div class="search-path">
									   	
									   	<a style="font-size:14px !important; padding:5px !important; background: #FF9F43 !important; width: auto !important; color: white;" onClick="muestra_oculta('contenido')" class="btn btn-filter" >
											<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
											<span><img src="{{ asset('assets/pos/img/icons/closes.svg') }}" alt="img"></span>
											<div style="margin-left: 5px; margin-right: 5px; font-size: 14px !important;">
											<b>Filtros</b> 
											</div>
										</a>
										
										<a hidden onClick="muestra_oculta('contenido')" class="btn btn-filter" id="filter_search">
											<img src="{{ asset('assets/pos/img/icons/filter.svg') }}"  alt="img">
											<span><img src="{{ asset('assets/pos/img/icons/closes.svg') }}" alt="img">
											
											</span>
										</a>
									</div>
									<div hidden class="search-input">
										<a class="btn btn-searchset"><img src="{{ asset('assets/pos/img/icons/search-white.svg') }}" alt="img"></a>
									</div>
								</div>
								<div class="wordset">
									<ul>
										<li>
											<a hidden data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img  src="{{ asset('assets/pos/img/icons/pdf.svg') }}"  alt="img"></a>
										</li>
										<li>
										    <a 
											style="font-size:12px !important; padding:5px !important; background: #198754 !important;" 
											class="btn btn-cancel" 
											wire:click="ExportarReporte (' {{ ( ($usuarioSeleccionado == '' ? '0' : $usuarioSeleccionado) . '/' . ($ClienteSeleccionado == '' ? '0' : $ClienteSeleccionado) . '/' . ($metodopagoSeleccionado == '' ? '0' : $metodopagoSeleccionado) . '/' . ($productoSeleccionado == '' ? '0' : $productoSeleccionado) . '/' . ($categoriaSeleccionado == '' ? '0' : $categoriaSeleccionado) . '/' . ($almacenSeleccionado == '' ? '0' : $almacenSeleccionado) . '/' . $dateFrom . '/' . $dateTo . '/' . $sucursal_id) }} ') "  title="Descargar Excel"  data-bs-placement="top" title="exportar excel"> 
											<svg style="margin-right: 5px;"  xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
											Exportar </a>
											
											<a hidden wire:click="ExportarReporte (' {{ ( ($usuarioSeleccionado == '' ? '0' : $usuarioSeleccionado) . '/' . ($ClienteSeleccionado == '' ? '0' : $ClienteSeleccionado) . '/' . ($metodopagoSeleccionado == '' ? '0' : $metodopagoSeleccionado) . '/' . ($productoSeleccionado == '' ? '0' : $productoSeleccionado) . '/' . ($categoriaSeleccionado == '' ? '0' : $categoriaSeleccionado) . '/' . ($almacenSeleccionado == '' ? '0' : $almacenSeleccionado) . '/' . $dateFrom . '/' . $dateTo . '/' . $sucursal_id) }} ') " data-bs-toggle="tooltip" data-bs-placement="top" title="excel"><img  src="{{ asset('assets/pos/img/icons/excel.svg') }}" alt="img"></a>
										</li>
										<li>
											<a hidden data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img  src="{{ asset('assets/pos/img/icons/printer.svg') }}" alt="img"></a>
										</li>
									</ul>
								</div>
							</div>
						
						
						<div id="contenido" class="card component-card_1">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-sm-12 col-md-4">
                       <div class="form-group">
                        <label>Cliente</label>
                        <div wire:ignore>

                            <select class="form-control tagging" multiple="multiple" id="select2-dropdown-cliente">
                              <option value="1">Consumidor final</option>
                              @foreach($clientes as $client)

                              <option value="{{$client->id}}">{{$client->nombre}}</option>
                              @endforeach
                          </select>
                      </div>
                      </div>
                      </div>


                      <div class="col-sm-12 col-md-4">
                       <div class="form-group">
                        <label>Vendedor</label>
                        <div wire:ignore>

                            <select class="form-control tagging" multiple="multiple" id="select2-dropdown-usuario">
                              @foreach($users as $u)
                              <option value="{{$u->id}}">{{$u->name}}</option>
                              @endforeach
                          </select>
                      </div>
                      </div>
                      </div>


                      <div class="col-sm-12 col-md-4">
                       <div class="form-group">
                        <label>Metodo de pago</label>
                        <div wire:ignore>

                            <select class="form-control tagging" multiple="multiple" id="select2-dropdown-metodo-pago">
                              <option value="1">Efectivo</option>
                              @foreach($metodo_pago as $mp)
                              <option value="{{$mp->id}}">{{$mp->nombre_banco}} - {{$mp->nombre}}</option>
                              @endforeach
                          </select>
                      </div>
                      </div>
                      </div>
                      <div class="col-sm-12 col-md-4">
                       <div class="form-group">
                        <label>Producto</label>
                        <div wire:ignore>

                            <select class="form-control tagging" multiple="multiple" id="select2-dropdown-producto">
                              @foreach($products as $p)
                              <option value="{{$p->id}}">{{$p->barcode}} - {{$p->name}}</option>
                              @endforeach
                          </select>
                      </div>
                      </div>
                      </div>

                      <div class="col-sm-12 col-md-4">
                       <div class="form-group">
                        <label>Categoria</label>
                        <div wire:ignore>

                            <select class="form-control tagging" multiple="multiple" id="select2-dropdown-categoria">
                              <option value="1">Sin categoria</option>
                              @foreach($categoria as $c)
                              <option value="{{$c->id}}">{{$c->name}}</option>
                              @endforeach
                          </select>
                      </div>
                      </div>
                      </div>
                      <div class="col-sm-12 col-md-4">
                       <div class="form-group">
                        <label>Seccion del almacen</label>
                        <div wire:ignore>

                            <select class="form-control tagging" multiple="multiple" id="select2-dropdown-seccion-almacen">
                              <option value="1">Sin almacen</option>
                              @foreach($seccion_almacen as $sa)
                              <option value="{{$sa->id}}">{{$sa->nombre}}</option>
                              @endforeach
                          </select>
                      </div>
                      </div>
                      </div>
                      <div class="col-sm-12 col-md-4">
                       <div class="form-group">
                        <label>Fecha desde</label>
                        <input type="date" wire:model="dateFrom" class="form-control">

                      </div>
                      </div>

                      <div class="col-sm-12 col-md-4">
                       <div class="form-group">
                        <label>Fecha hasta</label>
                        <input type="date" wire:model="dateTo" class="form-control">

                      </div>
                      </div>
                      
                      <div class="col-sm-12 col-md-4">
                       <div class="form-group">
                        <label>Estado de entrega</label>
                        <div wire:ignore>

                            <select class="form-control" wire:model="estado_entrega_search">
                              <option value="all">Todos</option>
                              <option value="1">Entregado</option>
                              <option value="0">Pendiente</option>
                          </select>
                      </div>
                      </div>
                      </div>

                   </div>

                   </div>






                      </div>
                      
                      <div class="card component-card_1">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-sm-12 col-md-3">
                           <div style="text-align:center !important; vertical-align: middle !important;" class="form-group">

                         <h6>Ventas Prod: $ {{number_format($this->suma_totales,2,",",".")}}</h6>

                      </div>
                    </div>
                    
                    <div class="col-sm-12 col-md-2">
                           <div style="text-align:center !important; vertical-align: middle !important;" class="form-group">

                             
                             <h6> - Descuentos: $ {{number_format( $this->suma_descuento + $this->suma_descuento_promo,2,",",".")}}</h6>



                      </div>
                    </div>     
                    
                     <div class="col-sm-12 col-md-2">
                           <div style="text-align:center !important; vertical-align: middle !important;" class="form-group">

                             <h6> + Recargos: $ {{number_format($this->suma_recargo ,2,",",".")}}</h6>


                      </div>
                    </div>
                    
                     <div class="col-sm-12 col-md-2">
                           <div style="text-align:center !important; vertical-align: middle !important;" class="form-group">

                             <h6> + Imp: $ {{number_format($this->suma_iva ,2,",",".")}}</h6>


                      </div>
                    </div>
                 
                                    
                     <div class="col-sm-12 col-md-3">
                           <div style="text-align:center !important; vertical-align: middle !important;" class="form-group">

                             
                             <h6>Ventas Total: $ {{number_format($this->suma_totales + $this->suma_iva + $this->suma_recargo - $this->suma_descuento - $this->suma_descuento_promo,2,",",".")}}</h6>



                      </div>
                    </div>

                  </div>
                    </div>
                    </div>
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
                                        <th @if(!$columns['created_at']) style="display: none;" @endif>FECHA</th>
                                        <th @if(!$columns['nro_venta']) style="display: none;" @endif>ID VENTA</th>
                                        <th @if(!$columns['barcode']) style="display: none;" @endif>COD PROD</th>
                                        <th @if(!$columns['product']) style="display: none;" @endif>PRODUCTO</th>
                                        <th @if(!$columns['nombre_categoria']) style="display: none;" @endif>CATEGORIA</th>
                                        <th @if(!$columns['nombre_cliente']) style="display: none;" @endif>CLIENTE</th>
                                        <th @if(!$columns['nombre_usuario']) style="display: none;" @endif>VENDEDOR</th>
                						<th @if(!$columns['price']) style="display: none;" @endif>PRECIO</th>
                						<th @if(!$columns['quantity']) style="display: none;" @endif>CANT.</th>
                						<th @if(!$columns['tipo_unidad_medida']) style="display: none;" @endif>UNIDAD MEDIDA</th>
                						<th @if(!$columns['iva']) style="display: none;" @endif>IVA.</th>
                						<th @if(!$columns['recargo']) style="display: none;" @endif>RECARGO</th>
                						<th @if(!$columns['descuento']) style="display: none;" @endif>DESCUENTO</th>
                						<th @if(!$columns['descuento_promo']) style="display: none;" @endif>DESCUENTO EN PROMOCIONES</th>
                						<th @if(!$columns['costo']) style="display: none;" @endif>COSTO TOTAL</th>
                						<th @if(!$columns['total']) style="display: none;" @endif>IMPORTE TOTAL</th>
                						<th @if(!$columns['almacen']) style="display: none;" @endif>ALMACEN</th>
                						<th @if(!$columns['nombre_banco']) style="display: none;" @endif>BANCO</th>
                						<th @if(!$columns['nombre_metodo_pago']) style="display: none;" @endif>FORMA DE PAGO</th>
                						<th @if(!$columns['entrega']) style="display: none;" @endif>ESTADO ENTREGA</th>
										</tr>
									</thead>
									<tbody>
									 @if(count($data)<1)
        							<tr><td class="text-center" colspan="10"><h5>Sin resultados</h5></td></tr>
        							@endif
        							@foreach($data as $d)
        							<tr>
                                    
        							<td @if(!$columns['created_at']) style="display: none;" @endif>{{\Carbon\Carbon::parse($d->created_at)->format('d-m-Y H:i')}}</td>
                                    <td @if(!$columns['nro_venta']) style="display: none;" @endif>{{$d->nro_venta}}</td>
                                    <td @if(!$columns['barcode']) style="display: none;" @endif>{{$d->barcode}}</td>
        							<td @if(!$columns['product']) style="display: none;" @endif>{{$d->product}}</td>
                                    <td @if(!$columns['nombre_categoria']) style="display: none;" @endif>{{$d->nombre_categoria}}</td>
                                    <td @if(!$columns['nombre_cliente']) style="display: none;" @endif>{{$d->nombre_cliente}}</td>
                                    <td @if(!$columns['nombre_usuario']) style="display: none;" @endif>{{$d->nombre_usuario}}</td>
        							<td @if(!$columns['price']) style="display: none;" @endif>${{number_format($d->price,2)}}</td>
        							<td @if(!$columns['quantity']) style="display: none;" @endif>
        							    @if($d->tipo_unidad_medida == 1) {{number_format($d->quantity,3,",",".")}} @endif @if($d->tipo_unidad_medida == 9) {{number_format($d->quantity)}} @endif
        							</td>
        							<td @if(!$columns['tipo_unidad_medida']) style="display: none;" @endif> @if($d->tipo_unidad_medida == 1) KG @endif @if($d->tipo_unidad_medida == 9) UNID @endif </td>
        							<td @if(!$columns['iva']) style="display: none;" @endif>${{number_format((($d->price*$d->quantity) - $d->descuento + $d->recargo)  * $d->iva,2)}}</td>
        							<td @if(!$columns['recargo']) style="display: none;" @endif> $ {{number_format($d->recargo)}}</td>
        							<td @if(!$columns['descuento']) style="display: none;" @endif> $ {{number_format($d->descuento)}}</td>
        							<td @if(!$columns['descuento_promo']) style="display: none;" @endif> $ {{number_format($d->descuento_promo * $d->cantidad_promo)}}</td>
        							<td @if(!$columns['costo']) style="display: none;" @endif> $ {{number_format($d->cost*$d->quantity)}}</td>
        							<td @if(!$columns['total']) style="display: none;" @endif>${{number_format( ((($d->price*$d->quantity) - $d->descuento + $d->recargo - ($d->descuento_promo * $d->cantidad_promo))  * (1+$d->iva)) ,2)}}</td>
        							<td @if(!$columns['almacen']) style="display: none;" @endif>{{$d->almacen}}</td>
        							<td @if(!$columns['nombre_banco']) style="display: none;" @endif>{{$d->nombre_banco}}</td>
        							<td @if(!$columns['nombre_metodo_pago']) style="display: none;" @endif>{{$d->nombre_metodo_pago}}</td>
        							<td @if(!$columns['entrega']) style="display: none;" @endif>{{$d->estado}}</td>


        							</tr>

        							@endforeach
									</tbody>
								</table>
								
							</div>
							<br>
							{{$data->links()}}
						</div>
					</div>

					
</div>

<script type="text/javascript">

function muestra_oculta(id){

if (document.getElementById){
   //se obtiene el id
var el = document.getElementById(id); //se define la variable "el" igual a nuestro div
el.style.display = (el.style.display == 'none') ? 'block' : 'none'; //damos un atributo display:none que oculta el div
}

}
window.onload = function(){
  /*hace que se cargue la función lo que predetermina que div estará oculto hasta llamar a la
  función nuevamente*/
muestra_oculta('contenido');/* "contenido_a_mostrar" es el nombre que le dimos al DIV */

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


        $('.tagging').select2({
                        tags: true
                    });

              //CLIENTES

                $('#select2-dropdown-cliente').on('change', function(e) {
                  var id = $('#select2-dropdown-cliente').select2('val');
                  var name = $('#select2-dropdown-cliente option:selected').text();
                  @this.set('clientesSelectedName', name);
                  @this.set('ClienteSeleccionado', ''+id);
                  @this.emit('locationUsersSelected', $('#select2-dropdown-cliente').select2('val'));
                });

                //USUARIOS


                $('#select2-dropdown-usuario').on('change', function(e) {
                  var id = $('#select2-dropdown-usuario').select2('val');
                  var name = $('#select2-dropdown-usuario option:selected').text();
                  @this.set('UsuarioSelectedName', name);
                  @this.set('usuarioSeleccionado', ''+id);
                  @this.emit('UsuarioSelected', $('#select2-dropdown-usuario').select2('val'));
                });


                //METODO DE PAGO


                $('#select2-dropdown-metodo-pago').on('change', function(e) {
                  var id = $('#select2-dropdown-metodo-pago').select2('val');
                  var name = $('#select2-dropdown-metodo-pago option:selected').text();
                  @this.set('metodopagoSelectedName', name);
                  @this.set('metodopagoSeleccionado', ''+id);
                  @this.emit('metodopagoSelected', $('#select2-dropdown-metodo-pago').select2('val'));
                });


                //PRODUCTOS


                $('#select2-dropdown-producto').on('change', function(e) {
                  var id = $('#select2-dropdown-producto').select2('val');
                  var name = $('#select2-dropdown-producto option:selected').text();
                  @this.set('productoSelectedName', name);
                  @this.set('productoSeleccionado', ''+id);
                  @this.emit('productoSelected', $('#select2-dropdown-producto').select2('val'));
                });

                //CATEGORIA


                $('#select2-dropdown-categoria').on('change', function(e) {
                  var id = $('#select2-dropdown-categoria').select2('val');
                  var name = $('#select2-dropdown-categoria option:selected').text();
                  @this.set('categoriaSelectedName', name);
                  @this.set('categoriaSeleccionado', ''+id);
                  @this.emit('categoriaSelected', $('#select2-dropdown-categoria').select2('val'));
                });


                //ALMACEN


                $('#select2-dropdown-seccion-almacen').on('change', function(e) {
                var id = $('#select2-dropdown-seccion-almacen').select2('val');
                var name = $('#select2-dropdown-seccion-almacen option:selected').text();
                @this.set('almacenSelectedName', name);
                @this.set('almacenSeleccionado', ''+id);
                @this.emit('almacenSelected', $('#select2-dropdown-seccion-almacen').select2('val'));
                });




        //EVENTOS
        window.livewire.on('show-modal', Msg =>{
            $('#modalDetails').modal('show')
        })
        var total = $('#suma_totales').val();
        $('#ver_totales').html('Ventas: '+total);
    })
    window.livewire.on('modal-show', msg => {
      $('#theModal').modal('show')
    });

    function rePrint(saleId)
    {
        window.open("print://" + saleId,  '_self').close()
    }
</script>

