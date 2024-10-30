 @include('common.modalHead')


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
    <div class="form-group col-6">
     <label>Precio</label>
       <input type="text" wire:model.lazy="price" class="form-control" placeholder="ej: 0.00" >
     @error('price') <span class="text-danger er">{{ $message }}</span> @enderror
    </div>
    <div class="form-group col-6">
     <label>Costo</label>
       <input type="text" wire:model.lazy="cost" class="form-control" {{$tipo_producto == 2 ? 'disabled' : '' }} placeholder="ej: 0.00" >
     @error('cost') <span class="text-danger er">{{ $message }}</span> @enderror
   </div>
    </div>



</div>


<div HIDDEN class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Precio Ecommerce</label>
    <input type="text" wire:model.lazy="price_e" class="form-control" placeholder="ej: 0.00" >
  @error('price_e') <span class="text-danger er">{{ $message }}</span> @enderror
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
    @error('almacen') <span class="text-danger err">{{ $message }}</span> @enderror
</div>
</div>
<div class="col-sm-12 col-md-4">
 <div class="form-group">
  <label>Codigo proveedor</label>
    <input type="text" wire:model.lazy="cod_proveedor" {{$tipo_producto == 2 ? 'disabled' : '' }} class="form-control" >
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


<div class="col-sm-12 col-md-8">
 <div class="form-group">
     <label>Canal de ventas</label>
     <br>
    <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px 3px 2px 14px;">
      <input type="checkbox" wire:model="ecommerce_canal" checked> <label for="">Tienda online</label>

        <input style="margin-left: 15px;" type="checkbox" wire:model="mostrador_canal" checked> <label for="">Mostrador</label>

     </div>



</div>
</div>


<div class="col-sm-12 col-md-12">
 <div class="form-group">
     <label>Sucursal</label>
     <br>

    <div style="border: solid 1px #bfc9d4;  border: solid 1px #bfc9d4;  border-radius: 6px;   padding: 12px 3px 2px 14px;">
       @foreach($sucursales as $s)
      <input type="checkbox" wire:model="sucursal" value="{{$s->id}}"> <label for="">{{$s->name}}</label> <br>

      @endforeach

      <div class="row">

        <div class="col-sm-12 col-md-4">
         <div class="form-group">
          <label>Stock</label>
            <input type="number" wire:model.lazy="stock" class="form-control" placeholder="ej: 0" >
          @error('stock') <span class="text-danger er">{{ $message }}</span> @enderror
        </div>
        </div>

        <div class="col-sm-12 col-md-4">
         <div class="form-group">
          <label>Inv. minimo</label>
            <input type="number" wire:model.lazy="alerts" class="form-control" placeholder="ej: 10" >
          @error('alerts') <span class="text-danger er">{{ $message }}</span> @enderror
        </div>
        </div>
        <div class="col-sm-12 col-md-4">
         <div class="form-group">
          <label>Maneja stock?</label>
            <select wire:model='stock_descubierto' class="form-control">
              <option value="Elegir" disabled >Elegir</option>
              <option value="si" > Activo </option>
              <option value="no" > Inactivo </option>

            </select>
            @error('stock_descubierto') <span class="text-danger err">{{ $stock_descubierto }}</span> @enderror
        </div>
        </div>
      </div>
      
     </div>


</div>
</div>

<div class="col-sm-12 col-md-12">
 <div class="form-group">
    <div id="toggleAccordion">
    <div class="card">
        <div class="card-header" id="...">
            <section class="mb-0 mt-0">
                <div role="menu" class="collapsed" data-toggle="collapse" data-target="#defaultAccordionOne" aria-expanded="true" aria-controls="defaultAccordionOne">
                    Descripcion del producto <div class="icons"></div>
                </div>
            </section>
        </div>

        <div>
            <div class="card-body">
              <textarea wire:model="descripcion" class="form-control" style="width: 100%;" rows="8" cols="80"></textarea>
            </div>
        </div>
    </div>
</div>



</div>
</div>



</div>



@include('common.modalFooter')
