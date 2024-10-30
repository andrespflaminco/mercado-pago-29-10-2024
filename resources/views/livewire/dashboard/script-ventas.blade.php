<script>
    // Obtenemos todos los datos
    var ventas_vendedor = {!!$data_ventas_vendedor!!};
    var vendedor  = {!!$data_vendedor!!};
    var promedio = {!!$promedio_ventas_vendedores!!}; 

    var options = {
        series: [{
            name: 'Ventas ($)',
            data: ventas_vendedor
        }],
        chart: {
            type: 'bar',
            height: 400
        },
        colors: ['rgba(0, 143, 251, 0.85)'],
        plotOptions: {
            bar: {
                horizontal: true,
                barWidth: '50%', // Cambiar este valor para ajustar el ancho de las barras
                borderRadius: 4, // Borde redondeado
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return '$ ' +  val.toLocaleString(); // Formato con separador de miles
            }
        },
        xaxis: {
            categories: vendedor,
            labels: {
                formatter: function(val) {
                    return '$ ' +  val.toLocaleString(); // Formato con separador de miles
                }
            }
        },
        grid: {
            xaxis: {
                lines: {
                    show: true
                }
            }
        },
        yaxis: {},
        states: {
            hover: {
                filter: 'none' // Eliminar el efecto de sombra al pasar el cursor sobre las barras
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            enabled: true
        },
        stroke: {
            width: 3,
            colors: ['rgba(0, 143, 251, 1)'],
        },
        annotations: {
            xaxis: [{
                x: promedio,
                borderColor: '#00e396',
                strokeDashArray: 0, // Opcional: eliminar cualquier patrón de guiones
                width: 7, // Aumentar el ancho de la línea
                label: {
                    borderColor: '#00e396',
                    style: {
                        color: '#fff',
                        background: '#00e396',
                    },
                    text: 'Promedio',
                }
            }],
        },
    };

    var chart = new ApexCharts(document.querySelector("#vendedores"), options);
    chart.render();

    
    document.addEventListener('livewire:load', function() {
    window.livewire.on('ventas-vendedor', (chartData) => {

    var ventas_vendedor = chartData.data_ventas_vendedor;
    var promedio = chartData.promedio_ventas_vendedores;

    var options = {
        series: [{
            name: 'Ventas ($)',
            data: ventas_vendedor
        }],
        chart: {
            type: 'bar',
            height: 400
        },
        colors: ['rgba(0, 143, 251, 0.85)'],
        plotOptions: {
            bar: {
                horizontal: true,
                barWidth: '50%', // Cambiar este valor para ajustar el ancho de las barras
                borderRadius: 4, // Borde redondeado
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return '$ ' +  val.toLocaleString(); // Formato con separador de miles
            }
        },
        xaxis: {
            categories: chartData.data_vendedor,
            labels: {
                formatter: function(val) {
                    return '$ ' +  val.toLocaleString(); // Formato con separador de miles
                }
            }
        },
        grid: {
            xaxis: {
                lines: {
                    show: true
                }
            }
        },
        yaxis: {},
        states: {
            hover: {
                filter: 'none' // Eliminar el efecto de sombra al pasar el cursor sobre las barras
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            enabled: true
        },
        stroke: {
            width: 3,
            colors: ['rgba(0, 143, 251, 1)'],
        },
        annotations: {
            xaxis: [{
                x: promedio,
                borderColor: '#00e396',
                strokeDashArray: 0, // Opcional: eliminar cualquier patrón de guiones
                width: 7, // Aumentar el ancho de la línea
                label: {
                    borderColor: '#00e396',
                    style: {
                        color: '#fff',
                        background: '#00e396',
                    },
                    text: 'Promedio',
                }
            }],
        },
    };

    var chart = new ApexCharts(document.querySelector("#vendedores"), options);
    chart.render();
    
    });
    });
        
</script>



<script>
    function validarDatos(datos) {
  return datos.every(valor => typeof valor === 'number' && !isNaN(valor));
}

// Obtenemos todos los datos
var data_canal = {!!$data_canal!!};
var data_total_canal  = {!!$data_total_canal!!};

console.log(data_canal, data_total_canal);

// Ajusta los datos para mostrar solo dos decimales en data_total y formato de número
var dataTotalCanalAjustado = data_total_canal.map(function(valor) {
  return parseFloat(valor);
});

if (!validarDatos(dataTotalCanalAjustado)) {
  console.error('Datos inválidos en dataTotalCanalAjustado:', dataTotalCanalAjustado);
} else {
  var options = {
    series: dataTotalCanalAjustado,
    chart: {
      type: 'donut',
    },
    labels: data_canal, // Asignar los títulos a las etiquetas
    responsive: [{
      breakpoint: 480,
      options: {
        chart: {
          width: 200
        },
        legend: {
          position: 'bottom'
        }
      }
    }]
  };

  var chart = new ApexCharts(document.querySelector("#canal"), options);
  chart.render();
}

document.addEventListener('livewire:load', function() {
  window.livewire.on('canal', (chartData) => {
    // Verifica la estructura de chartData
    console.log(chartData);

    var dataTotalCanal = chartData.data_total_canal.map(function(valor) {
      return parseFloat(valor);
    });

    if (!validarDatos(dataTotalCanal)) {
      console.error('Datos inválidos en chartData.data_total_canal:', dataTotalCanal);
    } else {
      var options = {
        series: dataTotalCanal,
        chart: {
          type: 'donut',
        },
        labels: chartData.data_canal, // Asignar los títulos a las etiquetas
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'bottom'
            }
          }
        }],
        tooltip: {
          y: {
            formatter: function(value) {
              return value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
          }
        }
      };

      var chart = new ApexCharts(document.querySelector("#canal"), options);
      chart.render();
    }
  });
});

