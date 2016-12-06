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
  <style>

    .import-img-popup{position: relative; padding: 40px 0}
    .import-img-popup img{max-width: 100%;}
    .import-img-single{width: 25%; float: left}
    .import-img-single img{border:1px solid #B7B7B7;}
    .import-img-single img:hover{border:1px solid #73BF21;}
    .import-img-single img:focus{border:1px solid #73BF21;}
    .import-img-single img.active{border:1px solid #73BF21;}
    .sigle-row{padding-bottom: 25px;}
    .floate-btn{background: #fff; width: 100%; position: fixed; bottom: 0;}
    .green-btn{background: #73BF21; color: #fff; padding: 5px 25px; border:none;}

    .import-img-single-txt{padding:4px 0; text-align: center; font-size: 13px;}
    .green-btn{text-align: center; padding: 5px 25px; margin-left: 15px;}

  </style>
</head>
<body>
  <div class="import-img-popup">
    <div class="container">

      <div class="row sigle-row">
        <input type="hidden" name="site_path" id="site_path" value="{{$site_path}}" />
        <input type="hidden" name="choice_id" id="choiceid" value="{{$choiceid}}" />
      
        @foreach($allproduct as $product)
          <div class="col-md-3 col-sm-3 import-img-single" id="{{$product->id}}">
            <img src="../{{$site_path}}{{$product->product_image}}" alt="img">
            <div class="import-img-single-txt">{{$product->product_name}}</div>
          </div>
        @endforeach
      </div>

      <div class="row sigle-row floate-btn">
        <button class="green-btn" onclick="use_product()">Done</button>
      </div>
    </div>
  </div>
@section('scripts')
  <script>
    var $= jQuery.noConflict();
    var selectd_product = null;
    $('.import-img-single').click(function(event) {
      selectd_product = $(this).attr('id');
      $(this).parent().find("img").removeClass("active"); //remove class for all images
      $(this).find("img").addClass("active");
    });

    function use_product() 
    { 
      var site_path=$("#site_path").val();
      $.ajax({
        url: "<?php echo url();?>/product/addselectedproduct/"+selectd_product, // Url to which the request is send
        type: "GET",             // Type of request to be send, called as method
        data:  selectd_product, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
        contentType: false,       // The content type used when sending data to the server.
        cache: false,             // To unable request pages to be cached
        processData:false,        // To send DOMDocument or non processed data file it is set to false
        success: function(productdata)   // A function to be called if request succeeds
        { 
            // console.log("$product_id :: "+JSON.stringify(productdata, null, 4));
            var choiceid = $("#choiceid").val();
            var image_new_path = site_path+productdata[0].product_image;
            var image_new_id = productdata[0].id;
            var product_name = "#product_name"+choiceid;
            var product_cost = '#product_cost'+choiceid;
            var product_selling_price = '#product_selling_price'+choiceid;
            var product_retail_value = '#product_retail_value'+choiceid;
            var prod_image = '#prod_image'+choiceid;
            var product_image_id = '#product_image_id'+choiceid;
            var one_product_cost = '#one_product_cost'+choiceid;
            var one_product_retail_price = '#one_product_retail_price'+choiceid;
            var one_product_selling_price = '#one_product_selling_price'+choiceid;

            parent.$(product_name).val(productdata[0].product_name);
            parent.$(product_cost).text("$"+productdata[0].cost);
            parent.$(product_selling_price).text("$"+productdata[0].sell_price);
            parent.$(product_retail_value).text("$"+productdata[0].retail_price);
            parent.$(prod_image).attr('src',image_new_path);   
            parent.$(product_image_id).val(image_new_id);
            parent.$(one_product_cost).val(productdata[0].cost);
            parent.$(one_product_retail_price).val(productdata[0].retail_price);
            parent.$(one_product_selling_price).val(productdata[0].sell_price);


            var old_total_cost = parseFloat(sessionStorage.getItem("total_cost"));
            var total_selling_price = parseFloat(sessionStorage.getItem("total_selling_price"));

            var new_total_cost = old_total_cost + parseFloat(productdata[0].cost);
            var new_total_selling_price = total_selling_price + parseFloat(productdata[0].sell_price);

            var newGrossMargin = parseFloat(new_total_selling_price-new_total_cost);
            parent.$("#grossMarginSpan").text(newGrossMargin);

            markupvalue = ((parseFloat(newGrossMargin)/parseFloat(new_total_cost))*100).toFixed(2);
            parent.$("#markupSpan").text(markupvalue);    
            sessionStorage.setItem('total_cost',new_total_cost);
            sessionStorage.setItem('total_selling_price',new_total_selling_price);
            // parent.jQuery.fancybox.close();
            // $.fancybox.close();      
        }
      });
    }
  </script> 
</body>
</html>
