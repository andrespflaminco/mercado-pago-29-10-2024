

<script>

        var options = {
            chart: {
                type: 'bar',
                height: 350
            },
            colors: ['#28a745', '#dc3545'],
            series: [{
                name: 'Ingresos',
                color: '#28a745', // Color verde (success)
                data: {!!$total_ingresos_grafico!!}
            }, {
                name: 'Egresos',
                color: '#dc3545', // Color rojo (danger)
                data: {!!$total_gastos_grafico!!}
                
            }],
            xaxis: {
                categories: {!!$mes!!}
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart-finanzas"), options);
        chart.render();

</script>

<script>
    document.addEventListener('livewire:load', function() {
      window.livewire.on('mes-ingresos', (chartData) => {
      
       // Datos de ejemplo (reemplázalos con tus datos reales)
      var dataTotalIngresos = chartData.total_ingresos_grafico;
      var dataTotalGastos = chartData.total_gastos_grafico;
      
      console.log(dataTotalIngresos);
      
        var options = {
            chart: {
                type: 'bar',
                height: 350
            },
            colors: ['#28a745', '#dc3545'],
            series: [{
                name: 'Ingresos',
                color: '#28a745', // Color verde (success)
                data: chartData.total_ingresos_grafico
            }, {
                name: 'Egresos',
                color: '#dc3545', // Color rojo (danger)
                data:  chartData.total_gastos_grafico
            }],
            xaxis: {
                categories:  chartData.mes
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart-finanzas"), options);
        chart.render();

    });
    });
    
</script>