</script> 

<script>

    // Seteo 
   
    // Obtenemos todos los datos
    var nombre_producto = {!!$nombre_producto!!};

    var totalVentas  = {!!$venta_producto_rentabilidad!!};
    
    // Ajusta los datos para mostrar solo dos decimales en data_total y formato de número
      var totalVentas = totalVentas.map(function(valor) {
        return parseFloat(valor.toLocaleString()); // Aplica formato de número
    });
  
    var totalCantidad  = {!!$cantidad_producto_rentabilidad!!};
   
    var RentabilidadVentas  = {!!$rentabilidad_producto_rentabilidad!!}; 
    var RentabilidadMargen = {!!$porcentaje_rentabilidad_producto_rentabilidad!!}; 

    var switch_ventas_unidades  = {!!$switch_ventas_unidades!!};
    var switch_margen_rentabilidad  = {!!$switch_margen_rentabilidad!!};
    
    var maxTotalVentas = Math.max(...totalVentas);
    var maxRentabilidadVentas = Math.max(...RentabilidadVentas);
    var maxValorY = Math.max(maxTotalVentas, maxRentabilidadVentas);

    if(switch_ventas_unidades == 1 && switch_margen_rentabilidad == 2){
    var  maxValorYAplicar = maxValorY;
    } else {
    var  maxValorYAplicar = undefined;    
    }
    
    
    // Seteamos lo que se va a usar 
    var dataToUse1;
    var Titulo1;

    if (switch_ventas_unidades === 1) {
      dataToUse1 = totalVentas;
      Titulo1 = 'Ventas ($)'
    } else {
      dataToUse1 = totalCantidad;
      Titulo1 = 'Unidades vendidas'
    } 

    // Seteamos lo que se va a usar 
    var dataToUse2;
    var Titulo2;

    if (switch_margen_rentabilidad === 1) {
      dataToUse2 = RentabilidadMargen;
      Titulo2 = 'Margen Rentabilidad (%)'
    } else {
      dataToUse2 = RentabilidadVentas;
      Titulo2 = 'Rentabilidad ($)'
    } 


    var options = {
      chart: {
        type: 'line',
        height: 350
      },
      series: [
      {
        name: Titulo1,
        type: 'bar',
        data: dataToUse1
      },
      {
        name: Titulo2,
        type: 'line',
        data: dataToUse2
      }],
      xaxis: {
        categories: nombre_producto
      },
      yaxis: [{
        title: {
          text: Titulo1
        },
        labels: {
        formatter: function(val) {
        return '$ ' +  val.toLocaleString(); // Formato con separador de miles
        }
        },
        max: maxValorYAplicar // Establece el mismo límite superior para ambas y-axis
      }, {
        opposite: true,
        title: {
          text: Titulo2
        },
        labels: {
        formatter: function(val) {
        return  val.toLocaleString() + '%'; // Formato con separador de miles
        }
        },
        max: maxValorYAplicar, // Establece el mismo límite superior para ambas y-axis
      }]
    }

    var chart = new ApexCharts(document.querySelector("#rentabilidad-producto"), options);
    chart.render();
    
    
    document.addEventListener('livewire:load', function() {
    window.livewire.on('rentabilidad-producto', (chartData) => {
          
    // Datos de ejemplo (reemplázalos con tus datos reales)
    var nombre_producto = chartData.nombre_producto;
    var totalSales  = chartData.venta_producto_rentabilidad; 
    var profitMarginPercentage = chartData.porcentaje_rentabilidad_producto_rentabilidad; 


    var totalVentas  = chartData.venta_producto_rentabilidad
    var totalCantidad  = chartData.cantidad_producto_rentabilidad;

    // Ajusta los datos para mostrar solo dos decimales en data_total y formato de número
    var totalVentas = totalVentas.map(function(valor) {
        return parseFloat(valor.toLocaleString()); // Aplica formato de número
    });   
    
    var RentabilidadVentas  = chartData.rentabilidad_producto_rentabilidad; 
    var RentabilidadMargen = chartData.porcentaje_rentabilidad_producto_rentabilidad; 

    var switch_ventas_unidades  = chartData.switch_ventas_unidades;
    var switch_margen_rentabilidad  = chartData.switch_margen_rentabilidad;
    
    var maxTotalVentas = Math.max(...totalVentas);
    var maxRentabilidadVentas = Math.max(...RentabilidadVentas);
    var maxValorY = Math.max(maxTotalVentas, maxRentabilidadVentas);

    if(switch_ventas_unidades == 1 && switch_margen_rentabilidad == 2){
    var  maxValorYAplicar = maxValorY;
    } else {
    var  maxValorYAplicar = undefined;    
    }

    // Seteamos lo que se va a usar 
    var dataToUse1;
    var Titulo1;

    if (switch_ventas_unidades === 1) {
      dataToUse1 = totalVentas;
      Titulo1 = 'Ventas ($)'
    } else {
      dataToUse1 = totalCantidad;
      Titulo1 = 'Unidades vendidas'
    } 

    // Seteamos lo que se va a usar 
    var dataToUse2;
    var Titulo2;

    if (switch_margen_rentabilidad === 1) {
      dataToUse2 = RentabilidadMargen;
      Titulo2 = 'Margen Rentabilidad (%)'
    } else {
      dataToUse2 = RentabilidadVentas;
      Titulo2 = 'Rentabilidad ($)'
    } 


    var options = {
      chart: {
        type: 'line',
        height: 350
      },
      series: [
      {
        name: Titulo1,
        type: 'bar',
        data: dataToUse1
      },
      {
        name: Titulo2,
        type: 'line',
        data: dataToUse2
      }],
      xaxis: {
        categories: nombre_producto
      },
      yaxis: [{
        title: {
          text: Titulo1
        },
        labels: {
        formatter: function(val) {
        return '$ ' +  val.toLocaleString(); // Formato con separador de miles
        }
        },
        max: maxValorYAplicar // Establece el mismo límite superior para ambas y-axis
      }, {
        opposite: true,
        title: {
          text: Titulo2
        },
        labels: {
        formatter: function(val) {
        return val.toLocaleString(); // Formato con separador de miles
        }
        },
        max: maxValorYAplicar // Establece el mismo límite superior para ambas y-axis
      }]
    }

    var chart = new ApexCharts(document.querySelector("#rentabilidad-producto"), options);
    chart.render();
    




      });
    });
    
    
  </script>



  <script>

    // Obtenemos todos los datos
    var nombre_categoria = {!!$nombre_categoria!!};

    var totalVentasCategoria  = {!!$venta_categoria_rentabilidad!!};
    var totalCantidadCategoria  = {!!$cantidad_categoria_rentabilidad!!};

    // Ajusta los datos para mostrar solo dos decimales en data_total y formato de número
      var totalVentasCategoria = totalVentasCategoria.map(function(valor) {
        return parseFloat(valor.toLocaleString()); // Aplica formato de número
    });
    
    var RentabilidadVentasCategoria  = {!!$rentabilidad_categoria_rentabilidad!!}; 
    var RentabilidadMargenCategoria = {!!$porcentaje_rentabilidad_categoria_rentabilidad!!}; 

    var switch_ventas_unidades_categoria  = {!!$switch_ventas_unidades_categoria!!};
    var switch_margen_rentabilidad_categoria  = {!!$switch_margen_rentabilidad_categoria!!};


    // Seteamos lo que se va a usar 
    var dataToUse1Categoria;
    var Titulo1Categoria;

    if (switch_ventas_unidades_categoria === 1) {
      dataToUse1Categoria = totalVentasCategoria;
      Titulo1Categoria = 'Ventas ($)'
    } else {
      dataToUse1Categoria = totalCantidadCategoria;
      Titulo1Categoria = 'Unidades vendidas'
    } 

    // Seteamos lo que se va a usar 
    var dataToUse2Categoria;
    var Titulo2Categoria;

    if (switch_margen_rentabilidad_categoria === 1) {
      dataToUse2Categoria = RentabilidadMargenCategoria;
      Titulo2Categoria = 'Margen Rentabilidad (%)'
    } else {
      dataToUse2Categoria = RentabilidadVentasCategoria;
      Titulo2Categoria = 'Rentabilidad ($)'
    } 

    
    var maxTotalVentasCategoria = Math.max(...totalVentasCategoria);
    var maxRentabilidadVentasCategoria = Math.max(...RentabilidadVentasCategoria);
    var maxValorYCategoria = Math.max(maxTotalVentasCategoria, maxRentabilidadVentasCategoria);
    
    if(switch_ventas_unidades_categoria == 1 && switch_margen_rentabilidad_categoria == 2){
    var  maxValorYAplicarCategoria = maxValorYCategoria;
    } else {
    var  maxValorYAplicarCategoria = undefined;    
    }
    
    
    var options = {
      chart: {
        type: 'line',
        height: 350
      },
      series: [
      {
        name: Titulo1Categoria,
        type: 'bar',
        data: dataToUse1Categoria
      },
      {
        name: Titulo2Categoria,
        type: 'line',
        data: dataToUse2Categoria
      }],
      xaxis: {
        categories: nombre_categoria
      },
      yaxis: [{
        title: {
          text: Titulo1Categoria
        },
        labels: {
        formatter: function(val) {
        return '$ ' +  val.toLocaleString(); // Formato con separador de miles
        }
        },
        max: maxValorYAplicarCategoria // Establece el mismo límite superior para ambas y-axis
      }, {
        opposite: true,
        title: {
          text: Titulo2Categoria
        },
        labels: {
        formatter: function(val) {
        return val.toLocaleString(); // Formato con separador de miles
        }
        },
        max: maxValorYAplicarCategoria // Establece el mismo límite superior para ambas y-axis
      }]
    }

    var chart = new ApexCharts(document.querySelector("#rentabilidad-categoria"), options);
    chart.render();
    
    
    document.addEventListener('livewire:load', function() {
      window.livewire.on('rentabilidad-categoria', (chartData) => {
          
    // Datos de ejemplo (reemplázalos con tus datos reales)
    var nombre_categoria = chartData.nombre_categoria;
  
    var totalVentasCategoria  = chartData.venta_categoria_rentabilidad
    var totalCantidadCategoria  = chartData.cantidad_categoria_rentabilidad;

    // Ajusta los datos para mostrar solo dos decimales en data_total y formato de número
      var totalVentasCategoria = totalVentasCategoria.map(function(valor) {
        return parseFloat(valor.toLocaleString()); // Aplica formato de número
    });
    
    var RentabilidadVentasCategoria  = chartData.rentabilidad_categoria_rentabilidad; 
    var RentabilidadMargenCategoria = chartData.porcentaje_rentabilidad_categoria_rentabilidad; 

    var switch_ventas_unidades_categoria  = chartData.switch_ventas_unidades_categoria;
    var switch_margen_rentabilidad_categoria  = chartData.switch_margen_rentabilidad_categoria;
    

    // Seteamos lo que se va a usar 
    var dataToUse1Categoria;
    var Titulo1Categoria;

    if (switch_ventas_unidades_categoria === 1) {
      dataToUse1Categoria = totalVentasCategoria;
      Titulo1Categoria = 'Ventas ($)'
    } else {
      dataToUse1Categoria = totalCantidadCategoria;
      Titulo1Categoria = 'Unidades vendidas'
    } 

    // Seteamos lo que se va a usar 
    var dataToUse2Categoria;
    var Titulo2Categoria;

    if (switch_margen_rentabilidad_categoria === 1) {
      dataToUse2Categoria = RentabilidadMargenCategoria;
      Titulo2Categoria = 'Margen Rentabilidad (%)'
    } else {
      dataToUse2Categoria = RentabilidadVentasCategoria;
      Titulo2Categoria = 'Rentabilidad ($)'
    } 

    
    var maxTotalVentasCategoria = Math.max(...totalVentasCategoria);
    var maxRentabilidadVentasCategoria = Math.max(...RentabilidadVentasCategoria);
    var maxValorYCategoria = Math.max(maxTotalVentasCategoria, maxRentabilidadVentasCategoria);
    
    if(switch_ventas_unidades_categoria == 1 && switch_margen_rentabilidad_categoria == 2){
    var  maxValorYAplicarCategoria = maxValorYCategoria;
    } else {
    var  maxValorYAplicarCategoria = undefined;    
    }
    
    
    var options = {
      chart: {
        type: 'line',
        height: 350
      },
      series: [
      {
        name: Titulo1Categoria,
        type: 'bar',
        data: dataToUse1Categoria
      },
      {
        name: Titulo2Categoria,
        type: 'line',
        data: dataToUse2Categoria
      }],
      xaxis: {
        categories: nombre_categoria
      },
      yaxis: [{
        title: {
          text: Titulo1Categoria
        },
        labels: {
        formatter: function(val) {
        return '$ ' +  val.toLocaleString(); // Formato con separador de miles
        }
        },
        max: maxValorYAplicarCategoria // Establece el mismo límite superior para ambas y-axis
      }, {
        opposite: true,
        title: {
          text: Titulo2Categoria
        },
        labels: {
        formatter: function(val) {
        return val.toLocaleString(); // Formato con separador de miles
        }
        },
        max: maxValorYAplicarCategoria // Establece el mismo límite superior para ambas y-axis
      }]
    }

    var chart = new ApexCharts(document.querySelector("#rentabilidad-categoria"), options);
    chart.render();
    




      });
    });
    
    
  </script>

  
