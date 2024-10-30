<div>
	                <div class="page-header">
					<div class="page-title">
							<h4>Produccion</h4>
							<h6>Ver listado de productos producidos y en produccion</h6>
						</div>
						<div class="page-btn">               											    
                			
                			@if(Auth::user()->sucursal != 1)
                			@if(Auth::user()->profile != "Cajero" )
							<a href="{{ url('produccion-nueva') }}" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar produccion</a>
						    @endif
						    @endif
						    
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
				
						<div class="card-body">

							 <div class="row">
                              <div class="col-sm-12 col-md-4">
                               <div class="form-group">
                                <label>Estado de producción</label>
                                <div wire:ignore>
        
                                    <select class="form-control tagging" multiple="multiple" id="select2-dropdown-estados">
                                      @foreach($estados as $e)
                                      <option value="{{$e->id}}">{{$e->nombre}}</option>
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
                                      <option value="{{$p->id}}">{{$p->name}}</option>
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

                              <div hidden class="card component-card_1">
                              <div class="card-body">
                                <div class="row">
                                  <div class="col-sm-12 col-md-2">
                                   <div style="text-align:center !important; vertical-align: middle !important;" class="form-group">
        
                                     <h5>Pendientes: {{number_format($this->pendiente,0)}}</h5>
        
        
                              </div>
                            </div>
                            <div class="col-sm-12 col-md-2">
                             <div style="text-align:center !important; vertical-align: middle !important;" class="form-group">
        
                               <h5>En proceso: {{number_format($this->fabricacion,0)}}</h5>
        
        
                        </div>
                      </div>
                      <div class="col-sm-12 col-md-2">
                       <div style="text-align:center !important; vertical-align: middle !important;" class="form-group">
        
                         <h5>Terminados: {{number_format($this->terminado,0)}}</h5>
        
        
                  </div>
                </div>
                <div class="col-sm-12 col-md-2">
                 <div style="text-align:center !important; vertical-align: middle !important;" class="form-group">
        
                   <h5>Cancelado s/desp: {{number_format($this->cancelado_s,0)}}</h5>
        
        
            </div>
          </div>
                <div class="col-sm-12 col-md-2">
                 <div style="text-align:center !important; vertical-align: middle !important;" class="form-group">
        
                   <h5>Cancelado c/desp: {{number_format($this->cancelado_c,0)}}</h5>
        
        
            </div>
          </div>
        
                      <div class="col-sm-12 col-md-2">
                       <div style="text-align:center !important; vertical-align: middle !important;" class="form-group">
        
                         <h5>Total: {{$this->suma_cantidades}}</h5>
        
        
                       </div>
                     </div>
                          </div>
                            </div>
                            </div>                           
 							 
						     <!--TABLA-->
                        <div class="table-responsive">
        									<table  class="table table-bordered table-striped  mt-1 ">
        										<thead>
        											<tr>
                                                    <th>ID PRODUCCION</th>
                                                    <th>FECHA</th>
                                                    <th>PRODUCTO</th>
                                                    <th>CATEGORIA</th>
                            						<th>COSTO</th>
                            						<th>CANT.</th>
                            						<th>COSTO TOTAL</th>
                                                    <th>ESTADO</th>
                                                    <th>SALE DETAIL ID</th>
                                                    <th></th>
        											</tr>
        										</thead>
        										<tbody>
        											@if(count($data) < 1)
        											<tr> <td class="text-center" colspan="10"><h5>Sin resultados</h5></td></tr>
        											@endif
        											@foreach($data as $d)
        											<tr>
                                	                    <td>{{$d->produccion_id}}</td>
                                                        <td>{{\Carbon\Carbon::parse($d->inicio_produccion)->format('d-m-Y')}}</td>
        												<td>{{$d->barcode}}</td>
                                                        <td>{{$d->nombre_producto}}</td>
        												<td>${{number_format($d->costo,2)}}</td>
        												<td>
        												<input class="form-control" style="width:100px;" type="number" value="{{number_format($d->cantidad)}}" id="qty{{$d->id}}" 
        												wire:keyup.enter="updateQtyProduccion({{$d->id}}, $('#qty' + {{$d->id}}).val() ,1)" 
                                                        wire:change="updateQtyProduccion({{$d->id}}, $('#qty' + {{$d->id}}).val() ,1)" 
        												>    
        												</td>
        												<td>${{number_format(($d->costo*$d->cantidad),2)}}</td>
                                                        <td>
                                                              @if($d->id_estado == '1')
                                                                <button style="min-width:130px;" wire:click.prevent="getDetails({{$d->id}})"
                                                                    class="btn btn-warning mb-2">
                                                                    {{$d->nombre_estado}}
                                                                </button>
                                                                @elseif($d->id_estado == '2')
                                                                <button style="min-width:130px;" wire:click.prevent="getDetails({{$d->id}})"
                                                                    class="btn btn-secondary  mb-2">
                                                                    {{$d->nombre_estado}}
                                                                </button>
                                                                @elseif($d->id_estado == '3')
                                                                <button style="min-width:130px;" wire:click.prevent="getDetails({{$d->id}})"
                                                                    class="btn btn-success mb-2">
                                                                    {{$d->nombre_estado}}
                                                                </button>
                                                                @elseif($d->id_estado == '4')
                                                                <button style="min-width:130px;" wire:click.prevent="getDetails({{$d->id}})"
                                                                    class="btn btn-dark  mb-2">
                                                                    {{$d->nombre_estado}}
                                                                </button>
                                                                @elseif($d->id_estado == '5')
                                                                <button style="min-width:130px;" wire:click.prevent="getDetails({{$d->id}})"
                                                                    class="btn btn-danger  mb-2">
                                                                    {{$d->nombre_estado}}
                                                                </button>

                                                                @elseif($d->id_estado == '6')
                                                                <button style="min-width:130px;" wire:click.prevent="getDetails({{$d->id}})"
                                                                    class="btn btn-danger  mb-2">
                                                                    {{$d->nombre_estado}}
                                                                </button>
                                                                @endif

                                                                </td>
                                                        <td>{{$d->sale_details_id}}</td>
                                                        <td><a href="{{ url('mostrar_receta_produccion/'.$d->id)}}" target="_blank" class="btn btn-light"> VER </a></td>
        										        </tr>
        										@endforeach
        									</tbody>
        								</table>
        				</div>
						</div>
					</div>
					

    @include('livewire.produccion-detalle.sales-detail')

</div>
<script>
    document.addEventListener('DOMContentLoaded', function(){


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


                //ESTADOS


                $('#select2-dropdown-estados').on('change', function(e) {
                var id = $('#select2-dropdown-estados').select2('val');
                var name = $('#select2-dropdown-estados option:selected').text();
                @this.set('estadoSelectedName', name);
                @this.set('estadoSeleccionado', ''+id);
                @this.emit('estadoSelected', $('#select2-dropdown-estados').select2('val'));
                });




        //EVENTOS
        window.livewire.on('hide-modal', Msg =>{
            $('#modalDetails').modal('hide')
        })
        window.livewire.on('show-modal', Msg =>{
            $('#modalDetails').modal('show')
        })
        var total = $('#suma_totales').val();
        $('#ver_totales').html('Ventas: '+total);
    })
    window.livewire.on('modal-show', msg => {
      $('#theModal').modal('show')
    });
    window.livewire.on('noty-msg', msg => {
      Noty(msg)
    });


</script>
