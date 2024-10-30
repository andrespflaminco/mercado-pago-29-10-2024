
						<div class="col-lg-4 col-sm-6 col-12">
							<div class="dash-widget dash3">
								<div class="dash-widgetimg">
									<span style="background-color: #63738112 !important;">
									    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-box"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
									</span>
								</div>
								<div class="dash-widgetcontent">
									<h5 ><span class="counters">{{$unidades_stock}}</span></h5>
									<h6>Unidades en Stock</h6>
								</div>
							</div>
						</div>
						<div class="col-lg-4 col-sm-6 col-12">
							<div class="dash-widget dash3">
								<div class="dash-widgetimg">
									<span>
									    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ea5455" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
									</span>
								</div>
								<div class="dash-widgetcontent">
									<h5 >$<span class="counters">{{number_format($costo_unidades_stock,2,",",".") }}</span></h5>
									<h6>Costo Total</h6>
								</div>
							</div>
						</div>
						<div class="col-lg-4 col-sm-6 col-12">
						
						
						<a target="_blank" href="https://app.flamincoapp.com.ar/products">
							<div class="dash-widget dash1">
								<div class="dash-widgetimg">
								<span><img src="{{ asset('assets/pos/img/icons/dash2.svg') }}" alt="img"></span>
								</div>
								<div class="dash-widgetcontent">
									<h5 >$<span class="counters">{{number_format($valor_unidades_stock,2,",",".") }}</span></h5>
									<h6>Valor de Venta Total</h6>
								</div>
							</div>
						</a>
						</div>
