@extends('app')

@section('content')
<style type="text/css">
  .autoUpdate{display: none;}
</style>
<div class="row">
        <div class="col-md-12 col-sm-12 registration">
            <h2>Registration</h2>
            
            
              
            <form id="add_reedemar_details" method="post" enctype="multipart/form-data" action=''> 
              <input type="hidden" name="_token" value="{{ csrf_token() }}" />
               
            <div class="col-md-6 col-sm-6 reg-left">
                  <h3>Your Company Information</h3>
                  <div class="form-group">

                    <input type="text" required placeholder="Company Name" value="{{ old('web_address') }}" id="company_name" name="company_name" class="form-control">
                    <div id="company_name_msg" class="red" ></div>
                  </div>
                  <div class="form-group">
                    <select class="form-control" name="category_id" id="category_id" onchange="get_sub_cat(this.value)">                      
                      <option  value="">---Select Category---</option>
                      @foreach($category_details as $category)
                      <option  value="{{$category->id}}">{{$category->cat_name}}</option>
                      @endforeach
                    </select>
                    <div id = "category_id_msg" class="red" ></div>                   
                  </div>
                  <div class="form-group">
                   <select class="form-control" name="subcat_id" id="subcat_id">
                        <option value="">Select category first</option>
                   </select>  
                    <div id = "subcat_id_msg" class="red" ></div>                               
                  </div>
                  <div class="form-group">       
                    <input type="text" placeholder="Company Website" value="{{ old('web_address') }}" id="web_address" name="web_address" class="form-control um-form-field valid">
                    <div id = "web_address_msg" class="red" ></div>                               
                  </div>
                  <div class="form-group">
                      <div class="row">
                    <div class="col-md-6">
                    <input type="text" placeholder="Owner Name" value="{{ old('first_name') }}" id="first_name" name="first_name" class="form-control " onblur="firstname()">
                    <div id = "first_name_msg" class="red" ></div>   
                    </div>
                    <div class="col-md-6">
                    <input type="text" placeholder="Last Name" value="{{ old('last_name') }}" id="last_name" name="last_name" class="form-control " onblur="lastname()">
                    <div id = "last_name_msg" class="red" ></div> 
                      </div>
                        </div>
                  </div>
                  <h3>Company Contact Details</h3>
                  <div class="row">
                  <div class="col-md-6">
                  <div class="form-group">  
                    <input type="email" placeholder="Email" value="{{ old('user_email') }}" id="user_email" name="user_email" class="form-control">
                    <div id = "user_email_msg" class="red"></div>   
                  </div>
                  </div>
                  <div class="col-md-6">
                  <div class="form-group">
                    <input type="text" onblur="mobileCheck()" class="form-control"  id="mobile" name="mobile" placeholder="Phone Number">
                    <div id = "mobile_msg" class="red"></div>                               
                  </div>
                  </div>

                  </div>
                  <h3>Company Address</h3>
                  <div class="form-group">
                    <input type="text" placeholder="Street Address" value="{{ old('address') }}" id="address" name="address" class="form-control">
                    <div id = "address_msg" class="red"></div>                               
                  </div> 
                  <div class="form-group">
                    <div class="row">
                      <div class="col-md-4">
                        <input type="text" class="form-control " id="city" name="city" placeholder="City">
                        <div id = "city_msg" class="red" ></div>  
                      </div>
                      <div class="col-md-4">
                        <input type="text" class="form-control " id="state" name="state" placeholder="State" class="red"> 
                        <div id = "state_msg"></div>     
                      </div>
                      <div class="col-md-4">
                        <input type="text" onblur="checkMe()" placeholder="Zip" value="{{ old('zipcode') }}" id="zipcode" name="zipcode" class="form-control ">
                        <div id = "zipcode_msg" class="red"></div>  
                      </div>
                    </div>                                                 
                  </div>
                  <h3>Username & Password</h3>
                   <div class="form-group">
                    
                   
                      <div class="row autoUpdate" id="autoUpdate">
                        <div class="col-md-12 col-sm-12">
                          <!-- username class was removed -->
                          <input type="text" class="form-control username" placeholder="Username">
                        </div>
                      
                      
                    </div>
                    <div class="row">
                      <div class="col-md-6 col-sm-6">
                        <input type="password" value="" placeholder="Password" id="user_password" name="user_password" class="form-control">
                        <div id = "user_password_msg" class="red"></div>
                      </div>
                      <div class="col-md-6 col-sm-6">
                        <input type="password" placeholder="Retype Password" value="" id="confirm_user_password" name="confirm_user_password" class="form-control">
                        <div id = "confirm_user_password_msg" class="red"></div>
                      </div>

                    </div>  
                   </div>

                   

                   <div class="checkbox policy-check">
                   <label>
                   <label>
                   <input  type="checkbox" value="1" name="email_as_username" class="checkbox-custom email_as_username" id="checkbox-10" > 
                      <label for="checkbox-10" class="checkbox-custom-label">I want to use email ID as my username</label>
                    </label> <br>
                    <label>
                      <input type="checkbox" value="1" name="owner" class="checkbox-custom" id="checkbox-2" > 
                      <label for="checkbox-2" class="checkbox-custom-label">I am the owner of this company</label>
                    </label>
                    <label>
                      <input type="checkbox" value="1" name="create_offer_permission" class="checkbox-custom" id="checkbox-3" >
                      <label for="checkbox-3" class="checkbox-custom-label">I have been given permission on behalf of the company to create 
