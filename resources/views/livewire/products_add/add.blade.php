<form wire:submit.prevent="Store">    

<div class="row sales layout-top-spacing">

	<div class="row" style="width:100%; ">
	<div class="col-md-1 col-sm-0">

	</div>
	<div class="col-md-10 col-sm-12">
	
	<div class="col-lg-9 col-md-12" style="margin:0 auto;">
	<div class="row" style="background: white; padding:15px; border-radius:5px; margin:0 auto;">

@if($product_added < 1)

@inject('cart', 'App\Services\CartVariaciones')
@inject('cart_atributos', 'App\Services\CartProductosAtributos')

    
<div class="row">
      <div class="col-sm-12 col-md-4 ">
     <label>Imagen</label>

     @if($selected_id < 1)

    
 
     @if($base64 != null)
     <div class="image-upload">
       <a href="javascript:void(0)" id="borrar-imagen" class="borrar-imagen" title="Eliminar imagen">
         <i class="far fa-times-circle"></i>
       </a>
    <label for="file-input">

        <img height="230" width="240" src="{{ $base64 }}"  wire:click="ModalImagenes" class="rounded">
    </label>

       @error('image') <span class="error">{{ $message }}</span> @enderror
      </div>


     @else
     <div class="image-upload">
    <label for="file-input">

        <img src="{{ asset('assets/img/noimg.png') }}" alt="" height="230" width="240" style="opacity:0.6;" wire:click="ModalImagenes" class="rounded">
    </label>

   
       @error('image') <span class="error">{{ $message }}</span> @enderror
      </div>
     @endif



     @else


     <div class="image-upload">
       <a href="javascript:void(0)" id="borrar-imagen" wire:click.prevent="DestroyImage({{$selected_id}})" class="borrar-imagen" title="Eliminar imagen">
         <i class="far fa-times-circle"></i>
       </a>
    <label for="file-input">
    @if($base64 != null)

     @if($base64 != null)
        <img height="230" width="240" src="{{ $base64 }}" wire:click="ModalImagenes" class="rounded">
     @else
        <img src="{{ asset('storage/products/' . $image ) }}" alt="" height="230" width="240" wire:click="ModalImagenes" class="rounded">
     @endif

     @else
       <img src="{{ asset('assets/img/noimg.png') }}" alt="" height="230" width="240" style="opacity:0.6;" wire:click="ModalImagenes" class="rounded">
      @endif
    </label>

       @error('image') <span class="error">{{ $message }}</span> @enderror
      </div>
      @endif

   </div>



 


  <div class="col-sm-12 col-md-8">
    <div class="row">
      <div class="form-group  col-12">
       <label>Nombre</label>
         <input type="text" wire:model="name" class="form-control" placeholder="ej: Caja de helados" >
       @error('name') <span class="text-danger er">{{ $message }}</span> @enderror
     </div>
     <div class="form-group col-6">
      <label>Codigo</label>
        <input type="text" wire:model="barcode" class="form-control" placeholder="ej: 02589" >
      @error('barcode') <span class="text-danger er">{{ $message }}</span> @enderror
    </div>
    <div class="form-group col-6">

   <label>Tipo de producto</label>
     <select wire:model="tipo_producto"class="form-control">
       <option value="Elegir" disabled >Elegir</option>
       <option value="1"> Compra-venta</option>
       <option value="2"> Produccion </option>
     </select>
     @error('tipo_producto') <span class="text-danger err">{{ $message }}</span> @enderror
   </div>
   @if($lista_precios == null)
    <div class="form-group col-6">
     <label>Precio</label>
       <input type="text" wire:model="price" class="form-control" placeholder="ej: 0.00" >
     @error('price') <span class="text-danger er">{{ $message }}</span> @enderror
    </div>
    @endif

    <div class="form-group col-6">
        <label>Categoria</label>
          <select wire:model='categoryid' wire:change.defer='ModalCategoria($event.target.value)' class="form-control">
            <option value="Elegir" disabled >Elegir</option>
            <option value="1" selected >Sin categoria</option>
            @foreach($categories as $c)
            <option value="{{$c->id}}">{{$c->name}}</option>
            @endforeach
             <option value="AGREGAR" style="padding:20px !important;" class="btn btn-dark">Agregar Categoria</option>
          </select>
          @error('categoryid') <span class="text-danger err">{{ $message }}</span> @enderror
      </div>


             <div class="form-group col-6">
            <div class="form-group">
             <label>Inv minimo</label>
             <input type="text" wire:model='alerts' class="form-control">
               @error('alerts') <span class="text-danger err">{{ $message }}</span> @enderror
           </div>
           </div>


    </div>



