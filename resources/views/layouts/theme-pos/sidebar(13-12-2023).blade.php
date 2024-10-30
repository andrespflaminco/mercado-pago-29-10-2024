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
				          </ul>
						</li>         
					    </ul>
					    @else
						<ul>
						    @if(Auth::user()->profile != "Cajero" )
							<li class="submenu-open">
								<h6 class="submenu-hdr">Inicio</h6>
								<ul>
									<li>
										<a  href="{{url('dashboard')}}" ><i data-feather="grid"></i><span>Dashboard</span></a>
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
							@endif
							<li class="submenu-open">
								<h6 class="submenu-hdr">Ingresos</h6>
								<ul>
									<li><a href="{{url('pos')}}"><i data-feather="shopping-cart"></i><span>Agregar venta</span></a></li>
									
									@if(Auth::user()->profile != "Cajero" )
									<li class="submenu">
										<a href="javascript:void(0);"><i data-feather="file-text"></i><span>Resumen de ventas</span><span class="menu-arrow"></span></a>
										<ul>
											<li><a href="{{url('reports')}}">Ventas diarias</a></li>
											<li><a href="{{url('reportes-detalle')}}">Ventas por producto </a></li>
										</ul>
									</li>
									@endif
							
								</ul>
							</li>
							
							@if(Auth::user()->profile != "Cajero" )
							<li class="submenu-open">
								<h6 class="submenu-hdr">Egresos</h6>
								<ul>
									<li><a href="{{ url('compras-resumen') }}"><i data-feather="shopping-bag"></i><span>Compras</span></a></li>
									<li><a href="{{ url('gastos') }}"><i data-feather="file-minus"></i><span>Gastos</span></a></li>									
								</ul>
							</li>
							@endif
							
							<li class="submenu-open">
								<h6 class="submenu-hdr">Productos</h6>
								<ul>
									<li><a href="{{ url('products') }}"><i data-feather="box"></i><span>Productos</span></a></li>
									<li><a href="{{url('lista-precios')}}"><i data-feather="list"></i><span>Lista de precios</span></a></li>
									
									<li><a href="{{ url('descargas') }}"><i data-feather="download"></i><span>Descargas</span></a></li>
									
									@if(Auth::user()->profile != "Cajero" )
									@if(Auth::user()->sucursal != 1 )
									<li><a href="{{ url('actualizacion-masiva') }}"><i data-feather="box"></i><span>Actualizacion masiva</span></a></li>
									@endif
									<li><a href="{{ url('atributos') }}"><i data-feather="box"></i><span>Atributos y variaciones</span></a></li>
									@endif
									<li><a href="{{url('categories')}}"><i data-feather="codepen"></i><span>Categorias</span></a></li>
									<li><a href="{{ url('almacenes') }}"><i data-feather="speaker"></i><span>Almacenes</span></a></li>	
									@if(Auth::user()->profile != "Cajero" )
									<li><a href="{{ url('historico-stock') }}"><i data-feather="align-justify"></i><span>Movimientos de stock</span></a></li>
									@endif
								
								</ul>
							</li>
							@if(Auth::user()->profile != "Cajero" )
							<li class="submenu-open">
								<h6 class="submenu-hdr">Bancos & plataformas</h6>								
								<ul>
									<li><a href="{{url('bancos')}}"><i data-feather="monitor"></i><span>Bancos y plataformas</span></a></li>
									<li><a href="{{url('metodo-pago')}}"><i data-feather="grid"></i><span> Metodos de cobro</span></a></li>
				
								</ul>
							</li>
							@endif
							<li class="submenu-open">
								<h6 class="submenu-hdr">Finanzas</h6>								
								<ul>
									<li><a href="{{url('cajas')}}"><i data-feather="list"></i><span>Cajas diarias</span></a></li>
				
								</ul>
							</li>
							
							@if(Auth::user()->profile != "Cajero" )
							<li class="submenu-open">
								<h6 class="submenu-hdr">Personas</h6>		
								<ul>
									<li><a  href="{{url('clientes')}}"><i data-feather="user"></i><span>Clientes</span></a></li>
									<li><a  href="{{url('proveedores')}}"><i data-feather="users"></i><span>Proveedores</span></a></li>
									<li><a  href="{{url('users')}}"><i data-feather="user-check"></i><span>Usuarios</span></a></li>
									
									@if(Auth::user()->sucursal != 1 )
									<li><a  href="{{url('sucursales')}}"><i data-feather="home"></i><span>Mis sucursales</span></a></li>
									@endif

								</ul>
							</li>
							@endif
							
							<li hidden class="submenu-open">
								<h6 class="submenu-hdr">Reports</h6>
								<ul>
									<li><a href="salesreport.html"><i data-feather="bar-chart-2"></i><span>Sales Report</span></a></li>
									<li><a href="purchaseorderreport.html"><i data-feather="pie-chart"></i><span>Purchase report</span></a></li>
									<li><a href="inventoryreport.html"><i data-feather="credit-card"></i><span>Inventory Report</span></a></li>									
									<li><a href="invoicereport.html"><i data-feather="file"></i><span>Invoice Report</span></a></li>
									<li><a href="purchasereport.html"><i data-feather="bar-chart"></i><span>Purchase Report</span></a></li>
									<li><a href="supplierreport.html"><i data-feather="database"></i><span>Supplier Report</span></a></li>
									<li><a href="customerreport.html"><i data-feather="pie-chart"></i><span>Customer Report</span></a></li>
								</ul>
							</li>
							@if(Auth::user()->profile != "Cajero" )
							<li class="submenu-open">
								<h6 class="submenu-hdr">Tiendas online</h6>
								<ul>
									<li><a href="{{url('ecommerce-config')}}"><i data-feather="bar-chart-2"></i><span>Tienda flaminco</span></a></li>
									@if(Auth::user()->sucursal != 1 )
									<li><a href="{{url('woocommerce')}}"><i style="margin-right:10px;" class="fab fa-wordpress-simple"></i><span>Woocommerce</span></a></li>
									@endif
								</ul>
							</li>
							@endif
							 @if(Auth::user()->profile != "Cajero" )
							<li class="submenu-open">
								<h6 class="submenu-hdr">Configuracion</h6>		
								<ul>
								   
								    <li><a  href="{{url('mi-comercio')}}" ><i data-feather="settings"></i><span>Configuracion</span></a></li>
								   
									<li hidden><a  href="{{url('ayuda')}}" ><i data-feather="settings"></i><span>Ayuda</span></a></li>
								
								</ul>
							</li>
							 @endif
						</ul>
						@endif
					</div>
				</div>
			</div>
			<!-- /Sidebar -->