new offers and purchase redeemar products.</label>
                    </label>
                    <label>
                      <input checked type="checkbox" value="1" name="create_user" class="checkbox-custom" id="checkbox-5" >
                      <label for="checkbox-5" class="checkbox-custom-label">Also create Reedemar User account with this email.</label>
                    </label>
                  </div>
            </div>
            <div class="col-md-6 col-sm-6 reg-right">
                <p class="heading-logo">Logo Creator</p>
                <h3>Search For my logo</h3>
                <div class="form-group logo-search">                    
                    <form name="search_form" id="live-search" action="" class="styled" method="post">
                        <fieldset>
                            <input type="text" class="form-control input-small-half" id="filter" value="" />
                            <button id="search_id" type="button" class="btn btn-default btn-block search-btn">Search</button>
                        </fieldset>
                    </form>
                <p class="hide_section_text" style="display:none">We have found <span id="filter-count"></span> logo(s) based on your search.</p>
                </div>
                <div class="image-slider-box "  >
                  <div id="">
                    <div class="inner">
                      <ul class="image_slider_item commentlist ">
                        @foreach($logo_details_unused as $logo_unused)
                          <li onclick="show_big_image('{{$logo_unused->id}}','{{$logo_unused->logo_name}}','{{$logo_unused->tracking_rating}}');" class=" sss img_li_{{$logo_unused->id}}" id="{{$logo_unused->logo_text}}" title="{{$logo_unused->company_name}}">
                              <!-- <a   class="thumb" href="../../uploads/original/{{$logo_unused->logo_name}}"></a> -->
                              <!-- <img id="logo_{{$logo_unused->id}}" src="../uploads/original/{{$logo_unused->logo_name}}" /> -->
                              <img style="cursor:pointer;" id="logo_{{$logo_unused->id}}" src="{{env('SITE_PATH')}}uploads/original/{{$logo_unused->logo_name}}" />
                          </li>                        
                        @endforeach 
                                      
                      </ul>
                    </div>
                  </div>
                </div>

                <p class="didnot-text">Didn't find your logo?</p>
                <div class="form-group upload">
                    <span class="upload-btn" >Upload your logo</span>
                      <form id="imageform" method="post" enctype="multipart/form-data" action='partner/add'> 
                        <input type="file" name="logo_image" id="logo_image" />
                      </form> 
                      
                </div>
                <h3>Preview </h3>
                <div class="image-preview">
                    <img src="{{env('IMAGE_URL')}}public/front_end/images/reg-logo1big.jpg" id="logo_preview" class="img-responsive">

                </div>
                <p class="logo-scan">Logo scanability</p>
                <div class="star-img">
                    <i class="fa fa-star-o" aria-hidden="true"></i>
                    <i class="fa fa-star-o" aria-hidden="true"></i>
                    <i class="fa fa-star-o" aria-hidden="true"></i>
                    <i class="fa fa-star-o" aria-hidden="true"></i>
                    <i class="fa fa-star-o" aria-hidden="true"></i>
                </div>
                <div class="rules">
                    <p>*By uploading the file you certify that you own the copyright for these photos or are authorized by the owner to make a photo-to-canvas reproduction.</p>
                    <p>**In order to be used by the logo scanner, the system will rate automaticaly your logo. You need at least two and half stars to be aprooved.</p>
                    <div class="checkbox policy-check">

                        <input checked type="checkbox" value="1" name="create_offer_permission" class="checkbox-custom" id="checkbox-4" >
                        <label for="checkbox-4" class="checkbox-custom-label">If your logo doesn’t meet this minimum requirement, you need to allow Redeemar to professionaly enhance it for best results.</label>

                        
                      
                    </div>
                </div>
            </div>
            <div class="clear">&nbsp</div>
            <button type="button" id="add_reedemer" class="btn btn-default btn-block complete-reg">Complete Registration</button>
            <input type="hidden" name="tracking_rating" id="tracking_rating" value="" />
            <input type="hidden" name="logo_id" id="logo_id" value="" />
            <input type="hidden" name="logo_name" id="logo_name" value="" />
            <input type="hidden" name="target_id" id="target_id" value="" />
            <input type="hidden" name="site_path" id="site_path" value="{{env('SITE_PATH')}}" />
            <input type="hidden" name="selected_subcat_id" id="selected_subcat_id" value="" />
            </form>
            <div class="row">
              <div id="error_div" style="text-align:center;display:none" class="alert alert-danger col-md-6 col-md-offset-3"></div>
            </div> 
        </div>
        <div id="image_data" img_url="" ></div>
    </div>
