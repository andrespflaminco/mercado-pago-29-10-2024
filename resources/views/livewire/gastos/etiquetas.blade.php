<style media="screen">
.table-hover:not(.table-dark) tbody tr:hover {
    background-color: transparent !important;
}
	.boton-etiqueta:hover {
		font-size: 1rem!important;
		width: auto;
		background-color:
		transparent;
		border-left: none;
		border-top: none;
		border-right: none;
		text-align: center;
		border-bottom: 1px solid #bfc9d4;
	}
	.boton-etiqueta:focus {
		font-size: 1rem!important;
		width: auto;
		background-color:
		transparent;
		border-left: none;
		border-top: none;
		border-right: none;
		text-align: center;
		border-bottom: 1px solid #bfc9d4;
	}
	.boton-etiqueta {
		font-size: 1rem!important;
		width: auto;
		background-color:
		transparent;
		border: none;
		text-align: center;
	}
</style>
<div wire:ignore.self class="modal fade" id="tabsModal" tabindex="-1" role="dialog" aria-labelledby="tabsModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tabsModalLabel">Etiquetas</h5>
        <button type="button" wire:click.prevent="DismissEtiqueta()"  class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

                <div class="form-group">
                  <label for="exampleInputEmail1">Agregar etiqueta</label>

                  <div class="input-group mb-4">

                  <input autocomplete="off" type="text" id="title" wire:model.lazy="nombre_etiqueta" class="form-control" required="">
                  <div class="input-group-append">
                            <button type="button" wire:click.prevent="CreateEtiqueta()" class="btn btn-dark">Agregar</button>

                 </div>
                 </div>
                </div>
                @if($procedencia == 0)
                <div style="margin-bottom: 0 !important;" class="table-responsive mb-4 mt-4">
                    <table class="multi-table table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-left">Nombre</th>
                                <th class="text-left"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($etiquetas as $e)
                            <tr>
                                <td class="text-left">
                                  <input style="text-align:left !important;" class="boton-etiqueta" type="text" id="p{{$e->id}}"
                                  wire:change.prevent="updateEtiqueta({{$e->id}}, $('#p' + {{$e->id}}).val() )"
                                  value="{{$e->nombre}}">

                                  </td>
                                <td class="text-left">
                                  <a href="javascript:void(0)" onclick="Confirm2('{{$e->id}}')" >
                                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                   </a>
                                </td>

                            </tr>
                            @endforeach

                        </tbody>

                    </table>
                </div>
                @endif



                  </div>

            </div>
        </div>
      <div class="modal-footer">
        @if($procedencia == 0)  
        <button class="btn btn-cancel" wire:click="DismissEtiqueta()" data-dismiss="modal"><i class="flaticon-cancel-12"></i> CERRAR</button>
        @else
        <button class="btn btn-cancel" wire:click="DismissEtiqueta()" data-dismiss="modal"><i class="flaticon-cancel-12"></i> CERRAR</button>
        @endif
      </div>
    </div>
  </div>
</div>
