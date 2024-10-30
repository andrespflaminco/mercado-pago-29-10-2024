<div>	                
	                <div class="page-header">
					<div class="page-title">
							<h4>Descargar</h4>
							<h6>Vea el listado de descargas</h6>
						</div>
						<div class="page-btn">
						    <a href="{{ url('products') }}" class="btn btn-added"> Volver</a>
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
				
						<div class="card-body">
							<div class="table-top">
								<div class="search-set">

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
							
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th>Tipo de descarga</th>
											<th>Fecha de creacion</th>
											<th>Descargar</th>
										</tr>
									</thead>
									<tbody>
									  @foreach($reportes as $r)
        							<tr>
        								<td>
        									    @if($r->tipo == "exportar_productos")
        									    Excel de Catalogo
        									    @endif
        									    @if($r->tipo == "exportar_etiquetas")
        									    PDF de etiquetas
        									    @endif
        									    @if($r->tipo == "exportar_etiquetas_excel")
        									    Excel de etiquetas
        									    @endif
        								</td>
        								
        								<td>{{\Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:i')}} hs.</td>
        									<td>
        									    @if($r->estado == 0 )
        											<span class="badge badge-warning text-uppercase" style="background: orange;">En preparacion</span>
        								        @endif
        								        @if($r->estado == 1 )
        											<span class="badge badge-warning text-uppercase" style="background: orange;">En preparacion</span>
        								        @endif
        								        @if($r->estado == 2 )
        								        
        								        @if($r->tipo != "exportar_etiquetas_excel")
        								        <a href="javascript:void(0)" wire:click="Descargar('{{$r->id}}')" class="btn btn-dark text-white" title="Descargar">
        										<i class="fas fa-download"></i>
        										
        									    </a>
        									    @endif
        									    
        									     @if($r->tipo == "exportar_etiquetas_excel")
        									    <a href="javascript:void(0)" wire:click="DescargarExcel('{{$r->id}}')" class="btn btn-dark text-white" title="Descargar Excel">
        										<i class="fas fa-file-excel"></i>
        										</a>
        								        @endif
        									    
        								        @endif
        								        
        								       
        									</td>
        								
        							</tr>
        							@endforeach
							        </tbody>
								</table>
								{{$reportes->links()}}
							</div>
						</div>
					</div>
					
					
	</div>
					