@endsection
@section('styles')
<style>
label > input{ /* HIDE RADIO */
  display:none;
}
label > input + img{ /* IMAGE STYLES */
  cursor:pointer;
  border:2px solid transparent;
}
label > input:checked + img{ /* (CHECKED) IMAGE STYLES */
  border:2px solid #f00;
}
.hide_section{
  display:none;
}
.error
{
  border: 1px solid red;
}
.red
{
  color:red;
}

.registration .form-group{margin-bottom: 0; padding: 0}
.registration input, .registration select{margin: 30px 0 10px}


</style>
@endsection
@section('scripts')
<script>
$(document).ready(function(){
  var ajaxRunning = false;
  $('#web_address').val("http://");
  $("#subcat_id").on("change", function(e) {
    $("#selected_subcat_id").val($("#subcat_id").val());
  });
  $("#add_reedemer").click(function(){

    var err = 0;
    if($("#company_name").val() == "" || $("#company_name").val() == null)
    {
      $("#company_name").addClass("error");
      $("#company_name_msg").text("Enter Company Name");
      err++;
    }else
    {
      $("#company_name").removeClass("error");
      $("#company_name_msg").text("");
    }

    if($("#category_id").val() == "" || $("#category_id").val() == null)
    {
      $("#category_id").addClass("error");
      $("#category_id_msg").text("Select A Catagory");
      err++;
    }else
    {
      $("#category_id").removeClass("error");
      $("#category_id_msg").text("");
    }

     if($("#subcat_id").val() == "" || $("#subcat_id").val() == null)
    {
      $("#subcat_id").addClass("error");
      $("#subcat_id_msg").text("Select A Subcatagory");
      err++;
    }else
    {
      $("#subcat_id").removeClass("error");
      $("#subcat_id_msg").text("");
    }

    if($("#web_address").val() == "" || $("#web_address").val() == null || urlvalidate($("#web_address").val()) == false)
    {
      $("#web_address").addClass("error");
      $("#web_address_msg").text("Enter Your Website Url");
      err++;
    }else
    {
      $("#web_address").removeClass("error");
      $("#web_address_msg").text("");
    }

    if($("#first_name").val() == "" || $("#first_name").val() == null)
    {
      $("#first_name").addClass("error");
      $("#first_name_msg").text("Enter the first name");
      err++;
    }else
    {
      $("#first_name").removeClass("error");
      $("#first_name_msg").text("");
    }

    if($("#last_name").val() == "" || $("#last_name").val() == null)
    {
      $("#last_name").addClass("error");
      $("#last_name_msg").text("Enter the last name");
      err++;
    }else
    {
      $("#last_name").removeClass("error");
      $("#last_name_msg").text("");
    }
   
    if($("#user_email").val() == "" || $("#user_email").val() == null || !isValidEmailAddress($("#user_email").val()))
    {
      $("#user_email").addClass("error");
      $("#user_email_msg").text("Enter a Valid Email Address");
      err++;
    }else
    {
      $("#user_email").removeClass("error");
      $("#user_email_msg").text("");
    }
    
    if($("#mobile").val() == "" || $("#mobile").val() == null)
    {
      $("#mobile").addClass("error");
      $("#mobile_msg").text("enter a valid mobile no");
      err++;
    }else
    {
      $("#mobile").removeClass("error");
      $("#mobile_msg").text("");
    }
    
    if($("#address").val() == "" || $("#address").val() == null)
    {
      $("#address").addClass("error");
      $("#address_msg").text("Enter the address");
      err++;
    }else
    {
      $("#address").removeClass("error");
      $("#address_msg").text("");
    }
    
    if($("#city").val() == "" || $("#city").val() == null)
    {
      $("#city").addClass("error");
      $("#city_msg").text("Enter the city");
      err++;
    }else
    {
      $("#city").removeClass("error");
      $("#city_msg").text("");
    }

    if($("#state").val() == "" || $("#state").val() == null)
    {
      $("#state").addClass("error");
      $("#state_msg").text("Enter the state");
      err++;
    }else
    {
      $("#state").removeClass("error");
      $("#state_msg").text("");
    }


     if($("#zipcode").val() == "" || $("#zipcode").val() == null)
    {
      $("#zipcode").addClass("error");
      $("#zipcode_msg").text("Enter the zipcode");
      err++;
    }else
    {
      $("#zipcode").removeClass("error");
      $("#zipcode_msg").text("");
    }

    if($("#user_password").val() == "" || $("#user_password").val() == null)
    {
      $("#user_password").addClass("error");
      $("#user_password_msg").text("Enter the password");
      err++;
      l = 0;
    }else
    {
      $("#user_password").removeClass("error");
      $("#user_password_msg").text("");
      l = 1;
    }

    if($("#confirm_user_password").val() == "" || $("#confirm_user_password").val() == null)
    {
      $("#confirm_user_password").addClass("error");
      $("#confirm_user_password_msg").text("Retype the password");
      err++;
      m = 0;
    }else
    {
      $("#confirm_user_password").removeClass("error");
      $("#confirm_user_password_msg").text("");
      m = 1;
    }
    
    if(l == 1 && m == 1 )
    {
      if($("#user_password").val() != $("#confirm_user_password").val())
      {
        $("#user_password").addClass("error");
        $("#confirm_user_password").addClass("error");
        $("#user_password_msg").text("Password Did not match");
        err++;

      }else
      {
        $("#user_password").removeClass("error");
        $("#confirm_user_password").removeClass("error");
        $("#user_password_msg").text("");
      }

    }
    
    if(!checkMe() || !mobileCheck() || !firstname() || !lastname){
      err++;
    }


  if(err <= 0){

    var owner_chk=$("#checkbox-2").prop("checked");
    var offer_permission=$("#checkbox-3").prop("checked");
    var enhance_logo=$("#checkbox-4").prop("checked");
    var create_user=$("#checkbox-5").prop("checked");
    if(owner_chk)
    {
      var owner=1;
    }
    else
    {
       var owner=0;
    }
    if(offer_permission)
    {
      var offer_permission=1;
    }
    else
    {
       var offer_permission=0;
    }
    if(enhance_logo)
    {
      var enhance_logo=1;
    }
    else
    {
       var enhance_logo=0;
    }
    if(create_user)
    {
      var create_user=1;
    }
    else
    {
       var create_user=0;
    }
    // alert(owner+"--"+offer_permission+"--"+enhance_logo);
    // return false;
    
    if(!ajaxRunning){
      $.ajax({
          url: 'partner/addreedemar',
          type: "POST",
          beforeSend:function(){
                  ajaxRunning = true;  
                },
          data: {'company_name':$('input[name=company_name]').val(), 
                  '_token': $('input[name=_token]').val(),
                  'category_id':$('#category_id').val(),
                  'subcat_id':$('#selected_subcat_id').val(),
                  'web_address':$('#web_address').val(),
                  'first_name':$('#first_name').val(),
                  'last_name':$('#last_name').val(),
                  'user_email':$('#user_email').val(),
                  'mobile':$('#mobile').val(),
                  'address':$('#address').val(),
                  'city':$('#city').val(),
                  'state':$('#state').val(),
                  'zipcode':$('#zipcode').val(),
                  'location':$('#address').val(),
                  'lat': $('#lat').val(),
                  'lng': $('#lng').val(),
                  'user_password':$('#user_password').val(),
                  'logo_name': $('#logo_name').val(),
                  'target_id': $('#target_id').val(),
                  'tracking_rating' : $("#tracking_rating").val(),
                  'owner':owner,
                  'offer_permission':offer_permission,
                  'enhance_logo':enhance_logo,
                  'create_user':create_user,
                  'email_as_username':$('.email_as_username').is(':checked'),
                  'username':$('.username').val()
                },        
          success: function(response){
            ajaxRunning = false;
            if(response.status=='ok')
            {
              window.location.href="partner/msg";
            }else{
              $("#error_div").hide();
              $("#show_message").slideDown();
              $("#error_div").html(response.msg);
              $("#error_div").show();
              $("#success_div").hide(); 
            }
          }
        }).fail(function(){
            ajaxRunning = false;
            $("#error_div").hide();
            $("#show_message").slideDown();
            $("#error_div").html('Oops. Something went wrong. Please try again later.');
            $("#error_div").show();
            $("#success_div").hide(); 
        });
    }
    }else{
      console.log(err);
    }

  });

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
};

 function urlvalidate(s) {    
      var regexp = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
      return regexp.test(s);    
 }

  function send_data()
  {

      //var file_data = $("#logo_image").prop("files")[0];   // Getting the properties of file from file field
      
      var form_data = new FormData();
      var ext = $('#logo_image').val().split('.').pop().toLowerCase();
     // alert(ext);
      form_data.append("logo_image", $("#image_data").attr("img_url"));
      form_data.append("image_type", ext);
          
     // alert(JSON.stringify(form_data,null,4));
     // var form_data = new FormData();                  // Creating object of FormData class
      var site_path=$("#site_path").val();
      //console.log($("#image_data").attr("img_url"));
      //alert("CC");
      //return false;
      //var file_data = $("#image_data").attr("img_url");   // Getting the properties of file from file field
     // form_data.append("logo_image", file_data); 
      //form_data.append("logo_image", file_data);     
      var loader_path=site_path+'public/images/reedemar-loader.gif';
      $(".image-preview img").attr("src",loader_path);
      $(".star-img").text('...');  
      //alert(JSON.stringify(file_data,null,4))    ;
      $.ajax({
      url: 'partner/uploadlogo',
      type: "POST",
      data:  form_data,
      contentType: false,
      cache: false,
      processData:false,
      headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },        
      success: function(response){
        $("#logo_name").val(response.logo_image);
        $('#target_id').val(response.target_id);
        $("#tracking_rating").val(response.tracking_rating);
        //alert(JSON.stringify(response,null,4));       
        if(response.success=='true')
        {
          if(response.tracking_rating ){
            setTimeout(function(){
              $.ajax({
                url: 'partner/vuforiarate/'+response.target_id,
                type: "GET",
                success: function(res){
                    $("#tracking_rating").val(res.tracking_rating);

                    var total=5;
                    var rating_val=res;
                    var blank_star=5-rating_val;
                    var add_html='';
                    var add_html_blank='';
                    $(".star-img").html('');
                   
                    for(var r=1; r<=rating_val; r++)
                    {
                      $(".star-img").append('<i aria-hidden="true" class="fa fa-star">');
                    }
                    for(var j=0; j<blank_star; j++)
                    {
                      $(".star-img").append('<i aria-hidden="true" class="fa fa-star-o">');
                    }

                }
              });
            },1000);
          }
          $(".image-preview").show();
          var image_path=site_path+'uploads/temp_medium/'+response.logo_image;
          $("#logo_preview").attr("src",image_path);
         

          var total=5;
          var rating_val=$("#tracking_rating").val();
          rating_val = parseInt(rating_val)<0?0:parseInt(rating_val);
          var blank_star=5-rating_val;
          var add_html='';
          var add_html_blank='';

          for(var r=1; r<=rating_val; r++)
          {
            add_html+='<i aria-hidden="true" class="fa fa-star">';
          }
          for(var j=0; j<blank_star; j++)
          {
            add_html_blank+='<i aria-hidden="true" class="fa fa-star-o">';
          }

          var total_html=add_html+add_html_blank;
          //alert(total_html);          
          $(".star-img").html(total_html);
        }

        if(response=='already_exists')
        {
          $("#error_div").hide();
          $("#show_message").slideDown();
          $("#error_div").html("Image with same name already exists. <br /> Please try with a diffrent name.");
          $("#error_div").show();
          $("#success_div").hide();
        }
      }
    }); 

  }

  $("#logo_image").on("change", function(e)
  { 
            var ext = $('#logo_image').val().split('.').pop().toLowerCase();
            if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
                alert('Invalid filetype! Please upload gif/png/jpg/jpeg type files.');
                return;
            }
            e.stopPropagation();
            e.preventDefault();
            var file = null;
            if (e.dataTransfer) {// file drag and drop
              file = e.dataTransfer.files[0] || null;
            } else if ($("#logo_image")[0].files) {// file upload
              file = $("#logo_image")[0].files[0] || null;
            }
            if (!file) {
              return;
            }

            var reader = new FileReader();
            reader.readAsDataURL(file, "UTF-8");
            reader.onload = function (e) {
             // $("#filename").html("Result: '"+ file.name +"' ("+ e.target.result.length +" B)");
              // $("#result").val(e.target.result);
              // console.log(e.target.result);
              $("#image_data").attr("img_url",e.target.result);
            };
            reader.onerror = function (e) {
              // console.log(e.target.error);

             // $("#result").val(e.target.error);
            };
             setTimeout(send_data, 2000);


  }); 
});


