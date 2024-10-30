

<main>
@inject('cart_ecommerce', 'App\Services\CartEcommerce')

@include('layouts.theme-ecommerce.header')

<section class="cart-area">

      @if(((new \Jenssegers\Agent\Agent())->isMobile()))
      <div style="color: {{$color}} !important; background-color: {{$background_color}} !important; width: 100%;  padding: 10px 0px;">
      <p style="padding-left:30px; color: {{$color}} !important; background-color: {{$background_color}} !important; text-rendering: optimizeSpeed; line-height: 1.5; font-family: Anton, Impact, 'sans-serif'; font-size: 22px;">Mis pedidos</p>
      </div>
      @else
      <!-- BreadCrumb Start-->
      <section class="breadcrumb-area mt-15">
          <div class="container">
              <div class="row">
                  <div class="col-lg-12">
                      <nav aria-label="breadcrumb">
                          <ol class="breadcrumb">
                              <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                              <li class="breadcrumb-item active" aria-current="page">Mis pedidos </li>
                          </ol>
                      </nav>
                      <h5>Mis pedidos</h5>
                  </div>
              </div>
          </div>
      </section>
      <!-- BreadCrumb Start-->
      @endif

      <!--Acount Area Start -->
      <section class="account">
          <div class="container">
              <div class="row">
                  <div class="col-lg-12">
                      <!-- Dashboard-Nav  Start-->
                      <div style="margin-bottom: 40px !important;" class="dashboard-nav">
                          <ul class="list-inline">
                              <li class="list-inline-item"><a href="{{ url('ecommerce-account/'.$slug)}}" >Datos de mi cuenta</a></li>
                              <li class="list-inline-item"><a href="{{ url('ecommerce-orders/'.$slug)}} " class="active">Mis pedidos</a></li>
                              <li class="list-inline-item">
                                <a href="javascript:void(0)"
                               wire:click="logout"
                                class="mr-0">
                              Cerrar sesion</a></li>
                                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                    @csrf
                                </form>


                          </ul>
                      </div>
                      <!-- Dashboard-Nav  End-->
                  </div>
                  <div class="col-lg-12 col-md-12">
                    <div class="widget-content widget-content-area">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-4">
                                <thead>
                                    <tr>
                                        <th>Numero de pedido</th>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Forma de pago</th>
                                        <th class="text-center">Estado</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                  @foreach($orders as $o)
                                    <tr>
                                        <td># {{$o->id}}</td>
                                        <td>{{$o->created_at}}</td>
                                        <td>$ {{$o->total - $o->descuento}}</td>
                                        <td>{{$o->metodo_pago}}</td>
                                        <td class="text-center"><span class="text-success">{{$o->status}}</span></td>
                                        <td class="text-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 icon"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></td>
                                    </tr>
                            @endforeach
                                </tbody>
                            </table>
                        </div>


          </div>
      </section>
      <!--Acount Area End -->


        </main>
