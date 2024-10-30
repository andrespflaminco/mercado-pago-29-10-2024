
<script>
    // Variables para almacenar el estado de los checkboxes
    let selectedSucursalesCheckbox = {};
    let selectedVendedoresCheckbox = {};

    // Maneja el cambio en los checkboxes de sucursal
    document.querySelectorAll('.sucursal-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            var sucursalId = this.dataset.sucursalId;
            var usuarioCheckboxes = document.querySelectorAll('.usuario-checkbox.sucursal-' + sucursalId);
            
            // Almacena el estado del checkbox de sucursal
            selectedSucursalesCheckbox[sucursalId] = checkbox.checked;

            // Cambia el estado de los checkboxes de usuario relacionados
            usuarioCheckboxes.forEach(function(usuarioCheckbox) {
                usuarioCheckbox.checked = checkbox.checked;

                // Almacena el estado del checkbox de usuario
                let userId = usuarioCheckbox.id.replace('usuario-', '');
                selectedVendedoresCheckbox[userId] = usuarioCheckbox.checked;
            });
        });
    });

    // Maneja el cambio en los checkboxes de usuario
    document.querySelectorAll('.usuario-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            let userId = this.id.replace('usuario-', '');
            // Almacena el estado del checkbox de usuario
            selectedVendedoresCheckbox[userId] = checkbox.checked;
        });
    });

    // Enviar datos a Livewire cuando se haga clic en el botón Aplicar
    document.querySelector('.applyBtn').addEventListener('click', function() {
        // Enviar datos a Livewire
        @this.set('selectedSucursalesCheckbox', selectedSucursalesCheckbox);
        @this.set('selectedVendedoresCheckbox', selectedVendedoresCheckbox);

        // Llamar al método Livewire
        @this.call('AplicarFiltro');
    });
</script>


<script>
    // Agrega un evento clic a cada bot贸n de toggle
    document.querySelectorAll('.toggle-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            // Obtiene el ID del objetivo del bot贸n
            var targetId = this.dataset.target;
            // Encuentra el elemento con el ID especificado
            var targetElement = document.getElementById(targetId);
            // Cambia la visibilidad del elemento
            if (targetElement.style.display === "none") {
                targetElement.style.display = "block";
            } else {
                targetElement.style.display = "none";
            }
        });
    });
</script>