function show_big_image(image_id,image_name,rating_val) 
{ 
  var site_path=$("#site_path").val();
  $("#logo_preview").attr("src",site_path+"uploads/medium/"+image_name);
  $("#tracking_rating").val(rating_val);
  $("#logo_id").val(image_id);  

  var total=5;
  var blank_star=5-parseInt(rating_val);
  var add_html='';
  var add_html_blank='';

  for(var r=1; r<=rating_val; r++)
  {
    add_html+='<i aria-hidden="true" class="fa fa-star">';
  }
  for(var j=0; j<blank_star; j++)
  {
    add_html_blank+='<i aria-hidden="true" class="fa fa-star-o">';
  }

  var total_html=add_html+add_html_blank;
  
  $(".star-img").html(total_html);
 // alert(image_id);
  $.ajax({
      url: 'partner/logodetails/'+image_id,
      type: "GET",
      data:  image_id,
      contentType: false,
      cache: false,
      processData:false,
      headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },        
      success: function(response){
      console.log(response);
     // alert(JSON.stringify(response,null,4))     ;
        
        $("#company_name").val(response.company_name);
        $("#web_address").val(response.web_address);
        $("#first_name").val(response.first_name);
        $("#last_name").val(response.last_name);
        $("#address").val(response.address);
        $("#city").val(response.city);
        $("#state").val(response.state);
        $("#zipcode").val(response.zipcode);
        $("#mobile").val(response.mobile);              
        $("#user_email").val(response.contact_email);  
        $('.username').val(response.contact_email); 
        var cat_id = response.cat_id;
        var subcat_id = response.subcat_id;   
        $('#category_id').val(cat_id); 
        get_sub_cat(cat_id); 
        $("#mobile").val(response.mobile);      
        $('#logo_name').val(response.logo_name);
        $('#target_id').val(response.target_id);
        
        setTimeout(function(){        
          if(subcat_id >0)
          {
            $('#subcat_id').val(subcat_id);
            $('#subcat_id').trigger('change');
          }
          else
          {
            $("#subcat_id").val($("#subcat_id option:first").val());
          }
        }, 1000);
      }
  });
}




