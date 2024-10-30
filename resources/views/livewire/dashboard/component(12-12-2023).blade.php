
	
<div class="row sales layout-top-spacing">

<div class="row">
    <div class="col-12">
    <div class="card" style="padding:15px;">
    <div class="row">
    <div class="col-2">
        <input type="text" id="date-range-picker" name="date_range" />
    </div>
    <div class="col-2"> 
    
    @if(auth()->user()->sucursal != 1)
	<div style="width: 90%; !important" class="dropdown">
		<button class="btn btn-white btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
		{{$nombre_sucursal}}
    	</button>
	    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
		    
		    <li>
		        <a href="javascript:void(0);" class="dropdown-item" wire:click="ElegirSucursal(0)">Todas las sucursales</a>
		    </li>
		    <li>
		        <a href="javascript:void(0);" class="dropdown-item" wire:click="ElegirSucursal('{{auth()->user()->id}}')">{{auth()->user()->name}}</a>
		    </li>
	        @foreach($sucursales as $s)
		    <li>
		        <a href="javascript:void(0);" class="dropdown-item" wire:click="ElegirSucursal('{{$s->sucursal_id}}')">{{$s->name}}</a>
		    </li>
		    @endforeach
		    
		</ul>
		</div>     
	@endif
	
    </div>        
    </div>

    

    </div>    
    </div>
</div>
<div class="row">
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget">
								<div class="dash-widgetimg">
									<span style="background-color: #63738112 !important;"><img  src="{{ asset('assets/pos/img/icons/dash1.svg') }}" alt="img"></span>
								</div>
								<div class="dash-widgetcontent">
									<h5 >$<span class="counters" data-count="{{$ventas_totales1}}">{{$ventas_totales}}</span></h5>
									<h6>Ventas Totales</h6>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget dash2">
								<div class="dash-widgetimg">
									<span><img src="{{ asset('assets/pos/img/icons/dash3.svg') }}" alt="img"></span>
								</div>
								<div class="dash-widgetcontent">
									<h5 >$<span class="counters" data-count="{{$ingresos_totales1}}">{{$ingresos_totales}}</span></h5>
									<h6>Ingresos Totales</h6>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget dash3">
								<div class="dash-widgetimg">
									<span><img src="{{ asset('assets/pos/img/icons/dash4.svg') }}" alt="img"></span>
								</div>
								<div class="dash-widgetcontent">
									<h5 >$<span class="counters" data-count="{{$egresos_totales1}}">{{$egresos_totales}}</span></h5>
									<h6>Egresos Totales</h6>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget dash1">
								<div class="dash-widgetimg">
									<span><img src="{{ asset('assets/pos/img/icons/dash2.svg') }}" alt="img"></span>
								</div>
								<div class="dash-widgetcontent">
									<h5 >$<span class="counters" data-count="{{$ganancias_totales1}}">{{$ganancias_totales}}</span></h5>
									<h6>Ganancias Totales</h6>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-sm-6 col-12 d-flex">
							<a href="{{url('clientes')}}" target="_blank" class="dash-count">
								<div class="dash-counts">
									<h4>{{$cantidad_clientes}}</h4>
									<h5>Clientes</h5>
								</div>
								<div class="dash-imgs">
									<i data-feather="user"></i> 
								</div>
							</a>
						</div>
						<div class="col-lg-3 col-sm-6 col-12 d-flex">
							<a href="{{url('proveedores')}}" target="_blank" class="dash-count das1">
								<div class="dash-counts">
									<h4>{{$cantidad_proveedores}}</h4>
									<h5>Proveedores</h5>
								</div>
								<div class="dash-imgs">
									<i data-feather="user-check"></i> 
								</div>
							</a>
						</div>
						<div class="col-lg-3 col-sm-6 col-12 d-flex">
							<a href="{{url('reports')}}" target="_blank" class="dash-count das2">
								<div class="dash-counts">
									<h4>{{$cantidad_facturas_ventas}}</h4>
									<h5>Cantidad de Ventas</h5>
								</div>
								<div class="dash-imgs">
									<i data-feather="file-text"></i>
								</div>
							</a>
						</div>
						<div class="col-lg-3 col-sm-6 col-12 d-flex">
							<a href="{{url('compras-resumen')}}" target="_blank" class="dash-count das3">
								<div class="dash-counts">
									<h4>{{$cantidad_facturas_compras}}</h4>
									<h5>Cantidad de Compras</h5>
								</div>
								<div class="dash-imgs">
									<i data-feather="file"></i>  
								</div>
							</a>
						</div>
					</div>
<!-- Button trigger modal -->

