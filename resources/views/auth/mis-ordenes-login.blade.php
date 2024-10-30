<main>


        <!-- Account-Login -->
        <section class="account-sign">
            <div class="container" style="min-height: 700px !important;">
                <div class="row">
                    <div class="col-lg-3 col-md-12">
                      
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div style="margin: 45px !important;" class="account-sign-in">
                            <h5 class="text-center">Iniciar sesion</h5>
                            
                            @if($imagen != null)
                          <img  style="max-height: 120px;" src="{{ asset('storage/users/'.$imagen) }}" alt="logo" />
                          @else
                          <h6></h6>
                          @endif

                        <form class="text-left mt-5" action="{{ url('store-login-mis-ordenes') }}" method="POST">
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
                               
                                <button  type="submit" class="btn bg-primary">Iniciar sesion</button>
                            </form>





                        </div>
                    </div>
                    <div class="col-lg-3 col-md-12">
                      
                    </div>
                  
                </div>
            </div>
        </section>
    </main>