</div>


<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Maneja stock?</label>
    <select wire:model.defer='stock_descubierto' class="form-control">
      <option value="Elegir" disabled >Elegir</option>
      <option value="si" > Activo </option>
      <option value="no" > Inactivo </option>

    </select>
    @error('stock_descubierto') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Seccion del almacen</label>
    <select wire:model.defer='almacen' wire:change.defer='ModalAlmacen($event.target.value)' class="form-control">
      <option value="Elegir" disabled >Elegir</option>
      <option value="1" >Sin almacen</option>
      @foreach($almacenes as $a)
      <option value="{{$a->id}}">{{$a->nombre}}</option>
      @endforeach
      <option value="AGREGAR" style="padding:20px !important;" class="btn btn-dark">Agregar Almacen</option>
    </select>
    @error('almacen') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-4" >
 <div class="form-group">
  <label>Proveedor</label>
    <select wire:model-defer='proveedor' {{$tipo_producto == 2 ? 'disabled' : '' }} class="form-control">
      <option value="Elegir" disabled >Elegir</option>
      <option value="1" >Sin proveedor</option>
      @foreach($prov as $pr)
      <option value="{{$pr->id}}">{{$pr->nombre}}</option>
      @endforeach
    </select>
    @error('proveedor') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>
@can('product_create')


           <div class="col-sm-12 col-md-8">
            <div class="form-group">
                <label>Canal de ventas</label>
                <br>
               <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px 3px 2px 14px;">
                 <input type="checkbox" wire:model="ecommerce_canal" checked> <label for="">Tienda online</label>

                 @if($wc_yes != 0)
                 <input style="margin-left: 15px;" type="checkbox" wire:model="wc_canal" checked> <label for="">Wocommerce</label>
                 @endif

                   <input style="margin-left: 15px;" type="checkbox" wire:model="mostrador_canal" checked> <label for="">Mostrador</label>



                </div>



           </div>
           </div>

@endcan






