                                <thead>
										<tr>
											<th>
											    
											    @if(Auth::user()->profile != "Cajero" )
												
												@can('accion en lote productos')    
												<label class="checkboxs">
											    <input name="Todos" type="checkbox" value="1" onclick="CheckTodosLote()" class="check_todos"/>    
                            					<span class="checkmarks"></span>
											    </label>
												@endif
												@endcan
												
											</th>
											<th>Nombre del producto</th>
											<th>SKU</th>
											<th>Categoria </th>
											<th>Marca</th>
											@if(Auth::user()->profile != "Cajero" )
											@can('ver proveedores en catalogo')   
											<th>Proveedor</th>
											@endcan
											@endif
											<th>Acciones</th>
										</tr>
									</thead>