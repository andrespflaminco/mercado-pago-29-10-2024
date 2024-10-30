

<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>

  // Enable pusher logging - don't include this in production
  Pusher.logToConsole = true;

  var pusher = new Pusher('93abd037e37ed5513af5', {
    cluster: 'sa1'
  });

  var channel = pusher.subscribe('my-channel');
  channel.bind('my-event', function(data) {
    alert(JSON.stringify(data));
  });
</script>

<script src="https://cdn.jsdelivr.net/gh/jamesssooi/Croppr.js@2.3.0/dist/croppr.min.js"></script>
<link href="https://cdn.jsdelivr.net/gh/jamesssooi/Croppr.js@2.3.0/dist/croppr.min.css" rel="stylesheet"/>

<script src="{{ asset('assets/js/loader.js') }}"></script>
<link href="{{ asset('assets/css/loader.css') }}" rel="stylesheet" type="text/css" />


<link href="{{ asset('plugins/pricing-table/css/component.css') }}" rel="stylesheet" type="text/css" />

  <link href="{{ asset('plugins/file-upload/file-upload-with-preview.min.css') }}" rel="stylesheet" type="text/css" />

<link href="https://fonts.googleapis.com/css?family=Quicksand:400,500,600,700&display=swap" rel="stylesheet">

<link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/plugins.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/structure.css') }}" rel="stylesheet" type="text/css" class="structure" />

<!--  BEGIN CUSTOM STYLE FILE  -->
<link href="{{ asset('assets/css/scrollspyNav.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/components/cards/card.css') }}" rel="stylesheet" type="text/css" />

  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/forms/switches.css') }}">

<!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
<link href="{{ asset('assets/css/pages/faq/faq.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->
<!--  END CUSTOM STYLE FILE  -->
    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link rel="stylesheet" type="text/css" href="plugins/dropify/dropify.min.css">
    <link href="assets/css/users/account-setting.css" rel="stylesheet" type="text/css" />

<link href="{{ asset('plugins/font-icons/fontawesome/css/fontawesome.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/fontawesome.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ asset('assets/css/elements/avatar.css') }}" rel="stylesheet" type="text/css" />

<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
<link href="{{ asset('assets/css/scrollspyNav.css') }}" rel="stylesheet" type="text/css" />
<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->

<!--  BEGIN CUSTOM STYLE FILE  -->
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/dropify/dropify.min.css') }}">
<link href="{{ asset('assets/css/users/account-setting.css" rel="stylesheet" type="text/css') }}" />
<!--  END CUSTOM STYLE FILE  -->

<link href="{{ asset('plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/notification/snackbar/snackbar.min.css') }}" rel="stylesheet" type="text/css" />

<!--  BEGIN CUSTOM STYLE FILE  -->
<link href="{{ asset('assets/css/elements/miscellaneous.css') }}" rel="stylesheet" type="text/css" />
<!--  END CUSTOM STYLE FILE  -->
<link href="{{ asset('css/custom.css') }}" rel="stylesheet" type="text/css" />

<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/widgets/modules-widgets.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/forms/theme-checkbox-radio.css') }}">

 <link href="{{ asset('assets/css/apps/scrumboard.css') }}" rel="stylesheet" type="text/css" />
 <link href="{{ asset('assets/css/apps/notes.css') }}" rel="stylesheet" type="text/css" />


 <!--  BEGIN CUSTOM STYLE FILE  -->
 <link href="{{ asset('assets/css/scrollspyNav.css') }}" rel="stylesheet" type="text/css" />
 <link href="{{ asset('assets/css/components/tabs-accordian/custom-tabs.css') }}" rel="stylesheet" type="text/css" />
 <!--  END CUSTOM STYLE FILE  -->


 <!--  BEGIN CUSTOM STYLE FILE  -->
 <link href="{{ asset('assets/css/scrollspyNav.css') }} " rel="stylesheet" type="text/css" />
 <link href="{{ asset('assets/css/components/tabs-accordian/custom-accordions.css') }} " rel="stylesheet" type="text/css" />
 <!--  END CUSTOM STYLE FILE  -->
 <!--  BEGIN CUSTOM STYLE FILE  -->
 <link href="{{ asset('assets/css/scrollspyNav.css') }} " rel="stylesheet" type="text/css" />
  <link href="{{ asset('plugins/select2/select2.min.css') }} " rel="stylesheet" type="text/css" />
 <!--  END CUSTOM STYLE FILE  -->

 <!--  BEGIN CUSTOM STYLE FILE  -->
 <link href="{{ asset('assets/css/apps/invoice.css') }} " rel="stylesheet" type="text/css" />
 <!--  END CUSTOM STYLE FILE  -->

