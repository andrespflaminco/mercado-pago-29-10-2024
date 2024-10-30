<style media="screen">
	.monto:hover {
		width: 80px;
    vertical-align: middle;
    color: #515365 !important;
    font-size: 13px !important;
    letter-spacing: 1px !important;
		background-color:
		transparent;
		border-left: none;
		border-top: none;
		border-right: none;
		text-align: center;
		border-bottom: 1px solid #bfc9d4;
	}
	.monto:focus {
		width: 80px;
		background-color:
		transparent;
    vertical-align: middle;
    color: #515365 !important;
    font-size: 13px !important;
    letter-spacing: 1px !important;
		border-left: none;
		border-top: none;
		border-right: none;
		text-align: center;
		border-bottom: 1px solid #bfc9d4;
	}
	.monto {
		width: 80px;
		background-color:
		transparent;
    vertical-align: middle;
    color: #515365 !important;
    font-size: 13px !important;
    letter-spacing: 1px !important;
		border: none;
		text-align: center;
	}
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div style="  max-width: 490px !important;" class="modal-dialog" role="document">
       <div class="modal-content">

           <div style="width:85%;" class="modal-body">
             <br><br>
               <div class="col-sm-12 col-md-12">
               <h4> $ Estado de Pagos </h4>
               </div>

               <div class="col-sm-12 col-md-12">
                 <div class="form-group">
                   <div style="margin-bottom: 0 !important;" class="table-responsive mb-4 mt-4">
                       <table id="tablaReq" class="multi-table table table-hover" style="width:100%">
                           <thead>
                               <tr>
                                   <th class="text-center">Fecha</th>
                                   <th class="text-center">Pago</th>
                               </tr>
                           </thead>
                           <tbody>
                               @foreach($pagos1 as $p1)
                               <tr>
                                   <td class="text-center">{{\Carbon\Carbon::parse( $p1->fecha_factura)->format('d-m-Y')}}</td>
                                   <td class="text-center">$ {{ number_format($p1->cash,2) }} <input hidden  type="number" class="monto" value="{{ number_format($p1->cash,2) }}"></td>

                               </tr>
                               @endforeach
                               @foreach($pagos2 as $p2)
                               @if ($p2->monto > 0)


                               <tr id="material{{$p2->id}}">
                                 <td class="text-center">{{\Carbon\Carbon::parse( $p2->fecha_pago)->format('d-m-Y')}}</td>
                                 <td class="text-center">$
                                <input type="number" class="monto" name="monto[]" id="cambio{{$p2->id}}" onchange="Cambio({{$p2->id}})"value="{{ number_format($p2->monto,2) }}"> </td>
                                <td class="text-center">

                                 <a href="javascript:void(0)" onclick="Confirm('{{$p2->id}}')" >
                                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                 </a>

                               </td>

                               </tr>


                                 @endif
                                 @endforeach





                           </tbody>
                           <tfoot>
                               <tr>
                                   <th class="text-center">Total </th>
                                   <th class="text-center">
                                     <div id="grantotal">
                                       $ {{number_format($suma_monto+$suma_cash,2)}}

                                     </div>
                                      <input hidden type="number" id="total">

                                    </th>
                               </tr>
                           </tfoot>
                       </table>
                       <form name="add-blog-post-form" id="add-blog-post-form" method="post" action="{{url('store-form-modal')}}">
                       @csrf
                       <input hidden  name="id_factura" value="{{$ventaId}}" class="form-control" >
                       @foreach($pagos2 as $p2)
                       @if ($p2->monto > 0)

                        <input hidden type="number" name='tipo[]'  value="0" >
                        <input hidden type="number" name="id[]" value="{{ $p2->id }}">
                         <input hidden type="number" name="monto[]" id="cambiado{{$p2->id}}" value="{{ number_format($p2->monto,2) }}">
                         <input hidden name="eliminado[]" id="eliminador{{$p2->id}}" value="0">


                         @endif
                         @endforeach

                       <div id="append">

                       </div>


                   </div>



                </div>

               </div>
               @if(($suma_monto+$suma_cash) < $tot)
                 <input hidden type="number" id="valor_pedido" value="{{$tot}}">
                 <div id="deuda">

                 <strong>Deuda: $ {{$tot - ($suma_monto+$suma_cash) }}</strong>
               </div>

               <br><br>

                <div class="form-group">
                  <label for="exampleInputEmail1">Agregar pago</label>

                  <div class="input-group mb-4">

                  <input autocomplete="off" type="number" id="monto"  class="form-control" >
                  <div class="input-group-append">
                            <button type="button" id="creaRenglon" class="btn btn-dark">+</button>

                 </div>
                 </div>
                </div>

              @endif
             </div>

           <div class="modal-footer">
               <button class="btn" data-dismiss="modal"><i class="flaticon-cancel-12"></i> Cancelar </button>
                <button type="submit" class="btn btn-dark">Guardar</button>
           </div>
           </form>
       </div>
   </div>
</div>

<script type="text/javascript">
  $('#creaRenglon').click(function(){
    var monto = $('#monto').val();


var cont = $(".material").length;

    var html = "<tr id = 'material"+(cont+1)+"' class = 'material'>"+"<td class='text-center'> <input hidden type='text' name='fecha[]'  value='{{\Carbon\Carbon::parse(now())}}' > {{\Carbon\Carbon::parse(now())->format('d-m-Y')}} </td>"+"<td class='text-center'> $ "+monto+"<input type='number' class='monto' name='monto[]'  value='"+monto+"' ></td>"+"</tr>";
    $("#tablaReq tbody").append(html);


    var html2 = "<input hidden type='text' name='tipo[]'  value='1' > <input hidden type='text' name='id[]'  value='' > <input hidden type='text' name='eliminado[]'  value='0' > "+"<input hidden type='number' name='monto[]'  value='"+monto+"'>";
    $("#append").append(html2);

$('#monto').val('');


var tot = 0;
$(".monto").each(function () {
tot+=Number($(this).val());
});


var valor_pedido = $('#valor_pedido').val();
var deuda = parseFloat(valor_pedido) - parseFloat(tot);

$("#grantotal").html("$ "+tot.toFixed(2));

$("#deuda").html("<strong>Deuda: $ "+deuda.toFixed(2)+"</strong>");


$('#total').val(tot.toFixed(2));


  });
</script>
<script type="text/javascript">
  function Cambio(index) {
    var cambio = $("#cambio"+index).val();
    $("#cambiado"+index).val(cambio);



    var tot = 0;
    $(".monto").each(function () {
    tot+=Number($(this).val());
    });


    var valor_pedido = $('#valor_pedido').val();
    var deuda = parseFloat(valor_pedido) - parseFloat(tot);

    $("#grantotal").html("$ "+tot.toFixed(2));

    $("#deuda").html("<strong>Deuda: $ "+deuda.toFixed(2)+"</strong>");


    $('#total').val(tot.toFixed(2));


  }
</script>
<script type="text/javascript">
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
      $("#eliminador"+id).val(1);
      $("#cambio"+id).val(0);
      $("#material"+id).css('display','none');

      var tot = 0;
      $(".monto").each(function () {
      tot+=Number($(this).val());
      });


      var valor_pedido = $('#valor_pedido').val();
      var deuda = parseFloat(valor_pedido) - parseFloat(tot);

      $("#grantotal").html("$ "+tot.toFixed(2));

      $("#deuda").html("<strong>Deuda: $ "+deuda.toFixed(2)+"</strong>");


      $('#total').val(tot.toFixed(2));

      swal.close()
    }

  })
}
</script>
