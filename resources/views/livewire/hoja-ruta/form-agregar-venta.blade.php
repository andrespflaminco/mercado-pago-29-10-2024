<div wire:ignore.self class="modal fade" id="AgregarVenta" tabindex="-1" role="dialog" style="overflow-y: auto !important;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
        	<b>AGREGAR VENTA A LA HOJA DE RUTA</b>
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body" style="height: 500px;  overflow-y: auto;">
        
							<div class="row">
							    <label>Detalle de ventas asociadas</label>
							     <div class="col-12">
							    <div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
										    <th>
												<label class="checkboxs">
												    <input name="Todos" type="checkbox" value="1" onclick="CheckTodosLote()" class="check_todos"/>    
                            						<span class="checkmarks"></span>
												</label>
											</th>
										 	<th>Nro Venta</th>
											<th>Fecha de venta</th>
											<th>Cliente</th>
											<th>Total</th>
										</tr>
									</thead>
									<tbody>
									    @foreach($ventas_todas as $venta)
									    <tr>
									    <td>
									    <label class="checkboxs">
										    <input type="checkbox" wire:model.defer="id_check" tu-attr-id="{{($venta->id)}}"  class="mis-checkboxes" value="{{$venta->id}}">
								    		<span class="checkmarks"></span>
										</label>
										</td>
									        <td>{{$venta->nro_venta}}</td>
									        <td>{{$venta->created_at}}</td>
									        <td>{{$venta->nombre_cliente}}</td>
									        <td>$ {{ number_format($venta->total,2)  }}</td>
									    </tr>
									    @endforeach
        							</tbody>
        							</table>
							    </div> 
							    </div>  
							    
							</div>         
        
        </div>
     <div class="modal-footer">

       <button type="button" wire:click.prevent="CerrarVentasAsociadas()" class="btn btn-cancel" data-dismiss="modal">CERRAR</button>
       <button type="button" onclick="Agregar()" class="btn btn-submit" >ACEPTAR</button>

     </div>
   </div>
 </div>
</div>

