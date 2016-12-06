
"use strict";

var MyApp = angular.module("beacon-app", ["ngFileUpload"]);
MyApp.controller('BeaconController',["$scope", "$rootScope", "PlaceholderTextService", "ngTableParams", "$filter", "$http", "$route", "fileToUpload", function (a, rs, b, c, d, x, r, fu) {          
    var site_path              = $("#site_path").val();
    a.file_path                = site_path;
    a.searchText               = "";
    a.SelectedAction           = "";
    a.SelectedOfferTrigger     = "";
    a.SelectedCampaignTrigger  = "";
    a.successmsg               = "";
    a.errormsg                 = "";
    a.changefor                = "";
    a.changeActionObject       = {};
    a.Beacon                   = {};
    a.requesting_beacon        = {};
    a.beacon_Details_view      = {};
    a.SelectedBeacon           = {};
    a.triggerDetails           = [];
    a.actionDetails            = [];
    a.request_list             = [];
    a.proximiti_beacon_list    = [];
    a.interactive_beacon_list  = [];
    a.ShowNone                 = true;
    a.beacon_update_error_show = true;
    a.ShowPopUp                = false;
    a.ShowcampaignList         = false;
    a.ShowofferList            = false;
    a.beaconPopShowing         = false;
    a.beacon_add_error_show    = false;
    a.add_button_clicked       = false;
    a.beacon_delete_error_show = false;
    a.add_new_beacon           = false;
    a.update_beacon            = false;
    rs.updateBeacon            = false;
    a.suc                      = false;
    a.err                      = false;
    a.show_message             = false;
    a.request_delete_error     = false;
    a.showRequestPopup         = false;
    a.changeActionPopShow      = false;
    a.updatingBeacon           = false;
    a.viewBeaconPopShowing     = false;

    rs.scopePageTitle       = 'Trigger Beacon';
    rs.pageTitle            = '';

    function getBeaconDetails(){
      x.get("../user/beacondetails").success(function(response){        
          a.beaconDetails = response;

          if(response.length){
            angular.forEach(response, function(beacon){
              if(beacon.beacon_type == 'proximity'){
                a.proximiti_beacon_list.push(beacon);
              } else {
                a.interactive_beacon_list.push(beacon);
              }
            });
          } else {
            a.proximiti_beacon_list      = [];
            a.interactive_beacon_list    = [];
          }
      });
    }

    function getBeaconList(){
      x.get("../user/beaconrequestlist").success(function(response){        
          a.request_list = response;
      });
    }

    getBeaconDetails();
    getBeaconList();

    a.applyforbeacon = function(){
      a.showRequestPopup = true;
    };

    a.HideRequestPopup = function(){
      a.showRequestPopup = false;
    };

    a.RequestForBeacon = function(){
      a.updatingBeacon = true;
      
      x.post("../user/requestbeacon", a.requesting_beacon).success(function(res){
        if(res == 'success'){
          a.show_message = true;
          a.success_div = true;
          a.error_div = false;
          setTimeout(function(){
            r.reload();
          },1000);
        } else {
          a.show_message = true;
          a.success_div = false;
          a.error_div = true;
          setTimeout(function(){
            a.show_message = false;
            a.error_div = false;
          },3000);
        }
      });
    };

    a.hideAddBeaconDiv = function(){
      a.beaconPopShowing = false;
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
        x.post("../user/deletebeacon/"+id).success(function(response){
          if(response = 'success'){
            r.reload();
          } else {
            $(beaconId).hide();
            a.beacon_delete_error_show = true;
          }
                     
        });
      }
    };

    a.delete_beacon_request = function($reqId){
      x.get('../user/deleterequest/'+$reqId).success(function(res){
        if(res == 'success'){
          r.reload();
        } else {
          a.request_delete_error = true;
        }
      });
    };

    a.view_detials = function(beacon){
      a.viewBeaconPopShowing = true;
      a.beacon_Details_view = beacon;
    };

    a.hideViewBeaconDiv = function(){
      a.viewBeaconPopShowing = false;
    };

    a.HideActionPopup = function(){
      a.ShowNone = false;
      a.ShowofferList = false;
      a.ShowcampaignList = false;

      a.ShowNone2 = false;
      a.ShowofferList2 = false;
      a.ShowcampaignList2 = false;
      
      a.changeActionPopShow = false;
    };

    a.change_beacon_action = function(beacon, type){
      a.SelectedBeacon = beacon;
      a.changefor      = type;
      a.changeActionPopShow      = true;
      rs.updateBeacon            = true;
      a.beaconId  = beacon.id;
      a.actionId  = beacon.action_id;
      x.get("../user/changebeaconaction/"+a.beaconId+"/"+a.actionId).success(function(res){
        a.actionDetails       = res.action_list;
        a.changeActionObject  = res;
      });
    };

    a.getList = function(){
      a.SelectedAction = $("#SelectedAction").val();
      a.triggerDetails = [];

      if(a.SelectedAction == 2 || a.SelectedAction == 3) {
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


    a.getOfferList = function(SelectedCampaignTrigger){
      if(SelectedCampaignTrigger.length){
        x.get("../user/offersbyuser/"+SelectedCampaignTrigger).success(function(data){
          a.ShowNone = false;
          a.ShowofferList = true;
          a.ShowcampaignList = true;
          a.triggersInDetails = data;
        });
      } else {
          a.ShowNone = false;
          a.ShowofferList = false;
          a.ShowcampaignList = true;
      }
    };

    a.updateBeaconTrigger = function(beacon, changefor){
      if(beacon.beacon_type == 'proximity'){
        if($("#SelectedCampaignTrigger").val() && !$("#SelectedOfferTrigger").val()){
          var offer_id = $("#SelectedCampaignTrigger").val();
        } else if($("#SelectedCampaignTrigger").val() && $("#SelectedOfferTrigger").val()) {
          var offer_id = $("#SelectedOfferTrigger").val();
        } else {
          var offer_id = "";
        }

        var updateTriggerDetails = { 
          'beacon_id': beacon.id,
          'action_id': a.SelectedAction,
          'offer_id': offer_id,
          'action_id_2':'',
          'particular_id_2':'',
          'beacon_type':'proximity'
        };
      } else {

        if($("#SelectedCampaignTrigger").val() && !$("#SelectedOfferTrigger").val()){
          var offer_id = $("#SelectedCampaignTrigger").val();
        } else if($("#SelectedCampaignTrigger").val() && $("#SelectedOfferTrigger").val()) {
          var offer_id = $("#SelectedOfferTrigger").val();
        } else {
          var offer_id = "";
        }

        if(changefor == 'proximity'){
          var updateTriggerDetails = { 
            'beacon_id': beacon.id,
            'action_id': a.SelectedAction,
            'offer_id': offer_id,
            'beacon_type':'interactive',
            'changefor' : changefor
          };
        } else {
          var updateTriggerDetails = { 
            'beacon_id': beacon.id,
            'action_id2': a.SelectedAction,
            'offer_id2': offer_id,
            'beacon_type':'interactive',
            'changefor' : changefor
          };
        }
      }

      x.post("../user/updatebeaconaction", updateTriggerDetails).success(function(res){
        r.reload();
      });
    };
}]);