$(document).ready(function(){    
   $("#search_id").click(function(){   
        // Retrieve the input field text and reset the count to zero
        var filter = $("#filter").val(), count = 0;
 
        // Loop through the comment list
        $(".commentlist li").each(function(){
            // If the list item does not contain the text phrase fade it out
            if ($(this).attr( "title" ).search(new RegExp(filter, "i")) < 0) {
                $(this).hide();                 
            // Show the list item if the phrase matches and increase the count by 1
            } else {
                $(this).show();
                count++;
            }           

        });
 
        // Update the count
        var numberItems = count;
        $("#filter-count").text(count);
        if(count>0)
        {
            $(".hide_section_text").show();
            $(".hide_section").show();
            $(".slider-show").show();
            $(".hide_section_first").show();
        }
        else
        {
           $(".hide_section").hide();
           $(".hide_section_first").hide();
        }
        var px = $( ".image-slider-box" );
        var y = $($( ".image-slider-box" )).children();
        $(px).empty();
        $(px).append(y);
    }); 
});

function get_sub_cat(val)
{
  var category_id = val;         
  if(category_id){
      $.ajax({
          type:'GET',
          url:'partner/subcategory/'+category_id,
          
          success:function(html){                    
             var new_html="<option value=''>Select Subcategory</option>";
              for(var i=0; i<html.length; i++)
              {
                new_html+="<option value='"+html[i].id+"'>"+html[i].cat_name+"</option>";
              }                    
              $('#subcat_id').html(new_html);
          }

      }); 
  }else{
      $('#subcat_id').html('<option value="">Select Category first</option>'); 
  }

}