<style>

span.custom-file-container__custom-file__custom-file-control {
  display: none !important;
}

input.custom-file-container__custom-file__custom-file-input {
  display: none !important;
}
.custom-file-container__image-preview {
    box-sizing: border-box;
    transition: all 0.2s ease;
    margin-top: 0 !important;
    margin-bottom: 0 !important;
    height: 250px;
    width: 100%;
    border-radius: 4px;
    background-size: contain;
    background-position: center center;
    background-repeat: no-repeat;
    background-color: #fff;
    overflow: auto;
    padding: 0 !important;
}

.borrar-imagen {
margin-left: 206px;
position: absolute;
display: block;
background-color: #191e3a!important;
color: white !important;
box-shadow: none !important;
border: none !important;
font-size: 19px !important;
padding: 7px!important;
}

.hide-sucursales {
  display: none;
}

#image-upload > input
{
    display: none;
}

#image-upload img
{
    width: 240px;
    cursor: pointer;
}


.boton-editar-products {
  color:white;
  border: none !important;
  font-size:17px; background:none;
  display: block !important;
}
.boton-editar-products:hover {
  color:#333;
  border: none !important;
  font-size:17px; background:none;
  display: block !important;
}


@media only screen and (max-width: 600px) {
	#connect-sorting {
		display: block !important;
	}
}

.card {
    width: 100%;

}

@media only screen and (min-width: 600px) {
	#connect-sorting {
		display: flex !important;
	}
}

	aside {
		display: none!important;
	}
	.page-item.active .page-link {
		z-index: 3;
		color: #fff;
		background-color: #3b3f5c;
		border-color: #3b3f5c;
	}

	@media (max-width: 480px)
	{
		.mtmobile {
			margin-bottom: 20px!important;
		}
		.mbmobile {
			margin-bottom: 10px!important;
		}
		.hideonsm {
			display: none!important;
		}
		.inblock {
			display: block;
		}
	}

	/*sidebar background*/
	.sidebar-theme #compactSidebar {
		background: #191e3a!important;
	}

	/*sidebar collapse background */
	.header-container .sidebarCollapse {
		color: #3B3F5C!important;
	}

	.navbar .navbar-item .nav-item form.form-inline input.search-form-control {
		font-size: 15px;
		background-color: #3B3F5C!important;
		padding-right: 40px;
		padding-top: 12px;
		border: none;
		color: #fff;
		box-shadow: none;
		border-radius: 30px;
	}


