<div class="row">
                
                <div  class="col-12">
                    <div style="cursor: pointer !important;" wire:click="ElegirTipoActualizacion(1)" class="card">
                    <div class="card-body">
                        <h3>Actualizar usando Costos Actuales</h3>
                    </div>
                </div>
                </div>
                <div hidden  class="col-12">
                  <div  style="cursor: pointer !important;" wire:click="ElegirTipoActualizacion(2)" class="card">
                    <div class="card-body">
                        <h3>Actualizar usando un Porcentaje %</h3>
                    </div>
                </div>  
                </div>
                <div hidden  class="col-12">
                  <div  style="cursor: pointer !important;" wire:click="ElegirTipoActualizacion(3)" class="card">
                    <div class="card-body">
                        <h3>Elegir individualmente por producto</h3>
                    </div>
                </div>  
                </div>
</div>