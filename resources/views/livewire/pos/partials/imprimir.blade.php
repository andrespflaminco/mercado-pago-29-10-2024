<head>
  <style media="screen">
  .carta-imprimir {
    margin-top: 12px !important;
  }
  .carta {
    outline: 0px;
    width: 100%;
    height: 100%;
    padding: 25px 40px;
    padding-bottom: 16px;
    display: flex;
    flex-direction: column;
    -webkit-box-align: center;
    align-items: center;
    cursor: pointer;
    background-color: var(--theme-ui-colors-background,#fff);
    border-radius: 6px;
    box-shadow: rgba(0, 0, 0, 0.1) 0px 1px 3px 0px, rgba(0, 0, 0, 0.06) 0px 1px 2px 0px;
    outline: 0px;
  }
  @media (min-width: 576px) {
.modal-body {
  margin: 0 auto !important;
}
}
  </style>
</head>
<div wire:ignore.self class="modal fade" id="modalImprimir" tabindex="-1" role="dialog">

      <div class="modal-dialog" style="max-width: 450px !important;
      margin: 1.75rem auto;" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">ELIJA UNA ACCION</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
              </div>
              <div class="modal-body">

              <div class="row">
                <div class="col-4">

                  <div class="carta text-center">
                    <a href="javascript:void(0);" wire:click="SwitchImprimir(2,{{$ventaId}})">
                      <svg style="color: black;" xmlns="http://www.w3.org/2000/svg" width="88" height="88" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                      <br>
                    <div class="carta-texto">
                    <h6 style="color: black;">IMPRIMIR A4</h6>
                    </div>

                    </a>
                  </div>

              </div>

              <div class="col-4">
                <div class="carta text-center">
                 <a href="javascript:void(0);" wire:click="SwitchImprimir(1,{{$ventaId}})">
                  <svg style="color: black;" xmlns="http://www.w3.org/2000/svg" width="88" height="88" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bookmark"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                  <br>

                  <div class="carta-texto">
                  <h6 style="color: black;">IMPRIMIR TICKET</h6>
                  </div>
                  </a>

                </div>
            </div>


                <div class="col-4">
                    @foreach($mail as $m)
                  <div class="carta text-center">
                    
                      @if($m->email != null)
                    <a style="margin-left: 10px !important;" href="{{ url('report-email-rapido/pdf' . '/' . $ventaId  . '/' . $m->email) }}" >
                         <svg style="color: black;" xmlns="http://www.w3.org/2000/svg" width="88" height="88" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg> </a>
                    @else 
                    <a style="margin-left: 10px !important;" href="javascript:void(0)" wire:click="MailModal({{$ventaId}})" > <svg style="color: black;" xmlns="http://www.w3.org/2000/svg" width="88" height="88" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg> </a>    
              
                    @endif
                      
                      <br>
                    <div class="carta-texto">
                    <h6 style="color: black;">ENVIAR</h6>
                    </div>

                    </a>
                  </div>
                    @endforeach

              </div>



              </div>
          </div>
      </div>
  </div>

  </div>