</div>



 <div class="row" style="width: 100%;">


   <div class="col-sm-12 col-md-12">
    <div class="form-group">
        <label>Tipo de productos </label>
        <br>
       <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px ;">

         <div class="row col-8">

           <select class="form-control" wire:model="producto_tipo" wire:change="ProductoTipo()">
             <option value="Elegir">Elegir</option>
             <option value="s">Producto Simple</option>
             <option value="v">Producto Variable</option>
           </select>
            @error('producto_tipo') <span class="text-danger err">{{ $message }}</span> @enderror




         </div>

        </div>



   </div>
   </div>

   <div hidden class="col-sm-12 col-md-12">
      <label>Atributos </label>
    <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px;">
    <div class="row">
  
    <!--- Atributos ---->
    
    
    <div class="col-12">
    <div class="row"> 
    <div class="col-6">
    <select wire:model="atributo_agregar" class="form-control">
    <option value="Elegir">Elegir</option>
    @foreach($atributos_var as $a)
    <option value="{{$a->id}}">{{$a->nombre}}</option>
    @endforeach
    </select>         
    </div>
   
   <div class="col-3">
    
   <button type="button" class="btn btn-dark" style="height:100%;" wire:click="GuardarProductosAtributos">+ Agregar </button>
   
    </div>
    </div>
    </div>
    
    <div class="col-12">
      @if ($cart_atributos->getContent()->count() > 0)
      
      @foreach ($cart_atributos->getContent() as $key => $atributos)
  
      <div class="col-sm-12 col-md-4">
        <div class="form-group">
        <label>{{$atributos['nombre']}}  </label>
        <div wire:ignore>

        <select class="form-control tagging" multiple="multiple" id="select2-dropdown">
        <option value="Pendiente">Pendiente</option>
        <option value="En proceso">En proceso</option>
        <option value="Entregado">Entregado</option>
        <option value="Cancelado">Cancelado</option>
        </select>
        </div>
        </div>
        </div>
        
      

      
      @endforeach
      
      @endif
    </div>
    <!----- Variaciones ---->
    
    
   
    </div>
    </div>
   </div>
   
   @if($producto_tipo == "v")
   <div class="col-sm-12 col-md-12">
    <div class="form-group">
      <label>Variaciones </label>

       <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px ;">

         <div class="row">
            @foreach($atributos_var as $a)
           <div class="col-3">
             <select class="form-control" wire:model="variacion_atributo.{{ $a->id }}">
                <option value="c"> Cualquier {{$a->nombre}}</option>

               @foreach($variaciones as $v)
               @if($a->id == $v->atributo_id)
               <option value="{{$v->id}}">{{$v->nombre}}</option>
               @endif

               @endforeach
             </select>
           </div>






           @endforeach

           @if(count($variaciones) < 1)
           <p style="margin-left:10px;" class="text-danger">Debe agregar variaciones para asociarlas al producto</p>
           @else

                      <button type="button" class="btn btn-dark"  wire:click="GuardarVariacion">+ Agregar
                      </button>
           @endif





         </div>

        </div>



   </div>
    @error('stock_sucursal') <span class="text-danger er">{{ $message }}</span> @enderror
    @error('precio_lista') <span class="text-danger er">{{ $message }}</span> @enderror



   </div>
   @endif
   
   <div class="col-sm-12 col-md-12">
    <div class="form-group">
      @if ($cart->getContent()->count() > 0)
      <?php $i = 1; ?>
      @foreach ($cart->getContent() as $key => $variaciones)


       <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;">
         <div class="col-12" style="background: #ebedf2; color: #3b3f5c;  border: none; padding: 12px; border-radius: 4px;">
            <b>
              {{$variaciones['var_nombre']}}
             </b>
              <button type="button" style="float:right;"  onclick="ConfirmVariacion('{{$variaciones['referencia_id']}}')">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
              </button>
             <button type="button" style="float:right; display:flex;" value="{{$variaciones['referencia_id']}}" onclick="showHtmlDiv(this);">


               <svg  id="123-{{$variaciones['referencia_id']}}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>

            </button>
         </div>



         <div class="row" style="padding: 30px;"  id="{{$variaciones['referencia_id']}}">


                      @can('product_create')

                      <div class="col-sm-12 col-md-12">
                       <div class="form-group">
                           <label> </label>
                           <br>
                          <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px ;">
                            <br>
                            <div class="row col-12">
                              <div class="form-group col-4">
                                <label>Cod. Variacion</label>
                                 <input type="text" wire:model="cod_variacion.{{$variaciones['referencia_id']}}" class="form-control">
                               @error('cod_variacion') <span class="text-danger er">{{ $message }}</span> @enderror
                              </div>

                               <div class="form-group col-4">
                                 <label>Costo</label>
                                  <input type="number" wire:model="costos_variacion.{{$variaciones['referencia_id']}}"  {{$tipo_producto == 2 ? 'readonly' : '' }}  class="form-control" placeholder="ej: 0.00" >
                                @error('costos_variacion') <span class="text-danger er">{{ $message }}</span> @enderror
                               </div>


                            </div>

                           </div>



                      </div>
                      </div>

                      @endcan

           @if($lista_precios != null)


           <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label>Listas de precios </label>
                <br>
               <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px ;">

                 <div class="row col-12">

                    <div class="form-group col-4">
                     <label>Precio base</label>
                       <input type="text" wire:model="precio_lista.{{$variaciones['referencia_id']}}|0" class="form-control" placeholder="ej: 0.00" >
                     @error('precio_lista') <span class="text-danger er">{{ $message }}</span> @enderror
                    </div>


                   @foreach ($lista_precios as $key => $lp)

                             <div class="col-4">
                               <label for="">{{$lp->nombre}}</label>
                             <input type="text" class="form-control" wire:model="precio_lista.{{$variaciones['referencia_id']}}|{{ $lp->id }}" />
                           </div>

               @endforeach
                    @error('precio_lista') <span class="text-danger er">{{ $message }}</span> @enderror





                 </div>

                </div>



           </div>
           </div>

           @endif

           <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label>Stock </label>
                <br>
               <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px ;">

                 <div class="row col-12">

                   @if(auth()->user()->sucursal != 1)

                    <div class="form-group col-4">
                     <label>{{auth()->user()->name}}</label>
                       <input type="text" wire:model="stock_sucursal.{{$variaciones['referencia_id']}}|0|{{$variaciones['var_nombre'] ?? 0}}|{{$variaciones['var_id'] ?? 0}}" class="form-control" placeholder="ej: 0.00" >
                     @error('stock_sucursal') <span class="text-danger er">{{ $message }}</span> @enderror
                    </div>

                    @endif

                    @foreach ($sucursales as $llave => $sucu)

                              <div class="col-4">
                                <label for="">{{$sucu->name}}</label>
                              <input type="text" class="form-control" wire:model="stock_sucursal.{{$variaciones['referencia_id']}}|{{ $sucu->sucursal_id }}|{{$variaciones['var_nombre'] ?? 0}}|{{$variaciones['var_id'] ?? 0}}" />
                            </div>

                @endforeach





                 </div>

                </div>



           </div>
           </div>



         </div>
        </div>
        @endforeach



      @endif


   </div>



   </div>


