                
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget">
								<div class="dash-widgetimg">
									<span style="background-color: #63738112 !important;">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
									</span>
								</div>
								<div class="dash-widgetcontent">
									<h5 >$<span class="counters">{{$ventas_totales}}</span></h5>
									<h6>Ventas Totales</h6>
								</div>
							</div>
						</div>
						
				
				                
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget">
								<div class="dash-widgetimg">
									<span style="background-color: #fceaea !important;">
									    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ea5455" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
									</span>
								</div>
								<div class="dash-widgetcontent">
									<h5 >$<span class="counters">{{$costos_ventas_totales}}</span></h5>
									<h6>Costos de ventas</h6>
								</div>
							</div>
						</div>
						
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget dash1">
								<div class="dash-widgetimg">
									<span><img src="{{ asset('assets/pos/img/icons/dash2.svg') }}" alt="img"></span>
								</div>
								<div class="dash-widgetcontent">
									<h5 >$<span class="counters">{{$rentabilidad_marginal_venta}}</span></h5>
									<h6>Rentabilidad marginal</h6>
								</div>
							</div>
						</div>			
						
						<div class="col-lg-3 col-sm-6 col-12">
							<div class="dash-widget">
								<div class="dash-widgetimg">
									<span style="background-color: #e0f9fc !important;">
									    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1cd4eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-percent"><line x1="19" y1="5" x2="5" y2="19"></line><circle cx="6.5" cy="6.5" r="2.5"></circle><circle cx="17.5" cy="17.5" r="2.5"></circle></svg>
									</span>
								</div>
								<div class="dash-widgetcontent">
									<h5 ><span>{{$rentabilidad_porcentaje*100}} %</span></h5>
									<h6>Rentabilidad %</h6>
								</div>
							</div>
						</div>