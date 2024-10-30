    
    @if(auth()->check())
        <div style="padding:25px !important; text-align:center;">
            <div class="pricing-plan mb-lg-5 mb-sm-3 mt-sm-3" >
                       <br>    
                       <br>    
                       <img style="width:200px !important;" src="../assets/pos/img/logo.png"   alt="">
                       <br>
                       <br>
                       <br>    
                       <h2><b>GRACIAS POR ELEGIRNOS</b></h2>
                       <b>REDIRIGIENDO...</b> 
                       <br><br><br>
                       <br>
                       <button hidden class="btn btn-primary" id="iniciar" onclick="presionarBotonIniciar" wire:click="Iniciar({{$slug}})">
                           REDIRIGIENDO...
                       </button>
                    </div>
        </div>
		@else
		@include('auth.inicio-sesion')
		@endif
		
		<script>

        document.addEventListener("livewire:load", function(event) {
        
        @if(auth()->check())
        
       !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '946528043924786');
        fbq('track', 'InitiateCheckout');
  
        window.livewire.emit('IniciarProceso', '');
        @endif
        
        });
        
	document.addEventListener('DOMContentLoaded', function() {

		window.livewire.on('enviar-face', msg => {
       !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '946528043924786');
        fbq('track', 'InitiateCheckout');
        
        window.livewire.emit('IniciarProceso', '');	
		});
		
	});
		</script>
		<noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=946528043924786&ev=PageView&noscript=1"
        /></noscript>
  
