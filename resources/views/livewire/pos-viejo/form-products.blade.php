
@inject('cart', 'App\Services\CartVariaciones')

<div wire:ignore.self class="modal fade" id="ModalProductos" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>Productos</b> | CREAR NUEVO
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">


       <div class="row">
          <div class="col-sm-12 col-md-4 ">
            <label>Imagen</label>

            @if($selected_id < 1)

            @if($image)
            <div class="image-upload">
              <a href="javascript:void(0)" id="borrar-imagen" class="borrar-imagen" title="Eliminar imagen">
                <i class="far fa-times-circle"></i>
              </a>
           <label for="file-input">

               <img height="230" width="240" src="{{ $image->temporaryUrl() }}" id="image-upload" class="rounded">
           </label>

           <input hidden id="file-input" type="file"  wire:model.defer="image" accept="image/x-png, image/gif, image/jpeg" />
              @error('image') <span class="error">{{ $message }}</span> @enderror
             </div>


            @else
            <div class="image-upload">
           <label for="file-input">

               <img src="{{ asset('assets/img/noimg.png') }}" alt="" height="230" width="240" style="opacity:0.6;" id="image-upload" class="rounded">
           </label>

           <input hidden id="file-input" type="file"  wire:model.defer="image" accept="image/x-png, image/gif, image/jpeg" />
              @error('image') <span class="error">{{ $message }}</span> @enderror
             </div>
            @endif



            @else


            <div class="image-upload">
              <a href="javascript:void(0)" id="borrar-imagen" wire:click.prevent="DestroyImage({{$selected_id}})" class="borrar-imagen" title="Eliminar imagen">
                <i class="far fa-times-circle"></i>
              </a>
           <label for="file-input">

               <img src="{{ asset('storage/products/' . $image ) }}" alt="" height="230" width="240" id="image-upload" class="rounded">
           </label>

           <input hidden id="file-input" type="file"  wire:model.defer="image" accept="image/x-png, image/gif, image/jpeg" />
              @error('image') <span class="error">{{ $message }}</span> @enderror
             </div>
             @endif

          </div>




         <div class="col-sm-12 col-md-8">
           <div class="row">
             <div class="form-group  col-12">
              <label>Nombre</label>
                <input type="text" wire:model.lazy="name" class="form-control" placeholder="ej: Caja de helados" >
              @error('name') <span class="text-danger er">{{ $message }}</span> @enderror
            </div>
            <div class="form-group col-6">
             <label>Código</label>
               <input type="text" wire:model.lazy="barcode" class="form-control" placeholder="ej: 02589" >
             @error('barcode') <span class="text-danger er">{{ $message }}</span> @enderror
           </div>
           <div class="form-group col-6">

          <label>Tipo de producto</label>
            <select wire:model='tipo_producto' class="form-control">
              <option value="Elegir" disabled >Elegir</option>
              <option value="1"> Compra-venta</option>
              <option value="2"> Produccion </option>
            </select>
            @error('categoryid') <span class="text-danger err">{{ $message }}</span> @enderror
          </div>
          @if($lista_precios == null)
           <div class="form-group col-6">
            <label>Precio</label>
              <input type="text" wire:model.lazy="price" class="form-control" placeholder="ej: 0.00" >
            @error('price') <span class="text-danger er">{{ $message }}</span> @enderror
           </div>
           @endif
           <div class="form-group col-6">
            <label>Costo</label>
              <input type="text" wire:model.lazy="cost" class="form-control" {{$tipo_producto == 2 ? 'disabled' : '' }} placeholder="ej: 0.00" >
            @error('cost') <span class="text-danger er">{{ $message }}</span> @enderror
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

       @if($iva_defecto != 0)

       <div class="col-sm-12 col-md-12">
        <div class="form-group">
            <label>Impuestos </label>
            <br>
           <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px ;">

             <div class="row col-12">

               <div class="col-6">
                 <label for="">Relacion precios -> iva </label>
                 <select class="form-control" wire:model="relacion_precio_iva">
                   <option value="0" selected>Elegir</option>
                    <option value="2">Incluido en el precio</option>
                    <option value="1">Precio + IVA</option>
                 </select>
               </div>

                 <div class="col-6">
                   <label for="">IVA</label>
                 <select class="form-control" wire:model="iva">
                    <option value="0" selected>Sin IVA</option>
                    <option value="0.105">10,5%</option>
                    <option value="0.21">21%</option>
                    <option value="0.27">27%</option>
                 </select>
               </div>


             </div>

            </div>



       </div>
       </div>

       @endif


       <div class="col-sm-12 col-md-4">
        <div class="form-group">
         <label>Maneja stock?</label>
           <select wire:model='stock_descubierto' class="form-control">
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
           <select wire:model='almacen' wire:change='ModalAlmacen($event.target.value)' class="form-control">
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
           <select wire:model='proveedor' {{$tipo_producto == 2 ? 'disabled' : '' }} class="form-control">
             <option value="Elegir" disabled >Elegir</option>
             <option value="1" >Sin proveedor</option>
             @foreach($prov as $pr)
             <option value="{{$pr->id}}">{{$pr->nombre}}</option>
             @endforeach
           </select>
           @error('proveedor') <span class="text-danger err">{{ $message }}</span> @enderror
       </div>
       </div>

       @if($iva_defecto != 0)

       <div class="col-sm-12 col-md-12">
        <div class="form-group">
            <label>Impuestos </label>
            <br>
           <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px ;">

             <div class="row col-12">

               <div class="col-6">
                 <label for="">Relacion precios -> iva </label>
                 <select class="form-control" wire:model="relacion_precio_iva">
                   <option value="0" selected>Elegir</option>
                    <option value="2">Incluido en el precio</option>
                    <option value="1">Precio + IVA</option>
                 </select>
               </div>

                 <div class="col-6">
                   <label for="">IVA</label>
                 <select class="form-control" wire:model="iva">
                    <option value="0" selected>Sin IVA</option>
                    <option value="0.105">10,5%</option>
                    <option value="0.21">21%</option>
                    <option value="0.27">27%</option>
                 </select>
               </div>


             </div>

            </div>



       </div>
       </div>

       @endif

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


          <div class="col-sm-12 col-md-4">
            <label>Categoría</label>
              <select wire:model='categoryid' wire:change='ModalCategoria($event.target.value)' class="form-control">
                <option value="Elegir" disabled >Elegir</option>
                <option value="1" selected >Sin categoria</option>
                @foreach($categories as $c)
                <option value="{{$c->id}}">{{$c->name}}</option>
                @endforeach
                 <option value="AGREGAR" style="padding:20px !important;" class="btn btn-dark">Agregar Categoria</option>
              </select>
              @error('categoryid') <span class="text-danger err">{{ $message }}</span> @enderror
          </div>




       </div>



        <div class="row">


          <div class="col-sm-12 col-md-12">
           <div class="form-group">
               <label>Tipo de productos </label>
               <br>
              <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px ;">

                <div class="row col-8">

                  <select class="form-control" wire:model="producto_tipo">
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

                  <button type="button" class="btn btn-dark" wire:click="GuardarVariacion">+ Agregar
                  </button>




                </div>

               </div>



          </div>
           @error('stock_sucursal') <span class="text-danger er">{{ $message }}</span> @enderror
           @error('precio_lista') <span class="text-danger er">{{ $message }}</span> @enderror



          </div>

          <div class="col-sm-12 col-md-12">
           <div class="form-group">
             @if ($cart->getContent()->count() > 0)

             @foreach ($cart->getContent() as $key => $variaciones)


              <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;">
                <div class="col-12" style="background: #ebedf2; color: #3b3f5c;  border: none; padding: 12px; border-radius: 4px;">
                   <b>
                   @foreach($productos_variaciones as $pv)

                   @if($pv->referencia_id == $variaciones['referencia_id'])


                   {{$pv->nombre_variacion}} -

                   @endif

                   @endforeach
                    </b>
                     <button type="button" style="float:right;"  onclick="ConfirmVariacion('{{$variaciones['referencia_id']}}')">
                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                     </button>
                    <button type="button" style="float:right; display:flex;" value="{{$variaciones['referencia_id']}}" onclick="showHtmlDiv(this);">


                      <svg  id="123-{{$variaciones['referencia_id']}}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>

                   </button>
                </div>



                <div class="row" style="padding: 30px;"  id="{{$variaciones['referencia_id']}}">

                  <div class="col-sm-12 col-md-12">
                   <div class="form-group">
                      <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px ;">

                        <div class="row col-12">

                           <div class="form-group col-4">
                            <label>Costo</label>
                              <input type="text" wire:model.lazy="costo.{{$variaciones['referencia_id']}}|0" class="form-control" placeholder="ej: 0.00" >
                            @error('costo') <span class="text-danger er">{{ $message }}</span> @enderror
                           </div>

                        </div>

                       </div>



                  </div>
                  </div>

                  @if($lista_precios != null)


                  <div class="col-sm-12 col-md-12">
                   <div class="form-group">
                       <label>Listas de precios </label>
                       <br>
                      <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px ;">

                        <div class="row col-12">

                           <div class="form-group col-4">
                            <label>Precio base</label>
                              <input type="text" wire:model.lazy="precio_lista.{{$variaciones['referencia_id']}}|0" class="form-control" placeholder="ej: 0.00" >
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
                              <input type="text" wire:model.lazy="stock_sucursal.{{$variaciones['referencia_id']}}|0" class="form-control" placeholder="ej: 0.00" >
                            @error('stock_sucursal') <span class="text-danger er">{{ $message }}</span> @enderror
                           </div>

                           @endif

                           @foreach ($sucursales as $llave => $sucu)

                                     <div class="col-4">
                                       <label for="">{{$sucu->name}}</label>
                                     <input type="text" class="form-control" wire:model="stock_sucursal.{{$variaciones['referencia_id']}}|{{ $sucu->sucursal_id }}" />
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

         @if($lista_precios != null)


         <div class="col-sm-12 col-md-12">
          <div class="form-group">
              <label>Listas de precios </label>
              <br>
             <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px ;">

               <div class="row col-12">

                  <div class="form-group col-4">
                   <label>Precio base</label>
                     <input type="text" wire:model.lazy="precio_lista.0" class="form-control" placeholder="ej: 0.00" >
                   @error('price') <span class="text-danger er">{{ $message }}</span> @enderror
                  </div>


                 @foreach ($lista_precios as $key => $lp)

                           <div class="col-4">
                             <label for="">{{$lp->nombre}}</label>
                           <input type="text" class="form-control" wire:model="precio_lista.{{ $lp->id }}" />
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
                     <input type="text" wire:model.lazy="stock_sucursal.0" class="form-control" placeholder="ej: 0.00" >
                   @error('stock') <span class="text-danger er">{{ $message }}</span> @enderror
                  </div>

                  @endif

                  @foreach ($sucursales as $llave => $sucu)

                            <div class="col-4">
                              <label for="">{{$sucu->name}}</label>
                            <input type="text" class="form-control" wire:model="stock_sucursal.{{ $sucu->sucursal_id }}" />
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

       <button type="button" wire:click.prevent="StoreProducto()" class="btn btn-dark close-modal" >GUARDAR</button>


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
