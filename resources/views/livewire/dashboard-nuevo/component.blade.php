
	
<div class="row sales layout-top-spacing" style="background: #fafbfe !important; z-index: 9999 !important;">
<div style="position:fixed !important; background: #fafbfe !important; z-index: 9999 !important;">
@include('livewire.dashboard-nuevo.filtros')
</div>
<div style="margin-top:50px !important;">

@include('livewire.dashboard-nuevo.filtros-tipo-grafico')    
@if($ver == "ventas")
@include('livewire.dashboard-nuevo.dash-ventas')     
@endif

@if($ver == "ingresos-gastos")
@include('livewire.dashboard-nuevo.dash-ingresos-egresos')     
@endif

@if($ver == "stock")
@include('livewire.dashboard-nuevo.dash-stock')     
@endif
    
</div>
         

</div>

				
			



    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
    <script src="assets/js/scrollspyNav.js"></script>
    <script src="plugins/apex/apexcharts.min.js"></script>
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->


 <script>
        // Obtener la tabla y filas
        var tabla = document.getElementById('miTabla');
        var filas = Array.from(tabla.getElementsByTagName('tr'));

        // Ordenar las filas por la segunda columna (índice 1, que es 'Nombre')
        filas.sort(function(a, b) {
            var nombreA = a.cells[2].textContent;
            var nombreB = b.cells[2].textContent;
            return nombreA.localeCompare(nombreB);
        });

        // Reorganizar las filas en la tabla
        for (var i = 0; i < filas.length; i++) {
            tabla.tBodies[0].appendChild(filas[i]);
        }
    </script>

@include('livewire.dashboard-nuevo.script-ventas')

@include('livewire.dashboard-nuevo.script-ingresos-egresos')
