<div>	                
@include('livewire.products.form-marcas')	
@include('livewire.products.form-categoria')
@include('livewire.products.form-almacen')
@include('livewire.products.form-proveedor')
@include('livewire.products.form-imagen')	

	                @if($agregar == 0)
	                <div class="page-header">
					<div class="page-title">
							<h4>@if($es_insumo_elegido_url == 1) Insumos @else Productos @endif</h4>
							<h6>Precios</h6>
						</div>
						<div class="page-btn">
						    @if(Auth::user()->sucursal != 1 )
						    @if(Auth::user()->profile != "Cajero" )
						    
						    @can('agregar producto')
							<a href="javascript:void(0)" wire:click="Agregar()" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar @if($es_insumo_elegido_url == 1) insumo @else producto @endif</a>
						    @endcan
						    
						    @endif
						    @endif
						</div>
					</div>
					
                    
					<!-- /product list -->
					<div class="card">
					    
					    	
                    <ul class="nav nav-tabs  mb-3">
            				<li class="nav-item">
            						<a class="nav-link" @if($es_insumo_elegido_url == 1) href="https://app.flamincoapp.com.ar/products?tipo=insumo" @else href="{{url('products')}}" @endif  > CATALOGO  </a>
            				</li>
            				<li class="nav-item">
            						<a class="nav-link active" @if($es_insumo_elegido_url == 1) href="https://app.flamincoapp.com.ar/products-precios?tipo=insumo" @else href="{{url('products-precios')}}" @endif  > PRECIOS </a>
            				</li>
            				<li class="nav-item">
            						<a class="nav-link" @if($es_insumo_elegido_url == 1) href="https://app.flamincoapp.com.ar/products-stock?tipo=insumo" @else href="{{url('products-stock')}}" @endif  > STOCK </a>
            				</li>
            			</ul>
			
					<div class="card-body">
							<div class="table-top">
								<div class="search-set">
									<div class="search-path">
									@include('common.boton-filtros')
									</div>
									<input type="text" autocomplete="off" wire:model="search" placeholder="Buscar.." class="form-control"	>
									<div hidden class="search-input">
										<a class="btn btn-searchset"><img src="{{ asset('assets/pos/img/icons/search-white.svg') }}" alt="img"></a>
									</div>
								</div>
								<div class="wordset">
								    
								   @if(Auth::user()->profile != "Cajero" )
								    
								    
									<ul>
                                    <li>
                                        <a style="padding: 2px 4px; text-align: center; color: #6c757d !important; border: solid 1px #6c757d; border-radius: 4px;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-columns">
                                                <path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7m0-18H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7m0-18v18"></path>
                                            </svg>
                                        </a>
                                        <div class="dropdown-menu">
                                            <div  style="color: #212B36; font-size: 13px; padding: 8px 15px; font-weight: 500;">
                                                <input type="checkbox" wire:model.defer="columns.precio_interno">
                                                <span style="margin-left:10px; margin-top:2px;">Precio de venta a sucursales</span>
                                            </div>
                                    
                                            <div style="color: #212B36; font-size: 13px; padding: 8px 15px; font-weight: 500;">
                                                <input type="checkbox" wire:model.defer="columns.precio_base">
                                                <span style="margin-left:10px; margin-top:2px;">Precio base</span>
                                            </div>
                                    
                                            @foreach($lista_precios as $list)
                                            <div
                                            
                                            @if($mapeoListaMuestra[$list->id] == 0) 
                                                hidden 
                                            @elseif($list->id == $lista_costo_defecto && !auth()->user()->can('ver costo defecto'))
                                                hidden 
                                            @endif
                                            
                                            style="color: #212B36; font-size: 13px; padding: 8px 15px; font-weight: 500;">
                                                <input type="checkbox" wire:model.defer="columns.precio_{{$list->id}}">
                                                <span style="margin-left:10px; margin-top:2px;">{{$list->nombre}}</span>
                                            </div>
                                            @endforeach
                                    
                                            <!-- Botón para aplicar los cambios -->
                                            <div style="color: #212B36; font-size: 13px; padding: 8px 15px; font-weight: 500; float:right;">
                                                <button class="applyBtn btn btn-sm btn-primary"  wire:click="aplicarCambiosColumnas">
                                                    Aplicar
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                
                                    @can('exportar excel precios')
                                    <li>
                                        <a style="font-size:12px !important; padding:5px !important; background: #198754 !important;" class="btn btn-cancel" wire:click="ExportarCatalogo()" data-bs-placement="top" title="exportar excel">
                                            <svg style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="7 10 12 15 17 10"></polyline>
                                                <line x1="12" y1="15" x2="12" y2="3"></line>
                                            </svg>
                                            Exportar
                                        </a>
                                    </li>
                                    @endcan
                                
                                    @if(Auth::user()->sucursal != 1)
                                    @can('importar excel precios')
                                    <li>
                                        <a style="font-size:12px !important; padding:5px !important;" class="btn btn-cancel" href="{{ url('import') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="importar">
                                            <svg style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-upload">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                            </svg>
                                            Importar
                                        </a>
                                    </li>
                                    @endcan
                                    @endif
                                </ul>


									
									
									
									@endif
						
								</div>
							</div>
							
							<!-- /Filter -->
							@include('common.filtros-productos') 
							<!-- /Filter -->
							
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th>
											    @if(Auth::user()->profile != "Cajero" )
											    
											    @can('accion en lote productos')
												<label class="checkboxs">
													<input type="checkbox" id="select-all" >
													<span class="checkmarks"></span>
												</label>
												@endcan
												
												@endif
											</th>
											<th colspan="2">Nombre del producto</th>
											<th>SKU</th>
											@if(auth()->user()->sucursal != 1)
											<th class="text-center">Costo</th>
											@endif
											
											@if($columns['precio_interno'])
											<th {{$lista_costo_defecto != 0 ? 'hidden' : ''}} class="text-center" @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif>Utilidad %</th>
											<th {{$lista_costo_defecto != 0 ? 'hidden' : ''}} class="text-center">
								            
								            <a style="margin-right: 15px; color:black !important;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Precio de venta a sucursales </a>
                                         	
                                         	@if(Auth::user()->sucursal != 1)
                                         	<div class="dropdown-menu">
                                         	    <button  wire:click="UpdateRegla(1,1)"   class="dropdown-item">Precio fijo</button>
                                         	    <button  wire:click="UpdateRegla(1,2)"   class="dropdown-item">% sobre el costo</button>
                                         	    <button hidden wire:click="UpdateRegla(1,3)"   class="dropdown-item">Precio fijo</button>
                                            </div>
                                            @endif
                                            
                                            @foreach($lista_precios_reglas as $lpr)
                                            @if($lpr->lista_id == 1)
                                            <p style="font-size:9px;">@if($lpr->regla == 1) Precio fijo @else % sobre costo @endif</p>
                                            @endif
                                            @endforeach
											
											</th>
											@endif
											
											@if($columns['precio_base'])
											<th class="text-center" @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif >Utilidad %</th>
											<th class="text-center">
								            
								            <a style="margin-right: 15px; color:black !important;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Precio </a>
                                         	@if(auth()->user()->sucursal != 1)
                                         	<div class="dropdown-menu">
                                         	    <button  wire:click="UpdateRegla(0,1)"   class="dropdown-item">Precio fijo</button>
                                         	    <button  wire:click="UpdateRegla(0,2)"   class="dropdown-item">% sobre el costo</button>
                                         	    <button hidden wire:click="UpdateRegla(0,3)"   class="dropdown-item">Precio fijo</button>
                                            </div>
                                            @endif
                                            @foreach($lista_precios_reglas as $lpr)
                                            @if($lpr->lista_id == 0)
                                            <p style="font-size:9px;">@if($lpr->regla == 1) Precio fijo @else % sobre costo @endif</p>
                                            @endif
                                            @endforeach
											
											</th>
											@endif
											
											@foreach($lista_precios as $list)
											
											@if($columns['precio_'.$list->id])
											<th 
											@if($mapeoListaMuestra[$list->id] == 0) 
                                                hidden 
                                            @elseif($list->id == $lista_costo_defecto && !auth()->user()->can('ver costo defecto'))
                                                hidden 
                                            @endif
                                            class="text-center" @if(!auth()->user()->can('ver porcentaje utilidad lista precios')) hidden @endif>Utilidad %</th>
                							<th
                							@if($mapeoListaMuestra[$list->id] == 0) 
                                                hidden 
                                            @elseif($list->id == $lista_costo_defecto && !auth()->user()->can('ver costo defecto'))
                                                hidden 
                                            @endif
                                            
                							class="text-center">
                							
                							<a style="margin-right: 15px; color:black !important;" href="javascript:void(0)" class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Precio {{$list->nombre}} </a>
                                         	
                                         	@if(auth()->user()->sucursal != 1)
                                         	<div class="dropdown-menu">
                                         	    <button  wire:click="UpdateRegla({{$list->id}},1)"   class="dropdown-item">Precio fijo</button>
                                         	    <button  wire:click="UpdateRegla({{$list->id}},2)"   class="dropdown-item">% sobre el costo</button>
                                         	    <button hidden wire:click="UpdateRegla({{$list->id}},3)"   class="dropdown-item">Precio fijo</button>
                                            </div>
                                            @endif
											
											@foreach($lista_precios_reglas as $lpr)
                                            @if($lpr->lista_id == $list->id)
                                            <p style="font-size:9px;"> @if($lpr->regla == 1) Precio fijo @else % sobre costo @endif </p>
                                            @endif
                                            @endforeach
                							</th>
                							@endif
                							
                							@endforeach
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>
									    @foreach($data as $product)

										@if($product->producto_tipo == "s")
										@include('livewire.products-precio.catalogo-producto-simple')
                                        @endif
                                        
                                		@if($product->producto_tipo == "v")
										@include('livewire.products-precio.catalogo-producto-variable')
                                        @endif

                                        
								
										
										@endforeach
									</tbody>
								</table>
								<br>
								{{$data->links()}}
								<br>
							</div>
						
					
					
					<!-- /product list -->
					@endif 
					
					@if($agregar == 1)
					@include('livewire.products.agregar-editar-producto')
					@endif 
					
					
	                @include('livewire.products.form-lista-precios')
	                
	                @include('livewire.products.exportar-stock')
                    @include('livewire.products.exportar-lista')
  
					</div>
					
					</div>
					