@if($producto_tipo == "s")
<div class="row" style="padding: 15px;">

@can('product_create')

  <div class="col-sm-12 col-md-12">
   <div class="form-group">
       <label>Costo </label>
       <br>
      <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px ;">

        <div class="row col-12">

           <div class="form-group col-4">
            <br>
            <input type="text" wire:model="cost" class="form-control" {{$tipo_producto == 2 ? 'disabled' : '' }} placeholder="ej: 0.00" >
          </div>

        </div>

       </div>



  </div>
  </div>
@endcan

  @if($lista_precios != null)



  <div class="col-sm-12 col-md-12">
   <div class="form-group">
       <label>Listas de precios </label>
       <br>
      <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px ;">

        <div class="row col-12">

           <div class="form-group col-4">
            <label>Precio base</label>
              <input type="number" required wire:model="precio_lista.0|0|0|0" class="form-control" placeholder="ej: 0.00" >
              @error('precio_lista') <span class="text-danger er">{{ $message }}</span> @enderror
           </div>


          @foreach ($lista_precios as $key => $lp)

                    <div class="col-4">
                      <label for="">{{$lp->nombre}}</label>
                    <input type="number" required class="form-control" wire:model="precio_lista.0|{{ $lp->id }}|0|0" />
                    @error('precio_lista') <span class="text-danger er">{{ $message }}</span> @enderror

                  </div>

      @endforeach




        </div>

       </div>



  </div>
  </div>

  @endif

  <div class="col-sm-12 col-md-12">
   <div class="form-group">
       <label>Stock </label>
       <br>
      <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px ;">

        <div class="row col-12">

          @if(auth()->user()->sucursal != 1)

           <div class="form-group col-4">
            <label>{{auth()->user()->name}}</label>
            
              <input type="number" required wire:model="stock_sucursal.0|0|0|0" class="form-control" placeholder="ej: 0.00" >
            @error('stock_sucursal') <span class="text-danger er">{{ $message }}</span> @enderror
           </div>

           @endif

           @foreach ($sucursales as $llave => $sucu)

                     <div class="col-4">
                       <label for="">{{$sucu->name}}</label>
                       
                     <input type="number" required class="form-control" wire:model="stock_sucursal.0|{{ $sucu->sucursal_id }}|0|0" />
                     @error('stock_sucursal') <span class="text-danger er">{{ $message }}</span> @enderror

                   </div>

       @endforeach





        </div>

       </div>



  </div>
  </div>


  </div>
@endif

</div>


			<div class="col-12 text-right">
			    <input type="submit" class="btn btn-dark" value="GUARDAR">

			</div>

			@else

<div style="width:100%;">
	<div class="col-sm-12 col-md-12">
	 <div class="form-group">
			 <label>Mostrar en sucursales</label>
			 <br>

			<div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px 3px 2px 14px;">
				<br><br>
				<div class="col-md-5 d-flex">
					<select class="form-control" wire:model="sucursal">
						<option value="Elegir" selected>Elegir</option>
 					  @foreach($sucursales as $s)
 					 <option value="{{$s->id}}">{{$s->name}}</option>
 					 @endforeach
 				 </select>
 				 <button type="button" wire:click="Sucursal()" class="btn btn-dark" name="button">Agregar</button>
				</div>
				<br><br>
				@foreach($sucursales as $su)
			 {{$su->name}}
			 @endforeach






			 </div>


	</div>
	</div>

</div>




	 </div>
	</div>

	 @endif

	</div>
	<div class="col-md-1 col-sm-0">

	</div>
	</div>


@include('livewire.products_add.form-prices')
@include('livewire.products.form-imagen')

</div>

</form>

