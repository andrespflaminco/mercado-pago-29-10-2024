<style>

    [id^="discount"]::before {
        content: "-";
        position: absolute;
        left: 5px; /* Ajustar según el padding del input */
        top: 50%;
        transform: translateY(-50%);
    }

</style>


<div class="page-header">
    <div class="page-title">
        <h4>PRESUPUESTO # {{$ventaId}} - </h4>
        <h6></h6>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-4 col-sm-6 col-12">
                <div class="form-group">
                    <label>Cliente</label>
                    <div class="row">
                        <div class="col-lg-10 col-sm-10 col-10">
                            <select wire:model="cliente_id" id="c" wire:change="selectCliente( $('#c').val() )"
                                    class="form-control">
                                <option value="1">Consumidor final</option>
                                @foreach($clientes as $c)
                                    <option value="{{$c->id}}">{{$c->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-1">
                            <button class="btn btn-dark" wire:click="ModalAgregarCliente">+</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6 col-12">
                <div class="form-group">
                    <div class="col-lg-10 col-sm-10 col-10">
                        <label>Tipo Presupuesto</label>
                        <div class="form-group">
                            <input type="text" class="form-control" disabled
                                   value="{{ $total_total->tipo_presupuesto == 1 ? 'Cerrado' : 'Abierto'}} ">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6 col-12 text-right">

                <div style="padding-top:18px; text-align: right !important;" class="form-group">
                    <label> </label>
                    @switch($total_total->estado)
                        @case(0)
                            <a class="btn" wire:click="CerrarVenta()"
                               style="box-shadow: none; border: solid 1px #515365; background:transparent; margin-left: 10px !important;"
                               href="javascript:void(0)" wire:click="MailModalVerVenta('venta',{{$ventaId}})">
                                Concretar Venta
                            </a>
                            @break
                        @case(1)
                            <span style="cursor:pointer;" class="badges bg-danger">Vencido</span>
                            @break
                        @case(2)
                            <span style="cursor:pointer;" class="badges bg-info">Concretado</span>
                    @endswitch


                </div>
            </div>

            <div class="col-lg-12 col-sm-6 col-12" {{$total_total->tipo_presupuesto == 1 ? 'hidden' : ''}}>
                <div class="form-group">
                    <label>Agregar producto</label>
                    <div class="input-groupicon">
                        <input style="font-size:14px !important;" type="text" class="form-control"
                               wire:model="query_product" wire:keydown.escape="resetProduct"
                               wire:keydown.tab="resetProduct" type="text" placeholder="Scanear/Buscar producto...">
                        <div class="addonset">
                            <img src="{{ asset('assets/pos/img/icons/scanners.svg') }}" alt="img">
                        </div>
                    </div>


                    @if(!empty($query_product))
                        <div class="fixed top-0 bottom-0 left-0 right-0" wire:click="reset"></div>

                        <div style="position:absolute;"
                             class="absolute z-10 w-full bg-white rounded-t-none shadow-lg list-group">
                            @if(!empty($products_s))
                                @foreach($products_s as $i => $product)
                                    <a style="z-index: 9999;" href="javascript:void(0)"
                                       wire:click="selectProduct({{$product['id']}})"
                                       class="btn" title="Seleccionar">{{ $product['barcode'] }}
                                        - {{ $product['name'] }}
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    @endif

                </div>
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table">
                    <thead class="">
                    <tr>
                        <th scope="col">Fila</th>
                        <th scope="col">Producto</th>
                        <th class="text-right" scope="col">Cantidad</th>
                        <th class="text-right" scope="col">Precio</th>
                        <th class="text-right" scope="col">% Bonif</th>
                        <th class="text-center" scope="col">IVA</th>
                        <th class="text-right" scope="col">Subtotal</th>
                        <th class="text-right" scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; ?>
                    @foreach($dci as $dxi)
                        <tr class="table-row">
                            <td><?php echo $i++; ?> </td>
                            <td> {{$dxi->nombre}}  </td>

                            <td class="text-right">

                                <input style="    padding-left: 15px;
                                                font-size: 15px;
                                                padding: 8px 10px;
                                                letter-spacing: 1px;
                                                width: 90px !important;
                                                padding: 0.5rem 0.5rem;
                                                margin-top:0;
                                                border: 1px solid #bfc9d4 !important;
                                                color: #3b3f5c !important;
                                                background-color: #fff;" type="number" class="boton-editar"
                                       {{$total_total->tipo_presupuesto == 1 ? 'disabled' : ''}}
                                       value="{{number_format($dxi->cantidad,0)}}" id="qty{{$dxi->id}}"
                                       wire:change="updateQtyPedido({{$dxi->id}}, $('#qty' + {{$dxi->id}}).val() )"
                                       min="1">


                            </td>


                            <td class="text-right">
                                @php
                                    $precio = $dxi->precio * ($total_total->tipo_comprobante == 'A' ? 1 : (1 + $dxi->alicuota_iva));
                                @endphp
                                <span style="font-size: 15px;float: left;padding: 0.5rem;">$</span>
                                <input style="font-size: 15px;
                                            letter-spacing: 1px;
                                            width: 145px !important;
                                            padding: 0.5rem;
                                            margin-top:0;
                                            border: 1px solid #bfc9d4 !important;
                                            color: #3b3f5c !important;
                                            background-color: #fff;"
                                       type="text" class="boton-editar"
                                       {{$total_total->tipo_presupuesto == 1 ? 'disabled' : ''}}
                                       value="{{$precio}}" id="precio{{$dxi->id}}"
                                       pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$"
                                       wire:change="updatePricePedido({{$dxi->id}}, parseFloat($('#precio' + {{$dxi->id}}).val().replace(/[^\d.-]/g, '')))"
                                       min="1">

                            </td>


                            <td class="text-center">
                                @if(0 < $dxi->precio)
                                    @php
                                        $discount = number_format( ceil($dxi->descuento/$dxi->precio*100),2);
                                    @endphp
                                       - {{$discount}}
{{--                                    <input style="font-size: 15px;--}}
{{--                                            letter-spacing: 1px;--}}
{{--                                            width: 145px !important;--}}
{{--                                            padding: 0.5rem;--}}
{{--                                            margin-top:0;--}}
{{--                                            border: 1px solid #bfc9d4 !important;--}}
{{--                                            color: #3b3f5c !important;--}}
{{--                                            background-color: #fff;" type="text" class="boton-editar"--}}
{{--                                           {{$total_total->tipo_presupuesto == 1 ? 'disabled' : ''}}--}}
{{--                                           value="{{$discount}}" id="discount{{$dxi->id}}"--}}
{{--                                           pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$"--}}
{{--                                           wire:change="updateDiscountPedido({{$dxi->id}}, $('#discount' + {{$dxi->id}}).val())"--}}
{{--                                           min="1">--}}

                                @endif
                            </td>


                            @if($total_total != null)
                                @if($total_total->tipo_comprobante == "A")
                                    <td class="text-right">
                                        {{number_format($dxi->alicuota_iva*100,2)}} % 2
                                    </td>
                                @endif
                            @endif


                            <td class="text-center">

                                <a style="color: #637381; font-weight: 500 !important;" href="javascript:void(0)"
                                   class="dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                   aria-expanded="false">{{$dxi->alicuota_iva*100}} % </a>

                                <div class="dropdown-menu">
                                    @if($total_total->tipo_presupuesto !== 1)
                                        <button id="iva{{$dxi->id}}"
                                                wire:click="UpdateIva('{{$dxi->id}}', $('#iva' + '{{$dxi->id}}').val() )"
                                                value="0" class="dropdown-item">Sin IVA
                                        </button>
                                        <button id="ivaprimero{{$dxi->id}}"
                                                wire:click="UpdateIva('{{$dxi->id}}', $('#ivaprimero' + '{{$dxi->id}}').val() )"
                                                value="0.105" class="dropdown-item">10,5%
                                        </button>
                                        <button id="ivasegundo{{$dxi->id}}"
                                                wire:click="UpdateIva('{{$dxi->id}}', $('#ivasegundo' + '{{$dxi->id}}').val() )"
                                                value="0.21" class="dropdown-item">21%
                                        </button>
                                        <button id="ivatercero{{$dxi->id}}"
                                                wire:click="UpdateIva('{{$dxi->id}}', $('#ivatercero' + '{{$dxi->id}}').val() )"
                                                value="0.27" class="dropdown-item">27%
                                        </button>
                                    @endif
                                </div>

                            </td>
                            <td class="text-right">

                                $

                                @if($total_total->tipo_comprobante == "A")
                                    {{number_format(( ($dxi->precio- $dxi->descuento) * (1+$dxi->alicuota_iva ) )*$dxi->cantidad,2)}}
                                @else
                                    {{number_format(( ($dxi->precio- $dxi->descuento) * (1+$dxi->alicuota_iva ) )*$dxi->cantidad,2)}}
                                @endif


                            </td>
                            <td class="text-right">
                                @if($total_total->tipo_presupuesto !== 1)
                                    <a href="javascript:void(0)"
                                       onclick="ConfirmEliminar('{{$dxi->id}}')" {{$total_total->tipo_presupuesto == 1 ? 'disabled' : ''}}>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round" class="feather feather-x-circle">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="15" y1="9" x2="9" y2="15"></line>
                                            <line x1="9" y1="9" x2="15" y2="15"></line>
                                        </svg>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 float-md-right">
                <div class="total-order">

                    @foreach ($total as $t)

                        <ul>
                            <li>
                                <h4>
                                    Subtotal
                                </h4>
                                <h5>$ {{number_format($t->subtotal,2)}} </h5>
                            </li>
                            <li>
                                <h4>
                                    Descuento general
                                </h4>
                                <h5>$ {{number_format($t->descuento,2)}} </h5>
                            </li>
                            <li>
                                <h4>Recargo</h4>
                                <h5>$ {{number_format($t->recargo,2)}}</h5>
                            </li>
                            <li>
                                <h4>
                                    IVA
                                </h4>
                                <h5>$ {{number_format($t->iva,2)}} ({{number_format($t->alicuota_iva,2)}}%)</h5>
                            </li>

                            <li class="total">
                                <h4>Total</h4>
                                <h5>$ {{number_format($t->total,2)}}</h5>
                            </li>

                        </ul>

                    @endforeach
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="form-group">
                    <label>Tipo de factura </label>
                    <select wire:model="tipo_factura" id="tipo_factura"
                            wire:change="UpdateTipoComprobante( $('#tipo_factura').val() , {{$NroVenta}} , 1 )"
                            class="form-control">
                        <option value="CF">CF</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                    </select>
                </div>
            </div>

            <div hidden class="col-lg-3 col-sm-6 col-12">
                <div class="form-group">
                    <label>IVA</label>

                    <a class="form-control dropdown-toggle" href="javascript:void(0)" type="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                    </a>
                    <div class="dropdown-menu">
                        <button id="iva" wire:click="UpdateIvaGral( $('#iva1').val()  , {{$NroVenta}} , 1)" value="0"
                                class="dropdown-item">Sin IVA
                        </button>
                        <button id="iva2" wire:click="UpdateIvaGral( $('#iva2').val()  , {{$NroVenta}} , 1 )"
                                value="0.105" class="dropdown-item">10,5%
                        </button>
                        <button id="iva3" wire:click="UpdateIvaGral( $('#iva3').val()  , {{$NroVenta}} , 1 )"
                                value="0.210" class="dropdown-item">21%
                        </button>
                        <button id="iva4" wire:click="UpdateIvaGral( $('#iva4').val()  , {{$NroVenta}} , 1 )"
                                value="0.270" class="dropdown-item">27%
                        </button>
                    </div>


                </div>
            </div>

            <div hidden class="col-lg-3 col-sm-6 col-12">
                <div class="form-group">
                    <label>Descuento</label>
                    <div class="input-group mb-0">
                        <input type="number"
                               wire:change="UpdateDescuentoGral()">
                        <div class="input-group-append">
                                         <span class="input-group-text input-gp">
                                         %
                                         </span>
                        </div>
                    </div>
                </div>
            </div>

            <div hidden class="col-lg-3 col-sm-6 col-12">
                <div class="form-group">
                    <label>Costo de envio</label>
                    <input type="number" class="form-control">
                </div>
            </div>


            <div class="col-lg-12">
                <div class="form-group">
                    <label>Nota interna</label>
                    <textarea wire:model="nota_interna" id="nota_interna"
                              wire:keydown.enter="UpdateNotaInterna( $('#nota_interna').val() )"
                              wire:change="UpdateNotaInterna( $('#nota_interna').val() )"
                              class="form-control"></textarea>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-group">
                    <label>Observaciones</label>
                    <textarea wire:model="observaciones" id="observaciones"
                              wire:keydown.enter="UpdateObservaciones( $('#observaciones').val() )"
                              wire:change="UpdateObservaciones( $('#observaciones').val() )"
                              class="form-control"></textarea>
                </div>
            </div>
            <div class="col-lg-3">
                <button class="btn btn-cancel" wire:click="resetUI">Volver</button>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function () {

        $("input[data-type='currency']").each(function () {
            formatCurrency($(this));
        }).on({
            keyup: function () {
                formatCurrency($(this));
            },
            blur: function () {
                formatCurrency($(this), "blur");
            },
            change: function () {
                formatCurrency($(this), "blur");
            }
        });


        function formatNumber(n) {
            // format number 1000000 to 1,234,567
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        }


        function formatCurrency(input, blur) {

            let input_val = input.val();

            // don't validate empty input
            if (input_val === "") {
                return;
            }

            // original length
            let original_len = input_val.length;

            // initial caret position
            let caret_pos = input.prop("selectionStart");

            // check for decimal
            if (input_val.indexOf(".") >= 0) {

                // get position of first decimal
                // this prevents multiple decimals from
                // being entered
                let decimal_pos = input_val.indexOf(".");

                // split number by decimal point
                let left_side = input_val.substring(0, decimal_pos);
                let right_side = input_val.substring(decimal_pos);

                // add commas to left side of number
                left_side = formatNumber(left_side);

                // validate right side
                right_side = formatNumber(right_side);

                // On blur make sure 2 numbers after decimal
                if (blur === "blur") {
                    right_side += "00";
                }

                // Limit decimal to only 2 digits
                right_side = right_side.substring(0, 2);

                input_val = "$ " + left_side + "." + right_side;

            } else {
                input_val = formatNumber(input_val);
                input_val = "$" + input_val;

                // final formatting
                if (blur === "blur") {
                    input_val += ".00";
                }
            }

            // send updated string to input
            input.val(input_val);

            // put caret back in the right position
            let updated_len = input_val.length;
            caret_pos = updated_len - original_len + caret_pos;
            input[0].setSelectionRange(caret_pos, caret_pos);
        }

    })

</script>