</div>

@include('common.script-productos') 

@include('common.script-etiquetas') 
<script>


const $imagen = document.querySelector("#imagen");
const $calidad1 = document.querySelector("#calidad1");
const $calidad2 = document.querySelector("#calidad2");
const $imagenPrevisualizar = document.querySelector("#imagenPrevisualizar");

const comprimirImagen = (imagenComoArchivo, porcentajeCalidad) => {
    return new Promise((resolve, reject) => {
        const imagen = new Image();
        imagen.onload = () => {
            const $canvas = document.createElement("canvas");
            $canvas.width = imagen.width;
            $canvas.height = imagen.height;
            const ctx = $canvas.getContext("2d");
            ctx.drawImage(imagen, 0, 0);
            const base64 = $canvas.toDataURL("image/jpeg", porcentajeCalidad / 100);
            resolve(base64);
        };
        imagen.src = URL.createObjectURL(imagenComoArchivo);
    });
};

$imagen.addEventListener("change", async () => {
    if ($imagen.files.length <= 0) {
        return;
    }
    
    // Muestra el loader antes de comenzar la compresi贸n
    document.getElementById("loader").style.display = "block";
    
    const archivo = $imagen.files[0];
    const nombreArchivoOriginal = archivo.name;
    
    // Verificar si el tama帽o del archivo supera 1 megabyte (en bytes)
    const maxSizeInBytes = 1024 * 1024; // 1 megabyte
    
    //SI EL ARCHIVO TIENE MAS DE 1 MB
    
    if (archivo.size > maxSizeInBytes) {
    const base64Image = await comprimirImagen(archivo, parseInt($calidad2.value));
    $imagenPrevisualizar.src = base64Image; // Mostrar la imagen Base64 en la etiqueta img
    
    // Asignar la cadena Base64 al input de texto
    const imagenBase64Input = document.querySelector("#imagenBase64");
    imagenBase64Input.value = base64Image;     
    
    
    window.livewire.emit('Base64', base64Image, nombreArchivoOriginal);
    }
    
    // SI EL ARCHIVO TIENE MENOS DE 1 MB
    
    if (archivo.size < maxSizeInBytes) {
    const base64Image = await comprimirImagen(archivo, parseInt($calidad1.value));
    $imagenPrevisualizar.src = base64Image; // Mostrar la imagen Base64 en la etiqueta img
    
    // Asignar la cadena Base64 al input de texto
    const imagenBase64Input = document.querySelector("#imagenBase64");
    imagenBase64Input.value = base64Image;     
    
    
    window.livewire.emit('Base64', base64Image, nombreArchivoOriginal);
    
    // Despu茅s de completar la compresi贸n, oculta el loader
    document.getElementById("loader").style.display = "none";

    }
        

});


</script>