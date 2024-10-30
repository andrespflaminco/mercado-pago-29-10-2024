<div wire:ignore.self class="modal fade" id="theModalStock" tabindex="-1" role="dialog" style="overflow-y: auto !important;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>STOCK</b> | {{$producto_variaciones}}
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">

<div class="row">
<div class="col-12">

<div class="table-responsive" style="overflow-x: auto !important;">
<table  id="default-ordering" class="table table-hover">
	<thead class="text-white" style="background: #3B3F5C; vertical-align: middle !important; color: white !important;">
	<tr style="color: white !important;">
   	<th style="color: white !important;">Codigo</th>
   	<th style="color: white !important;">Nombre</th>
   	<th style="color: white !important;">Stock</th>
   	</tr>
   	</thead>
   <tbody>
    @foreach($stock_variaciones as $sv)
    <tr style="color:black;">
        <td>{{$sv->codigo_variacion}}</td>
        <td>{{$sv->variaciones}}</td>
        <td>
            <b>
            {{$sv->stock}} Unid.    
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

       <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

     </div>
   </div>
 </div>
</div>
