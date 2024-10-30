<head>
  <style media="screen">
    .navbar .navbar-item .nav-item.theme-logo a img {
      width: 150PX !important;
      height: 42px !important;
      border-radius: 5px !important;
    }

    .time-left {
      margin-top: 0px;
      margin-bottom: -10px !important;
      vertical-align: middle;
    padding: 5px 5px 5px 28%;
      font-size: 16px;
      margin: 0 auto;
      position: relative;
      background-color: #f71926;
      width: 100%;
      height: 35px;
      color: #f1f1f1;
    }
  </style>

</head>
<div class="header-container fixed-top">
        <header class="header navbar navbar-expand-sm justify-content-between">

          <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3" y2="6"></line><line x1="3" y1="12" x2="3" y2="12"></line><line x1="3" y1="18" x2="3" y2="18"></line></svg>
          </a>
            
            @if((new \Jenssegers\Agent\Agent())->isDesktop() || (new \Jenssegers\Agent\Agent())->isTablet())
            <ul style="margin-left:15%;"  class="navbar-item flex-row">



                <li  class="nav-item theme-logo">
                    <a href="{{url('pos')}}">
                        <img src="assets/img/LOGO_03.png" class="navbar-logo" alt="logo">
                    </a>
                </li>


            </ul>
            @endif
            
            @if((new \Jenssegers\Agent\Agent())->isMobile())
             <a href="{{url('pos')}}">
            <img src="assets/img/LOGO_03.png" style="width:100px !important;" alt="logo">
            </a>
            @endif
           
       


            <ul class="navbar-item flex-row navbar-dropdown">
                
                @if((new \Jenssegers\Agent\Agent())->isDesktop())
                <p style="margin-top:6%;">{{Auth::user()->id}} - {{Auth::user()->name}}</p>
                @endif            


                <li class="nav-item dropdown user-profile-dropdown  order-lg-0 order-1" >
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="far fa-user text-dark"></i>
                    </a>
                    <div class="dropdown-menu position-absolute animated fadeInUp" aria-labelledby="userProfileDropdown">
                        <div class="user-profile-section">
                            <div class="media mx-auto">

                                <div class="media-body">
                                    <h5>Bienvenido,</h5>
                                    <h5> {{Auth::user()->name}}</h5>

                                </div>
                            </div>
                        </div>


                        <div class="dropdown-item">
                            <a href="{{ route('logout') }}" onclick="localclear()"  >
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg> <span>Salir</span>
                            </a>
                            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                @csrf
                            </form>
                        </div>
                    </div>
                </li>
                
                @if((new \Jenssegers\Agent\Agent())->isDesktop())
                <li class="nav-item dropdown notification-dropdown" style="margin-left:0px !important; border-right: solid 1px #eee; border-left:  solid 1px #eee; color:#888EA8 !important; padding-right: 20px !important; padding-left: 20px !important;">
                                  <a href="javascript:void(0);" class="nav-link dropdown-toggle" id="notificationDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      <i class="far fa-bell text-dark"></i>
                                      @if(count(auth()->user()->unReadNotifications))
                                      <div class="badge badge-danger" style="border-radius: 50% !important; font-size: 12px !important; padding: 1px 6px !important;" >  {{ count(auth()->user()->unReadNotifications) }} </div>
                                      @endif
                                  </a>
                                  <div class="dropdown-menu position-absolute animated fadeInUp" aria-labelledby="notificationDropdown">
                                      <div class="notification-scroll">
                                          @foreach(auth()->user()->unreadNotifications()->take(3)->get() as $notifications)

                                          <div class="dropdown-item">
                                              <div class="media server-log">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tag"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                                                  <a href="{{ url('read-notificacion' . '/' . $notifications->id) }}" >
             
                                                  <div class="media-body" style="padding-left:15px;">
                                                      <div class="data-info">
                                                          <h6 class=""> {{ $notifications->data['titulo'] }}</h6>
                                                          <p class=""> {{ $notifications->data['contenido'] }}</p>
                                                          <p class=""> {{ $notifications->created_at->diffForHumans() }}</p>
                                                      </div>


                                                  </div>
                                                  </a>
                                              </div>
                                          </div>

                                          @endforeach

                                          <div hidden class="dropdown-item">
                                            <a href="#"> Ver todas las notificaciones </a>

                                          </div>

                                      </div>
                                  </div>
                              </li>
                <!----
                <li class="nav-item dropdown user-profile-dropdown  order-lg-0 order-1" style="padding-left:0px !important;" >
                    <a href="{{url('ayuda')}}" class="nav-link dropdown-toggle user">
                        <i class="far fa-question-circle text-dark"></i>
                    </a>
                </li>
                ---->
                @endif
            </ul>
        </header>

    </div>
<script type="text/javascript">
  function localclear() {
    event.preventDefault();
    document.getElementById('logout-form').submit();

    localStorage.clear();
  }
</script>
