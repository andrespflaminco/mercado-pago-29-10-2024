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
							<h6>Stock  </h6>
						</div>
						<div class="page-btn">
						    @if(Auth::user()->sucursal != 1 )
						    @if(Auth::user()->profile != "Cajero" )
						    
						    @can('agregar producto')
							<a hidden href="javascript:void(0)" wire:click="Agregar()" class="btn btn-added"><img src="{{ asset('assets/pos/img/icons/plus.svg') }}"  alt="img" class="me-1">Agregar @if($es_insumo_elegido_url == 1) insumo @else producto @endif</a>
						    
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
            						<a class="nav-link" @if($es_insumo_elegido_url == 1) href="https://app.flamincoapp.com.ar/products-precios?tipo=insumo" @else href="{{url('products-precios')}}" @endif  > PRECIOS </a>
            				</li>
            				<li class="nav-item">
            						<a class="nav-link active" @if($es_insumo_elegido_url == 1) href="https://app.flamincoapp.com.ar/products-stock?tipo=insumo" @else href="{{url('products-stock')}}" @endif  > STOCK </a>
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
									@can('exportar catalogo')     
										<li>
									
											<a style="font-size:12px !important; padding:5px !important; background: #198754 !important;" class="btn btn-cancel" wire:click="ExportarCatalogo()"  data-bs-placement="top" title="exportar excel"> 
											<svg style="margin-right: 5px;"  xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
											Exportar </a>
										</li>
									
									@endcan 
									
									
									@if(Auth::user()->sucursal != 1 )
									
									@can('importar catalogo')    
									<li>
											<a style="font-size:12px !important; padding:5px !important;" class="btn btn-cancel" href="{{ url('import') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="importar"> 
									<svg  style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-upload"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>		
											Importar</a>
									</li>
									@endif
									
									@endcan
									
									
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
                        <input type="checkbox" id="select-all">
                        <span class="checkmarks"></span>
                    </label>
                    @endcan
                    @endif
                </th>
                <th>Nombre del producto</th>
                <th>SKU</th>
                <th>Stock minimo</th>
                <th {{ ($muestra_stock_casa_central == 0) && (Auth::user()->casa_central_user_id != $comercio_id) ? 'hidden' : ''}}>Stock casa central</th>
                @foreach($sucursales as $suc)
                
                
                <th wire:click="sort('stock')" {{ ($muestra_stock_otras_sucursales == 0) && ($suc->sucursal_id != $comercio_id) && (Auth::user()->casa_central_user_id != $comercio_id) ? 'hidden' : ''}}>
                    STOCK {{$suc->name}} 
                </th>
                @endforeach
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $product)
            <tr>
                <td>
                    @if(Auth::user()->profile != "Cajero" )
                    @can('accion en lote productos')  
                    <label class="checkboxs">
                        <input type="checkbox" wire:model.defer="id_check" tu-attr-id="{{($product->id)}}"  class="mis-checkboxes" value="{{$product->id}}">
                        <span class="checkmarks"></span>
                    </label>
                    @endcan
                    @endif
                </td>
                <td class="productimgname">
                    <a href="javascript:void(0);" class="product-img">
                        @if($product->image != null)
                        <img src="{{ asset('storage/products/' . $product->image ) }}" alt="{{$product->name}}" height="70" width="80" class="rounded">
                        @else
                        <img src="{{ asset('storage/products/noimg.png') }}" alt="{{$product->name}}" height="70" width="80" class="rounded">
                        @endif
                    </a>
                    <a href="javascript:void(0);">{{$product->name}}</a>
                </td>
                <td>{{$product->barcode}}</td>
                <td>{{$product->alerts}} unid.</td>
                <td {{ ($muestra_stock_casa_central == 0) && (Auth::user()->casa_central_user_id != $comercio_id) ? 'hidden' : ''}}> 
                    @foreach($stock_sucursales as $pl)
                    @if($pl->sucursal_id == 0)
                    @if($product->id == $pl->product_id)
                    @if($product->producto_tipo == "v")
                    <a href="javascript:void(0)" style="color: #007bff !important; cursor: pointer;" wire:click="MostrarStock({{$product->id}},0)">
                        Disp: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock, $configuracion_decimales_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock, 3, ',', '.') }} Kg
                        @endif
                        <br>   
                        Real: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock_real, $configuracion_decimales_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock_real, 3, ',', '.') }} Kg
                        @endif   
                    </a>
                    @else
                    @if($pl->stock < $product->alerts)
                    <text style="color:red;">
                        Disp: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock, $configuracion_decimales_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock, 3, ',', '.') }} Kg
                        @endif  
                    </text> <br>
                    <text style="color:red;">
                        Real: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock_real, $configuracion_decimales_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock_real, 3, ',', '.') }} Kg
                        @endif  
                    </text> 
                    @else
                    <text>
                        Disp: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock, $configuracion_decimales_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock, 3, ',', '.') }} Kg
                        @endif  
                    </text> <br>
                    <text>
                        Real: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock_real, $configuracion_decimales_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock_real, 3, ',', '.') }} Kg
                        @endif  
                    </text>
                    @endif 
                    @endif
                    @endif
                    @endif
                    @endforeach   
                </td>
                @foreach($sucursales as $suc)
                <td {{ ($muestra_stock_otras_sucursales == 0) && ($suc->sucursal_id != $comercio_id) && (Auth::user()->casa_central_user_id != $comercio_id) ? 'hidden' : ''}}>
                    @foreach($stock_sucursales as $pl)
                    @if($suc->sucursal_id == $pl->sucursal_id)
                    @if($product->id == $pl->product_id)
                    @if($product->producto_tipo == "v")
                    <a href="javascript:void(0)" style="color: #007bff !important; cursor: pointer;" wire:click="MostrarStock({{$product->id}},{{$pl->sucursal_id}})">
                        Disp: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock, $configuracion_decimales_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock, 3, ',', '.') }} Kg
                        @endif   
                        <br> 
                        Real: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock_real, $configuracion_decimales_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock_real, 3, ',', '.') }} Kg
                        @endif
                    </a>
                    @else
                    @if($pl->stock < $product->alerts)
                    <text style="color:red;">
                        Disp: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock, $configuracion_decimales_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock, 3, ',', '.') }} Kg
                        @endif  
                    </text> <br>
                    <text style="color:red;">
                        Real: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock_real, $configuracion_decimales_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock_real, 3, ',', '.') }} Kg
                        @endif  
                    </text> 
                    @else
                    <text>
                        Disp: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock, $configuracion_decimales_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock, 3, ',', '.') }} Kg
                        @endif  
                    </text> <br>
                    <text>
                        Real: 
                        @if($product->unidad_medida == 9) 
                            {{ number_format($pl->stock_real, $configuracion_decimales_unidades, ',', '.') }} Unid.
                        @elseif($product->unidad_medida == 1) 
                            {{ number_format($pl->stock_real, 3, ',', '.') }} Kg
                        @endif  
                    </text>
                    @endif 
                    @endif
                    @endif
                    @endif
                    @endforeach
                </td>
                @endforeach
                <td>
                    <a wire:click.prevent="Ver({{$product->id}})" class="me-3" href="javascript:void(0)">
                        <img src="{{ asset('assets/pos/img/icons/eye.svg') }}" alt="img">
                    </a>
                    @if(Auth::user()->profile != "Cajero" )
                    
                    @can('editar productos')  
                    <a class="me-3" href="javascript:void(0)" wire:click.prevent="Edit({{$product->id}})" >
                        <img src="{{ asset('assets/pos/img/icons/edit.svg') }}" alt="img">
                    </a>					
					@endcan
												
					@can('eliminar productos')  
                    <a class="confirm-text" href="javascript:void(0)" onclick="Confirm('{{$product->id}}')"  >
                        <img src="{{ asset('assets/pos/img/icons/delete.svg') }}" alt="img">
                    </a>					
					@endcan 

                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{$data->links()}}
</div>

						</div>
					</div>
					<!-- /product list -->
					@endif 
					
					@if($agregar == 1)
					@include('livewire.products.agregar-editar-producto')
					@endif 
					
					@include('livewire.products-stock.form-stock')
					
					
					
@include('livewire.products.exportar-stock')
@include('livewire.products.exportar-lista')
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