<script>
    document.addEventListener('DOMContentLoaded', function(){

    $('#select2-etiquetas').on('change', function(e) {
      var id = $('#select2-etiquetas').select2('val');
      var name = $('#select2-etiquetas:selected').text();
      @this.set('etiqueta_nombre', name);
      @this.set('etiqueta_id', id);
      @this.emit('EtiquetasSeleccionadas', $('#select2-etiquetas').select2('val'));
    });
        
    $('#select2-buscar-etiquetas').on('change', function(e) {
      var id_buscar = $('#select2-buscar-etiquetas').select2('val');
      var name_buscar = $('#select2-buscar-etiquetas:selected').text();
      @this.set('etiqueta_buscar_nombre', name_buscar);
      @this.set('etiqueta_buscar_id', id_buscar);
      @this.emit('SearchEtiquetas', $('#select2-buscar-etiquetas').select2('val'));
    });
    
    $('#forma-pago').on('change', function(e) {
      @this.emit('FormaPagoSeleccionado', $('#forma-pago').select2('val'));
    });
    
    });

    
    document.addEventListener('livewire:load', function () {
        Livewire.hook('message.processed', function () {
            $('#select2-etiquetas').select2({
                data: @json($etiqueta_json),
                tags: true, // Habilitar la creación de etiquetas si es necesario
            });
            
            $('#select2-buscar-etiquetas').select2({
                data: @json($etiqueta_json),
                tags: true, // Habilitar la creación de etiquetas si es necesario
            });
            
            $('#forma-pago').select2({
            data: @json($forma_pago_json),
            tags: true,
            language: 'es',
            maximumSelectionLength: 1 // Establece el límite de selección a 2 elementos
            });

            
        });
        
        
    Livewire.on('etiquetasCargadas', function (etiquetas) {
        // Actualizar el JSON del Select2 con las nuevas etiquetas
        $('#select2-etiquetas').empty().select2({
            data: etiquetas,
            tags: true,
        });
        
        $('#select2-buscar-etiquetas').empty().select2({
            data: etiquetas,
            tags: true,
        });
    });
    
    Livewire.on('etiquetasCargadasEdit', function (etiquetas,etiquetasSeleccionadas) {
        // Actualizar el JSON del Select2 con las nuevas etiquetas
        $('#select2-etiquetas').empty().select2({
            data: etiquetas,
            tags: true,
        });
        
        $('#select2-buscar-etiquetas').empty().select2({
            data: etiquetas,
            tags: true,
        });
        // Asignar valores ya seleccionados

        $('#select2-etiquetas').val(etiquetasSeleccionadas).trigger('change');
        });
        
    Livewire.on('LimpiarEtiquetas', function (etiquetas) {
        // Actualizar el JSON del Select2 con las nuevas etiquetas
        $('#select2-buscar-etiquetas').empty().select2({
            data: etiquetas,
            tags: true,
        });
        // Asignar valores ya seleccionados

        $('#select2-buscar-etiquetas').val([]).trigger('change');
        });
        
    Livewire.on('FormasPagoCargadasEdit', function (etiquetas,etiquetasSeleccionadas) {
        // Actualizar el JSON del Select2 con las nuevas etiquetas
        $('#forma-pago').empty().select2({
            data: etiquetas,
            tags: true, 
            language: 'es',
            maximumSelectionLength: 1 // Establece el límite de selección a 2 elementos
           
        });
        // Asignar valores ya seleccionados

        $('#forma-pago').val(etiquetasSeleccionadas).trigger('change');
        });
    
    });

    //document.addEventListener('DOMContentLoaded', function(){

    //});
</script>	