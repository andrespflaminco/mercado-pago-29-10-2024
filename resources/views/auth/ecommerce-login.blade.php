<main>

@inject('cart_ecommerce', 'App\Services\CartEcommerce')


@include('layouts.theme-ecommerce.header')

        <!-- Account-Login -->
        <section class="account-sign">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="account-sign-in">
                            <h5 class="text-center">Iniciar sesion</h5>

                        <form class="text-left mt-5" action="{{ url('store-login') }}" method="POST">
                              @csrf
                                <div class="form__div">
                                    <input type="email" class="form__input" placeholder=" "  id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                    <label for="" class="form__label">Email</label>
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="form__div mb-0">
                                    <input type="password" class="form__input" id="password" name="password" type="password" required autocomplete="current-password">
                                    <label for="" class="form__label">Password</label>
                                    @error('password')
                                   <span class="invalid-feedback" role="alert">
                                       <strong>{{ $message }}</strong>
                                   </span>
                               @enderror
                                </div>
                                <div class="password-info d-flex align-items-center justify-content-between flex-wrap">

                                    <div class="password-info-right">
                                        <a href="{{url('password/reset')}}">Olvido su contraseña?</a>
                                    </div>
                                    <input hidden type="text" name="slug" value="{{$slug}}">

                                </div>
                                @if(session()->has('message'))
                                    <div class="alert alert-danger">
                                        {{ session()->get('message') }}
                                    </div>
                                @endif
                               
                                <button  type="submit" class="btn btn-dark" style="background: {{$background_color}} !important; color: {{$color}} !important;">Iniciar sesion</button>
                            </form>





                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="account-sign-up">
                            <h5 class="text-center">Registrate</h5>
                            <form>
                                <div class="form__div">
                                    <input type="text" class="form__input" id="name" type="text" wire:model.prevent="name" value="{{ old('name') }}" required autocomplete="name" >
                                    <label for="" class="form__label">Nombre del cliente</label>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form__div">
                                    <input id="email" type="email"  wire:model.prevent="email" value="{{ old('email') }}" required type="email" class="form__input" placeholder=" ">
                                    <label for="" class="form__label">Email</label>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror


                                </div>

                                <div class="form__div">
                                    <input id="phone" type="text"  wire:model.prevent="phone" required  class="form__input" placeholder=" ">
                                    <label for="" class="form__label">Telefono</label>
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror


                                </div>
                                <div class="form__div">
                                    <input id="password" type="password" wire:model.prevent="password" class="form__input" placeholder=" ">
                                    <label for="" class="form__label">Contraseña</label>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form__div mb-0">
                                    <input type="password" id="password-confirm" class="form__input" placeholder=" ">
                                    <label for="" class="form__label">Repita la contraseña</label>

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror

                                </div>
                              <br><br>
                                <button type="button" wire:click="Store('{{$slug}}')" class="btn btn-dark" style="background: {{$background_color}} !important; color: {{$color}} !important;">
                                    Registrar
                                </button>

                            </form>




                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
