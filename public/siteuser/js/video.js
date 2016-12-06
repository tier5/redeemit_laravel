
"use strict";

var MyApp = angular.module("video-app", ["ngFileUpload"]);


MyApp.controller('VideoListController',["$scope", "PlaceholderTextService", "ngTableParams", "$filter", "$http", "fileToUpload", "$route", function (a, b, c, d, x, fu, $route) {            
  
    a.add_video_disable = false ;
    a.add_video_button = "Save" ;
    var site_path=$("#site_path").val();
    
    x.post("../video/list").success(function(data_response){              
        a.video_details = data_response; 
        a.file_path = site_path;         
    });


    a.make_default=function(itemId){      
       x.get("../video/mainvideo/"+itemId).success(function(response){
          a.status = response;                 
          if(response=='success')
          {
            $("#error_div").hide();
            $("#show_message").slideDown();
            $("#success_div").html("Data updated successfully. <br />Please wait,we will reload this page.");
            $("#success_div").show();              

            $route.reload();               
         
          }
          else
          {
            $("#error_div").hide();
            $("#show_message").slideDown();
            $("#error_div").html("Some error occoure.");
            $("#error_div").show();
            $("#success_div").hide();     

            $route.reload();                
                    
          }              
       });
    }

    // Function for deleting a Video
    a.delete_video=function(itemId){ 
    
     if(confirm("Are you sure?"))
     { 
       x.get("../video/delete/"+itemId).success(function(response){
          $route.reload();             
       })
     }
    }    
}]);

MyApp.controller('VideoAddController',["$scope", "PlaceholderTextService", "ngTableParams", "$filter", "$http", "fileToUpload", "$route", function (a, b, c, d, x, fu, $route) {            
  
    a.provider = '1';
    
    $(document).ready(function() {
      $(".various").fancybox({
        maxWidth  : 800,
        maxHeight : 600,
        fitToView : false,
        width   : '70%',
        height    : '70%',
        autoSize  : false,
        closeClick  : false,
        openEffect  : 'elastic',
        closeEffect : 'none'
      });
    });

    a.add_video_button = "Save" ;
    var site_path=$("#site_path").val();
    a.file_path = site_path;  
    
    var site_path = $("#site_path").val();

    
    a.showVideo = function(){
        var provider = a.provider;
        var newurl = a.video_url;

        if(newurl == "" || newurl == undefined)
        {
          $("#error_div").hide();
          $("#show_message").slideDown();
          $("#error_div").html("Please insert video URL.");
          $("#error_div").show();
          $("#success_div").hide();

          return false;
        }

        switch(provider) {
            case '1':
                if(newurl.indexOf('youtube') == -1){
                    $("#error_div").hide();
                    $("#show_message").slideDown();
                    $("#error_div").html("Please enter valid youtube URL.");
                    $("#error_div").show();
                    $("#success_div").hide();

                    return false;
                }

                var videoid = newurl.match(/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/);
                var video_id = videoid[1];

                $(".put_href").attr("href","https://www.youtube.com/embed/"+video_id+"?autoplay=1");
                break;
            case '2':
                if(newurl.indexOf('vimeo') == -1){
                    $("#error_div").hide();
                    $("#show_message").slideDown();
                    $("#error_div").html("Please enter valid vimeo URL.");
                    $("#error_div").show();
                    $("#success_div").hide();

                    return false;
                }

                var regExp = /(?:https?:\/{2})?(www\.)?vimeo.com\/(\d+)($|\/)/;        
                var match = newurl.match(regExp);
                var video_id = match[2];    

                $(".put_href").attr("href","https://player.vimeo.com/video/"+video_id+"?autoplay=1");    
                break;
        }
    }

    a.addVideo = function(){      
        
        var provider = a.provider;
        var newurl = a.video_url;
        var video_name = a.video_name;
        
        if(newurl == "" || newurl == undefined)
        {
            $("#error_div").hide();
            $("#show_message").slideDown();
            $("#error_div").html("Please insert video URL.");
            $("#error_div").show();
            $("#success_div").hide();

            return false;
        }
        
        a.video = {};
        a.video.provider = provider;
        a.video.video_name = video_name;
        a.video.video_url = newurl;
        
        $('#add_video').prop('disabled', true);
        a.add_video_button = "Saving ..." ;

        x.post("../video/store",a.video).success(function(response){
         
          switch(response) {
              case 'success':  
                  var redirect_url = 'dashboard#/video/list';        
                  window.location.href = redirect_url;
                  break;
              case 'invalid_video':
                  $("#error_div").hide();
                  $("#show_message").slideDown();
                  $("#error_div").html("Invalid video URL.Please put youtube or vimeo video url only.");
                  $("#error_div").show();
                  $("#success_div").hide();         

                  $('#add_video').prop('disabled', false);
                  $("#add_video").text('Save'); 
                  break;
              case 'invalid_url':
                  $("#error_div").hide();
                  $("#show_message").slideDown();
                  $("#error_div").html("Invalid video URL.");
                  $("#error_div").show();
                  $("#success_div").hide();         

                  $('#add_video').prop('disabled', false);
                  $("#add_video").text('Save'); 
                  break;
              default :
                  $("#error_div").hide();
                  $("#show_message").slideDown();
                  $("#error_div").html("Please insert all field.");
                  $("#error_div").show();
                  $("#success_div").hide();
                  break;
          }
        });
    }; 
}]);