<div wire:ignore.self class="modal fade" id="theModal" tabindex="-1" role="dialog">
  <div  style="z-index: 9999999; max-width: 800px !important;" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
        	<b> Hoja de ruta nueva</b>
        </h5>
        <h6 class="text-center text-warning" wire:loading>POR FAVOR ESPERE</h6>
      </div>
      <div class="modal-body">
 <div class="row">
   <div class="col-sm-12 col-md-6">
    <div class="form-group">

     <label>Nombre del transportista</label>
     <div class="input-group mb-4">
       <input type="text"  wire:model.lazy="nombre_hr" class="form-control">


         </div>

   </div>
   </div>
   <div class="col-sm-12 col-md-6">
    <div class="form-group">
     <label>Tipo de transporte</label>
     <select wire:model.lazy='tipo' class="form-control">
       <option value="Elegir" disabled >Elegir</option>
       <option value="SIN ASIGNAR" >SIN ASIGNAR</option>
       <option value="PROPIO" >PROPIO</option>
       <option value="TERCEROS" >DE TERCEROS</option>

     </select>
   </div>
   </div>

   <div class="col-sm-12 col-md-6">
    <div class="form-group">
     <label>Fecha de entrega</label>
     <div class="input-group mb-4">

       <input type="date" wire:model.lazy="fecha_hr" class="form-control" placeholder="Click para elegir">


         </div>

   </div>
   </div>

<div class="col-sm-12 col-md-6">
 <div class="form-group">
  <label>Turno</label>
  <select wire:model.lazy="turno" class="form-control">
    <option value="Elegir" disabled >Elegir</option>
    <option value="SIN ASIGNAR" >SIN TURNO</option>
    <option value="MAÑANA" >MAÑANA</option>
    <option value="TARDE" >TARDE</option>

  </select>
</div>
</div>

<div class="col-sm-12 col-md-12">
 <div class="form-group">
  <label>Obserevaciones:</label>
  <div class="input-group mb-12">

  <textarea wire:model.lazy="observaciones_hr" class="form-control" rows="3" cols="60"></textarea>


      </div>

</div>
</div>


</div>
</div>
     <div class="modal-footer">

       <button type="button" wire:click.prevent="resetUI()" class="btn btn-dark close-btn text-info" data-dismiss="modal">CERRAR</button>

       <button type="button" wire:click.prevent="GuardarHojaDeRuta({{$ventaId}})" class="btn btn-dark close-modal" >GUARDAR Y ASIGNAR PEDIDO</button>



     </div>
   </div>
 </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function(){


        flatpickr(document.getElementsByClassName('flatpickr'),{
            enableTime: false,
            dateFormat: 'Y-m-d',
            locale: {
                firstDayofWeek: 1,
                weekdays: {
                    shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                    longhand: [
                    "Domingo",
                    "Lunes",
                    "Martes",
                    "Miércoles",
                    "Jueves",
                    "Viernes",
                    "Sábado",
                    ],
                },
                months: {
                    shorthand: [
                    "Ene",
                    "Feb",
                    "Mar",
                    "Abr",
                    "May",
                    "Jun",
                    "Jul",
                    "Ago",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dic",
                    ],
                    longhand: [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Septiembre",
                    "Octubre",
                    "Noviembre",
                    "Diciembre",
                    ],
                },

            }

        })



    });

</script>
