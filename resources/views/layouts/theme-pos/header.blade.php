			<!-- Header -->
			<div class="header">
			
				<!-- Logo -->
				 <div class="header-left active">
					<a href="{{url('pos')}}" class="logo logo-normal">
						<img style="width:120px !important;" src="assets/pos/img/logo.png"   alt="">
					</a>
					<a href="{{url('pos')}}" class="logo logo-white">
						<img src="assets/pos/img/logo.png"   alt="">
					</a>
					<a href="{{url('pos')}}" class="logo-small">
						<img src="assets/pos/img/logo.png"  alt="">
					</a>
					<a id="toggle_btn" href="javascript:void(0);">
						<i data-feather="chevrons-left" class="feather-16"></i>
					</a>
				</div>
				<!-- /Logo -->
				
				<a id="mobile_btn" class="mobile_btn" href="#sidebar">
					<span class="bar-icon">
						<span></span>
						<span></span>
						<span></span>
					</span>
				</a>
				
				<!-- Header Menu -->
				<ul class="nav user-menu">
				
					<!-- Search -->
					<li class="nav-item nav-searchinputs">
						<div hidden class="top-nav-search">
							
							<a href="javascript:void(0);" class="responsive-search">
								<i class="fa fa-search"></i>
							</a>
							<form action="#">
								<div class="searchinputs">
									<input type="text" placeholder="Search">
									<div class="search-addon">
										<span><i data-feather="search" class="feather-14"></i></span>
									</div>
								</div>
								<!-- <a class="btn"  id="searchdiv"><img src="assets/img/icons/search.svg" alt="img"></a> -->
							</form>
						</div>
					</li>
					<!-- /Search -->
				
					<!-- Flag -->
					<li hidden class="nav-item dropdown has-arrow flag-nav nav-item-box">
						<a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);" role="button">
							<i data-feather="globe"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-right">
							<a href="javascript:void(0);" class="dropdown-item active">
								<img src="assets/img/flags/us.png" alt="" height="16"> English
							</a>
							<a href="javascript:void(0);" class="dropdown-item">
								<img src="assets/img/flags/fr.png" alt="" height="16"> French
							</a>
							<a href="javascript:void(0);" class="dropdown-item">
								<img src="assets/img/flags/es.png" alt="" height="16"> Spanish
							</a>
							<a href="javascript:void(0);" class="dropdown-item">
								<img src="assets/img/flags/de.png" alt="" height="16"> German
							</a>
						</div>
					</li>
					<!-- /Flag -->

					<li class="nav-item nav-item-box">
						<a href="javascript:void(0);" id="btnFullscreen">
							<i data-feather="maximize"></i>
						</a>
					</li>
					<li hidden class="nav-item nav-item-box">
						<a href="email.html">
							<i data-feather="mail"></i>
							<span class="badge rounded-pill">1</span>
						</a>
					</li>
					<!-- Notifications -->
					<li class="nav-item dropdown nav-item-box">
					    
					    @if(count(auth()->user()->unReadNotifications))
						<a href="javascript:void(0);" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
							<i data-feather="bell"></i><span class="badge rounded-pill">{{ count(auth()->user()->unReadNotifications) }}</span>
						</a>
						@endif
						
						<div class="dropdown-menu notifications">
							<div class="topnav-dropdown-header">
								<span class="notification-title">Notificaciones</span>
								<a href="javascript:void(0)" class="clear-noti"> Borrar todas </a>
							</div>
							<div class="noti-content">
								<ul class="notification-list">
								    @foreach(auth()->user()->unreadNotifications()->take(3)->get() as $notifications)
									<li class="notification-message">
										<a>
											<div class="media d-flex">
												<span class="avatar flex-shrink-0">
													<img alt="" src="assets/pos/img/profiles/avatar-02.jpg">
												</span>
												<div class="media-body flex-grow-1">
													<p class="noti-details"><span class="noti-title"> {{ $notifications->data['titulo'] }}:</span> <span class="noti-title">{{ $notifications->data['contenido'] }}</span></p>
													<p class="noti-time"><span class="notification-time">{{ $notifications->created_at->diffForHumans() }}</span></p>
												</div>
											</div>
										</a>
									</li>
									@endforeach
								</ul>
							</div>
							<div class="topnav-dropdown-footer">
								<a hidden>Ver todas las notificaciones</a>
							</div>
						</div>
					</li>
					<!-- /Notifications -->
					
					<li class="nav-item nav-item-box">
						<a title="Ayuda" href="https://academia.flaminco.com.ar">
						    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
						</a>
					</li>
					
					<li class="nav-item nav-item-box">
						<a title="Configuracion" href="{{ url('mi-comercio')}}"><i data-feather="settings"></i></a>
					</li>
					<li class="nav-item dropdown has-arrow main-drop">
						<a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
							<span class="user-info">
								<span class="user-letter">
									<img src="assets/img/profiles/avator1.jpg" alt="" class="img-fluid">
								</span>
								<span class="user-detail">
									<span class="user-name">{{Auth::user()->id}} - {{Auth::user()->name}}</span>
									<span class="user-role">{{Auth::user()->profile}} - @if(Auth::user()->sucursal == 1) Sucursal @else Casa central @endif</span>
								</span>
							</span>
						</a>
						<div class="dropdown-menu menu-drop-user">
							<div class="profilename">
								<div class="profileset">
									<span class="user-img"><img src="assets/img/profiles/avator1.jpg" alt="">
									<span class="status online"></span></span>
									<div class="profilesets">
										<h6>{{Auth::user()->id}} - {{Auth::user()->name}}</h6>
										<h5>{{Auth::user()->profile}} - @if(Auth::user()->sucursal == 1) Sucursal @else Casa central @endif</h5>
									</div>
								</div>
								<hr class="m-0">
								<a class="dropdown-item" href="https://academia.flaminco.com.ar"> <i class="me-2"  data-feather="user"></i> Ayuda </a>
								<a class="dropdown-item" href="{{ url('mi-comercio')}}"><i class="me-2" data-feather="settings"></i>Configuracion</a>
								<hr class="m-0">
								<a class="dropdown-item logout pb-0" href="{{ route('logout') }}" onclick="localclear()" ><img src="assets/pos/img/icons/log-out.svg" class="me-2" alt="img">Cerrar sesion</a>
						        <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                @csrf
                                 </form>
							</div>
						</div>
					</li>
				</ul>
				<!-- /Header Menu -->
				
				<!-- Mobile Menu -->
				<div class="dropdown mobile-user-menu">
					<a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item" href="https://academia.flaminco.com.ar">Ayuda</a>
						<a class="dropdown-item"  href="{{ url('mi-comercio')}}">Configuracion</a>
							<a class="dropdown-item" href="{{ route('logout') }}" onclick="localclear()">Cerrar sesion</a>
						       <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                @csrf
                            </form>
					</div>
				</div>
				<!-- /Mobile Menu -->
			</div>
			<!-- Header -->
			
			
<script type="text/javascript">
  function localclear() {
    event.preventDefault();
    document.getElementById('logout-form').submit();

    localStorage.clear();
  }
</script>
