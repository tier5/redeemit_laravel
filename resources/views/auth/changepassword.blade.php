@extends('loginapp')

@section('title', 'Reset Password')

@section('content')
<main class="demo-main mdl-layout__content">
  <div class="login-top-menu">
    <ul>
      <li><a href="../auth/login">Sign In</a></li>
      <li><a href="../partner">Sign Up</a></li>
    </ul>
  </div>  
  <!-- <h2 class="t-center mdl-color-text--white text-shadow">Redeemar</h2> -->
  <div class="login-logo"><img src="{{ asset('/images/login.png') }}" class="img-responsive"></div>
  <a id="top"></a>
  <div class="demo-container mdl-grid">
    <div class="mdl-cell mdl-cell--4-col mdl-cell--hide-tablet mdl-cell--hide-phone"></div>
    <div class="demo-content mdl-color--white login-section mdl-shadow--4dp content mdl-color-text--grey-800 mdl-cell mdl-cell--4-col mdl-cell--12-col-tablet">

      <!-- <div class="mdl-card__title ">
        <h2 class="mdl-card__title-text">
          <i class="material-icons mdl-color-text--grey m-r-5 lh-13">account_circle</i>
          Login
        </h2>
      </div> -->
      @if ($errors->has())
        <div class="p-l-20 p-r-20 p-b-20">
          <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
              {{ $error }}<br>        
            @endforeach
          </div>
        </div>
      @endif
      @if (session('status'))

      <p class="otp-notice">{{session('status')}}</p>
      
      
      @endif
      <div class="p-l-20 p-r-20 p-b-20 nopadding">
        <form class="form-horizontal login-form" role="form" method="POST" action="{{ url('/password/reset') }}">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" name="email" value="{{$user_email}}">
          <input type="hidden" name="user_type" value="{{$user_type}}">

          <div class="form-inputs">
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label textfield-demo">
              <input class="mdl-textfield__input login-field" name="password" type="password" id="email" placeholder="Enter New Password"/>
              <!-- <label class="mdl-textfield__label" for="email">email</label> -->
            </div>
            <hr>
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label textfield-demo">
              <input class="mdl-textfield__input login-field" name="password_confirmation" type="password" id="password" placeholder="Confirm New Password"/>
              <!-- <label class="mdl-textfield__label" for="password">Password</label> -->
            </div>
          </div>

          <div class="m-t-20">
          <button type="submit" type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect mdl-color--light-blue">
            Change Password
          </button>
          </div>
        </form>
        {!! Form::close() !!}
      </div>


    </div>
  </div>
</main>
@endsection
@section('scripts')
  <script >
  //function redirect_url()
  //{   
    //window.location.href={!! json_encode(url('/user/add')) !!};
  //}
  </script>
@endsection