$("#company_name").keyup(function(){
  var company_val=$("#company_name").val();
  $("#filter").val(company_val);
  // Retrieve the input field text and reset the count to zero
  var filter = $("#filter").val(), count = 0;

  // Loop through the comment list
  $(".commentlist li").each(function(){
      // If the list item does not contain the text phrase fade it out
      if ($(this).attr( "title" ).search(new RegExp(filter, "i")) < 0) {
          $(this).hide();                 
      // Show the list item if the phrase matches and increase the count by 1
      } else {
          $(this).show();
          count++;
      }           

  });

  // Update the count
  var numberItems = count;
  $("#filter-count").text(count);
  if(count>0)
  {
      $(".hide_section_text").show();
      $(".hide_section").show();
      $(".slider-show").show();
      $(".hide_section_first").show();
  }
  else
  {
     $(".hide_section").hide();
     $(".hide_section_first").hide();
  }
  var px = $( ".image-slider-box" );
  var y = $($( ".image-slider-box" )).children();
  $(px).empty();
  $(px).append(y);
});
</script>

<script type="text/javascript">


$(document).ready(function(){
  $('#autoUpdate').show();
  $('#user_email').change(function(){
    //$('.username').val($('#user_email').val());
  });
  $('#checkbox-10').change(function(){
    if(this.checked){
      
      $('#autoUpdate').fadeOut('slow');
    }
    else{
      $('#autoUpdate').fadeIn('slow');
    }
  });
});


  var alphanumers = /^[a-zA-Z0-9 ]+$/;
  var onlytext = /^[a-zA-Z ]+$/;
  var numbersonly = /^[0-9]+$/;
  //var mobileChrAllow = /\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/
  var mobileChrAllow = /^[0-9 -+(.)]+$/;

  function checkMe(){
    if(!numbersonly.test($("#zipcode").val())){
        $("#zipcode_msg").text("Zipcode can have only numbers.");
        return false
    } else {
        $("#zipcode_msg").text("");
        return true;
    }
  }
  
  
  function mobileCheck(){
    if(!mobileChrAllow.test($("#mobile").val())){
        $("#mobile_msg").text("Please can have only numbers and few symbols(-(+).).");
        return false
    } else {
        $("#mobile_msg").text("");
        return true;
    }
  };

  function firstname(){
    if(!onlytext.test($("#first_name").val())){
        $("#first_name_msg").text("First name can have only alphabets");
        return false
    } else {
        $("#first_name_msg").text("");
        return true;
    }
  }
  function lastname(){
    if(!onlytext.test($("#last_name").val())){
        $("#last_name_msg").text("last name can have only alphabets.");
        return false
    } else {
        $("#last_name_msg").text("");
        return true;
    }
  }

</script>




@endsection
