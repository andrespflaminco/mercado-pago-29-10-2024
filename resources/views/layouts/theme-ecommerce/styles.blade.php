<script src="{{ asset('assets/js/loader.js') }}"></script>
<link href="{{ asset('assets/css/loader.css') }}" rel="stylesheet" type="text/css" />





<link rel="stylesheet" type="text/css" href="{{ asset('assets/ecommerce/main-e.css') }}">

    <link href="{{ asset('assets/css/tables/table-basic.css') }}" rel="stylesheet" type="text/css" />
<!-- END GLOBAL MANDATORY STYLES -->

<!--  BEGIN CUSTOM STYLE FILE  -->
<link href="{{ asset('assets/css/components/custom-carousel.css') }}" rel="stylesheet" type="text/css" />

  <link href="{{ asset('assets/css/components/custom-modal.css') }}" rel="stylesheet" type="text/css" />

<style>




button.mercadopago-button {
  background-color: #335AFF !important;
  font-family: inherit !important;
  padding: 18px 45px !important;
  font-size: 16px !important;
  line-height: 1 !important;
  font-weight: 500 !important;
  text-transform: uppercase !important;
  border: 2px solid transparent !important;
}

.mensaje {
  padding: 20px;
  border: solid 1px #c8c8c8;
  display: block;
  border-radius: 6px;
  background: rgb(51 90 255 / 7%);
  margin-bottom: 20px;
}

#gracias_ecommerce {
  background-image: url("../assets/img/gracias.jpg");
  background-size: 100%;
  height: 550px;
}

.agotado {
position: absolute;
bottom: 30px;
width: 100%;
padding: 12px;
text-align: center;
background: #dc3545;
opacity: 0.9;
color: #ffffff;
font-weight: 700;
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


  .agotado {
    position: absolute;
    bottom: 20px;
    width: 100%;
    padding: 3px;
    text-align: center;
    background: #dc3545;
    opacity: 0.9;
    color: #ffffff;
    font-weight: 700;
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

</style>


 <link href="{{ asset('plugins/flatpickr/flatpickr.dark.css') }}" rel="stylesheet" type="text/css" />

@livewireStyles
