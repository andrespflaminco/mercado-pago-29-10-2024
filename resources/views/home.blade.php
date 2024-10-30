<style media="screen">
.navbar {
  display: none !important;
}
#boton {
		position: absolute;
		margin-top: 40%;
		font-size: 26px;
		font-weight: bold;
		margin-left: 44%;
		background: #1e324b !important;
		border-color: #1e324b !important;

}
.logo img {

width: 100%;
height: auto;

}
.layout-px-spacing {
  padding: 0 !important;
}
</style>
@extends('layouts.theme2.app')

@section('content')
@if (session()->has('success'))

	<a type="button" class="btn btn-danger" id="boton" href="{{ url('pos') }}"> Comenzar
	</a>

<div class="logo">
  <img src="../assets/img/regist2.jpg">

</div>
@endif

@if (isset($errors) && $errors->any())

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <br><br><br>
            <div class="card">


                <div class="card-body">


                  <div class="faq-layouting layout-spacing">


                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>



                </div>

                  </div>
            </div>
            <br><br><br>
            <br><br><br>
            <br><br><br>
            <br><br><br>
            <br><br><br>
            <br><br><br>
            <br>
        </div>
    </div>
</div>
@endif

@endsection
