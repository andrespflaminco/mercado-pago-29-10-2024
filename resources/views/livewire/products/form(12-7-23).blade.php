<div wire:ignore.self class="modal fade" id="theModal" tabindex="-1" role="dialog" style="overflow-y: auto !important;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>{{$componentName}}</b> | {{ $selected_id > 0 ? 'EDITAR' : 'CREAR' }} # {{$selected_id}} 
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">

@inject('cart', 'App\Services\CartVariaciones')

<div class="row">
      <div class="col-sm-12 col-md-4 ">
     <label>Imagen</label>

     @if($selected_id < 1)

    
 
     @if($base64 != null)
     <div class="image-upload">
       <a href="javascript:void(0)" wire:click.prevent="DestroyImageBase64()" id="borrar-imagen" class="borrar-imagen" title="Eliminar imagen">
         <i class="far fa-times-circle"></i>
       </a>
    <label for="file-input">

        <img height="230" width="240"  src="{{ $base64 }}"  wire:click="ModalImagenes" class="rounded">
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
         <input type="text" wire:model.defer="name" class="form-control" placeholder="ej: Caja de helados" >
       @error('name') <span class="text-danger er">{{ $message }}</span> @enderror
     </div>
     <div class="form-group col-6">
      <label>Código</label>
        <input type="text" wire:model.defer="barcode" class="form-control" placeholder="ej: 02589" >
      @error('barcode') <span class="text-danger er">{{ $message }}</span> @enderror
    </div>
    
    
    @if(Auth::user()->plan == 4)
   
    <div class="form-group col-6">

   <label>Tipo de producto</label>
     <select wire:model='tipo_producto' class="form-control">
       <option value="Elegir" disabled >Elegir</option>
       <option value="1"> Compra-venta</option>
       <option value="2"> Produccion </option>
     </select>
     @error('categoryid') <span class="text-danger err">{{ $message }}</span> @enderror
   </div>
   
   @endif
   
   @if($lista_precios == null)
    <div class="form-group col-6">
     <label>Precio</label>
       <input type="text" wire:model.defer="price" class="form-control" placeholder="ej: 0.00" >
     @error('price') <span class="text-danger er">{{ $message }}</span> @enderror
    </div>
    @endif

    <div class="form-group col-6">
        <label>Categoría</label>
          <select wire:model.defer='categoryid' wire:change.defer='ModalCategoria($event.target.value)' class="form-control">
             <option value="AGREGAR" style="padding:20px !important; " class="btn btn-dark">+ NUEVA CATEGORIA</option>
            <option value="Elegir" disabled >Elegir</option>
            <option value="1" selected >Sin categoria</option>
            @foreach($categories as $c)
            <option value="{{$c->id}}">{{$c->name}}</option>
            @endforeach
            
          </select>
          @error('categoryid') <span class="text-danger err">{{ $message }}</span> @enderror
      </div>


             <div class="form-group col-6">
            <div class="form-group">
             <label>Inv minimo</label>
             <input type="text" wire:model.defer='alerts' class="form-control">
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
    <option value="AGREGAR" style="padding:20px !important;" class="btn btn-dark">+ NUEVO ALMACEN</option>
      <option value="Elegir" disabled >Elegir</option>
      <option value="1" >Sin almacen</option>
      @foreach($almacenes as $a)
      <option value="{{$a->id}}">{{$a->nombre}}</option>
      @endforeach
      
    </select>
    @error('almacen') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-4" >
 <div class="form-group">
  <label>Proveedor</label>
    <select wire:model.defer='proveedor' {{$tipo_producto == 2 ? 'disabled' : '' }}  wire:change.defer='ModalProveedor($event.target.value)' class="form-control">
     <option value="AGREGAR" style="padding:20px !important;" class="btn btn-dark">+ NUEVO PROVEEDOR</option>
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
                
                @if(1 < Auth::user()->plan)
                <input type="checkbox" wire:model.defer="ecommerce_canal" checked> <label for="">Tienda online</label>
                @else
                <input type="checkbox" id="checkbox" onclick="MejorarPlan()"> <label for="">Tienda online</label>
                @endif

                 @if($wc_yes != 0)
                 <input style="margin-left: 15px;" type="checkbox" wire:model.defer="wc_canal" checked> <label for="">Wocommerce</label>
                 @endif

                   <input style="margin-left: 15px;" type="checkbox" wire:model.defer="mostrador_canal" checked> <label for="">Mostrador</label>



                </div>



           </div>
           </div>

@endcan