<script type="text/javascript">
 
 // Datos de ejemplo (reemplázalos con tus datos reales)
  var dataTotal = {!!$data_total!!};
  var dataMesAjustado = {!!$data_mes!!};

  // Ajusta los datos para mostrar solo dos decimales en data_total y formato de número
  var dataTotalAjustado = dataTotal.map(function(valor) {
    return valor.toLocaleString(); // Aplica formato de número
  });

        
	var options = {
		series: [{
		name: 'Ventas',
		data: dataTotal,
	  }],
	  colors: ['#28C76F'],
		chart: {
		type: 'bar',
		height: 300,
		stacked: true,
		
		zoom: {
		  enabled: true
		}
	  },
        dataLabels: {
          position: 'top', // Coloca las etiquetas de datos en la parte superior
          enabled: false // Desactiva las etiquetas dentro de las barras
        },
	  responsive: [{
		breakpoint: 280,
		options: {
		  legend: {
			position: 'bottom',
			offsetY: 0
		  }
		}
	  }],

	  plotOptions: {
		bar: {
		  horizontal: false,
		  columnWidth: '20%',
	//	  endingShape: 'rounded',
		  dataLabels: {
          position: 'top', // Coloca las etiquetas de datos en la parte superior
          enabled: false // Desactiva las etiquetas dentro de las barras
        },
		},
	  },
	  xaxis: {
		categories: dataMesAjustado,
	  },
	  yaxis: {
      labels: {
        formatter: function (value) {
          // Formatea el valor con separadores de miles y decimales y signo de dólar
          return value.toLocaleString();
        }
      }
      },
	  legend: {
		position: 'right',
		offsetY: 40
	  },
	  fill: {
		opacity: 1
	  }
	  };

	  var chart = new ApexCharts(document.querySelector("#sales_charts"), options);
	  chart.render();
	
