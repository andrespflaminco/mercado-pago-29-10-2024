                    <div class="mt-2 mb-2">
                    @if($datos_punto_venta_elegido != null)
                    <button style="font-size:14px !important; background:white; font-weight: 400;  line-height: 1.5; color: #212529; background-color: #fff; background-clip: padding-box; border: 1px solid #ced4da;" class="btn btn-outline-secondary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block; max-width: 90%;">{{$datos_punto_venta_elegido->razon_social}} - PTO: {{$datos_punto_venta_elegido->pto_venta}} - (CUIT: {{$datos_punto_venta_elegido->cuit}} )</span>
                    </button>      
                    <div class="dropdown-menu">
                        @foreach($puntos_venta_listado as $p)
                            <a class="dropdown-item w-100" href="javascript:void(0);" wire:click.prevent="ElegirPuntoVenta({{$p->id}})"> {{$p->razon_social}} - (CUIT: {{$p->cuit}} ) - PTO: {{$p->pto_venta}}  </a>
                        @endforeach
                    </div>   
                    @else

                    @endif
                    </div>    