</div>



 <div class="row">

   <!--div hidden class="col-sm-12 col-md-12"-->
   <div  class="col-sm-12 col-md-12">
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
  ///
    <?php //debug {{$testGuardarReferernciaID}} ?>


   </div>

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
                              <div class="form-group col-sm-12 col-md-4">
                                <label>Cod. Variacion</label>
                                 <input type="text" wire:model.lazy="cod_variacion.{{$variaciones['referencia_id']}}" class="form-control">
                               @error('cod_variacion.' . $variaciones['referencia_id'] ) <span class="text-danger er">{{ $message }}</span> @enderror 
                             

                            
                            
                                                           
                              </div>

                               <div class="form-group col-sm-12 col-md-4">
                                 <label>Costo</label>
                                  <input type="number" wire:model.lazy="costos_variacion.{{$variaciones['referencia_id']}}"  {{$tipo_producto == 2 ? 'readonly' : '' }}  class="form-control" placeholder="ej: 0.00" >
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
                       <!--input type="text" wire:model.lazy="precio_lista.{{$variaciones['referencia_id']}}|0" class="form-control" placeholder="ej: 0.00" -->
                       <input type="number" wire:model.lazy="precio_lista.{{$variaciones['referencia_id']}}|0" class="form-control" placeholder="ej: 0.00" >
                     @error('precio_lista') <span class="text-danger er">{{ $message }}</span> @enderror
                    </div>


                   @foreach ($lista_precios as $key => $lp)

                    <div class="col-4">
                       <label for="">{{$lp->nombre}}</label>
                       <!--input type="text" class="form-control" wire:model="precio_lista.{{$variaciones['referencia_id']}}|{{ $lp->id }}" /-->
                       <input type="number" class="form-control" wire:model="precio_lista.{{$variaciones['referencia_id']}}|{{ $lp->id }}" />
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
                       <input type="number" wire:model.lazy="stock_sucursal.{{$variaciones['referencia_id']}}|0|{{$variaciones['var_nombre'] ?? 0}}|{{$variaciones['var_id'] ?? 0}}" class="form-control" placeholder="ej: 0.00" >
                     @error('stock_sucursal') <span class="text-danger er">{{ $message }}</span> @enderror
                    </div>

                    @endif

                    @foreach ($sucursales as $llave => $sucu)

                              <div class="col-4">
                                <label for="">{{$sucu->name}}</label>
                              <input type="number" class="form-control" wire:model="stock_sucursal.{{$variaciones['referencia_id']}}|{{ $sucu->sucursal_id }}|{{$variaciones['var_nombre'] ?? 0}}|{{$variaciones['var_id'] ?? 0}}" />
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
@endif

@if($producto_tipo == "s")
<div class="row" style="padding: 15px;">

@can('product_create')

  <div class="col-sm-12 col-md-12">
   <div class="form-group">
       <label>Costo </label>
       <br>
      <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px ;">

        <div class="row col-12">

           <div class="form-group col-sm-12 col-md-4">
            <br>
            <input type="number" wire:model.lazy="cost" class="form-control" {{$tipo_producto == 2 ? 'disabled' : '' }} placeholder="ej: 0.00" >
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

           <div class="form-group col-sm-12 col-md-4">
            <label>Precio base</label>
              <input type="number" required wire:model.lazy="precio_lista.0|0|0|0" class="form-control" placeholder="ej: 0.00" >
              @error('precio_lista') <span class="text-danger er">{{ $message }}</span> @enderror
           </div>


          @foreach ($lista_precios as $key => $lp)

                    <div class="col-sm-12 col-md-4">
                      <label for="">{{$lp->nombre}}</label>
                    <input type="number" required class="form-control" wire:model="precio_lista.0|{{ $lp->id }}|0|0" />
                    @error('precio_lista') <span class="text-danger er">{{ $message }}</span> @enderror

                  </div>

      @endforeach


                <!----- BOTON PARA AGREGAR LISTA DE PRECIOS NUEVA ---->
                
                @if(Auth::user()->plan == 4)
                <div class="col-12">
                <a style="color: #007bff !important;" wire:click="ModalListaPrecio()" href="javascript:void(0)" >+ Agregar lista de precios</a>
                </div>
                @else
                <div class="col-12">
                <a style="color: #007bff !important;" onclick="MejorarPlan()" href="javascript:void(0)" >+ Agregar lista de precios</a>
                </div>
                @endif
                
                <!----------------------------------------------------->
                




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

           <div class="form-group col-sm-12 col-md-4">
            <label>{{auth()->user()->name}}</label>
            
              <input type="number" required wire:model.lazy="stock_sucursal.0|0|0|0" class="form-control" placeholder="ej: 0.00" >
            @error('stock_sucursal') <span class="text-danger er">{{ $message }}</span> @enderror
           </div>

           @endif

           @foreach ($sucursales as $llave => $sucu)
                    
                     <div class="col-sm-12 col-md-4">
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

</div>
     <div class="modal-footer">

       <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

       @can('product_create')
       @if($selected_id < 1)
       <button type="button" wire:click.prevent="Store()" class="btn btn-dark close-modal" >GUARDAR</button>
       @else
       <button type="button" wire:click.prevent="Update()" class="btn btn-dark close-modal" >ACTUALIZAR</button>
       @endif
       @endcan


     </div>
   </div>
 </div>
</div>

<script>
function showHtmlDiv(value) {
  var value = value.value;
  var value2 = '123-'+value;

  var htmlShow = document.getElementById(value);
  var htmlShow2 = document.getElementById(value);

  htmlShow2.classList.toggle('active');

  if (htmlShow.style.display === "none") {
    htmlShow.style.display = "block";


  } else {
    htmlShow.style.display = "none";
  }
}

</script>
