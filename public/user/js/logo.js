
"use strict";

var MyApp = angular.module("logo-app", ["ngFileUpload"]);
MyApp.controller('LogoController',["$scope", "$rootScope", "PlaceholderTextService", "ngTableParams", "$filter", "$http", "$route", "fileToUpload", function (a, rs, b, c, d, x, r, fu) {          
    var site_path              = $("#site_path").val();
    var file                   = a.myFile;
    a.ShowPopUp                = false;
    a.searchText               = '';     // set the default search/filter term
    a.save_logo_btn            = "save"; 
    a.add_logo_btn             = "UPLOAD";
    a.currentImage             = 0;
    a.changeActionObject       = {};
    a.actionDetails            = [];
    a.SelectedAction           = "";
    a.triggerDetails           = [];
    a.SelectedOfferTrigger     = "";
    a.SelectedCampaignTrigger  = "";
    a.ShowNone                 = true;
    a.ShowcampaignList         = false;
    a.ShowofferList            = false;
    a.deletingLogo             = false;
    a.beacon_details           = [];
    a.beaconPopShowing         = false;
    a.Beacon                   = {};
    a.beacon_add_error_show    = false;
    a.add_button_clicked       = false;
    a.file_path                = site_path;
    a.beacon_delete_error_show = false;
    a.add_new_beacon           = false;
    a.update_beacon            = false;
    a.beacon_update_error_show = true;
    rs.updateLogo              = false;
    rs.updateBeacon            = false;
    a.suc                      = false;
    a.err                      = false;
    a.successmsg               = "";
    a.errormsg                 = "";

    rs.scopePageTitle       = 'Trigger';
    rs.pageTitle            = '';

    $("#logo_details_div").hide();
    $("#loading_div").hide();
    $(".small-info").hide();
    $(".black-bg").hide();

    function getLogoDetails(){
      x.post("../admin/dashboard/logobyuser").success(function(data_response){              
          a.logo_details = data_response;
          angular.forEach(a.logo_details, function(logoData){
            if(logoData.default_logo == '1'){
              rs.default_logo_image = logoData.logo_name;
            }
          });
      });
    }

    // function getBeaconDetails(){
    //   x.get("../user/beacondetails").success(function(response){        
    //       a.beacon_details = response;
    //   });
    // }

    getLogoDetails();
    // getBeaconDetails();


    a.add_logo = function(){
      var logo_name=$("#logo_name").val() ;

      a.add_logo_btn = "saving ...";
      a.add_logo_disable = true ; 

      var ext = $('#logo_name').val().split('.').pop().toLowerCase();
     // alert(ext);
      if($.inArray(ext, ['jpg','jpeg']) == -1) {
        
        // showing error message if image not upload or not .jpg type 
        $("#notification_success").hide();
        $("#notification_info").hide();
        $("#notification").slideDown();
        $("#notification_error").html("Please upload only .jpg /.jpeg image.");       

        a.add_logo_disable = false ; 
        a.add_logo_btn = "UPLOAD";

        setTimeout(function() { 
          $("#notification").slideUp();
        }, 5000);      

        return false;
      }
    };

    
    a.save_logo=function(){      
        var logo_details=a.userlogo;
        var main_site_url=$('#main_site_url').val();
        x.post("../admin/dashboard/updatestatus",logo_details).success(function(response){
      
          a.save_logo_btn = "saving ...";       

          $("#error_div").hide();
          $("#show_message").slideDown();
          $("#success_div").html("Thank you for choosing this logo.");
          $("#success_div").show(); 
          r.reload();
       });
    };

      function OnGetFile (e) {
        e.stopPropagation();
        e.preventDefault();
        var file = null;
        if (e.dataTransfer) {// file drag and drop
          file = e.dataTransfer.files[0] || null;
        } else if ($("#logo_name")[0].files) {// file upload
          file = $("#logo_name")[0].files[0] || null;
        }
        if (!file) {
          return;
        }

        var reader = new FileReader();
        reader.readAsDataURL(file, "UTF-8");
        reader.onload = function (e) {
         // $("#filename").html("Result: '"+ file.name +"' ("+ e.target.result.length +" B)");
          // $("#result").val(e.target.result);
          //console.log(e.target.result);
        $("#img_data").empty();
        $("#img_data").append("<img id='image_src' src='"+e.target.result+"' style='width:200px;height:200px'>");
        };
        reader.onerror = function (e) {
          //console.log(e.target.error);

         // $("#result").val(e.target.error);
        };
      }
     

        // if (window.File && window.FileReader && window.FileList && window.Blob) {
        // document.getElementById('logo_name').addEventListener('change', OnGetFile, false);
        // } else {
        // alert('The File APIs are not fully supported in this browser.');
        // }

     
      function send_data() {
            console.log('send data');
          var file_data = $("#logo_name").prop("files")[0];   // Getting the properties of file from file field
          var form_data = new FormData();
          x = $("#image_src");
          var resp = x[0].currentSrc;
          var fd = new FormData(); 

          // Creating object of FormData class
          var site_path=$("#site_path").val();
          var ext = $('#logo_name').val().split('.').pop().toLowerCase();
          //alert(file_data.type+"---"+ext);
          form_data.append("logo_image", resp);
          form_data.append("image_type", ext);
          form_data.append('enhance_logo',$('#enhance_logo').is(':checked'));
          
         
          $.ajax({
          url: '../partner/uploadlogoback',
          type: "POST",
          data:  form_data,
          contentType: false,
          cache: false,
          processData:false,
          headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },        
          success: function(response){ 
          //  alert(JSON.stringify(response,null,4));
          //alert(JSON.stringify(response.response,null,4));
           
            a.showUploadloader = false;
            
            if (a.$root && a.$root.$$phase != '$apply' && a.$root.$$phase != '$digest') {
                a.$apply();
            }
            
            if(response.status=='ok')
            { 
              window.location="dashboard#/tables/trigger";
            }else{
              $("#error_div").hide();
              $("#show_message").slideDown();
              $("#error_div").html(response.msg);
              $("#error_div").show();
              $("#success_div").hide();
            }
          }
        }).fail(function(){
              $("#error_div").hide();
              $("#show_message").slideDown();
              $("#error_div").html('Oops. Something went wrong. Please try again later.');
              $("#error_div").show();
              $("#success_div").hide();
            });

      }


     

  

    a.delete_user_logo=function(itemId){
        if(a.deletingLogo){
            return false;
        }
       if(a.default_logo_id == itemId){
            alert("You can't delete your default logo");
            return false;
       }
      if(confirm("Are you sure?"))
      {
        a.deletingLogo = true;
        x.post("../partner/deletelogo",{'logo_id': itemId}).success(function(response){
          
            r.reload();
                     
        })
      }
    };
    
    
    a.fileSelected = function(e){
            var file = null;
            if (e.dataTransfer) {// file drag and drop
              file = e.dataTransfer.files[0] || null;
            } else if ($("#logo_name")[0].files) {// file upload
              file = $("#logo_name")[0].files[0] || null;
            }
            if (!file) {
              return;
            }
           // alert("a");
            var reader = new FileReader();
            reader.readAsDataURL(file, "UTF-8");
            reader.onload = function (e) {
             // $("#filename").html("Result: '"+ file.name +"' ("+ e.target.result.length +" B)");
              // $("#result").val(e.target.result);
             // console.log(e.target.result);
            $("#img_data").empty();
            $("#img_data").append("<img id='image_src' src='"+e.target.result+"' style='width:200px;height:200px'>");
            };
            reader.onerror = function (e) {
              //console.log(e.target.error);

             // $("#result").val(e.target.error);
            };
             a.showUploadloader = true;
             setTimeout(send_data, 1000);

    };


    a.show_rating=function(itemId){                
       var main_site_url=$('#main_site_url').val();
       var site_path=$('#site_path').val();

       $("#loading_div").show();  
       $("#rating_div").hide(); 
       $("#logo_details_div").hide();
       $(".small-info").hide();
       
       x.get("../partner/logodetails/"+itemId).success(function(data_response){
            $("#logo_details_div").show();
            $("#loading_div").hide();  
            $("#rating_div").show();
            a.tracking_rating=data_response.tracking_rating || 0;
            a.logo_name=data_response.logo_name;    
            a.target_id=data_response.target_id;  
            
            $("#logo_image_first").attr("src", site_path+'../uploads/original/'+a.logo_name)
            a.itemId = itemId;
            a.default_logo_id = 0;
            if(data_response.default_logo == 1){
                 a.default_logo_id = itemId;
            }
            
            a.userlogo = {user_logo_id: itemId,user_logo_target_id:a.target_id};          
            $( "#rateYo" ).hide();
            $( "#rating_div" ).after( '<div id="rateYo"></div>' );
            $("#rateYo").rateYo({
                rating: a.tracking_rating,
                readOnly: true
            });
            $(".small-info").hide();
       });         
    };

    a.make_default=function(itemId){ 

      x.get("../admin/dashboard/updatedefault/"+itemId).success(function(response){
                         
        if(response=='success') {             
          r.reload();               
        }                
      });
    };

    a.changeAction = function(logoId, actionId){
      rs.updateLogo              = true;
      rs.updateBeacon            = false;
      a.logoId    = logoId;
      a.actionId  = actionId;
      x.get("../user/changeaction/"+logoId+"/"+actionId).success(function(res){
        a.actionDetails       = res.action_list;
        a.changeActionObject  = res;
        $(".black-bg").show();
      });
    };

    a.HidePopup = function(){
      $(".black-bg").hide();
    };

    a.getList = function(){
      a.triggerDetails = [];
      if(a.SelectedAction == 3){
        x.get("../promotion").success(function(data){
          a.ShowNone = false;
          a.ShowofferList = true;
          a.ShowcampaignList = false;
          a.triggersInDetails = data;
        });
      } else if(a.SelectedAction == 2) {
        x.get("../promotion/campaignbyuser").success(function(data){
          a.ShowNone = false;
          a.ShowcampaignList = true;
          a.ShowofferList = false;
          a.triggerDetails = data;
        });
      } else {
        a.ShowNone = false;
        a.ShowofferList = false;
        a.ShowcampaignList = false;
      }
    };

    a.updateTrigger = function(){
      if($("#SelectedCampaignTrigger").val()){
        var offer_id = $("#SelectedCampaignTrigger").val();
      } else if($("#SelectedOfferTrigger").val()) {
        var offer_id = $("#SelectedOfferTrigger").val();
      } else {
        var offer_id = "";
      }

      var updateTriggerDetails = { 
        'logo_id': a.logoId,
        'action_id': a.SelectedAction,
        'offer_id': offer_id
      };

      x.post("../user/updateaction", updateTriggerDetails).success(function(res){
        r.reload();
      });
    };

    setTimeout(function() { 
        x.get("../cron/updaterating").success(function(data_new){
            getLogoDetails();
        });
    }, 10000);

    /* Beacons Area */

    a.addBeaconPopShow = function(){
      a.beaconPopShowing = true;
      a.add_new_beacon          = true;
      a.update_beacon          = false;
    };

    a.hideAddBeaconDiv = function(){
      a.beaconPopShowing = false;
    };

    a.addBeacon = function(){
      a.add_button_clicked = true;
      var alphanumers = /^(0|[1-9][0-9]*)$/;

      if(a.Beacon.name !== undefined && a.Beacon.uuid !== undefined && a.Beacon.major !== undefined && a.Beacon.minor !== undefined && a.Beacon.color !== undefined){
        if(!alphanumers.test(a.Beacon.major) && !alphanumers.test(a.Beacon.minor)){
          a.add_button_clicked = false;
          a.beacon_add_error_show = true;
          a.suc                   = false;
          a.err                   = true;
          a.successmsg = "";
          a.errormsg = "Please give only Numbers for Major and Minor value. No decimal numbers please!";
          setTimeout(function(){
            a.beacon_add_error_show = false;
          },1000);
        } else {
          x.post("../user/addbeacon", a.Beacon).success(function(res){
            if(res.success){
              a.add_button_clicked = false;
              a.beacon_add_error_show = true;
              a.suc                   = true;
              a.err                   = false;
              a.successmsg = "Beacon details added successfully";
              a.errormsg = "";
              setTimeout(function(){
                r.reload();
              },1000);
            } else {
              a.add_button_clicked = false;
              a.beacon_add_error_show = true;
              a.suc                   = false;
              a.err                   = true;
              a.successmsg = "";
              a.errormsg = "Some error occured! Please try again after reloading the page.";
              setTimeout(function(){
                a.beacon_add_error_show = false;
              },1000);
            }
          });
        }
      } else {
        a.add_button_clicked = false;
        a.beacon_add_error_show = true;
        a.suc                   = false;
        a.err                   = true;
        a.successmsg = "";
        a.errormsg = "All fields are required";
        setTimeout(function(){
          a.beacon_add_error_show = false;
        },1000);
      }
    };

    a.change_beacon_action = function(id, action_id){
      rs.updateLogo              = false;
      rs.updateBeacon            = true;
      a.beaconId  = id;
      a.actionId  = action_id;
      x.get("../user/changebeaconaction/"+a.beaconId+"/"+a.actionId).success(function(res){
        a.actionDetails       = res.action_list;
        a.changeActionObject  = res;
        $(".black-bg").show();
      });
    };

    a.change_beacon_status = function(id){
      x.post("../user/updatebeaconstatus",{'beacon_id': id}).success(function(response){
          if(response.success){
            r.reload();
          } else {
            a.beacon_delete_error_show = true;
          }
                     
        });
    };

    a.edit_beacon_data = function(beacondata){
      a.beaconPopShowing = true;
      a.add_new_beacon   = false;
      a.update_beacon    = true;
      a.Beacon           = beacondata;
    };

    a.editBeacon = function(){
      a.edit_button_clicked = true;
      x.post("../user/updatebeacon",a.Beacon).success(function(response){
        if(response.success){
          r.reload();
        } else {
          a.edit_button_clicked = false;
          a.beacon_update_error_show = true;
        }         
      });
    };

    a.delete_beacon_details = function(id){
      if(confirm("Are you sure?"))
      {
        var beaconId = "#beacon-"+id;
        var beaconDeleting = "#beacon-deleting-"+id;
        $(beaconId).show();
        $(beaconDeleting).hide();
        x.post("../user/deletebeacon",{'beacon_id': id}).success(function(response){
          if(response.success){
            r.reload();
          } else {
            $(beaconId).hide();
            a.beacon_delete_error_show = true;
          }
                     
        });
      }
    };

    a.updateBeaconTrigger = function(){
      if($("#SelectedCampaignTrigger").val()){
        var offer_id = $("#SelectedCampaignTrigger").val();
      } else if($("#SelectedOfferTrigger").val()) {
        var offer_id = $("#SelectedOfferTrigger").val();
      } else {
        var offer_id = "";
      }

      var updateTriggerDetails = { 
        'beacon_id': a.beaconId,
        'action_id': a.SelectedAction,
        'offer_id': offer_id
      };

      x.post("../user/updatebeaconaction", updateTriggerDetails).success(function(res){
        r.reload();
      });
    };
}]);