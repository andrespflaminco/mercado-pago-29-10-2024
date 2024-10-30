<div class="row sales layout-top-spacing">

	<div class="col-sm-12">
		<div class="widget widget-chart-one">
			<div class="widget-heading">
				<h4 class="card-title">
					<b>Ayuda</b>
				</h4>
				
			</div>
	    	<div class="row">
	
            	<div class="col-lg-4 col-md-4 col-sm-12">
            		
            		<div class="input-group mb-4">
            			<div class="input-group-prepend">
            				<span class="input-group-text input-gp">
            					<i class="fas fa-search"></i>
            				</span>
            			</div>
            			<input type="text" wire:model="search" placeholder="Buscar" class="form-control" 
            			>
            		</div>
            
            	</div>
            	<div class="col-lg-4 col-md-4 col-sm-12">
            		
            		<div class="input-group mb-4">
            			<select wire:model="categoria" class="form-control">
            			    <option value="Elegir" disabled>Elija una categoria</option>
            			    <option value="0">Todas las categorias</option>
            			    <option value="Productos">Productos</option>
            			    <option value="Ventas">Ventas</option>
            			    <option value="Metodos">Metodos de pago</option>
            			    <option value="Cajas">Cajas</option>
            			</select>
            		</div>
            
            	</div>
            </div>
			
			

			<div class="widget-content">

                <div style="padding:5px;" class="card mb-2">
                    <div class="row" style="margin-left: 0px; margin-right:0px; height: 100%;">
                        
            @foreach($ayuda as $a)
            
                        <div class="col-3" style="margin-top: 15px; margin-bottom:75px;">
                           
                            <figure style="height: 90%; width: 100%; margin: 0 auto;" >
                                <div style="height: 90%; width:100%;" >
                                     <iframe style="border-radius:8px;" width="100%" height="100%" src="{{$a->url}}" title="{{$a->titulo}}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                           

                                </div>
                            </figure>
                             <h4 style="padding: 20px 5px;">
                              <b>{{$a->titulo}}</b>
                             </h4>
                        </div>

            
            @endforeach
                    </div>
                
              </div>

			</div>


		</div>


	</div>

</div>


