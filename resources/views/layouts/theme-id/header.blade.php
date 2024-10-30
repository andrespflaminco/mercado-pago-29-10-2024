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

            <ul class="navbar-item flex-row">



                <li class="nav-item theme-logo">
                    <a href="pos">
                        <img src="../assets/img/LOGO_03.png" class="navbar-logo" alt="logo">
                    </a>
                </li>


            </ul>


            <ul class="navbar-item flex-row navbar-dropdown">


                <li class="nav-item dropdown user-profile-dropdown  order-lg-0 order-1">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="far fa-user text-dark"></i>
                    </a>
                    <div class="dropdown-menu position-absolute animated fadeInUp" aria-labelledby="userProfileDropdown">
                        <div class="user-profile-section">
                            <div class="media mx-auto">

                                <div class="media-body">
                                    <h5>Bienvenido,</h5>
                                    <h5>{{Auth::user()->name}}</h5>

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
