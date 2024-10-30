<br>
<div class="row">

    <div class="col-sm-12">
        <div>
            <div class="connect-sorting">
                <!---	<h5 class="text-center mb-3">RESUMEN DE VENTA </h5> -->
                <div class="connect-sorting-content">
                    <div class="card simple-title-task ui-sortable-handle">
                        <div class="card-body">
                            <div class="task-header">
                                <div>
                                    <!---	<h4 class="mt-3">Articulos: {{$itemsQuantity}}</h4> --->
                                </div>
                                <label>Vigencia hasta</label>
                                <div class="input-group mb-0">
                                    <input type="date" wire:model="vigencia" class="form-control">
                                </div>
                                <br>
                                <label>Forma de pago</label>
                                <select wire:model='tipo_pago' class="form-control"
                                        wire:change='TipoPago($event.target.value)'>
                                    <option value="Elegir" selected>Elegir</option>
                                    <option value="1">Efectivo</option>
                                    @foreach($metodo_pago as $mp)
                                        <option value="{{$mp->id}}">{{ucfirst($mp->nombre)}}</option>
                                    @endforeach
                                </select>
                                <br>

                                @if($tipo_pago != 1 && $tipo_pago != "Elegir")

                                    <label>Forma de pago</label>

                                    <select wire:model='metodo_pago_elegido'
                                            wire:change='MetodoPago($event.target.value)' class="form-control">
                                        <option value="Elegir" selected disabled>Elegir</option>
                                        @foreach($metodos as $metodo_pago)
                                            <option
                                                value="{{$metodo_pago->id}}">{{ucfirst($metodo_pago->nombre)}}</option>
                                        @endforeach
                                        <option hidden value="1">Efectivo</option>
                                        <option hidden value="2">Pago dividido</option>
                                        <option value="OTRO" class="btn btn-dark">Agregar otro medio de pago</option>
                                    </select>
                                @else

                                @endif
                                <br>

                                <label>Tipo de presupuesto</label>

                                <select wire:model='tipo_presupuesto' class="form-control">
                                    <option value="Elegir" selected>Elegir</option>
                                    <option value="1">Cerrado</option>
                                    <option value="2">Abierto</option>
                                </select>
                                <br>
                                <label> Descuento</label>

                                <div class="input-group mb-0">
                                    <input type="number" id="descuento" wire:model="descuento"
                                           wire:keydown.enter="updateDescuentoGral($('#descuento').val() )"
                                           wire:change="updateDescuentoGral($('#descuento').val() )"
                                           class="form-control text-center" min="0" value="${{floatval(0)}}"
                                    >
                                    <div class="input-group-append">
                                            <span class="input-group-text"
                                                  style="background: #3B3F5C; color:white">
                                             %
                                            </span>
                                    </div>
                                </div>
                                <hr>
                                <p>SUBTOTAL: $ {{number_format( $cart->subtotalAmount()  , 2)}}</p>
                                <hr>
                                <p>RECARGO: ${{number_format( $cart->totalRecargo() , 2 )}} </p>
                                <p>DESCUENTO: ${{number_format( $cart->totalDescuento() , 2 )}} </p>
                                <hr>
                                <p>IVA: ${{number_format( $cart->totalIva() , 2 )}} </p>
                                <h6>CANT. ITEMS: {{number_format( $cart->totalCantidad() )}}</h6>
                                <h3>TOTAL: ${{number_format( $cart->totalAmount() , 2)}}</h3>
                                <br><br>
                                <i>
                                    @if($deuda != null)
                                        <b>Deuda: $ {{$deuda}}</b>
                                    @endif
                                </i>

                                <div class="row justify-content-between mt-5">
                                    <div class="col-sm-12 col-md-12 col-lg-6">
                                        @if($cart->hasProducts())
                                            <button onclick="Confirm('','clearCart','¿SEGURO DE ELIMINAR EL CARRITO?')"
                                                    class="btn btn-dark mtmobile">
                                                CANCELAR F4
                                            </button>
                                        @endif
                                    </div>

                                    <div class="col-sm-12 col-md-12 col-lg-6">
                                        @if($cart->hasProducts())

                                            <button wire:click.prevent="saveSale" class="btn btn-dark btn-md btn-block">
                                                GUARDAR
                                            </button>

                                        @endif
                                    </div>


                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
