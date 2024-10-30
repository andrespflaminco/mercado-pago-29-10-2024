<div wire:ignore.self class="modal fade" id="DetalleProducto" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="max-width: 500px; margin: 1.75rem auto;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detalle del Producto</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 text-center">
                        <!-- Imagen del producto -->
                        @if($imagen_ver)
                        <img src="{{ asset('storage/products/' . $imagen_ver ) }}" alt="Imagen del producto" class="img-fluid rounded mb-3" style="max-height: 200px;">
                        @else
                        <img src="https://app.flamincoapp.com.ar/storage/products/noimg.png" alt="Imagen del producto" class="img-fluid rounded mb-3" style="max-height: 200px;">
                        @endif
                    </div>
                    <div class="col-12 text-center">
                    <!-- Tabla de detalles del producto -->
                    <table style="width: 100%; border-collapse: collapse;">
                    <tbody>
                        <tr>
                            <th style="border: 1px solid #eee; padding: 8px; text-align: left;">Nombre:</th>
                            <td style="border: 1px solid #eee; padding: 8px;">{{ $nombre_ver }}</td>
                        </tr>
                        <tr>
                            <th style="border: 1px solid #eee; padding: 8px; text-align: left;">Código de barras:</th>
                            <td style="border: 1px solid #eee; padding: 8px;">{{ $barcode_ver }}</td>
                        </tr>
                        @if($codigo_variacion_ver != 0)
                        <tr>
                            <th style="border: 1px solid #eee; padding: 8px; text-align: left;">SKU de la variacion:</th>
                            <td style="border: 1px solid #eee; padding: 8px;">{{ $codigo_variacion_ver }}</td>
                        </tr> 
                        @endif
                        <tr>
                            <th style="border: 1px solid #eee; padding: 8px; text-align: left;">Categoría:</th>
                            <td style="border: 1px solid #eee; padding: 8px;">{{ $categoria_ver }}</td>
                        </tr>
                        <tr>
                            <th style="border: 1px solid #eee; padding: 8px; text-align: left;">Marca:</th>
                            <td style="border: 1px solid #eee; padding: 8px;">{{ $marca_ver }}</td>
                        </tr>
                        <tr>
                            <th style="border: 1px solid #eee; padding: 8px; text-align: left;">Etiquetas:</th>
                            <td style="border: 1px solid #eee; padding: 8px;">{{ $etiquetas_ver }}</td>
                        </tr>

                    </tbody>
                </table>
                </div>
                

                </div>
                
            </div>

            <div class="modal-footer">
                <button type="button" wire:click.prevent="resetUIDetalleProducto()" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
