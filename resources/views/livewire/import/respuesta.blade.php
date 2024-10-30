                              <div class="row mt-5">
                                <div class="col-sm-12 col-md-12">
                                    <div class="form-group custom-file text-center">
                                        <h6 class="text-center">
                                            
                                            @if($respuesta == "success")
                                            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#28a745" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            IMPORTACION COMPLETADA CORRECTAMENTE
                                            @endif
                                            
                                            @if($respuesta == "error")
                                            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke=" #dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-triangle"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                            <br><br>
                                            SE HA PRODUCIDO UN ERROR
                                            @endif
                                        </h6>
                                        @if($respuesta == "error")
                                        <p>Intente nuevamente...</p>
                                        @endif
                                        
                                        
                                        <br><br><br>
                                        <a href=" {{ url('import') }}" class="btn btn-submit">VOLVER</a>
                                    </div>
                                </div>
                            </div>