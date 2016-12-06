<!doctype html>
<html lang="en"  ng-app="myapp">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="A front-end template that helps you build fast, modern mobile web apps.">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Redeemar - @yield('title')</title>

  <!-- Add to homescreen for Chrome on Android -->
  <meta name="mobile-web-app-capable" content="yes">
  <link rel="icon" sizes="192x192" href="images/touch/chrome-touch-icon-192x192.png">

  <!-- Add to homescreen for Safari on iOS -->
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-title" content="Material Design Lite">
  <link rel="apple-touch-icon-precomposed" href="apple-touch-icon-precomposed.png">

  <!-- Tile icon for Win8 (144x144 + tile color) -->
  <meta name="msapplication-TileImage" content="images/touch/ms-touch-icon-144x144-precomposed.png">
  <meta name="msapplication-TileColor" content="#3372DF">

  <link rel="canonical" href="">

  <link href='//fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en' rel='stylesheet' type='text/css'>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

  <link rel="stylesheet" href="{{ asset('/frontend/admin/css/material.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/frontend/admin/css/helpers.css') }}">
  <link rel="stylesheet" href="{{ asset('/frontend/admin/css/login.css') }}">
  <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
</head>
<body>
<div class="demo-layout mdl-layout mdl-layout--fixed-header mdl-js-layout mdl-color--grey-100">
  <!-- <div class="demo-ribbon mdl-color--accent"></div> -->
  @yield('content')
  
</div>

<script src="{{ asset('/frontend/admin/js/material.min.js') }}"></script>
<script src="{{ asset('/frontend/admin/js/material.min.js') }}"></script>

@yield('scripts')
</body>
</html>
