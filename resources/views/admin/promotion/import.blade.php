<!DOCTYPE html>
<html lang="en">
<head>
  <title> Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
</head>
<body>

<div class="container-fluid">
  <!-- <h1>Hello World!</h1>
  <p>Resize the browser window to see the effect.</p> -->
  <div class="row">
    <table width="100%" border="1" cellpadding="5" cellspacing="5">
      
      <tr>
          <td>Select</td>
          <td>Image</td>
          <td>Name</td>
      </tr>
      <input type="hidden" name="site_path" id="site_path" value="{{$site_path}}" />
      
      @foreach($directory_list as $directory)
        
          <tr class="listing-row">
              <td>
                @if($directory->directory==0)
                  <input type="radio" name="select_image" id="select_image" value="{{$directory->id}}" />
                @endif
              </td>
              @if($directory->directory==0)
              <td>
                @if($directory->directory_id >0)
                <img src="{{$site_path}}../../{{$directory->copy_url}}" width="80" />
                @else
                <img src="{{$site_path}}../{{$directory->copy_url}}" width="80" />
                @endif
              </td>
              @else
              <td>
                <span style="cursor:pointer" class="glyphicon glyphicon-folder-open" onclick="go_under_folder({{$directory->id}});"></span>  
              </td>
              @endif
              <td>{{$directory->original_name}}</td>
          </tr>
        
      @endforeach
      <tr>
        <td colspan="3" align="center">
          <input type="button" name="back" id="back" value="Back" onclick="history.go(-1);" />
          <input type="button" name="select_image" id="select_image" value="Select" onclick="use_image()" />
        </td>
      </tr>
    </table>
    
    
    
  </div>
</div>
@section('scripts')
<script>
function go_under_folder(val) 
{ 

  var site_path=$("#site_path").val();
  var folder_id=val;
  window.location.href=site_path+'../index.php/directory/repository/'+folder_id; 
}

function use_image() 
{ 
  //alert("a");
  var site_path=$("#site_path").val();
  var selectd_image=$('input[name=select_image]:checked').val();
  //alert(selectd_image);
  //return false;
  $.ajax({
    url: "<?php echo url();?>/directory/repositoryimage/"+selectd_image, // Url to which the request is send
    type: "GET",             // Type of request to be send, called as method
    data:  selectd_image, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
    contentType: false,       // The content type used when sending data to the server.
    cache: false,             // To unable request pages to be cached
    processData:false,        // To send DOMDocument or non processed data file it is set to false
    success: function(data)   // A function to be called if request succeeds
    { 
        var image_new_name=site_path+data.copy_url;
        var image_new_id=data.id;
       
        parent.$('.prod_image').attr('src',image_new_name);   
        parent.$("#image_id").val(image_new_id);       
        parent.$.fancybox.close();      
    }
  }); 
}
</script>    
</body>
</html>
