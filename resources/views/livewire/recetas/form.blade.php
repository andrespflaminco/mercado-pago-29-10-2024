<div wire:ignore.self class="modal fade" id="theModal" tabindex="-1" role="dialog" style="overflow-y: auto !important;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>{{$componentName}}</b> | {{ $selected_id  }}
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">


 <div class="row">

<div class="col-12">


      <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
          <div class="widget-content widget-content-area br-6">
              <div class="table-responsive mb-4 mt-4">
                  <table id="html5-extension" class="table table-hover non-hover" style="width:100%">
    <thead>
      <tr>
        <th>Insumo</th>
        <th>Cantidad</th>
        <th>Unidad de medida</th>
        <th>Costo</th>
      </tr>
    </thead>
    <tbody>
      @foreach($receta as $r)
      <tr>
        <td>{{$r->nombre_insumo}}</td>
        <td>{{$r->cantidad}}</td>
        <td>{{$r->unidad_medida }}</td>
        <td> $ {{$r->cantidad*$r->relacion_medida*$r->costo_unitario   }}</td>
      </tr>

      @endforeach

    </tbody>
  </table>

  <h6>Costo Total: $ {{$sum_receta}}</h6>
  <br>
  @if($rinde != 0)
  <h6>Costo por unidad: $ {{ number_format(($sum_receta/$rinde),2)  }}</h6>
  @endif
  <br>
  <h6>Cantidad de insumos: {{$cantidad_receta}}</h6>
</div>

</div>

</div>
</div>

</div>

</div>
     <div class="modal-footer">

       <button type="button" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

     </div>
   </div>
 </div>
</div>
