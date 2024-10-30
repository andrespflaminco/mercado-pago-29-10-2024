<div class="row sales layout-top-spacing">

    <div class="col-sm-12">
       <ul class="nav nav-tabs  mb-0">
        <li style="background:white; border: solid 1px #eee;" class="nav-item">
            <a style="{{ $sucursal_id == $comercio_id ? 'color: #e95f2b;' : '' }}" class="nav-link  {{ $sucursal_id == $comercio_id ? 'active' : '' }} " href="javascript:void(0)"  wire:click="ElegirSucursal({{$comercio_id}})"  > {{auth()->user()->name}} </a>
        </li>
        @foreach($sucursales as $item)
        <li style="background:white; border: solid 1px #eee;"  class="nav-item">
            <a style="{{ $sucursal_id == $item->sucursal_id ? 'color: #e95f2b;' : '' }}" class="nav-link {{ $sucursal_id == $item->sucursal_id ? 'active' : '' }}" href="javascript:void(0)"  wire:click="ElegirSucursal({{$item->sucursal_id}})"  >{{$item->name}}</a>
        </li>
        @endforeach
      </ul>
        <div class="widget">
            <div class="widget-heading">
                <h4 class="card-title text-center"><b>{{$componentName}}</b></h4>

              <button type="button" class="btn btn-dark" onClick="muestra_oculta('contenido')"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>  FILTROS</button>

                <button  class="btn btn-dark  {{count($data) <1 ? 'disabled' : '' }}"
              wire:click="ExportarReporte (' {{ ( ($usuarioSeleccionado == '' ? '0' : $usuarioSeleccionado) . '/' . ($ClienteSeleccionado == '' ? '0' : $ClienteSeleccionado) . '/' . ($metodopagoSeleccionado == '' ? '0' : $metodopagoSeleccionado) . '/' . ($productoSeleccionado == '' ? '0' : $productoSeleccionado) . '/' . ($categoriaSeleccionado == '' ? '0' : $categoriaSeleccionado) . '/' . ($almacenSeleccionado == '' ? '0' : $almacenSeleccionado) . '/' . $dateFrom . '/' . $dateTo . '/' . $sucursal_id) }} ') " >Exportar a Excel</button>
            
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
                              <option value="{{$mp->id}}">{{$mp->nombre}}</option>
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

                   </div>

                   </div>






                      </div>
                      <div class="card component-card_1">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-sm-12 col-md-3">
                           <div style="text-align:center !important; vertical-align: middle !important;" class="form-group">

                         <h5>Ventas Prod: $ {{number_format($this->suma_totales,2)}}</h5>

                      </div>
                    </div>
                    
                    <div class="col-sm-12 col-md-2">
                           <div style="text-align:center !important; vertical-align: middle !important;" class="form-group">

                             
                             <h5> - Descuento: $ {{number_format( $this->suma_descuento,2)}}</h5>



                      </div>
                    </div>     
                    
                     <div class="col-sm-12 col-md-2">
                           <div style="text-align:center !important; vertical-align: middle !important;" class="form-group">

                             <h5> + Recargos: $ {{number_format($this->suma_recargo ,2)}}</h5>


                      </div>
                    </div>
                    
                     <div class="col-sm-12 col-md-2">
                           <div style="text-align:center !important; vertical-align: middle !important;" class="form-group">

                             <h5> + Imp: $ {{number_format($this->suma_iva ,2)}}</h5>


                      </div>
                    </div>
                 
                                    
                     <div class="col-sm-12 col-md-3">
                           <div style="text-align:center !important; vertical-align: middle !important;" class="form-group">

                             
                             <h5>Ventas Total: $ {{number_format($this->suma_totales + $this->suma_iva + $this->suma_recargo - $this->suma_descuento,2)}}</h5>



                      </div>
                    </div>

                  </div>
                    </div>
                    </div>

            <div class="widget-content">
                <div class="row">


                    <div class="col-12 col-md-12">
                        <!--TABLAE-->
                        <div class="table-responsive">
        									<table  class="table table-bordered table-striped  mt-1 ">
        										<thead class="text-white" style="background: #3B3F5C">
        											<tr>
                                <th class="table-th text-center text-white">FECHA</th>
                                <th class="table-th text-center text-white">ID VENTA</th>
                                    <th class="table-th text-center text-white">COD PROD</th>

                                <th class="table-th text-center text-white">PRODUCTO</th>
                                <th class="table-th text-center text-white">CATEGORIA</th>

                                <th class="table-th text-center text-white">CLIENTE</th>
                                <th class="table-th text-center text-white">VENDEDOR</th>
        						<th class="table-th text-center text-white">PRECIO</th>
        						<th class="table-th text-center text-white">CANT.</th>
        						<th class="table-th text-center text-white">IVA.</th>
        						<th class="table-th text-center text-white">RECARGO</th>
        						<th class="table-th text-center text-white">DESCUENTO</th>
        						<th class="table-th text-center text-white">COSTO TOTAL</th>
        						<th class="table-th text-center text-white">IMPORTE TOTAL</th>
        						<th class="table-th text-center text-white">ALMACEN</th>
        						<th class="table-th text-center text-white">FORMA DE PAGO</th>

        											</tr>
        										</thead>
        										<tbody style="overflow-y: visible !important;">
        											@if(count($data)<1)
        											<tr><td class="text-center" colspan="10"><h5>Sin resultados</h5></td></tr>
        											@endif
        											@foreach($data as $d)
        											<tr>
                                <td class="text-center"><h6>
        													{{\Carbon\Carbon::parse($d->created_at)->format('d-m-Y H:i')}}
        												</h6></td>
                                                        <td class="text-center"><h6>{{$d->sale_id}}</h6></td>
                                                         <td class="text-center"><h6>{{$d->barcode}}</h6></td>
        												<td class="text-center"><h6>{{$d->product}}</h6></td>
                                                        <td class="text-center"><h6>{{$d->nombre_categoria}}</h6></td>
                                                        <td class="text-center"><h6>{{$d->nombre_cliente}}</h6></td>
                                                        <td class="text-center"><h6>{{$d->nombre_usuario}}</h6></td>
        												<td class="text-center"><h6>${{number_format($d->price,2)}}</h6></td>
        												<td class="text-center"><h6>{{number_format($d->quantity)}}</h6></td>
        												<td class="text-center"><h6>${{number_format((($d->price*$d->quantity) - $d->descuento + $d->recargo)  * $d->iva,2)}}</h6></td>
        												<td class="text-center"><h6> $ {{number_format($d->recargo)}}</h6></td>
        												<td class="text-center"><h6> $ {{number_format($d->descuento)}}</h6></td>
        												<td class="text-center"><h6> $ {{number_format($d->cost*$d->quantity)}}</h6></td>
        												<td class="text-center"><h6>${{number_format( ((($d->price*$d->quantity) - $d->descuento + $d->recargo)  * (1+$d->iva)) ,2)}}</h6></td>
        												<td class="text-center"><h6>{{$d->almacen}}</h6></td>
        												<td class="text-center"><h6>{{$d->nombre_metodo_pago}}</h6></td>


        										</tr>

        										@endforeach
        									</tbody>
        								</table>




        							</div>
    
                      {{$data->links()}}
                    </div>
                </div>
            </div>
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
