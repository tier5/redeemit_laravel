<script src="//code.jquery.com/jquery-1.12.0.min.js"></script> 
<link href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="../../public/css/vendors.min.cc72de2f21cf6e67f523.css">
<link rel="stylesheet" href="../../public/css/demo.min.a2f360834fafcc0ef2d1.css">
<link rel="stylesheet" href="../../public/css/custom.css">

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
		<input type="hidden" name="_token" value="{{ csrf_token() }}">	
		<input type="hidden" name="site_path" id="site_path" value="{{ env('SITE_PATH') }}">
		<div class="col-md-12">
			<input name="inventory_name" class="mdl-textfield__input" type="text" id="inventory_name" placeholder="Image Name" />
		</div>
		<div class="col-md-12">
			<input type="file" name="file" id="inventory_image"  />   
		</div>		
		<div class="col-md-12">
			<button type="button" onclick="addFiles()"  class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect" id="add_inventory">
		Save
		</button> 
		</div>
		</form>
	</div>	
</div>
<script>
	function addFiles() 
	{			
		var inventory_name = $('#inventory_name').val();      

        if(!$('input[type=file]')[0].files[0])
        {
        	 $("#error_div").hide();
             $("#show_message").slideDown();
             $("#error_div").html("Please insert all fields.");
             $("#error_div").show();
             $("#success_div").hide();

             return false;
        }
		var formData = new FormData();
		formData.append('file', $('input[type=file]')[0].files[0]);
		formData.append('inventory_name', inventory_name);		
		$.ajax({
			url: "../redeemar/storeaddformfile", // Url to which the request is send
			type: "POST",             // Type of request to be send, called as method
			data:  formData, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data)   // A function to be called if request succeeds
			{		
				//alert(JSON.stringify(data, null, 4));
				//return false;
				var data_str=data.split('^^');
				//alert(data_str[0]+"---"+data_str[1]);
				//return false;
				if(data=="error")
				{
				  $("#error_div").hide();
	              $("#show_message").slideDown();
	              $("#error_div").html("Please insert all fields.");
	              $("#error_div").show();
	              $("#success_div").hide();
				}
				else
				{		
					//parent.$(".inventory_class").html(data);
					//parent.$(".inventory_cost").val('');
					parent.$("#camp_img_id").val(data_str[1]);		
					parent.$('.campaign_image_show').attr('src',$("#site_path").val()+data_str[0]);				
		           	parent.$.fancybox.close();
		        }
			}
		});		
	}
</script>