</script>

<script>
    
    
    document.addEventListener('livewire:load', function() {
      window.livewire.on('mes', (chartData) => {

 // Datos de ejemplo (reemplázalos con tus datos reales)
  var dataTotal = chartData.data_total;
  var dataMesAjustado = chartData.data_mes;

  // Ajusta los datos para mostrar solo dos decimales en data_total y formato de número
  var dataTotalAjustado = dataTotal.map(function(valor) {
    return parseFloat(valor.toLocaleString()); // Aplica formato de número
  });
  
	var options = {
		series: [{
		name: 'Ventas',
		data: dataTotal,
	  }],
	  colors: ['#28C76F'],
		chart: {
		type: 'bar',
		height: 300,
		stacked: true,
		
		zoom: {
		  enabled: true
		}
	  },
        dataLabels: {
          position: 'top', // Coloca las etiquetas de datos en la parte superior
          enabled: false // Desactiva las etiquetas dentro de las barras
        },
	  responsive: [{
		breakpoint: 280,
		options: {
		  legend: {
			position: 'bottom',
			offsetY: 0
		  }
		}
	  }],

	  plotOptions: {
		bar: {
		  horizontal: false,
		  columnWidth: '20%',
	//	  endingShape: 'rounded',
		  dataLabels: {
          position: 'top', // Coloca las etiquetas de datos en la parte superior
          enabled: false // Desactiva las etiquetas dentro de las barras
        },
		},
	  },
	  xaxis: {
		categories: dataMesAjustado,
	  },
	  yaxis: {
      labels: {
        formatter: function (value) {
          // Formatea el valor con separadores de miles y decimales y signo de dólar
          return '$' + value.toLocaleString();
        }
      }
      },
	  legend: {
		position: 'right',
		offsetY: 40
	  },
	  fill: {
		opacity: 1
	  }
	  };

	  var chart = new ApexCharts(document.querySelector("#sales_charts"), options);
	  chart.render();


    });
    });
