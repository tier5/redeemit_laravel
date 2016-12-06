<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redeemar Sign In</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('/front_end/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/front_end/css/custom.css') }}">

    <link rel="stylesheet" href="{{ asset('/front_end/fonts/font-awesome/css/font-awesome.min.css') }}">
    

   <link rel="stylesheet" href="{{ asset('/front_end/css/jquery.bxslider.css') }}">
   
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    @yield('styles')
  </head>
  <body style="background: #f1f1f1;">
    @include('layout.nav')
<div class="clear"></div> 

<div class="container">
    @yield('content')
</div>
<div class="clear"></div>
<!-- Footer Start -->  
@include('layout.footer')
<!-- Footer End --> 
<input type="hidden" name="main_site_url" id="main_site_url" value="{{ url() }}" />
<input type="hidden" name="site_path" id="site_path" value="{{getenv('SITE_PATH')}}"  />
</body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>


    <script src="{{ asset('/front_end/js/jquery.bxslider.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/front_end/js/jquery.form.js') }}"></script>
    
    <script type="text/javascript"> 

    $(document).ready(function(){
        $('.bxslider').bxSlider({
            auto: true,
            autoControls: false,
            minSlides: 3,
            maxSlides: 3,
            slideWidth: 170,
            slideMargin: 5,
            pager: false,
            speed: 500
        });
        $(".hide_section_first").hide();
    });

    jQuery(document).ready(function(){
       // var site_path=$("site_path").val();
        //alert(site_path);
    jQuery(".click").click(function(){
    jQuery(".main_nav").slideToggle(700);
        //$('.image-slider-box').text('a');
    });
    
    });

    function jqUpdateSize(){
    var width = jQuery('.banner_vdobox').width();
    var sqwidth = width * 33.85 / 100;
    jQuery('.banner_vdobox').css('height', sqwidth);
    };
    jQuery(document).ready(jqUpdateSize);    // When the page first loads
    jQuery(window).resize(jqUpdateSize);     // When the browser changes size
    </script>       
    <script src="{{ asset('/front_end/js/bootstrap.min.js') }}"></script>
    @yield('scripts')
</html>