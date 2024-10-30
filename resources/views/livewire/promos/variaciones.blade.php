
<div wire:ignore.self class="modal fade" id="Variaciones" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Variaciones</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div class="modal-body">
                <br>
                             
                <div class="table-responsive" style="max-height: 400px;">
                    <table class="table">
                        <tbody>
                            <thead>
                                <tr>
                                    <th>
                                        <label class="form-check-label">
                                            <input type="checkbox" id="select-all-checkbox" onclick="seleccionarTodos()"> 
                                        </label>
                                    </th>
                                    <th>Producto</th>                               
                                </tr> 
                            </thead>
                            
                            @foreach($productos_variaciones_datos as $p)
                            <tr>
                                <td>
                                    <label class="form-check-label">
                                        <input type="checkbox" value="{{$p->product_id.'|-|'.$p->referencia_variacion}}" onclick="actualizarSeleccion(this)"> 
                                    </label>
                                </td>
                                <td>{{$p->variaciones}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
              </div>
              
              <div class="modal-footer">
                  <button class="btn btn-secondary" onclick="guardarSeleccion()">CERRAR</button>
                  <button class="btn btn-primary" onclick="guardarSeleccion()">AGREGAR</button>
              </div>

          </div>
      </div>
  </div>