</style>

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



        .loader-wrap {
      height: 100%;
      width: 100%;
      position: fixed;
      left: 0;
      top: 0;
      z-index: 999;
    }

    .loader-wrap .loader-cube-wrap {
      width: 100px;
    }

    .progressstimer {
      height: 50px;
      width: 50px;
      line-height: 50px;
      text-align: center;
      vertical-align: middle;
      display: inline-block;
      margin: 0 auto 10px auto;
      position: relative;
    }

    .progressstimer img {
      position: absolute;
      left: 0;
      right: 0;
      z-index: 0;
      -webkit-animation: rotating 1s linear infinite;
      animation: rotating 1s linear infinite;
    }

    .progressstimer .timer {
      font-size: 12px;
      display: inline-block;
      vertical-align: middle;
      margin-top: -2px;
    }

    @-webkit-keyframes rotating {
      from {
        -webkit-transform: rotate(0deg);
        -o-transform: rotate(0deg);
        transform: rotate(0deg);
      }
      to {
        -webkit-transform: rotate(360deg);
        -o-transform: rotate(360deg);
        transform: rotate(360deg);
      }
    }

    @keyframes rotating {
      from {
        -ms-transform: rotate(0deg);
        -moz-transform: rotate(0deg);
        -webkit-transform: rotate(0deg);
        -o-transform: rotate(0deg);
        transform: rotate(0deg);
      }
      to {
        -ms-transform: rotate(360deg);
        -moz-transform: rotate(360deg);
        -webkit-transform: rotate(360deg);
        -o-transform: rotate(360deg);
        transform: rotate(360deg);
      }
    }


    .logo-wallet {
      height: 100px;
      width: 100px;
      border-radius: 25px;
      position: relative;
      z-index: 1;
      overflow: hidden;
      display: inline-block;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }

    .logo-wallet .wallet-bottom {
      height: 100px;
      width: 100px;
      position: relative;
      border-radius: 25px;
      background-color: var(--finwallapp-theme-color);
    }

    .logo-wallet .wallet-cards {
      height: 80px;
      width: 50px;
      top: 18px;
      left: 0px;
      position: absolute;
      border-radius: 15px;
      transform: rotate(0deg);
      background-color: #f73563;
      z-index: 3;
      box-shadow: 0 3px 5px rgba(0, 0, 0, 0.25);
      -webkit-box-shadow: 0 3px 5px rgba(0, 0, 0, 0.25);
      -moz-box-shadow: 0 3px 5px rgba(0, 0, 0, 0.25);
      animation: rotateanimation ease 2s infinite;
    }

    .logo-wallet .wallet-cards:after {
      content: "";
      position: absolute;
      border-radius: 15px;
      height: 80px;
      width: 50px;
      left: -5px;
      background-color: #ffbd17;
      transform: rotate(-15deg);
      box-shadow: 0 3px 5px rgba(0, 0, 0, 0.25);
      -webkit-box-shadow: 0 3px 5px rgba(0, 0, 0, 0.25);
      -moz-box-shadow: 0 3px 5px rgba(0, 0, 0, 0.25);
      z-index: -1;
    }

    .logo-wallet .wallet-cards::before {
      content: "";
      position: absolute;
      border-radius: 15px;
      left: -10px;
      height: 80px;
      width: 50px;
      transform: rotate(-30deg);
      background-color: #00dfa3;
      box-shadow: 0 3px 5px rgba(0, 0, 0, 0.25);
      -webkit-box-shadow: 0 3px 5px rgba(0, 0, 0, 0.25);
      -moz-box-shadow: 0 3px 5px rgba(0, 0, 0, 0.25);
      z-index: 3;
    }

    .logo-wallet .wallet-top {
      height: 100px;
      width: 65px;
      border-radius: 25px;
      top: 0;
      left: 0;
      position: absolute;
      background: var(--finwallapp-theme-color-grad-1);
      background: -moz-radial-gradient(30% 30%, ellipse cover, var(--finwallapp-theme-color-grad-1) 0%, var(--finwallapp-theme-color-grad-2) 50%, var(--finwallapp-theme-color-grad-3) 100%);
      background: -webkit-radial-gradient(30% 30%, ellipse cover, var(--finwallapp-theme-color-grad-1) 0%, var(--finwallapp-theme-color-grad-2) 50%, var(--finwallapp-theme-color-grad-3) 100%);
      background: radial-gradient(ellipse at 30% 30%, var(--finwallapp-theme-color-grad-1) 0%, var(--finwallapp-theme-color-grad-2) 50%, var(--finwallapp-theme-color-grad-3) 100%);
      filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='var(--finwallapp-theme-color-grad-1)', endColorstr='var(--finwallapp-theme-color-grad-3)',GradientType=1 );
      box-shadow: 0 3px 5px rgba(0, 0, 0, 0.25);
      -webkit-box-shadow: 0 3px 5px rgba(0, 0, 0, 0.25);
      -moz-box-shadow: 0 3px 5px rgba(0, 0, 0, 0.25);
      z-index: 5;
      animation: widthtopanimation infinite 2s ease;
    }



    .tab-title .nav-pills.group-list .nav-item a.g-dot-dark:before {
      background: #c3baac;
      border: 1px solid #0e1726;
}


.tab-title .nav-pills.group-list .nav-item a.g-dot-green:before {
  background: #e5f9c9;
  border: 1px solid #28a745;
}

.note-container.note-grid .note-item .note-inner-content .note-footer .tags-selector .dropdown-menu a.g-dot-dark:before {
  background: #c3baac;
  border: 1px solid #0e1726;
}


.note-container.note-grid .note-item .note-inner-content .note-footer .tags-selector .dropdown-menu a.g-dot-green:before {
  background: #e5f9c9;
  border: 1px solid #28a745;
}

.note-container.note-grid .note-item.note-dark {
      background: #c3baac;
}

.note-container.note-grid .note-item.note-green {
      background: #e5f9c9;
}

.arrow {
  transition: all .5s;
  display: inline-block;
}

.arrow.active {
  transform: rotate(90deg);
}

.estado {
padding: 0px 20px;
cursor:pointer;
}

.estado-activo {
padding: 0px 20px;
color: blue !important;
cursor:pointer;
}


<link rel="preconnect" href="https://rsms.me/">
<link rel="stylesheet" href="https://rsms.me/inter/inter.css">

/* CSS */
:root { font-family: 'Inter', sans-serif; }
@supports (font-variation-settings: normal) {
  :root { font-family: 'Inter var', sans-serif; }
}

  </style>


 <link href="{{ asset('plugins/flatpickr/flatpickr.dark.css') }}" rel="stylesheet" type="text/css" />

@livewireStyles
