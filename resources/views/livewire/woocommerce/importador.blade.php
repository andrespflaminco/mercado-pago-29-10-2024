<div wire:ignore.self class="modal fade" id="Importador" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
        	<b>IMPORTADOR</b>
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">
 <div class="row">
<div class="col-12"> <h5>Hemos encontrado un producto repetido</h5></div>
    <br><br>
    <!----- Flaminco ----------->
    
   <div class="col-sm-6 col-md-6">
   <div class="col-12"> </div>
   <div class="col-12"> <strong>FLAMINCO</strong> </div>
   <div class="col-12"></div>
   <div class="col-12"> NOMBRE: {{$name_flaminco}} </div>
   <div class="col-12"> SKU: {{$sku_flaminco}} </div>
   <div class="col-12"> PRECIO: ${{$price_flaminco}}</div>
   <div class="col-12"> STOCK: {{$stock_wc}} </div>
   </div>

        
        
    <!----- / Flaminco ----------->
   

    <!----- Wocommerce ----------->
    
   <div class="col-sm-6 col-md-6">
   <div class="col-12"> </div>
   <div class="col-12"> <strong>WOOCOMMERCE</strong> </div>
   <div class="col-12"></div>
   <div class="col-12"> NOMBRE: {{$name_wc}} </div>
   <div class="col-12"> SKU: {{$sku_wc}} </div>
   <div class="col-12"> PRECIO: ${{$price_wc}}</div>
   <div class="col-12"> STOCK: {{$stock_wc}} </div>
   </div>

    <!----- / Wocommerce ----------->

</div>

</div>
<br><br>
     <div class="modal-footer">
    <div class="col-12">
       <button type="button" wire:click.prevent="Omitir()" class="btn btn-dark close-btn text-info" data-dismiss="modal">OMITIR</button>
       <button type="button" wire:click.prevent="UpdateFlaminco()" class="btn btn-dark close-modal" >ACTUALIZAR EN FLAMINCO</button>
       <button type="button" wire:click.prevent="UpdateWC()" class="btn btn-dark close-modal" >ACTUALIZAR EN WOCOMMCERCE</button>        
    </div>
    <div class="col-12">
        <input type="checkbox" wire:model="eleccion"> Aplicar esta eleccion en todos los casos
    </div>

     </div>
   </div>
 </div>
</div>
