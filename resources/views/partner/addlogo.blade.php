@extends('app')

@section('content')
<div class="container"> 
    <div id="products" class="row list-group col-md-8"> 
		<div class="um-form margin-right-10">
			@if ($errors->has())
				<div class="p-l-20 p-r-20 p-b-20">
					<div class="alert alert-danger">
						@foreach ($errors->all() as $error)
							{{ $error }}<br>        
						@endforeach
					</div>
				</div>
			@endif
			@if (Session::get('message'))
				<div class="p-l-20 p-r-20 p-b-20">
					<div class="alert alert-success">
						{{Session::get('message')}}
					</div>
				</div>
			@endif

			<form name="add_user" id="add_user" action="{{url()}}/partner/addlogo" method="post" enctype="multipart/form-data" onSubmit="return validate();">
				<input type="hidden" name="reedemer_id" value="{{$reedemer_id}}">
				<input type="hidden" name="logo_text" value="{{$logo_text}}">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<div class="um-row _um_row_1 ">
					<div class="um-col-1">
						
						<div class="um-field um-field-user_login um-field-text">
							<div id="file_error"></div>
							<div class="um-field-label">
								<label for="company_name">Upload Logo</label>
								<div class="um-clear"></div>
							</div>
							<div class="um-field-area">
								<input accept="image/jpeg" type="file" id="file" name="logo_image" class="demoInputBox" value="" />
								
							</div>
							
						</div>	
					</div>
				</div>
				
				<div class="um-col-alt">
					<div class="um-left um-half">
						<input type="submit" id="register" class="um-button" value="Register">
					</div>
					<div class="um-right um-half">
						
					</div>
					<div class="um-clear"></div>					
				</div>
			</form>
		</div>       
    </div> 
    <div id="products" class="row list-group col-md-4"> 
    	<h2>New User ?</h2>
    	<p>
    		By creating an account you'll be able to move through the checkout process faster, view and track your orders, create offers and more.
    	</p>
    	<p>
    		Store owners, you will be able to customize your offers to redeemar and start building your loyalt program
    	</p>
    	<p>
    		Existing owners sign in
    	</p>
    	<p>
    		<button onclick="window.location='{{url()}}/auth/login'" type="button" class="btn btn-primary">Sign In</button>
    		<!-- <input type="button" id="sign_in" class="um-button" value="Sign In">-->
    	</p>
    </div>
</div>
@endsection
@section('styles')
<style>

</style>
@endsection
@section('scripts')
<script>
    // $(document).ready(function(){
    //     $("#register").click(function(){
    //         alert("a");
    //     });

        

   // });

	function Upload() {
		var fileUpload = document.getElementById("fileUpload");
		if (typeof (fileUpload.files) != "undefined") {
			var size = parseFloat(fileUpload.files[0].size / 1024).toFixed(2);
			alert(size + " KB.");
		} else {
			alert("This browser does not support HTML5.");
		}
	}

	function validate() {
		var file = $('#file').val();
		var extension = file.substr( (file.lastIndexOf('.') +1) );
		if(file=='')
		{
			$("#file_error").html("<div class='alert alert-danger'>Select a file to upload.</div>");
			$("#file_error").css("border-color","#FF0000");
			return false;
		}
		if(extension=="jpg" || extension=="jpeg")
		{
			$("#file_error").html("");
			$(".demoInputBox").css("border-color","#F0F0F0");
			var file_size = $('#file')[0].files[0].size;
			if(file_size>2097152) {
				$("#file_error").html("<div class='alert alert-danger'>Upload only .jpg file within 2MB size</div>");
				$("#file_error").css("border-color","#FF0000");
				return false;
			} 
		}
		else
		{
			$("#file_error").html("<div class='alert alert-danger'>Upload only .jpg file within 2MB size</div>");
			$("#file_error").css("border-color","#FF0000");
			return false;
		}
		return true;
	}	
</script>
@endsection
