@extends('app')

@section('content')
<div class="container">     
    <div id="products" class="row list-group col-md-12"> 
    	<div class="alert alert-success">
			<h4>
                Your account created successfully.<br> Please <a href="../auth/login">CLICK HERE</a> to login in your account or we will redirect you in <span class="cntdwn">10</span> Seconds.
            </h4>
       
		</div>
      <div style="min-height:200px"></div>
    </div>
</div>
@endsection
@section('styles')
<style>
</style>
@endsection
@section('scripts')
<script>
    $(document).ready(function(){
         var totoalSeconds = 10;
         setInterval(function(){
          totoalSeconds--;
          $('.cntdwn').html(totoalSeconds);
         },1000);
        setTimeout(function()
        {             
            window.location.href="../auth/login";
        }, 10000);   
    });


</script>
@endsection
