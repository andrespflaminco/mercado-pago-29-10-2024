<style>
 
#loadingOverlayProducts {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.4); /* Fondo gris translè´‚cido */
    z-index: 9999; /* Asegurarse de que estè´± por encima de otros elementos */
    display: flex;
    justify-content: center; /* Centrado horizontal */
    align-items: center; /* Centrado vertical */
    color: white;
    font-size: 1.5em;
    visibility: hidden; /* Ocultar por defecto */
}

#loadingOverlayProducts.show {
    visibility: visible; /* Mostrar cuando se agrega la clase .show */
}

    .dropzone {
        border: 2px dashed #cccccc;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        margin-bottom: 20px;
    }


   .dropzone.dragover {
        border-color: #0000ff;
    }

 
        .color-box {
            display: flex;
            align-items: center;
            margin-bottom: 0px;
        }
        .color-box div {
            width: 10px;
            height: 10px;
            margin-right: 10px;
            border-radius: 50%; /* Para que los cuadros de color sean redondos */
            margin-left: 15px;
        }
        .color-container {
            margin-top: 20px;
            display: flex;
            align-items: center;
            text-align: center;
            margin:0 auto;
        }
        
  /* Estilo para el punto de color */
  .point-apex-chart-flaminco {
    width: 10px; /* Tamaè´–o del punto */
    height: 10px; /* Tamaè´–o del punto */
    background-color: #008ffb; /* Color del punto */
    border-radius: 50%; /* Hacer el punto redondo */
    display: inline-block; /* Para que no afecte al flujo del texto */
    margin-right: 5px; /* Espacio entre el punto y el texto */
  }

#dropdown-toggle
{
color: black !important; background: 
#FAFBFE !important; 
padding: 1px 8px; 
border-radius: 8px; 
border: 1px solid #E9ECEF;    
}

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

/* Estilos para la burbuja del chat cuando haces hover en el botè´—n de chat */
.chat-bot:hover + .chatbot-bubble {
    display: block; /* Mostrar la burbuja del chat cuando haces hover en el botè´—n de chat */
}
.chatbot-bubble::before {
    content: "";
    position: absolute;
    bottom: -15px;
    right: 20px;
    border-width: 0 10px 10px 0;
    border-style: solid;
    border-color: #fff #fff #33d951 #fff; /* triè´°ngulo transparente para el color de fondo */
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
    
    /* Media query para dispositivos peque√±os */
    @media screen and (max-width: 768px) {
    #accion-lote {
        display: none; /* Ocultar el div en dispositivos peque√±os */
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
        
        <link href="{{ asset('plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('plugins/notification/snackbar/snackbar.min.css') }}" rel="stylesheet" type="text/css" />
		<!-- Wizard CSS -->
        <link rel="stylesheet" href="{{ asset('assets/pos/plugins/twitter-bootstrap-wizard/form-wizard.css') }}">
        
        <!-- Toatr CSS -->		
        <link rel="stylesheet" href="{{ asset('assets/plugins/toastr/toatr.css') }}">

		<!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
		
		<!-- Feather CSS -->
		<link rel="stylesheet" href="{{ asset('assets/pos/assets/plugins/icons/feather/feather.css') }}">
		
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
        <link rel="stylesheet" href="{{ asset('assets/pos/plugins/select2/css/select2.min.css') }} ">
        
		<!-- Datatable CSS -->
		<link rel="stylesheet" href="{{ asset('assets/pos/css/dataTables.bootstrap4.min.css') }}" >
		
        <!-- Fontawesome CSS -->
		<link rel="stylesheet" href="{{ asset('assets/pos/plugins/fontawesome/css/fontawesome.min.css') }}" >
		<link rel="stylesheet" href="{{ asset('assets/pos/plugins/fontawesome/css/all.min.css') }}" >
		
        	<!-- Select2 CSS -->
		<link rel="stylesheet" href="{{ asset('assets/pos/assets/plugins/select2/css/select2.min.css') }}">
		
		<!-- Main CSS -->
        <link rel="stylesheet" href="{{ asset('assets/pos/css/style.css') }}" href="">
        
        
    <link href="{{ asset('plugins/pricing-table/css/component.css') }}" rel="stylesheet" type="text/css" /> 
        
<style>

</style>

@livewireStyles
