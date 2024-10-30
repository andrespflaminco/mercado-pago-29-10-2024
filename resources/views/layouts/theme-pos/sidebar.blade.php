			<!-- Sidebar -->
			<div class="sidebar" id="sidebar">
				<div class="sidebar-inner slimscroll">
					<div id="sidebar-menu" class="sidebar-menu">
					    								

                                      
					    @if(Auth::user()->id == 1 )
					    <ul>
					    
					    <li class="submenu-open">
						<h6 class="submenu-hdr">Usuarios y permisos</h6>								
							<ul>
                             <li>
                                <a href="{{ url('users-admin') }}"> <span> Usuarios </span>  </a>
                             </li>
						    <li>
                              <a href="{{ url('roles') }}"> <span> Roles </span></a>
                            </li>
                            <li>
                              <a href="{{ url('permisos') }}"> <span> Permisos </span>  </a>
                             </li>
                             <li>
                                <a href="{{ url('asignar') }}"> <span> Asignar permisos</span>  </a>
                             </li>
                             <li>
                                <a href="{{ url('import-categorias-monotributo') }}"> <span> Categorias Monotributo </span>  </a>
                             </li>
                             <li>
                                <a href="{{ url('planes-suscripcion') }}"> <span> Planes de suscripcion </span>  </a>
                             </li>
                             <li>
                                <a href="{{ url('suscripciones-admin') }}"> <span> Cobro de suscripciones </span>  </a>
                             </li>
				          </ul>
						</li>     
						
						<li class="submenu-open">
						<h6 class="submenu-hdr">CRM</h6>								
							<ul>
                             <li>
                                <a href="{{ url('crm-admin') }}"> <span> CRM Clientes </span>  </a>
                             </li>
						  </ul>
						</li>     
					    </ul>
					    @else
						<ul>
						    @can('ver dashboard')
						    <li class="submenu-open">
								<h6 class="submenu-hdr">Inicio</h6>
								<ul>
									<li>
										<a href="{{url('dashboard')}}"><i data-feather="grid"></i><span>Dashboard</span></a>
									</li>
									<li hidden class="submenu">
										<a href="javascript:void(0);"><i data-feather="smartphone"></i><span>Application</span><span class="menu-arrow"></span></a>
										<ul>
											<li><a href="chat.html">Chat</a></li>
											<li><a href="calendar.html">Calendar</a></li>
											<li><a href="email.html">Email</a></li>
										</ul>
									</li>
								</ul>								
							</li>
							@endcan
							
							@if (Gate::allows('ver resumen venta') || Gate::allows('ver resumen por producto') || Gate::allows('ver agregar venta'))
							<li class="submenu-open">
								<h6 class="submenu-hdr">Ingresos</h6>
								<ul>
								    @can('ver agregar venta')
									<li><a href="{{url('pos')}}"><i data-feather="shopping-cart"></i><span>Agregar venta</span></a></li>
									@endcan

									@if (Gate::allows('ver resumen venta') || Gate::allows('ver resumen por producto'))
							
									<li class="submenu">
										<a href="javascript:void(0);"><i data-feather="file-text"></i><span>Resumen de ventas</span><span class="menu-arrow"></span></a>
										<ul>
										    @can('ver resumen venta')
											<li><a href="{{url('reports')}}">Ventas diarias</a></li>
											@endcan
											
											@can('ver resumen por producto')
											<li><a href="{{url('reportes-detalle')}}">Ventas por producto </a></li>
										    @endcan
										</ul>
									</li>
									@endif
									
									@if(Auth::user()->casa_central_user_id == 586 || Auth::user()->casa_central_user_id == 906  || Auth::user()->casa_central_user_id == 615)
									@can('ver agregar venta')
									<li><a href="{{url('venta-rapida')}}"><i data-feather="briefcase"></i><span>Otros ingresos</span></a></li>
									@endcan		
									@endif
								</ul>
							</li>
							@endif
							
							@if (Gate::allows('ver compras') || Gate::allows('ver gastos'))
							<li class="submenu-open">
								<h6 class="submenu-hdr">Egresos</h6>
								<ul>
								    @can('ver compras')
									<li><a href="{{ url('compras-resumen') }}"><i data-feather="shopping-bag"></i><span>Compras</span></a></li>
									@endcan
									
									@can('ver gastos')
									<li><a href="{{ url('gastos') }}"><i data-feather="file-minus"></i><span>Gastos</span></a></li>		
									@endcan
								</ul>
							</li>
							@endif
							
							@if ( Gate::allows('ver descargas') )
							<li class="submenu-open">
								<h6 class="submenu-hdr">Datos</h6>
								<ul>
									@can('ver importaciones')
									<li><a href="{{ url('import') }}"><i data-feather="upload"></i><span>Importar</span></a></li>
									@endcan
							
							
									@can('ver descargas')
									<li><a href="{{ url('descargas') }}"><i data-feather="download"></i><span>Descargas</span></a></li>
									@endcan
								    
								</ul>
							</li>
							@endif
							

							
							@if (Gate::allows('ver productos') || Gate::allows('ver control stock') || Gate::allows('ver lista precios') || Gate::allows('ver actualizacion masiva')
							|| Gate::allows('ver atributos') || Gate::allows('ver categorias') || Gate::allows('ver almacenes')  || Gate::allows('ver descargas') 
							|| Gate::allows('ver movimientos de stock') 
							)
							<li class="submenu-open">
								<h6 class="submenu-hdr">Productos</h6>
								<ul>
								    
									@can('ver productos')
									<li><a href="{{ url('products') }}"><i data-feather="box"></i><span>Productos</span></a></li>
									@endcan

																	    
									@can('ver promociones')
									<li><a href="{{ url('promos') }}"><i data-feather="gift"></i><span>Promociones</span></a></li>
									@endcan
									
									
									@can('ver etiquetas')
									<li><a href="{{ url('etiquetas') }}"><i data-feather="tag"></i><span>Etiquetas</span></a></li>
									@endcan
									
									
									@can('ver lista precios')
									<li><a href="{{url('lista-precios')}}"><i data-feather="list"></i><span>Lista de precios</span></a></li>
									@endcan
																		
									@can('ver movimientos de stock')
									
									<li class="submenu">
										<a href="javascript:void(0);">
										    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shuffle"><polyline points="16 3 21 3 21 8"></polyline><line x1="4" y1="20" x2="21" y2="3"></line><polyline points="21 16 21 21 16 21"></polyline><line x1="15" y1="15" x2="21" y2="21"></line><line x1="4" y1="4" x2="9" y2="9"></line></svg>
										    <span>Movimientos stock</span><span class="menu-arrow"></span></a>
										<ul>
											<li><a href="{{ url('movimiento-stock') }}">Agregar movimiento</a></li>
											<li><a href="{{url('movimiento-stock-resumen')}}">Resumen movimientos </a></li>
										</ul>
									</li>
									
									@endcan

									
									@can('ver atributos')
									<li><a href="{{ url('atributos') }}"><i data-feather="box"></i><span>Atributos y variaciones</span></a></li>
									@endcan
									
									@can('ver categorias')
									<li><a href="{{url('categories')}}"><i data-feather="codepen"></i><span>Categorias</span></a></li>
									@endcan

																		
									@can('ver categorias')
									<li><a href="{{url('marcas')}}"><i data-feather="bold"></i><span>Marcas</span></a></li>
									@endcan

									
																		
									@can('ver categorias')
									<li><a href="{{url('etiquetas-marcadores')}}"><i data-feather="underline"></i><span>Etiquetas / Tags</span></a></li>
									@endcan

									
									
									@if(Auth::user()->sucursal != 1 )
									
									@can('ver actualizacion masiva')
									<li><a href="{{ url('actualizacion-masiva') }}"><i data-feather="dollar-sign"></i><span>Actualizacion masiva</span></a></li>
									@endcan
									
									@endif
									
									@can('ver control stock')
									<li hidden><a href="{{url('controlador-stock')}}"><i data-feather="list"></i><span>Control fisico stock</span></a></li>
									@endcan
									
									@can('ver almacenes')
									<li><a href="{{ url('almacenes') }}"><i data-feather="speaker"></i><span>Almacenes</span></a></li>	
									@endcan
									
									@can('ver movimientos de stock')
									<li><a href="{{ url('historico-stock') }}"><i data-feather="align-justify"></i><span>Movimientos historicos stock</span></a></li>
									@endcan

									<li><a href="{{ url('image') }}"><i data-feather="image"></i><span>Imagenes</span></a></li>
			
								
								</ul>
							</li>
							@endif
							
							
							@if (Gate::allows('ver bancos') || Gate::allows('ver metodos de cobro'))
							<li class="submenu-open">
								<h6 class="submenu-hdr">Bancos & plataformas</h6>								
								<ul>
								    @can('ver bancos')
									<li><a href="{{url('bancos')}}"><i data-feather="monitor"></i><span>Bancos y plataformas</span></a></li>
									@endcan
									
									@can('ver metodos de cobro')
									<li><a href="{{url('metodo-pago')}}"><i data-feather="grid"></i><span> Metodos de cobro</span></a></li>
				                    @endcan
								</ul>
							</li>
							@endif
							
							@can('ver cajas')
							<li class="submenu-open">
								<h6 class="submenu-hdr">Finanzas</h6>								
								<ul>
									<li><a href="{{url('cajas')}}"><i data-feather="list"></i><span>Cajas diarias</span></a></li>
									 @can('ver pagos')
									<li><a href="{{url('pagos')}}"><i data-feather="dollar-sign"></i><span>Movimientos de dinero</span></a></li>
									@endcan
									
									 @can('ver consolidado')
									<li><a href="{{url('consolidado')}}"><i data-feather="grid"></i><span>Resumen de saldos</span></a></li>
				                    @endcan
				                    
				                	
								
								</ul>
							</li>
							@endcan

							@if ( Gate::allows('ver facturacion') )
							<li class="submenu-open">
								<h6 class="submenu-hdr">Facturacion</h6>
								<ul>
									@can('ver facturacion')
									<li><a href="{{ url('facturacion') }}"><i data-feather="clipboard"></i><span>Facturas Emitidas</span></a></li>
									<li><a href="{{ url('facturacion-compras') }}"><i data-feather="file"></i><span>Facturas Recibidas</span></a></li>
									<li><a href="{{ url('puntos-venta') }}"><i data-feather="smartphone"></i><span>Puntos de venta</span></a></li>
									@endcan
								</ul>
							</li>
							@endif
							
							@if (Gate::allows('ver clientes') || Gate::allows('ver proveedores')
							|| Gate::allows('ver usuarios') || Gate::allows('ver sucursales'))
							<li class="submenu-open">
								<h6 class="submenu-hdr">Personas</h6>		
								<ul>
								    @can('ver clientes')

																
									<li class="submenu">
										<a href="javascript:void(0);"><i data-feather="user"></i><span>Clientes</span><span class="menu-arrow"></span></a>
										<ul>
										    @can('ver clientes')
										    <li><a href="{{url('clientes')}}">Base datos Clientes </a></li>
											@endcan
											
											@can('ver clientes')
											<li><a href="{{url('ctacte-clientes')}}">Cuenta corriente </a></li>
										    @endcan

										</ul>
									</li>
									@endcan
									@can('ver proveedores')
						
							
									<li class="submenu">
										<a href="javascript:void(0);"><i data-feather="users"></i><span>Proveedores</span><span class="menu-arrow"></span></a>
										<ul>
										    @can('ver proveedores')
										    <li><a href="{{url('proveedores')}}">Base datos Proveedores </a></li>
											@endcan
											
											@can('ver proveedores')
											<li><a href="{{url('ctacte-proveedores')}}">Cuenta corriente </a></li>
										    @endcan

										</ul>
									</li>									
									@endcan
									
									@if (Gate::allows('ver usuarios') || Gate::allows('ver roles'))
							
									<li class="submenu">
										<a href="javascript:void(0);"><i data-feather="file-text"></i><span>Usuarios</span><span class="menu-arrow"></span></a>
										<ul>
										    @can('ver usuarios')
										    <li><a href="{{url('users')}}">Usuarios </a></li>
											@endcan
											
											@can('ver roles')
											<li><a href="{{url('roles')}}">Roles </a></li>
										    @endcan
										    
											@can('ver asignar permisos')
											<li><a href="{{url('asignar')}}">Permisos </a></li>
										    @endcan
										</ul>
									</li>
									@endif
									
									@if(Auth::user()->sucursal != 1 )
									
									@can('ver sucursales')
									<li><a  href="{{url('sucursales')}}"><i data-feather="home"></i><span>Mis sucursales</span></a></li>
									@endcan
									
									@endif

								</ul>
							</li>
							@endif
							
							@if (Gate::allows('ver tienda flaminco') || Gate::allows('ver wocommerce'))
							
							
							<li class="submenu-open">
								<h6 class="submenu-hdr">Tiendas online</h6>
								<ul>
								    @can('ver tienda flaminco')
									<li><a href="{{url('ecommerce-config')}}"><i data-feather="bar-chart-2"></i><span>Tienda flaminco</span></a></li>
									@endcan 
									
									@can('ver wocommerce')
									@if(Auth::user()->sucursal != 1 )
									<li><a href="{{url('woocommerce')}}"><i style="margin-right:10px;" class="fab fa-wordpress-simple"></i><span>Woocommerce</span></a></li>
									@endif
									@endcan
									
								</ul>
							</li>
							
							@endif
							
							@if (Gate::allows('ver tienda flaminco') || Gate::allows('ver wocommerce'))
							
							
							<li class="submenu-open">
								<h6 class="submenu-hdr">Distribucion</h6>
								<ul>
								    @can('ver tienda flaminco')
									<li><a href="{{url('hoja-ruta')}}"><i data-feather="truck"></i><span>Hojas de ruta</span></a></li>
									@endcan 

								</ul>
							</li>
							
							@endif
							
						@if(Auth::user()->id == 970  || Auth::user()->id == 1035 || Auth::user()->id == 1043 || Auth::user()->id == 317  || Auth::user()->id == 362 || Auth::user()->id == 615 || Auth::user()->id == 1019 ||  Auth::user()->casa_central_user_id == 738  || (Auth::user()->casa_central_user_id == 499 && Auth::user()->profile == "Comercio") || Auth::user()->casa_central_user_id == 364)
					    

						
						<li class="submenu-open">
						<h6 class="submenu-hdr">Produccion</h6>								
							<ul>
                              <li>
                                <a href="{{ url('insumos') }}"> <span> Insumos </span>  </a>
                             </li>
						    <li>
                              <a href="{{ url('recetas') }}"> <span> Recetas </span></a>
                            </li>
                            <li>
                                <a href="{{ url('produccion') }}"> <span> Produccion </span>  </a>
                             </li>
				          </ul>
						</li>     
				
					    @endif
							@can('ver configuracion')
							<li class="submenu-open">
								<h6 class="submenu-hdr">Configuracion</h6>		
								<ul>
								   
								    <li><a  href="{{url('mi-comercio')}}" ><i data-feather="settings"></i><span>Configuracion</span></a></li>
							        @if(Auth::user()->sucursal != 1 )
								    <li><a  href="{{url('suscripcion-configuracion')}}" ><i data-feather="credit-card"></i><span>Mi suscripcion</span></a></li>
							        @endif	   
									<li hidden><a  href="{{url('ayuda')}}" ><i data-feather="settings"></i><span>Ayuda</span></a></li>
								
								</ul>
							</li>
							 @endcan
						</ul>
						@endif
					</div>
				</div>
			</div>
			<!-- /Sidebar -->
