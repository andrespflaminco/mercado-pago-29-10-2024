

<main>
@inject('cart_ecommerce', 'App\Services\CartEcommerce')

@include('layouts.theme-ecommerce.header')

<section class="cart-area">

      @if(((new \Jenssegers\Agent\Agent())->isMobile()))
      <div style="color: {{$color}} !important; background-color: {{$background_color}} !important; width: 100%;  padding: 10px 0px;">
      <p style="padding-left:30px; color: {{$color}} !important; background-color: {{$background_color}} !important; text-rendering: optimizeSpeed; line-height: 1.5; font-family: Anton, Impact, 'sans-serif'; font-size: 22px;">Datos de la cuenta</p>
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
                              <li class="breadcrumb-item active" aria-current="page">Datos de la cuenta </li>
                          </ol>
                      </nav>
                      <h5>Datos de la cuenta</h5>
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
                      <div style="margin-bottom:40px !important;" class="dashboard-nav">
                          <ul class="list-inline">
                              <li class="list-inline-item"><a href="{{ url('ecommerce-account/'.$slug)}}" class="active">Datos de mi cuenta</a></li>
                              <li class="list-inline-item"><a href="{{ url('ecommerce-orders/'.$slug)}}">Mis pedidos</a></li>
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
                  <div class="col-lg-6 col-md-12">
                      <div class="account-setting">
                          <h6>Mis datos</h6>
                          <form action="#">
                              <div class="form__div">
                                  <input type="text"  wire:model="nombre_cliente"  class="form__input" placeholder="
                                  ">
                                  <label for=""class="form__label">Nombre y apellido</label>
                              </div>
                              <div class="form__div">
                                  <input type="email" wire:model="mail_cliente" class="form__input" placeholder="
                                  ">
                                  <label for="" class="form__label">Email</label>
                              </div>
                              <button type="submit" class="btn btn-dark" style="color: {{$color}} !important; background-color: {{$background_color}} !important; border-color: {{$background_color}} !important;">Guardar cambios</button>
                          </form>
                      </div>
                  </div>
                  <div class="col-lg-6 col-md-12">
                      <div class="change-password">
                          <h6>Cambiar la contraseña</h6>
                          <form action="#">
                            <div class="form__div">
                                  <input type="password" wire:model="password" class="form__input" placeholder=" ">
                                  <label for="" class="form__label">Nueva contraseña</label>
                              </div>
                              @error('password') <span style="color:red;" class="error">{{ $message }}</span> @enderror
                            <div class="form__div mb-40">
                                  <input type="password" wire:model="password_confirm"  class="form__input" placeholder=" ">
                                  <label for="" class="form__label">Confirmar la contraseña</label>
                              </div>
                              @error('password_confirm') <span style="color:red;" class="error">{{ $message }}</span> @enderror

                              @if($mensaje)
                              <span style="color:red;">{{$mensaje}}</span>
                              @endif
                              <button type="button" wire:click="CambiarContraseña()" class="btn btn-dark" style="color: {{$color}} !important; background-color: {{$background_color}} !important; border-color: {{$background_color}} !important;">Guardar cambios</button>
                          </form>
                      </div>
                  </div>
              </div>
          </div>
      </section>
      <!--Acount Area End -->


        </main>
