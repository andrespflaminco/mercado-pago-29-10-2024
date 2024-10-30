<div>	                
	                <div class="page-header">
					<div class="page-title">
							<h4>Ayuda</h4>
							<h6>Ver listado de ayuda Flaminco</h6>
						</div>
						<div class="page-btn">               											    

						    
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
				
						<div class="card-body">
							<div class="table-top">
								<div class="search-set">
									<div class="search-path">
										<a class="btn btn-filter" id="filter_search">
											<img src="{{ asset('assets/pos/img/icons/filter.svg') }}"  alt="img">
											<span><img src="{{ asset('assets/pos/img/icons/closes.svg') }}" alt="img"></span>
										</a>
									</div>
									<input type="text" autocomplete="off" wire:model="search" placeholder="Buscar.." class="form-control"	>
									<div hidden class="search-input">
										<a class="btn btn-searchset"><img src="{{ asset('assets/pos/img/icons/search-white.svg') }}" alt="img"></a>
									</div>
								</div>
								<div hidden class="wordset">
									<ul>
										<li>
											<a data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img  src="{{ asset('assets/pos/img/icons/pdf.svg') }}"  alt="img"></a>
										</li>
										<li>
											<a data-bs-toggle="tooltip" data-bs-placement="top" title="excel"><img  src="{{ asset('assets/pos/img/icons/excel.svg') }}" alt="img"></a>
										</li>
										<li>
											<a data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img  src="{{ asset('assets/pos/img/icons/printer.svg') }}" alt="img"></a>
										</li>
									</ul>
								</div>
							</div>
							
							@foreach($ayuda as $a)
							
							<a href="https://app.flamincoapp.com.ar/ayuda?ayuda_id={{ $a->id }}">
							<div class="card">
							    <div class="card-body">
							        {{$a->titulo}}
							    </div>
							</div>							
							</a>
							
							

                            @endforeach
							
						</div>
					</div>
					

					
					
					</div>
					
