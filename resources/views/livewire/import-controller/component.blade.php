
<div class="row sales layout-top-spacing">

    <div class="col-sm-12">
        <div class="widget widget-chart-one">
            <div class="widget-heading">
                <h4 class="card-title">
                    <b>Módulo de Importar Catálogos</b>
                </h4>

            </div>


            <div class="widget-content">
              <p>{{ session('status') }}</p>

              <form method="POST" action="{{ url("import") }}" enctype="multipart/form-data">
              {{ csrf_field() }}

              <div class="form-group{{ $errors->has('file') ? ' has-error' : '' }}">
                  <label for="file" class="control-label">CSV file to import</label>

                  <input id="file" type="file" class="form-control" name="file" required>

                  @if ($errors->has('file'))
                      <span class="help-block">
                      <strong>{{ $errors->first('file') }}</strong>
                      </span>
                  @endif

              </div>

              <p><button type="submit" class="btn btn-success" name="submit"><i class="fa fa-check"></i> Submit</button></p>

              </form>

              @if ($toImport > 0)
                <p>Contacts left to import: {{ $toImport }}</p>
            @endif
            </div>


        </div>


    </div>


</div>
<script type="text/javascript">
	document.addEventListener('DOMContentLoaded', function() {

    window.livewire.on('import', msg => {
      swal({
            title: 'IMPORTACION EXITOSA!',
            type: 'success',
            padding: '2em'
          })
		});





});
</script>
