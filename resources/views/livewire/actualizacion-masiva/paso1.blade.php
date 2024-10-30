<div style="{{$paso1}}">
                                                <div class="mb-4">
                                                    <h5>Filtre los productos que desea actualizar</h5>
                                                </div>
                                                <div class="row">                  
                            				    <!----- CATEGORIAS ------>
                            					<div class="col col-lg-6 col-sm-12">
                            					<div class="form-group">
                            					    <label>Categoria</label>
                            					    <select class="form-control" wire:model.defer="categoria_id">
                            					        <option value="0">Todos</option>
                            					        <option value="1">Sin categoria</option>
                            					        @foreach($categorias as $c)
                            					        <option value="{{$c->id}}">{{$c->name}}</option>
                            					        @endforeach
                            					    </select>
                            					</div>	    
                            					</div>
                            					<div class="col col-lg-6 col-sm-12"></div>
                            				    <!------/CATEGORIAS ------>
                            				    
                            				    <!----- CATEGORIAS ------>
                            					<div class="col col-lg-6 col-sm-12">
                            					<div class="form-group">
                            					    <label>Marcas</label>
                            					    <select class="form-control" wire:model.defer="marca_id">
                            					        <option value="0">Todos</option>
                            					        <option value="1">Sin asignar</option>
                            					        @foreach($marcas as $m)
                            					        <option value="{{$m->id}}">{{$m->name}}</option>
                            					        @endforeach
                            					    </select>
                            					</div>	    
                            					</div>
                            					<div class="col col-lg-6 col-sm-12"></div>
                            				    <!------/CATEGORIAS ------>
                            				    
                            				    <!----- PROVEEDORES ------>
                            					<div class="col col-lg-6 col-sm-12">
                            					<div class="form-group">
                            					    <label>Proveedor</label>
                            					    <select class="form-control" wire:model.defer="proveedor_id">
                            					        <option value="0">Todos</option>
                            					        <option value="1">Sin proveedor</option>
                            					        @foreach($proveedores as $p)
                            					        <option value="{{$p->id}}">{{$p->nombre}}</option>
                            					        @endforeach
                            					    </select>
                            					</div>	    
                            					</div>
                            					<div class="col col-lg-6 col-sm-12"></div>
                            				    <!------/PROVEEDORES ------>			
                            				    
                            				    				
                            				    <!----- ALMACEN ------>
                            					<div class="col col-lg-6 col-sm-12">
                            					<div class="form-group">
                            					    <label>Almacen</label>
                            					    <select class="form-control" wire:model.defer="almacen_id">
                            					        <option value="0">Todos</option>
                            					        <option value="1">Sin almacen</option>
                            					        @foreach($almacenes as $a)
                            					        <option value="{{$a->id}}">{{$a->nombre}}</option>
                            					        @endforeach
                            					    </select>
                            					</div>	    
                            					</div>
                            					<div class="col col-lg-6 col-sm-12"></div>
                            				    <!------/ALMACEN ------>                  
                            			        
                            				    </div>
                                                <ul class="pager wizard twitter-bs-wizard-pager-link">
                                                    <li class="next"><a href="javascript: void(0);" class="btn btn-submit" wire:click="Filtrar()" onclick="nextTab()">SIGUIENTE <i
                                                        class="bx bx-chevron-right ms-1"></i></a></li>
                                                </ul>
                                                
                                            </div>