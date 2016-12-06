@extends('loginapp')

@section('content')
<main class="demo-main mdl-layout__content">
  <h2 class="t-center mdl-color-text--white text-shadow">Redeemar</h2>
  <a id="top"></a>
  <div class="demo-container mdl-grid">
    <div class="mdl-cell mdl-cell--4-col mdl-cell--hide-tablet mdl-cell--hide-phone"></div>
    <div class="demo-content mdl-color--white mdl-shadow--4dp content mdl-color-text--grey-800 mdl-cell mdl-cell--4-col mdl-cell--12-col-tablet">

      <div class="mdl-card__title ">
        <h2 class="mdl-card__title-text">
          <i class="material-icons mdl-color-text--grey m-r-5 lh-13">account_circle</i>
          Register
        </h2>
      </div>
      @if ($errors->has())
        <div class="p-l-20 p-r-20 p-b-20">
          <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
              {{ $error }}<br>        
            @endforeach
          </div>
        </div>
      @endif   

      @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))
          <div class="p-l-20 p-r-20 p-b-20">
            <div class="alert alert-{{ $msg }}">
              {{ Session::get('alert-' . $msg) }} 
            </div>
          </div>
       @endif
      @endforeach
        
      <div class="p-l-20 p-r-20 p-b-20">
        <form class="form-horizontal" role="form" method="POST" action="{{ url('/user/store') }}">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label textfield-demo">
            <input class="mdl-textfield__input" name="company_name" type="text" id="company_name" />
            <label class="mdl-textfield__label" for="company_name">Company Name</label>
          </div>
          <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label textfield-demo">
            <input class="mdl-textfield__input" name="email" type="text" id="email" />
            <label class="mdl-textfield__label" for="email">Email</label>
          </div>
          <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label textfield-demo">
            <input class="mdl-textfield__input" name="password" type="password" id="password" />
            <label class="mdl-textfield__label" for="password">Password</label>
          </div>
          <!-- <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label textfield-demo">
            <input class="mdl-textfield__input" name="cpassword" type="password" id="cpassword" />
            <label class="mdl-textfield__label" for="cpassword">Confirm Password</label>
          </div> -->

          <div class="m-t-20">
          <button ng-disabled type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect mdl-color--light-blue">
            Register
          </button>
          <button type="button" onclick='redirect_url()' class="mdl-button mdl-js-button mdl-js-ripple-effect">
            Login
          </button>
          </div>

        {!! Form::close() !!}
      </div>


    </div>
  </div>
</main>
@endsection
@section('scripts')
  <script >
  function redirect_url()
  {   
    window.location.href={!! json_encode(url('/auth/login')) !!};
  }
  </script>
@endsection