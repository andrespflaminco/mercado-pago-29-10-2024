<style>
@keyframes shake {
    0% { transform: translateX(0); }
    10% { transform: translateX(-3px); }
    20% { transform: translateX(3px); }
    30% { transform: translateX(-2px); }
    40% { transform: translateX(2px); }
    50% { transform: translateX(-1px); }
    60% { transform: translateX(1px); }
    70% { transform: translateX(0); }
    100% { transform: translateX(0); }
}

.chat-bot {
    position: fixed;
    bottom: 15px;
    right: 15px;
    cursor:pointer;
    border-radius: 50%;
    padding: 10px; 
    background-color: #33d951;  
    color: #fff;  
    text-align: left;
    animation: shake 1s ease-in-out infinite;
}

.chatbot-bubble {
    position: fixed;
    bottom: 79px;
    right: 18px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    max-width: 300px;
    padding: 15px;
    font-size: 14px;
    z-index: 9999;
    animation: shake 1s ease-in-out infinite;
    display: none;
}

/* Estilos para la burbuja del chat cuando haces hover en el bot���n de chat */
.chat-bot:hover + .chatbot-bubble {
    display: block; /* Mostrar la burbuja del chat cuando haces hover en el bot���n de chat */
}
.chatbot-bubble::before {
    content: "";
    position: absolute;
    bottom: -15px;
    right: 20px;
    border-width: 0 10px 10px 0;
    border-style: solid;
    border-color: #fff #fff #33d951 #fff; /* tri���ngulo transparente para el color de fondo */
}


/* ------ Dashboard ---- */
#date-range-picker {
    background-color: #fff;
    border: 1px solid rgba(145, 158, 171, 0.32);
    color: #212B36;
    padding: 3px 10px;
    border-radius: 5px;
    min-width: 90px;
    cursor:pointer;
}
.date-dashboard {
    cursor: pointer;
    background-color: #fff;
    border: 1px solid rgba(145, 158, 171, 0.32);
    color: #212B36;
    padding: 3px 10px;
    border-radius: 5px;
    min-width: 90px;
}
a.dash-count:hover {
    color: #dfdbdb !important;
}

/* ------ / Dashboard ---- */


.select2-container--default .select2-selection--multiple .select2-selection__rendered li {
    list-style: none;
    width: auto;
    max-width: 300px !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

	.boton-precio:hover {
		font-size: 1rem!important;
		width: 80px;
		background-color:
		transparent;
		border-left: none;
		border-top: none;
		border-right: none;
		text-align: center;
		border-bottom: 1px solid #bfc9d4;
	}
	.botones1 {
		height: auto;
		border: 1px solid #bfc9d4;
		color: #3b3f5c;
		font-size: 15px;
		text-align: center;
		letter-spacing: 1px;
		max-width: 90px;
		padding: 0.5rem 0.5rem;
		border-radius: 6px;
	}

	.botones2 {
		height: auto;
		border: 1px solid #bfc9d4;
		color: #3b3f5c;
		font-size: 15px;
		padding: 8px 10px;
		letter-spacing: 1px;
		max-width: 105px;
		padding: 0.5rem 0.5rem;
		border-radius: 6px;
	}
	.boton-precio:focus {
		font-size: 1rem!important;
		width: 80px;
		background-color:
		transparent;
		border-left: none;
		border-top: none;
		border-right: none;
		text-align: center;
		border-bottom: 1px solid #bfc9d4;
	}
	.boton-precio {
		font-size: 1rem!important;
		width: 80px;
		background-color:
		transparent;
		border: none;
		text-align: center;
	}
	
    /* Estilo predeterminado para el div */
    #accion-lote {
      display: block; /* Mostrar el div en pantallas grandes */
    }
    
    /* Media query para dispositivos pequeños */
    @media screen and (max-width: 768px) {
    #accion-lote {
        display: none; /* Ocultar el div en dispositivos pequeños */
      }
    }

    .customizer-links {
        display: none !important;
    }
    
    .estado {
    padding: 0px 10px;
    cursor:pointer;
    color: #637381; !important;
    }
    
    .estado-activo {
    padding: 0px 10px;
    color: #FF9F43 !important;
    cursor:pointer;
    }
    
    .boton-editar {
      color:#637381;
      border: none !important;
      display: block !important;
    }

</style>

<script src="https://js.pusher.com/7.0/pusher.min.js"></script>

        	<!--  END CUSTOM STYLE FILE  -->
        
        <link href="{{ asset('assets/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/notification/snackbar/snackbar.min.css') }}" rel="stylesheet" type="text/css" />
		<!-- Wizard CSS -->
        <link rel="stylesheet" href="{{ asset('assets/pos/plugins/twitter-bootstrap-wizard/form-wizard.css') }}">
        
        <!-- Toatr CSS -->		
        <link rel="stylesheet" href="{{ asset('assets/pos/plugins/toastr/toatr.css') }}">

		<!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="/assets/img/favicon.ico">
		
		<!-- Feather CSS -->
		<link rel="stylesheet" href="{{ asset('assets/pos/plugins/icons/feather/feather.css') }}">
		
		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="{{ asset('assets/pos/css/bootstrap.min.css') }}" >
		
		<!-- animation CSS -->
        <link rel="stylesheet" href="{{ asset('assets/pos/css/animate.css') }}" >

		<!-- Select2 CSS -->
	    		
      <!---  <link href="{{ asset('plugins/select2/select2.min.css') }} " rel="stylesheet" type="text/css" /> --->
      <style>
        .select2-container .select2-selection--multiple {
            width: 100% !important;
            min-height: 38px !important;
            color: #212529 !impor
            tant;
            background-color: #fff !important;
            background-clip: padding-box !important;
            border: 1px solid #ced4da !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            border-radius: 0.25rem !important;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }    
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #FF9F43 !important;
            margin-right: 5px !important;
         }
        </style>
        
		<!-- Datatable CSS -->
		<link rel="stylesheet" href="{{ asset('assets/pos/css/dataTables.bootstrap4.min.css') }}" >
		
        <!-- Fontawesome CSS -->
		<link rel="stylesheet" href="{{ asset('assets/pos/plugins/fontawesome/css/fontawesome.min.css') }}" >
		<link rel="stylesheet" href="{{ asset('assets/pos/plugins/fontawesome/css/all.min.css') }}" >
		
        	<!-- Select2 CSS -->
		<link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2.min.css') }}">
		
		<!-- Main CSS -->
        <link rel="stylesheet" href="{{ asset('assets/pos/css/style.css') }}" >
        
        
    <link href="{{ asset('assets/plugins/pricing-table/css/component.css') }}" rel="stylesheet" type="text/css" />       



@livewireStyles
