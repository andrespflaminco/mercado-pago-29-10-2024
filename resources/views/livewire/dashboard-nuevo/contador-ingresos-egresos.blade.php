						<div class="col-lg-4 col-sm-6 col-12">
							
							
							<a target="_blank" href="https://app.flamincoapp.com.ar/pagos?tipo_movimiento=ingreso&dateFrom={{$from}}&dateTo={{$to}}&sucursal_id={{$sucursal_elegida}}">

							<div class="dash-widget dash2">
								<div class="dash-widgetimg">
									<span><img src="{{ asset('assets/pos/img/icons/dash3.svg') }}" alt="img"></span>
								</div>
								<div class="dash-widgetcontent">
									<h5 >$<span class="counters">{{$ingresos_totales}}</span></h5>
									<h6>Ingresos Totales</h6>
								</div>
							</div>							    
							</a>
							
						</div>
						<div class="col-lg-4 col-sm-6 col-12">
						<a target="_blank" href="https://app.flamincoapp.com.ar/pagos?tipo_movimiento=egreso&dateFrom={{$from}}&dateTo={{$to}}&sucursal_id={{$sucursal_elegida}}">
							<div class="dash-widget dash3">
								<div class="dash-widgetimg">
									<span><img src="{{ asset('assets/pos/img/icons/dash4.svg') }}" alt="img"></span>
								</div>
								<div class="dash-widgetcontent">
									<h5 >$<span class="counters">{{$egresos_totales}}</span></h5>
									<h6>Egresos Totales</h6>
								</div>
							</div>
						</a>
						</div>

						<div class="col-lg-4 col-sm-6 col-12">
						<div class="dash-widget dash1">
								<div class="dash-widgetimg">
									<span><img src="{{ asset('assets/pos/img/icons/dash2.svg') }}" alt="img"></span>
								</div>
								<div class="dash-widgetcontent">
									<h5 >$<span class="counters">{{$ganancias_totales}}</span></h5>
									<h6>Dinero Total</h6>
								</div>
							</div>
						</div>