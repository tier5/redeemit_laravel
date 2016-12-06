    a.imageTypeChanged = function(image_type){
      // default settings
      sessionStorage.counter = 0 ;

      // $('#inner_con').empty();
      // $('#layers').empty();
      sessionStorage.counter = 0 ;
      var wth = 0;
      var hgt = 0;
      var aspectRatio = 1;
      $('#low_section').css({"display":"block"});

      if(image_type == 'small'){
          $("#layers").attr("image_type","small");
          var con_width = 400 ;
          var con_height = 300 ;
          wth = 318 ;
          hgt = 222 ;
      } else if (image_type == 'large'){
          $("#layers").attr("image_type","large");
          var con_width = 950 ;
          var con_height = 460 ;
          wth = 919 ;
          hgt = 439 ;
      }

      $("#container").css({"width":con_width,"height":con_height});   
      $("#inner_con").css({"width":wth,"height":hgt,"position":"absolute","overflow":"hidden","background":"white"});

      $('#inner_con').draggable( {"containment": "parent"});
      $('#inner_con').resizable({
          aspectRatio: false,
          minHeight: hgt,
          minWidth: wth,
          maxHeight: hgt,
          maxWidth: wth,
          containment:"parent",
          handles: 'ne, se, sw, nw'
      });
      // $("#add_layer").click();
      $("#add_layer_btn").removeAttr("disabled");
      $("#delete_layer").removeAttr("disabled");
    };

    a.uploadFirstImage = function(){
      $("#add_layer").click();
      a.imageTypeChanged('small');
      $("#uploadFirstImage").hide();
    };

    // $("#file").on('change',function(){
    //   a.showPreview = true;
    // });

    // a.enable_cropping = function(){
    //   a.cropMe = true;
    // };

    // a.cancel_cropping = function(){
    //   a.cropMe = false;
    // };

    a.upload_repo_img = function(){

      //console.log($('#upload1')[0]);
      localStorage.getItem('pdid');

      var token=$("#token").val();

      // if(a.cropMe){
      //   var imageData = a.cropper.croppedImage;
      // } else {
      //   var imageData = a.cropper.sourceImage;
      // }

      //var imageData = $('#img_canv').attr("src"); ;
 
      
        var data = {
        'dir_id' : $("#dir_id").val(),
        'image_name' : $("#image_name").val(),
        'home_img' : $("#home_img").attr("src"),
        'image_type' : "image/png",
        'home_thumb_img' : $("#home_thumb_img").attr("src"),
        'deal_details_img' : $("#deal_details_img").attr("src")
        // 'deal_details_thumb_img' : $("#deal_details_thumb").attr("src")
      }
      console.log(data);

      if(data.dir_id == null ||  data.dir_id == "? undefined:undefined ?" || data.image_name == null || data.image_name == null  ){
        $("#error_div").hide();
        $("#show_message").slideDown();
        $("#error_div").html("Please insert required fields.");
        $("#error_div").show();
        $("#success_div").hide();

        $('#add_inventory').prop('disabled', false);
        $("#add_inventory").text('Save');
      } else {

        if( data.home_img == "" || data.home_thumb_img == "" || data.deal_details_img == "" )
        {

        $("#error_div").hide();
        $("#show_message").slideDown();
        $("#error_div").html("Please crop all types.");
        $("#error_div").show();
        $("#success_div").hide();

        $('#add_inventory').prop('disabled', false);
        $("#add_inventory").text('Save');

        }else
        {

                    x.post('../directory/upload', data).success(function(response){
             //console.log(response);
             //alert(JSON.stringify(response,null,4));
              switch(response.status) {
                case 'success':
                    a.upload_repo_img_disabled = true;
                    a.upload_repo_img_btn = "Uploading ...";

                    $("#error_div").hide();
                    $("#show_message").slideDown();
                    $("#success_div").html("Data inserted successfully. <br />Please wait,we will redirect you to listing page.");
                    $("#success_div").show();              

                    var redirect_url='dashboard#/repository/list';
                    window.location = redirect_url; 
                    break;
                case 'already_exists':
                    $("#error_div").hide();
                    $("#show_message").slideDown();
                    $("#error_div").html("Image with same name already exists. <br /> Please try with a diffrent name.");
                    $("#error_div").show();
                    $("#success_div").hide();
                    break;
              }
          });

        }
        

      }
         
    }

 

    $('#upload1').on('change', function () { 
        $("#pre").show();
    });
   
    // $('.upload-result').on('click', function (ev) {
    //   $uploadCrop.croppie('result', {
    //     type: 'canvas',
    //     size: 'viewport'
    //   }).then(function (resp) {
    //    console.log(resp);
    //   });
    // });



