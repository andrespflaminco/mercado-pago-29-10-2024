<div class="modal fade" id="theModal" tabindex="-1" aria-labelledby="create"  aria-hidden="true">
			<div class="modal-dialog modal-xl modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						 <h5 class="modal-title" >	<b>AYUDA</b></h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-lg-12 col-sm-12 col-12">
							
				            @if($ayuda == 2)
                            
                            <h6>
                                Valores a incorporar en el excel</h6>
                            <div class="col-12 mb-4">
                                
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Codigo</th>
                                                <th>Tipo producto</th>
                                                <th>Cod variacion</th>
                                                <th>Costo </th>
                                                <th>Precio interno </th>
                                                <th>Precio </th>
                                                @foreach($lista_precios as $lp)
                                                <th>{{$lp->id}}_Precio_{{$lp->nombre}} </th>
                                                @endforeach
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><b>Valor alfanumerico</b> - <br> Requerido </td>
                                                <td><b>"simple" o "variable"</b> - <br> Requerido </td>
                                                <td><b>Valor alfanumerico</b> -<br> Requerido </td>
                                                <td><b>Valor numerico</b> -<br> Requerido </td>
                                                <td><b>Valor numerico</b> -<br> Requerido </td>
                                                <td><b>Valor numerico</b> -<br> Requerido </td>
                                                @foreach($lista_precios as $lp)
                                                <td><b>Valor numerico</b> -<br> Opcional </td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                
                            </div>   
                            @endif	
							
				            @if($ayuda == 3)
                            
                            <h6>
                                Valores a incorporar en el excel</h6>
                            <div class="col-12 mb-4">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Codigo</th>
                                                <th>Tipo producto</th>
                                                <th>Cod variacion</th>
                                                <th>Stock </th>
                                                <th>Almacen </th>
                                                @foreach($sucursales as $s)
                                                <th>{{$s->sucursal_id}}_Stock_{{$s->name}} </th>
                                                <th>{{$s->sucursal_id}}_Almacen_{{$s->name}} </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><b>Valor alfanumerico</b> - <br> Requerido </td>
                                                <td><b>"simple" o "variable"</b> - <br> Requerido </td>
                                                <td><b>Valor alfanumerico</b> -<br> Requerido </td>
                                                <td><b>Valor numerico</b> -<br> Opcional </td>
                                                <td><b>Valor alfanumerico</b> -<br> Opcional </td>
                                                @foreach($sucursales as $s)
                                                <td><b>Valor numerico</b> -<br> Opcional </td>
                                                <td><b>Valor alfanumerico</b> -<br> Opcional </td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table> 
                                </div>
                                   
                                
                            </div>   
                            @endif
                            @if($ayuda == 4)
                            
                            <h6>
                                Valores a incorporar en el excel</h6>
                            <div class="col-12 mb-4">
                                
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Codigo</th>
                                                <th>Cod variacion</th>
                                                <th>Costo </th>
                                                <th>Cantidad </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><b>Valor alfanumerico</b> - <br> Requerido </td>
                                                <td><b>Valor alfanumerico</b> -<br> Requerido </td>
                                                <td><b>Valor numerico</b> -<br> Requerido </td>
                                                <td><b>Valor numerico</b> -<br> Requerido </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                
                            </div>   
                            @endif	

							
				            @if($ayuda == 5)
                            
                            <h6>¿Como asignar las columas del excel?</h6>
                            <div class="col-12 mb-4">
                            
                            <ul class="mt-3">
                            <li>- Si esta importando productos simples, la columna obligatoria es Codigo.</li>
                            <li>- Si esta importando tambien productos variables, las columnas obligatorias son Codigo, Cod producto y Variacion.</li>
                            </ul>  
                            <p style="padding: 20px 20px 20px 20px; border: solid 1px #eee;" class="mt-3">
                            En la primera fila encontras los desplegables para seleccionar en donde se importara cada columna del excel. 
                            </p>
                            </div>   
                            @endif								
							
							</div>
						</div>
						<div class="col-lg-12">
						
							<a class="btn btn-cancel" wire:click.prevent="CerrarAyuda()"  data-bs-dismiss="modal">CERRAR</a>
						</div>
					</div>
				</div>
			</div>
		</div>

