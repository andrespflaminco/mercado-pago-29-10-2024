<div wire:ignore.self class="modal fade" id="theModalStock" tabindex="-1" role="dialog" style="overflow-y: auto !important;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
        	<b>STOCK</b> | {{$producto_variaciones}}
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">

<div class="row">
<div class="col-12">

  <div style="margin-bottom: 0 !important;" class="table-responsive mb-4 mt-4">
    <table class="multi-table table table-hover" style="width:100%">
	<thead>
	<tr>
   	<th>Codigo</th>
   	<th>Nombre</th>
   	<th>Stock Disponible</th>
   	<th>Stock Real</th>
   	</tr>
   	</thead>
   <tbody>
    @foreach($stock_variaciones as $sv)
    <tr>
        <td>{{$sv->codigo_variacion}}</td>
        <td>{{$sv->variaciones}}</td>
         <td>
            <b>
            {{$sv->stock}} Unid.    
            </b>
            
        </td>
        <td>
            <b>
            {{$sv->stock_real}} Unid.    
            </b>
            
        </td>
       
    </tr>
    @endforeach
   </tbody>	
</table>

</div>

</div>



</div>
     <div class="modal-footer">

       <button type="button" wire:click.prevent="OcultarMostrarStock()"  class="btn btn-cancel" data-dismiss="modal">CERRAR</button>

     </div>
   </div>
 </div>
</div>
</div>