// croping code

$(document).ready(function(){

// default settings
sessionStorage.counter = 0 ;

//initialize


$("#add_layer").on("change",function(){


var file    = document.querySelector('input[type=file]').files[0];
  var reader  = new FileReader();

  reader.addEventListener("load", function () {
    // preview.src = reader.result;
    //console.log("consoling source data :: "+reader.result);
    $('#container').attr("url_dta",reader.result);
  }, false);

  if (file) {
    reader.readAsDataURL(file);
  }

setTimeout(add_layer, 1000);


});

function add_layer()
{

var data_url_set = $('#container').attr("url_dta");
sessionStorage.counter++ ;
$('#inner_con').append('<div class="draggable" id="draggable'+sessionStorage.counter+'"  style="position:absolute;top:0px;left:0px;"><div class="rotatable" id="rotatable'+sessionStorage.counter+'"><div class="resizable" id="resizable'+sessionStorage.counter+'"><img src="'+data_url_set+'" alt="" / "></div></div></div>');


$('#draggable'+sessionStorage.counter).draggable();

$('#resizable'+sessionStorage.counter).resizable({
    aspectRatio: true,
      minHeight: 50,
      minWidth: 50,
    handles: 'ne, se, sw, nw'
});
$('#layers').append('<div class="layer_div" layer_name="draggable'+sessionStorage.counter+'">layer '+sessionStorage.counter+'</div>');
$("#layers").attr("curr_layer",'draggable'+sessionStorage.counter);
}

// $('#inner_con .draggable').click(function(){
// console.log("hi");
// });






//  $("#save_pic").click(function() { 
//     $(".resizable").addClass("white_border");
//     $(".resizable .ui-resizable-handle").css({"background":"none","border":"none"});
//     console.log($("#inner_con"));
//         html2canvas($("#inner_con"), {
//             height: $("#inner_con").height(),
//             onrendered: function(canvas) {
//                 //theCanvas = canvas;
//                document.body.appendChild(canvas);
//               var dataUrl = canvas.toDataURL();
//               var img = $("#layers").attr("image_type");
//               console.log("dataUrl :: "+dataUrl);
//               $('#'+img).attr("src",dataUrl);
              
//               // console.log(dataUrl);
//               // window.open(dataUrl, "toDataURL() image", "width=600, height=200");
//                 // Convert and download as image 
//                 //Canvas2Image.saveAsPNG(canvas); 
//                 // $("#preview").append(canvas);
//                 // Clean up 
//                 //document.body.removeChild(canvas);
//             }
// });
  $("#save_pic").click(function() { 
    $(".resizable").addClass("white_border");
    $(".resizable .ui-resizable-handle").css({"background":"none","border":"none"});
        html2canvas($("#inner_con"), {
            onrendered: function(canvas) {
                var theCanvas = canvas;
               // document.body.appendChild(canvas);
              var dataUrl = canvas.toDataURL();
              var img = $("#layers").attr("image_type");
              // console.log("dataUrl :: "+dataUrl);
              $('#'+img).attr("src",dataUrl);
              // console.log(dataUrl);
               // window.open(dataUrl, "toDataURL() image");
                // Convert and download as image 
                //Canvas2Image.saveAsPNG(canvas); 
                // $("#preview").append(canvas);
                // Clean up 
                //document.body.removeChild(canvas);
            }
});

});

sessionStorage.past_val = "";
$( "#slider_data" ).slider({
      // orientation: "vertical",
      range: "min",
      value: 1,
      min: 0,
      max: 360,
      slide: function( event, ui ) {
       var curr_dv = $("#layers").attr("curr_layer");
        if(sessionStorage.past_val == "")
        {
          sessionStorage.past_val  =  ui.value;
          // console.log(  sessionStorage.past_val);
         // $("#image").cropper("rotate", sessionStorage.past_val);
         $('#'+curr_dv).css({'-webkit-transform' : 'rotate('+ui.value+'deg)',
                 '-moz-transform' : 'rotate('+ui.value+'deg)',
                 '-ms-transform' : 'rotate('+ui.value+'deg)',
                 'transform' : 'rotate('+ui.value+'deg)'});

        }else
        {
          var lst = ui.value -  sessionStorage.past_val;
          console.log(lst);
          //$("#image").cropper("rotate", lst);
         
         $('#'+curr_dv).css({'-webkit-transform' : 'rotate('+ui.value+'deg)',
                 '-moz-transform' : 'rotate('+ui.value+'deg)',
                 '-ms-transform' : 'rotate('+ui.value+'deg)',
                 'transform' : 'rotate('+ui.value+'deg)'});

          sessionStorage.past_val = ui.value;
        }
      }
    });

$("#lock_outer_section").click(function(){
  $( "#inner_con" ).draggable( "disable" );
});

$("#unlock_outer_section").click(function(){
  $( "#inner_con" ).draggable( "enable" );
});

//  function call_data(obj)
// {
// $(".resizable").addClass("white_border");

//    $(obj).parent().removeClass("white_border");
//    var pt = $(obj).parent();
//    $("#layers").attr("curr_layer",$(obj).parent().parent().parent().attr("id"));
//    $(".resizable .ui-resizable-handle").css({"background":"none","border":"none"});
//    $(pt).find(".ui-resizable-handle").each(function(){
//    $(this).css({ "background": "#f5dc58","border" : "1px solid #FFF"});
//    });
// }

  // $('#image_type').change(function(){
  //   $('#inner_con').empty();
  //   $('#layers').empty();
  //   sessionStorage.counter = 0 ;
  //   var wth = 0;
  //   var hgt = 0;
  //   var aspectRatio = 1;
  //   $('#low_section').css({"display":"block"});
  //   var typ_data = $(this).val();
  //   if(typ_data != ""){
   
  //     if(typ_data == "small") {
  //       $("#layers").attr("image_type","small");
  //       var con_width = 400 ;
  //       var con_height = 300 ;
  //       wth = 318 ;
  //       hgt = 222 ;
  //     } else if(typ_data == "large") {
  //       $("#layers").attr("image_type","large");
  //       var con_width = 950 ;
  //       var con_height = 460 ;
  //       wth = 919 ;
  //       hgt = 439 ;
  //     }

  //     $("#container").css({"width":con_width,"height":con_height});   
  //     $("#inner_con").css({"width":wth,"height":hgt,"position":"absolute","overflow":"hidden","background":"white"});

  //     $('#inner_con').draggable( {"containment": "parent"});
  //     $('#inner_con').resizable({
  //         aspectRatio: false,
  //         minHeight: hgt,
  //         minWidth: wth,
  //         maxHeight: hgt,
  //         maxWidth: wth,
  //         containment:"parent",
  //         handles: 'ne, se, sw, nw'
  //     });
  //     $("#add_layer").click();
  //     $("#add_layer_btn").removeAttr("disabled");
  //     $("#delete_layer").removeAttr("disabled");
  //   }
  // });

$("#add_layer_btn").click(function(){

$("#add_layer").click();

});

$("#delete_layer").click(function(){

var curr_layer = $('#layers').attr("curr_layer");
$('.layer_div').each(function(){
if($(this).attr("layer_name") == curr_layer)
  {
    $(this).remove();
  }
});
$('#'+curr_layer).remove();
});

});





$("#layers").on( "click", 'div', function(){
var layer_name = $(this).attr('layer_name');
$(".resizable").addClass("white_border");
$(".resizable .ui-resizable-handle").css({"background":"none","border":"none"});
// console.log($("#"+layer_name).children().children());
$("#"+layer_name).children().children().removeClass("white_border");
$("#"+layer_name).find(".ui-resizable-handle").each(function(){
   $(this).css({ "background": "#f5dc58","border" : "1px solid #FFF"});
   });


$("#layers").attr("curr_layer",layer_name);
$( ".draggable" ).draggable( "disable" );
$("#"+layer_name).draggable( "enable" );
// //$("#"+layer_name).children().children().css({ "background": "#f5dc58","border" : "1px solid #FFF"});
});