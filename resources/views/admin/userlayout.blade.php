<!DOCTYPE html>
<html lang="en" ng-app="redeemar-app">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Material Lite Angular Admin Theme">
  <meta name="author" content="Theme Guys - The Netherlands">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script>
    var base_url = '{{url()}}';
    var root_base_url = '{{env('SITE_URL')}}';
  </script>
  <title>Redeemar</title>

  <link href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('/css/vendors.min.cc72de2f21cf6e67f523.css') }}">
  <link rel="stylesheet" href="{{ asset('/css/demo.min.a2f360834fafcc0ef2d1.css') }}">
  <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
  <link rel="stylesheet" href="{{ asset('/css/jquery.rateyo.css') }}">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <link rel="stylesheet" href="{{ asset('/css/thumbelina.css') }}"> 
  <link rel="stylesheet" type="text/css" href="{{ asset('/css/bootstrap.min.css') }}"> 
  <link rel="stylesheet" href="{{ asset('/css/ng-img-crop.css') }}"/>
  <link rel="stylesheet" href="{{ asset('/css/rzslider.css') }}"/>
  
  <link rel="stylesheet" type="text/css" href="{{ asset('/css/fancybox/jquery.fancybox.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('/js/cropee/dist/cropper.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('/js/cropee/css/main.css') }}">

  <link rel="stylesheet" type="text/css" href="{{ asset('/css/jquery.tagit.css') }}">

  <link rel="stylesheet" type="text/css" href="{{ asset('/css/slick/slick.css') }}">

  <link rel="stylesheet" type="text/css" href="{{ asset('/css/slick/slick-theme.css') }}">

  








  <!-- IE Compatibility shims -->
  <!--[if lt IE 9]>
  <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv.min.js""></script>
  <![endif]-->
  <!--[if IE]>
  <script src="//cdnjs.cloudflare.com/ajax/libs/es5-shim/4.1.7/es5-shim.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/classlist/2014.01.31/classList.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/selectivizr/1.0.2/selectivizr-min.js"></script>
  
  <![endif]-->
  <!-- end shims -->
  <style type="text/css">
            /* Some styles for the containers */
            #slider1 {
                position:relative;  /* Containers need relative or absolute position. */
                margin-left:10px;
                /*width:500px;*/
                height:120px;
               /* border-top:1px solid #aaa;
                border-bottom:1px solid #aaa;*/
            }  


.tech-slideshow {
  height: 200px;
  max-width: 800px;
  margin: 0 auto;
  position: relative;
  overflow: hidden;
  transform: translate3d(0, 0, 0);
}

.tech-slideshow > div {
  height: 200px;
  width: 2526px;
  background: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/collage.jpg);
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  transform: translate3d(0, 0, 0);
}
.tech-slideshow .mover-1 {
  animation: moveSlideshow 12s linear infinite;
}
.tech-slideshow .mover-2 {
  opacity: 0;
  transition: opacity 0.5s ease-out;
  background-position: 0 -200px;
  animation: moveSlideshow 15s linear infinite;
}
.tech-slideshow:hover .mover-2 {
  opacity: 1;
}

@keyframes moveSlideshow {
  100% { 
    transform: translateX(-66.6666%);  
  }
}


        </style>
  @yield('style')
</head>

<body ng-controller="MainController" > 

  <!-- <div id="app" class="app" ng-include="'{{ asset('/tpl/demo/app.html') }}'"></div> -->
  <div class="demo-layout mdl-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
    <ml-header></ml-header><!-- This directive uses the partial: src/tpl/demo/partials/header.html -->
    <ml-sidebar></ml-sidebar><!-- This directive uses the partial: src/tpl/demo/partials/sidebar.html -->
    <main class="mdl-layout__content mdl-color--grey-100 page" ng-view></main>   
  </div>
  <input type="hidden" name="main_site_url" id="main_site_url" value="{{ url() }}" />
  <input type="hidden" name="site_path" id="site_path" value="{{getenv('SITE_PATH')}}"  />
  <input type="hidden" name="site_image_path" id="site_image_path" value="{{ asset('/images') }}"  />
  <input type="hidden" name="update_id" id="update_id" value="" />
  
  @yield('content')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
  <script src="http://code.jquery.com/jquery-migrate-1.0.0.js"></script>

   
  <script src="{{ asset('/user/js/vendors.min.c95e87841078c8095acf.js') }}"></script>
  <script src="{{ asset('/user/js/demo.min.9506beb44f2c95b2945c.js') }}"></script>
  <script src="{{ asset('/js/jquery.rateyo.min.js') }}"></script>
  <script src="{{ asset('/js/jquery-ui.js') }}"></script>
  <script src="{{ asset('/user/js/rating.js') }}"></script>  
  <script src="{{ asset('/js/dirPagination.js') }}"></script>
  <script src="{{ asset('/js/fancybox/jquery.fancybox.js') }}"></script>
  <script src="{{ asset('/js/ui-bootstrap-tpls.js') }}"></script>
  <script src="{{ asset('/js/rzslider.js') }}"></script>
  <script src="{{ asset('/js/cropee/assets/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('/js/cropee/dist/cropper.min.js') }}"></script>
  
  <script src="{{ asset('/js/jquery.base64img.js') }}"></script>

  <script src="{{ asset('/js/tag-it.js') }}"></script>
  <script src="{{ asset('/js/angular-dragdrop.min.js') }}"></script>
  

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js"></script>
   
  <link rel="Stylesheet" type="text/css" href="
http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css
" />
  
  <script src="{{ asset('/user/js/campaign.js') }}"></script>
  <script src="{{ asset('/user/js/inventory.js') }}"></script>
  <script src="{{ asset('/user/js/product.js') }}"></script>
  <script src="{{ asset('/user/js/repo.js') }}"></script>
  <script src="{{ asset('/user/js/logo.js') }}"></script>
  <script src="{{ asset('/user/js/video.js') }}"></script>
  <script src="{{ asset('/user/js/promotion.js') }}"></script>
  <script src="{{ asset('/user/js/partnersetting.js') }}"></script>
  <script src="{{ asset('/user/js/profile.js') }}"></script>
  
  @yield('scripts')

</body>
</html>
