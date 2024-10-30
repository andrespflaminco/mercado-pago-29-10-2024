<style media="screen">
	.boton-editar {
		font-size: 1rem!important;
		width: 80px;
		background-color:
		transparent;
		border-left: none;
		border-top: none;
		border-right: none;
		text-align: center;
		border-bottom: 1px solid #bfc9d4;
	}

</style>
<div wire:ignore.self class="modal fade" id="modalDetails" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b>Editar Venta # {{$saleId}}</b>
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">

				@foreach($estado_estado as $es_es)


				@if($es_es->status === 'Pendiente')

        <div class="col-lg-8 col-md-4 col-sm-12">
        <div style="margin-bottom: 0 !important;" class="input-group mb-4">
          <div class="input-group-prepend">
            <span class="input-group-text input-gp">
              <i class="fas fa-clipboard-list"></i>
            </span>
          </div>


            <input
                style="font-size:14px !important;"
                type="text"
                class="form-control"
                placeholder="Agregar un producto"
                wire:model="query_product"
                wire:keydown.escape="resetProduct"
                wire:keydown.tab="resetProduct"
                wire:keydown.enter="selectProduct"
            />
            </div>


            @if(!empty($query_product))
                <div class="fixed top-0 bottom-0 left-0 right-0" wire:click="reset"></div>

                <div style="position:absolute;" class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
                    @if(!empty($products_s))
                        @foreach($products_s as $i => $product)
                        <a href="javascript:void(0)"
                        wire:click="selectProduct({{$product['id']}})"
                        class="btn btn-light" title="Edit">{{ $product['barcode'] }} - {{ $product['name'] }}
                        </a>

                        @endforeach

                    @else

                    @endif
                </div>
            @endif
						Metodo de pago:
						{{$nombre_mp}}


        </div>

				@endif


        <br><br>
        <div class="table-responsive">
          <table class="multi-table table table-hover" style="width:100%">
              <thead>
              <tr>
                <th class="text-center">CODIGO</th>
                <th class="text-center">PRODUCTO</th>
                <th class="text-center">PRECIO</th>
                <th class="text-center">CANT</th>
                <th class="text-center">IMPORTE</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @foreach($details as $d)
              <tr>
                <td class='text-center'><h6>{{$d->barcode}}</h6></td>
                <td class='text-center'><h6>{{$d->product}}</h6></td>
                <td class='text-center'>
									@if($es_es->status === 'Pendiente')
									<input style="padding-left:15px;"  type="text" class="boton-editar"  value="{{$d->price}}" id="price{{$d->id}}"
	                wire:change="updatePricePedido({{$d->id}}, $('#price' + {{$d->id}}).val() )" min="1" >
									@else

									<h6>{{$d->price}}</h6>
									@endif

								</td>
                <td class='text-center'>
								@if($es_es->status === 'Pendiente')
                @if ($d->stock_descubierto === "si")
                <input style="padding-left:15px;"  type="number" class="boton-editar"  value="{{number_format($d->quantity,0)}}" id="qty{{$d->id}}"
  							wire:change="updateQtyPedido({{$d->id}}, $('#qty' + {{$d->id}}).val() )" min="1" max="{{$d->stock+$d->quantity}}" onchange="Update({{$d->id}});" >
                <p style="color:red;" id="stock_maximo{{$d->id}}" hidden >Stock maximo</p>
                @else
                <input style="padding-left:15px;"  type="number" class="boton-editar"  value="{{number_format($d->quantity,0)}}" id="qty{{$d->id}}"
                wire:change="updateQtyPedido({{$d->id}}, $('#qty' + {{$d->id}}).val() )" min="1" onchange="Update({{$d->id}});" >
                @endif

								@else
								{{number_format($d->quantity,0)}}
								@endif

                <input hidden  id="stock_descubierto{{$d->id}}"	value="{{$d->stock_descubierto}}">


                <input hidden id="stock{{$d->id}}" value="{{$d->stock}}">

                  <input hidden  id="stock_max{{$d->id}}" value="{{$d->stock+$d->quantity}}">

              </td>
                <td class='text-center'><h6>{{number_format($d->price * $d->quantity,2)}}</h6></td>
                <td class='text-center'>
									@if($es_es->status === 'Pendiente')
                     <a href="javascript:void(0)" onclick="Confirm('{{$d->id}}')" >
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                      </a>

											@endif

                </td>

              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td class="text-center"><h6><strong>TOTALES</strong></h6></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"><h6><strong>{{$countDetails}}</strong></h6></td>
                <td class="text-center"><h6><strong>$ {{number_format($sumDetails,2)}}</strong></h6>
                  <td></td>

                </td>
              </tr>
            </tfoot>
          </table>
        </div>




      </div>
      <div class="modal-footer">
				@if($es_es->status === 'Pendiente')
				<button type="button" class="btn btn-dark close-btn text-info" data-dismiss="modal">CONFIRMAR</button>
				@else
				<button type="button" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

				@endif
      </div>
    </div>
  </div>
</div>
@endforeach

<script type="text/javascript">
function Confirm(id_pedido_prod) {

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
      window.livewire.emit('deleteRow', id_pedido_prod)
      swal.close()
    }

  })
}
</script>
<script type="text/javascript">
function Update(index){
	var stock_descubierto = $("#stock_descubierto"+index).val();
	if(stock_descubierto === "si") {
	var cantidad = $("#qty"+index).val();
	var stock_max = $("#stock_max"+index).val();

	if(cantidad === stock_max) {
    $("#stock_max"+index).css("display","block");
	} else {
    $("#stock_max"+index).css("display","none");
  }
  }
}

</script>

<script type="text/javascript">

function muestra_oculta(id){


   //se obtiene el id
var el = document.getElementById('contenido'); //se define la variable "el" igual a nuestro div
el.style.display = (el.style.display == 'none') ? 'block' : 'none'; //damos un atributo display:none que oculta el div


   //se obtiene el id
var el2 = document.getElementById('contenido2'); //se define la variable "el" igual a nuestro div
el2.style.display = (el2.style.display == 'none') ? 'block' : 'none'; //damos un atributo display:none que oculta el div


   //se obtiene el id
var el3 = document.getElementById('contenido3'); //se define la variable "el" igual a nuestro div
el3.style.display = (el3.style.display == 'none') ? 'block' : 'none'; //damos un atributo display:none que oculta el div
}
window.onload = function(){
  /*hace que se cargue la función lo que predetermina que div estará oculto hasta llamar a la
  función nuevamente*/
muestra_oculta('contenido');/* "contenido_a_mostrar" es el nombre que le dimos al DIV */

}


</script>