<div class="row">
    					<div class="col-lg-7 col-sm-12 col-12 d-flex">
							<div class="card flex-fill">
								<div class="card-header pb-0 d-flex justify-content-between align-items-center">
									<h5 class="card-title mb-0">Ventas</h5>
									<div class="graph-sets">
										<ul>
											<li>
												<span>Ventas</span>
											</li>
											<li hidden>
												<span>Purchase</span>
											</li>
										</ul>
										<div hidden class="dropdown">
											<button class="btn btn-white btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
												Ventas
											</button>
											<ul style="max-height:350px !important;" class="dropdown-menu" aria-labelledby="dropdownMenuButton">
												<li>
													<a href="javascript:void(0);" class="dropdown-item">Ventas</a>
												</li>
												<li>
													<a href="javascript:void(0);" class="dropdown-item">Compras</a>
												</li>	
												<li>
													<a href="javascript:void(0);" class="dropdown-item">Ingresos vs Egresos</a>
												</li>	
											</ul>
										</div>
										<div hidden class="dropdown">
											<button class="btn btn-white btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
												Mensual
											</button>
											<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
												<li>
													<a href="javascript:void(0);" class="dropdown-item">Mensual</a>
												</li>
												<li>
													<a href="javascript:void(0);" class="dropdown-item">Diario</a>
												</li>			
											</ul>
										</div>
									</div>
									
								</div>
							   <div class="widget-content">
                                <div class="tabs tab-content">
                                    <div id="content_1" class="tabcontent">
                                        <div id="sales_charts"></div>
                                    </div>
                                </div>
                            </div>
							</div>
						</div>
						<div class="col-lg-5 col-sm-12 col-12 d-flex">
							<div class="card flex-fill">
								<div class="card-header pb-0 d-flex justify-content-between align-items-center">
									<h4 class="card-title mb-0">Ventas por metodos de pago</h4>
									<div class="dropdown">
										<a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false" class="dropset">
											<i class="fa fa-ellipsis-v"></i>
										</a>
										<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" >
											<li>
												<a href="productlist.html" class="dropdown-item">Metodo de pago</a>
											</li>
											<li>
												<a href="addproduct.html" class="dropdown-item">Total</a>
											</li>
										</ul>
									</div>
								</div>
								

    
								<div class="card-body">
									<div class="table-responsive dataview">
										<table id="MetodosPago" class="table" id="miTabla">
											<thead >
												<tr>
													<th>Banco</th>
													<th>Metodo de pago</th>
													<th>Total</th>
												</tr>
											</thead>
											<tbody>
											    @foreach($metodos_pago as $mp)
												<tr >
													<td>
													{{$mp->banco}}
													</td>
													<td>{{$mp->nombre}}</td>
													<td>$ {{ number_format($mp->total,2)}}</td>
												</tr>
												@endforeach
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
        
        <div class="row">
            <div class="col-12">
                <div class="card mb-0">
						<div class="card-body">
							<h4 class="card-title">Productos mas vendidos</h4>
							<div style="height: 350px;" class="table-responsive dataview">
								<table class="table">
									<thead>
										<tr>
											<th>Codigo producto</th>
											<th>Nombre</th>
											<th>Cantidad</th>
											<th>Total vendido</th>
										</tr>
									</thead>
									<tbody>
									    @foreach( $productos as $p)
												<tr>
													<td>{{$p->barcode}}</td>
													<td>
														<a href="productlist.html">{{$p->product}}</a>
													</td>
													<td>{{number_format($p->quantity,0)}} </td>
													<td>$ {{ number_format($p->total,2)}}</td>
												</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
            </div>
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

<script type="text/javascript">
 
 // Datos de ejemplo (reemplázalos con tus datos reales)
  var dataTotal = {!!$data_total!!};
  var dataMesAjustado = {!!$data_mes!!};

  // Ajusta los datos para mostrar solo dos decimales en data_total y formato de número
  var dataTotalAjustado = dataTotal.map(function(valor) {
    return parseFloat(valor.toFixed(2)); // Aplica formato de número
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
          return '$' + value.toFixed(2);
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
    return parseFloat(valor.toFixed(2)); // Aplica formato de número
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
          return '$' + value.toFixed(2);
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
    document.addEventListener('DOMContentLoaded', function(){


        flatpickr(document.getElementsByClassName('flatpickr'),{
            enableTime: false,
            dateFormat: 'Y-m-d',
            locale: {
                firstDayofWeek: 1,
                weekdays: {
                    shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                    longhand: [
                    "Domingo",
                    "Lunes",
                    "Martes",
                    "Miércoles",
                    "Jueves",
                    "Viernes",
                    "Sábado",
                    ],
                },
                months: {
                    shorthand: [
                    "Ene",
                    "Feb",
                    "Mar",
                    "Abr",
                    "May",
                    "Jun",
                    "Jul",
                    "Ago",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dic",
                    ],
                    longhand: [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Septiembre",
                    "Octubre",
                    "Noviembre",
                    "Diciembre",
                    ],
                },

            }

        })


        $('.tagging').select2({
                        tags: true
                    });

                $('#select2-dropdown').on('change', function(e) {
                  var id = $('#select2-dropdown').select2('val');
                  var name = $('#select2-dropdown option:selected').text();
                  @this.set('clientesSelectedName', name);
                  @this.set('ClienteSeleccionado', ''+id);
                  @this.emit('locationUsersSelected', $('#select2-dropdown').select2('val'));
                });

                $('#select2-dropdown2').on('change', function(e) {
                  var id = $('#select2-dropdown2').select2('val');
                  var name = $('#select2-dropdown2 option:selected').text();
                  @this.set('UsuarioSelectedName', name);
                  @this.set('usuarioSeleccionado', ''+id);
                  @this.emit('UsuarioSelected', $('#select2-dropdown2').select2('val'));
                });
        //eventos
        window.livewire.on('show-modal', Msg =>{
            $('#modalDetails').modal('show')
        })
        var total = $('#suma_totales').val();
        $('#ver_totales').html('Ventas: '+total);
    })
    window.livewire.on('modal-show', msg => {
      $('#theModal').modal('show')
    });

    function rePrint(saleId)
    {
        window.open("print://" + saleId,  '_self').close()
    }
</script>