</script>

    <script>
    
        // Obtenemos todos los datos
        var meses = {!!$data_mes!!};
        var data_descuento  = {!!$data_descuento!!};
        var data_descuento_promo  = {!!$data_descuento_promo!!};   
        var data_total_ventas_descuento  = {!!$data_total_ventas_descuento!!}; 
        
        var options = {
          series: [{
          name: 'DESCUENTO',
          data: data_descuento
        }, {
          name: 'DESCUENTO PROMOCION',
          data: data_descuento_promo
        }],
          chart: {
          type: 'bar',
          height: 350,
          stacked: true,
          toolbar: {
            show: true
          },
          zoom: {
            enabled: true
          }
        },
        responsive: [{
          breakpoint: 480,
          options: {
            legend: {
              position: 'bottom',
              offsetX: -10,
              offsetY: 0
            }
          }
        }],
        plotOptions: {
          bar: {
            horizontal: false,
            borderRadius: 10,
            borderRadiusApplication: 'end', // 'around', 'end'
            borderRadiusWhenStacked: 'last', // 'all', 'last'
            dataLabels: {
              total: {
                enabled: true,
                style: {
                  fontSize: '13px',
                  fontWeight: 900
                }
              }
            }
          },
        },
        xaxis: {
          type: 'datetime',
          categories: meses,
        },
        fill: {
          opacity: 1
        }
        };

        var chart = new ApexCharts(document.querySelector("#descuentos"), options);
        chart.render();
    </script>