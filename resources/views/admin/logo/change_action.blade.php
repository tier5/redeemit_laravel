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
		<h2>Update Trigger</h2>
	</div>
	<div class="col-md-12">
		<form id="uploadimage" action="" method="post" enctype="multipart/form-data">	
		<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">	
		<input type="hidden" name="site_path" id="site_path" value="{{ url() }}">
		<input type="hidden" name="logo_id" id="logo_id" value="{{ $logo_id }}">
		
		<div class="mdl-textfield">
			<p>Select Trigger</p>
			<select id="action_id" onchange="open_offer(this.value);">	
		        @foreach ($action_list as $action)
			        <option value="{{ $action->id }}" <?php if($action->id==$action_id){ echo "selected"; } ?>>
			        	{{ $action->action_name }}
			        </option>
			    @endforeach
			</select>  
		</div> 

		<div class="trigger_div mdl-textfield">
			<p>Select Trigger</p>
			<select id="offer_id">	
		        <option value="">--Select Trigger--</option>
			</select>  
		</div> 	
		
		<div class="col-md-12">
			<button type="button" onclick="update_action()" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect" id="add_inventory">
		Save
		</button> 
		</div>
		</form>
	</div>	
</div>
<script>
	function update_action() 
	{
		var logo_id = $('#logo_id').val();
        var _token = $('#token').val();
        var action_id = $('#action_id').val();
        var site_path = $('#site_path').val();
        var formData = new FormData();
        if(action_id==3)
        {
        	 var offer_id = $('#offer_id').val();
        }
        else if(action_id==2)
        {
        	 var offer_id = $('#offer_id').val();
        }
        else
        {
        	 var offer_id = '';
        }

		formData.append('logo_id', logo_id);
		formData.append('action_id', action_id);
		formData.append('_token', _token);
		formData.append('offer_id', offer_id);
		//formData.append('dir_id', dir_id);
		$.ajax({
			url: site_path+"/user/updateaction", // Url to which the request is send
			type: "POST",             // Type of request to be send, called as method
			data:  formData, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data)   // A function to be called if request succeeds
			{	
				//alert(JSON.stringify(data.action_name,null,4));
				if(data.success=="error")
				{
				  
				  $("#error_div").hide();
	              $("#show_message").slideDown();
	              $("#error_div").html("Please insert all fields.");
	              $("#error_div").show();
	              $("#success_div").hide();
				}
				else
				{
					//var image_new_name=data.image_name;
					//var img_id=data.image_id;

					parent.$("#logo_text_"+logo_id).html(data.action_name);
					//parent.$(".trigger_div").show();
					//parent.$("#camp_img_id").val(img_id);				
					//parent.$('.campaign_image_show').attr('src',site_path+image_new_name);				
		           	parent.$.fancybox.close();
		        }
			}
		});		
	}

	function open_offer(trigger_id)
	{
		//alert(trigger_id);
		var site_path = $('#site_path').val();
		if(trigger_id==3)
		{			
			$.ajax({
				url: site_path+"/promotion", // Url to which the request is send
				type: "GET",             // Type of request to be send, called as method
				contentType: false,       // The content type used when sending data to the server.
				cache: false,             // To unable request pages to be cached
				processData:false,        // To send DOMDocument or non processed data file it is set to false
				success: function(data)   // A function to be called if request succeeds
				{
					$(".trigger_div").show();
					var new_html="<option value=''>Select Offer</option>";
					for(var i=0; i<data.length; i++)
					{
					new_html+="<option value='"+data[i].id+"'>"+data[i].offer_description+"</option>";
					}                    
					$('#offer_id').html(new_html);						
			       
				}
			});
		}
		else if(trigger_id==2)
		{
			//alert("A");
			$.ajax({
				url: site_path+"/promotion/campaignbyuser", // Url to which the request is send
				type: "GET",             // Type of request to be send, called as method
				contentType: false,       // The content type used when sending data to the server.
				cache: false,             // To unable request pages to be cached
				processData:false,        // To send DOMDocument or non processed data file it is set to false
				success: function(data)   // A function to be called if request succeeds
				{
					//alert(JSON.stringify(data,null,4));
					$(".trigger_div").show();
					var new_html="<option value=''>Select Campaign</option>";
					for(var i=0; i<data.length; i++)
					{
					new_html+="<option value='"+data[i].id+"'>"+data[i].campaign_name+"</option>";
					}                    
					$('#offer_id').html(new_html);						
			       
				}
			});
		}
		else
		{	
			var new_html="<option value=''>Select Campaign</option>";		
			$('#offer_id').html(new_html);	
			$(".trigger_div").hide();
		}
	}	
</script>