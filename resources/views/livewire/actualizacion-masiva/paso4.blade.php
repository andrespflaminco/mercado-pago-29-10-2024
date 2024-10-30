
                                                <div style="{{$paso4}}">
                                                
                                                                                                
                                                <div style="padding-top:40px; padding-bottom:40px; ">
                                                    Estado de procesamiento: 
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">{{ round($progress) }}%</div>
                                                    </div>
                                           
                                                </div>
                                                
                                                
                                                    @if ($progress == 100)
                                                        <table class="table mt-4">
                                                            <thead>
                                                                <tr>
                                                                    <th>Nombre</th>
                                                                    <th>Código de barras</th>
                                                                    <th>Precio Anterior</th>
                                                                    <th>Precio Nuevo</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($productos_actualizados as $producto)
                                                                    <tr>
                                                                        <td>{{ $producto['name'] }}</td>
                                                                        <td>{{ $producto['barcode'] }}</td>
                                                                        <td>$ {{ number_format($producto['precio_anterior'],2,",",".") }}</td>
                                                                        <td>$ {{ number_format($producto['precio_nuevo'],2,",",".") }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    @endif  
                                                    
                                                <ul class="pager wizard twitter-bs-wizard-pager-link">
                                                    <li class="next"><a class="btn btn-submit" href="{{ url('actualizacion-masiva') }}">VOLVER </a></li>
                                                </ul>
                                                </div>