<script>
	document.addEventListener('DOMContentLoaded', function() {

        $('.tagging').select2({
                        tags: true
        });
                    
		window.livewire.on('modal-imagen-show', msg => {
			$('#Imagenes').modal('show')
			$('#theModal').modal('hide')
		});

		window.livewire.on('modal-imagen-hide', msg => {
			$('#Imagenes').modal('hide')
			$('#theModal').modal('show')
		});


		window.livewire.on('product-added', msg => {
			$('#theModalPrices').modal('hide')
			noty(msg)
		});

		window.livewire.on('show-modal-prices', msg => {
			$('#theModalPrices').modal('show')
		});

		window.livewire.on('category-added', msg => {
			$('#Categoria').modal('hide')
			$('#theModal').modal('show')
			noty(msg)
		});

		window.livewire.on('almacen-added', msg => {
			$('#Almacen').modal('hide')
			$('#theModal').modal('show')
			noty(msg)
		});


		window.livewire.on('product-updated', msg => {
			$('#theModal').modal('hide')

			noty(msg)
		});
		window.livewire.on('product-deleted', msg => {
			// noty
		});
		window.livewire.on('modal-show', msg => {
			$('#theModal').modal('show')
		});

		window.livewire.on('mostrar-precios', msg => {
			$('#theModal').modal('show')
		});

		window.livewire.on('modal-categoria-show', msg => {
			$('#Categoria').modal('show')
			$('#theModal').modal('hide')
		});

		window.livewire.on('modal-almacen-show', msg => {
			$('#Almacen').modal('show')
			$('#theModal').modal('hide')
		});

		window.livewire.on('modal-hide', msg => {
			$('#theModal').modal('hide')
		});
		window.livewire.on('hidden.bs.modal', msg => {
			$('.er').css('display', 'none')
		});
		$('#theModal').on('hidden.bs.modal', function(e) {
			$('.er').css('display', 'none')
		})
		$('#theModal').on('shown.bs.modal', function(e) {
			$('.product-name').focus()
		})



	});

	function Confirm(id) {

		swal({
			title: 'CONFIRMAR',
			text: '¿CONFIRMAS ELIMINAR EL REGISTRO?',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if (result.value) {
				window.livewire.emit('deleteRow', id)
				swal.close()
			}

		})
	}

	function ConfirmCheck(id) {

		swal({
			title: 'CONFIRMAR',
			text: '¿CONFIRMAS ELIMINAR LOS REGISTROS?',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cerrar',
			cancelButtonColor: '#fff',
			confirmButtonColor: '#3B3F5C',
			confirmButtonText: 'Aceptar'
		}).then(function(result) {
			if (result.value) {
				window.livewire.emit('ConfirmCheck', id)
				swal.close()
			}

		})
	}
</script>
<script>
		$('#default-ordering').DataTable( {
				"stripeClasses": [],
				drawCallback: function () { $('.dataTables_paginate > .pagination').addClass(' pagination-style-13 pagination-bordered mb-5'); }
	} );
</script>
<script type="text/javascript">

function getEditar(item)
{

    var id =item.value;

		var x = document.getElementById("id"+id);
		var y = document.getElementById("id2-"+id);

		if (x.style.display === "block") {
			x.style.display = "none";
			y.style.display = "block";
		} else {
			x.style.display = "block";
			y.style.display = "none";
		}


}

function getCerrarEditar(item)
{

    var id =item.value;

		var y = document.getElementById("id"+id);
		var x = document.getElementById("id2-"+id);

		if (x.style.display === "block") {
			x.style.display = "none";
			y.style.display = "flex";
		} else {
			x.style.display = "flex";
			y.style.display = "none";
		}


}

//
</script>
<script type="text/javascript">

function getEditarPrice(item)
{

    var id =item.value;

		var a = document.getElementById("idprice"+id);
		var b = document.getElementById("idprice2-"+id);

		if (a.style.display === "block") {
			a.style.display = "none";
			b.style.display = "block";
		} else {
			a.style.display = "block";
			b.style.display = "none";
		}


}

function getCerrarEditarPrice(item)
{

    var id =item.value;

		var b = document.getElementById("idprice"+id);
		var a = document.getElementById("idprice2-"+id);

		if (a.style.display === "block") {
			a.style.display = "none";
			b.style.display = "flex";
		} else {
			a.style.display = "flex";
			b.style.display = "none";
		}


}

//
</script>
<script type="text/javascript">
document.getElementById("file-input").onchange = function(e) {
// Creamos el objeto de la clase FileReader
let reader = new FileReader();

// Leemos el archivo subido y se lo pasamos a nuestro fileReader
reader.readAsDataURL(e.target.files[0]);

// Le decimos que cuando este listo ejecute el código interno
reader.onload = function(){
	let preview = document.getElementById('image-upload'),
					image = document.createElement('img');

	image.src = reader.result;

	preview.innerHTML = '';
	preview.append(image);
};
}
</script>
