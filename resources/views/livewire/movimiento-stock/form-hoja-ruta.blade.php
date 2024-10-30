<div class="modal fade" id="tabsModal" tabindex="-1" role="dialog" aria-labelledby="tabsModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tabsModalLabel">Hojas de ruta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @foreach ($listado_hojas_ruta as $lh)
          <a href="{{ url('cambio-hoja-ruta/' . $lh->id . '/' . $ventaId) }}" style="min-width:220px;" class="btn btn-warning mb-2">{{\Carbon\Carbon::parse($lh->fecha)->format('d-m-Y')}} ({{$lh->turno}})</a><br>

        @endforeach
          <a href="{{ url('cambio-hoja-ruta/0/' . $ventaId) }}" style="min-width:220px;" class="btn btn-light mb-2"> Sin asignar </a><br>
          <br><br><br>
          	<a href="javascript:void(0)" style="color:blue;" data-toggle="modal" data-target="#theModal">Crear nueva Hoja de ruta</a>
        </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> CERRAR</button>
      </div>
    </div>
  </div>
</div>
