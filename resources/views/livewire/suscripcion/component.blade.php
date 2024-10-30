
<div style="padding:25px !important;">

                    @php
                        $name = auth()->user() ? "%20Mi%20nombre%20es:%20" . urlencode(auth()->user()->name) . "%20" : "";
                        $email = auth()->user() ? "%20y%20mi%20email%20es:%20" . urlencode(auth()->user()->email) : "";
                    @endphp
                                                
                                                                        
                    @if($corroboracion_estado == 0)
                    
                    @if($user->confirmed_at == null)
                    
                    @include('livewire.suscripcion.planes')
                    
                    @else

                    <div style="margin: 0 auto;" class="row mb-2 text-center">

                    @if($suscripcion !== null && $user->confirmed_at !== null)  
                    
                    @if($suscripcion->suscripcion_status == "activa")
                    
                    <div class="pricing-plan mb-lg-5 mb-sm-3 mt-sm-3" >
                       <br>    
                       <br>    
                       <img style="width:200px !important;" src="assets/pos/img/logo.png"   alt="">
                       <br>
                       <br>
                       <br>    
                       <h2><b>GRACIAS POR ELEGIRNOS</b></h2>
                       <b>La suscripción se encuentra activa</b> 
                       <br><br><br>
                       <br>
                       <a class="btn btn-primary" href="{{ url('pos') }}">
                           EMPEZAR >
                       </a>
                    </div>  
                    
                    @elseif($suscripcion->suscripcion_status == "cancelada")
                    <div class="pricing-plan mb-lg-5 mb-sm-3 mt-sm-3" >
                       <br>    
                       <br>    
                       <img style="width:200px !important;" src="assets/pos/img/logo.png"   alt="">
                       <br>
                       <br>
                       <br>    
                       <h2><b>SUSCRIPCION CANCELADA</b></h2>
                       <b>Por favor suscribite de nuevo</b> 
                       <br><br><br>
                       <br>
                    </div> 
                    
                    @include('livewire.suscripcion.planes')
                    @else
                    @include('livewire.suscripcion.planes')
                    @endif
                    
                    @endif
                    </div>
                    <br><br>
                    @endif
                     
                     @else
                     @include('livewire.suscripcion.comprobacion')
                     @endif
 
  <a href="javascript:void(0)" wire:click="CerrarSesion()" >Volver al login</a>
    			
</div>

