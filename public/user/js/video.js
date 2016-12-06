
"use strict";

var MyApp = angular.module("video-app", ["ngFileUpload", 'ngSanitize']);


MyApp.controller('VideoListController',["$scope", "PlaceholderTextService", "ngTableParams", "$filter", "$http", "fileToUpload", "$route", "$sce", function (a, b, c, d, x, fu, r, sce) {            
  
    $(".black-bg").hide();
    $(".video-bg").hide();
    a.add_video_disable = false ;
    a.add_video_button = "Save" ;
    a.VideoLink = "";
    a.showVideoPopup = false;
    a.showAddPopup = false;
    var site_path = $("#site_path").val();
    a.file_path = site_path;
    a.defaultVideo = "";
    
    x.post("../video/list").success(function(data_response){              
        a.video_details = data_response;
        angular.forEach(a.video_details, function(res){
          if(res.default_video == 1){
            a.defaultVideo = res.id;
          }
        });         
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

            r.reload();               
         
          }
          else
          {
            $("#error_div").hide();
            $("#show_message").slideDown();
            $("#error_div").html("Some error occoure.");
            $("#error_div").show();
            $("#success_div").hide();     

            r.reload();                
                    
          }              
       });
    }

    // Function for deleting a Video
    a.delete_video=function(itemId){ 
    
      if(confirm("Are you sure?")) { 
        x.get("../video/delete/"+itemId).success(function(response){
          r.reload();             
        })
      }
    };

    a.showVideoInPopup = function(video){
      $(".video-bg").show();
      if(video.video_url.indexOf('youtube') > 0){
        var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        var match = video.video_url.match(regExp);
        if (match && match[2].length == 11) {
            a.VideoLink = match[2];
        }

        $('#myCode').html('<iframe width="560" height="315" src="//www.youtube.com/embed/' + a.VideoLink + '" frameborder="0" allowfullscreen></iframe>');
      } else {
        var regExp = /(?:https?:\/{2})?(www\.)?vimeo.com\/(\d+)($|\/)/;        
        var match = video.video_url.match(regExp);
        a.VideoLink = match[2];

        $('#myCode').html('<iframe width="560" height="315" src="https://player.vimeo.com/video/'+a.VideoLink+'?autoplay=1" frameborder="0" allowfullscreen></iframe>');
      }
    };

    a.hidePopup = function(){
      a.VideoLink = "";
      $(".video-bg").hide();
      $('iframe').attr('src', $('iframe').attr('src'));
    };

    a.hideAddPopup = function(){
      $(".black-bg").hide();
    };

    a.showAddVideoPopup = function(){
      $(".black-bg").show();
    };

    a.addVideo = function(){      
        
        var newurl = a.video_url;
        var video_name = a.video_name;
        a.video = {};
        
        if(newurl == "" || newurl == undefined)
        {
            $("#error_div").hide();
            $("#show_message").slideDown();
            $("#error_div").html("Please insert video URL.");
            $("#error_div").show();
            $("#success_div").hide();
            setTimeout(function(){
              $("#error_div").hide();
            }, 5000);
            return false;
        }

        if(newurl.indexOf('youtube') > 0){
          a.video.provider = 1;
        } else if(newurl.indexOf('vimeo') > 0){
          a.video.provider = 2;
        }

        a.video.video_name = video_name;
        a.video.video_url = newurl;
        
        a.add_video_button = "Saving ..." ;

        x.post("../video/store",a.video).success(function(response){
         
          switch(response) {
              case 'success':  
                  r.reload();
                  break;
              case 'invalid_video':
                  $("#error_div").hide();
                  $("#show_message").slideDown();
                  $("#error_div").html("Invalid video URL.Please put youtube or vimeo video url only.");
                  $("#error_div").show();
                  $("#success_div").hide();         

                  $('#add_video').prop('disabled', false);
                  $("#add_video").text('Save');
                  setTimeout(function(){
                    $("#error_div").hide();
                  }, 5000);
                  break;
              case 'invalid_url':
                  $("#error_div").hide();
                  $("#show_message").slideDown();
                  $("#error_div").html("Invalid video URL.");
                  $("#error_div").show();
                  $("#success_div").hide();         

                  $('#add_video').prop('disabled', false);
                  $("#add_video").text('Save');
                  setTimeout(function(){
                    $("#error_div").hide();
                  }, 5000);
                  break;
              default :
                  $("#error_div").hide();
                  $("#show_message").slideDown();
                  $("#error_div").html("Please insert all field.");
                  $("#error_div").show();
                  $("#success_div").hide();
                  setTimeout(function(){
                    $("#error_div").hide();
                  }, 5000);
                  break;
          }
        });
    }; 

}]);