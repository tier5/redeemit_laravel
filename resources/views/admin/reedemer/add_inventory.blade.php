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
		<div class="container">
		  <h2>Add Inventory</h2>
		  <form id="uploadimage" action="" method="post" enctype="multipart/form-data">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">	
			<input type="hidden" name="site_path" id="site_path" value="{{ env('SITE_PATH') }}">
		    <div class="form-group">
		      <label class="control-label col-sm-2" for="inventory_name">Name:</label>
		      <div class="col-sm-10">
		        <input name="inventory_name" class="mdl-textfield__input" type="text" id="inventory_name" placeholder="Name" />
		      </div>
		    </div>
		    <div class="form-group">
		      <label class="control-label col-sm-2" for="inventory_image">Image:</label>
		      <div class="col-sm-10">          
		        <input type="file" name="inventory_image" id="inventory_image"  />   
		      </div>
		    </div>
		    <div class="form-group">
		      <label class="control-label col-sm-2" for="sell_price">Sell Price($):</label>
		      <div class="col-sm-10">          
		        <input name="sell_price" class="mdl-textfield__input" type="text" id="sell_price" placeholder="Sell Price($)" />
		      </div>
		    </div>
		    <div class="form-group">
		      <label class="control-label col-sm-2" for="cost">Cost($):</label>
		      <div class="col-sm-10">          
		        <input name="cost" class="mdl-textfield__input" type="text" id="cost" placeholder="Cost($)" />
		      </div>
		    </div>
		    
		    <div class="form-group">        
		      <div class="col-sm-offset-2 col-sm-10">
		        <button type="button" onclick="addInventory()"  class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect" id="add_inventory">
				Save
				</button> 
		      </div>
		    </div>
		  </form>
		</div>
	</div>	
</div>
<script>
	function addInventory() 
	{		
		var inventory_name = $('#inventory_name').val();
        var sell_price = $('#sell_price').val();
        var cost = $('#cost').val();
        var site_path = $('#site_path').val();

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
		formData.append('sell_price', sell_price);
		formData.append('cost', cost);
		$.ajax({
			url: "storeaddform", // Url to which the request is send
			type: "POST",             // Type of request to be send, called as method
			data:  formData, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data)   // A function to be called if request succeeds
			{	
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
					parent.$(".inventory_class").html(data);
					parent.$(".inventory_cost").val('');
					parent.$(".selling_price_class").val('');				
					parent.$('.inventory_image_class').attr('src',site_path+'../uploads/no-image-found.gif');				
		           	parent.$.fancybox.close();
		        }
			}
		});		
	}
</script>