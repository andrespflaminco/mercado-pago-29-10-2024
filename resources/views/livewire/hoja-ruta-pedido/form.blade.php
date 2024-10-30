 @include('common.modalHead')


 <div class="row">

   <div class="col-sm-12 col-md-6">
    <div class="form-group">
     <label>Fecha de entrega</label>
     <div class="input-group mb-4">

       <input type="text" wire:model="fecha" class="form-control flatpickr" placeholder="Click para elegir">


         </div>

   </div>
   </div>

<div class="col-sm-12 col-md-6">
 <div class="form-group">
  <label>Turno</label>
  <select wire:model='turno' class="form-control">
    <option value="Elegir" disabled >Elegir</option>
    <option value="MAÑANA" >MAÑANA</option>
    <option value="TARDE" >TARDE</option>

  </select>
</div>
</div>


</div>



@include('common.modalFooter')

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
