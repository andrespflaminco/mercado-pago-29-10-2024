

	<div class="row layout-top-spacing">
    
    <div class="row">
            
            <div class="col-3">
                
            </div>
            <div class="col-6">
               <div class="row">
               
               @can('compra nueva a proveedor')
               <div id="productos" class="col-md-6 productos">
                <a href="{{ url('compras') }}" class="btn btn-light" title="Click en el producto">
                        <div id="product-item" class="product-item">
                            <div id="product-item-image" class="product-item-image">
                               <svg xmlns="http://www.w3.org/2000/svg" width="110" height="110" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                <div id="cart-icon" class="cart-icon">

                                </div>
                            </div>
                            <div class="product-item-info">
                                <br>
                                 <h5>COMPRAR A UN PROVEEDOR</h5>
                                <div class="descripcion">
                                </div>
                                <br>
                            </div>
                        </div>
                  
                </a>
                </div>  
               @endcan
                
                @if(Auth::user()->sucursal == 1)
                @can('compra nueva a casa central')
                <div id="productos" class="col-md-6 productos">
                <a href="{{ url('compras-central') }}" class="btn btn-light" title="Click en el producto">
                        <div id="product-item" class="product-item">
                            <div id="product-item-image" class="product-item-image">
                               
                                 <svg xmlns="http://www.w3.org/2000/svg" width="110" height="110" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-package"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                                <div id="cart-icon" class="cart-icon">

                                </div>
                            </div>
                            <div class="product-item-info">
                                <br>
                                 <h5>COMPRA EN CASA CENTRAL</h5>
                                <div class="descripcion">
                                </div>
                                <br>
                            </div>
                        </div>
                  
                </a>
                </div>  
                @endcan
                @endif
               
               </div> 
            </div>
            <div class="col-3">
                
            </div>
            
            
    </div>

	</div>
