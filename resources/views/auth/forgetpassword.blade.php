@extends('loginapp')

@section('title', 'Forgot Password')

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

      @if ($errors->has())
        <div class="p-l-20 p-r-20 p-b-20">
          <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
              {{ $error }}<br>        
            @endforeach
          </div>
        </div>
      @endif   
      <div class="p-l-20 p-r-20 p-b-20 nopadding">
          @if (session('status'))

          <p class="otp-notice">{{session('status')}}</p>
          
          @endif
          @if (session('email'))

          <p class="otp-notice">  We are having issues to send email on this {{session('email')}} </p>

          @endif
          <div class="form-group">
            <p class="otp-notice">
              A One-Time Password (OTP) will be sent to your registered Email ID/ Mobile Number.
            </p>
          </div>
          
        <form class="form-horizontal login-form" role="form" method="POST" action="{{ url('/password/email') }}">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">

          <div class="form-group">
            <p class="forgt-pass"><font color="white">Forgot Password for</font></p><p class="forgt-pass"><font color="white"> <input type="radio" name="user_type" value="2" checked="checked"> Reedemer Partner  <input type="radio" name="user_type" value="1"> Reedemer Admin </font></p>
          </div>

          <div class="form-inputs">
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label textfield-demo">
              <input class="mdl-textfield__input login-field" name="email" type="text" id="email" placeholder="Email ID/ Mobile Number"/>
              <!-- <label class="mdl-textfield__label" for="email">email</label> -->
            </div>
          </div>

          <div class="m-t-20">
          <button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect mdl-color--light-blue">
            Continue
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