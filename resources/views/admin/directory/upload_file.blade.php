<script src="//code.jquery.com/jquery-1.12.0.min.js"></script> 
<link href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('/css/vendors.min.cc72de2f21cf6e67f523.css') }}">
<link rel="stylesheet" href="{{ asset('/css/demo.min.a2f360834fafcc0ef2d1.css') }}">
<link rel="stylesheet" href="{{ asset('/css/custom.css') }}">


<div class="p-l-20 p-r-20 p-b-20" id="show_message" style="display:none">
<div id="success_div" class="alert alert-success">
    
</div>
 <div id="error_div" class="alert alert-danger">
    
</div>
</div>
<div class="col-md-12">
	<div class="col-md-12">
		<h2>Upload Image</h2>
	</div>
	<div class="col-md-12">
		<form id="uploadimage" action="" method="post" enctype="multipart/form-data">	
		<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">	
		<input type="hidden" name="site_path" id="site_path" value="{{ env('SITE_PATH') }}">
		<div class="mdl-textfield">
			<p>Image Name</p>			
			<input name="image_name" class="mdl-textfield__input" type="text" id="image_name" placeholder="Image Name" required />
		</div> 	
		<div class="mdl-textfield">
			<p>Select Directory</p>
			<select id="directory_id" ng-model="selectedFolder">	
				<option value="">Root Folder</option>
				@foreach ($directory_list as $directory)
			        <option value="{{ $directory->id }}"  >{{ $directory->original_name }}</option>
			    @endforeach
			</select>  
		</div> 	
		<div class="mdl-textfield">
			<p>Upload Image</p>
			
			<input type="file" accept="image/*" id="file" name="file"  />
		</div> 		
		<div class="col-md-12">
			<button type="button" onclick="upload_files()" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect" id="add_inventory">
		Save
		</button> 
		</div>
		</form>
	</div>	
</div>
<script>
	var img = "";
    // you can do this once in a page, and this function will appear in all your files 
    File.prototype.convertToBase64 = function(callback){
            var FR= new FileReader();
            FR.onload = function(e) {
                 callback(e.target.result)
            };       
            FR.readAsDataURL(this);
    }

    $("#file").on('change',function(){
      var selectedFile = this.files[0];
      selectedFile.convertToBase64(function(base64){
           img = base64;
      }) 
    });

	function upload_files() 
	{		
		//alert("a");
		//return false;
		var image_name = $('#image_name').val();
        var _token = $('#token').val();
        var dir_id = $('#directory_id').val();
        var site_path = $('#site_path').val();
        var formData = new FormData();

        if(!$('input[type=file]')[0].files[0])
        {
        	 $("#error_div").hide();
             $("#show_message").slideDown();
             $("#error_div").html("Please insert all fields.");
             $("#error_div").show();
             $("#success_div").hide();

             return false;
        }

		formData.append('image_data', img);
		formData.append('image_name', image_name);
		formData.append('_token', _token);
		formData.append('dir_id', dir_id);
		$.ajax({
			url: "../directory/upload", // Url to which the request is send
			type: "POST",             // Type of request to be send, called as method
			data:  formData, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data)   // A function to be called if request succeeds
			{	
				//alert(JSON.stringify(data,null,4));
				if(data.status=="error")
				{
				  
				  $("#error_div").hide();
	              $("#show_message").slideDown();
	              $("#error_div").html("Please insert all fields.");
	              $("#error_div").show();
	              $("#success_div").hide();
				}
				else
				{
					var image_new_name=data.image_name;
					var img_id=data.image_id;

					//parent.$(".inventory_class").html(data);
					//parent.$(".inventory_cost").val('');
					parent.$("#camp_img_id").val(img_id);				
					parent.$('.campaign_image_show').attr('src',site_path+image_new_name);				
		           	parent.$.fancybox.close();
		        }
			}
		});		
	}
</script>