"use strict";

var MyApp = angular.module("beacon-app", []);
MyApp.controller('BeaconsController',["$scope", "$rootScope", "$http", "$route",function (a, rs, x, r) {
	
	/* All Variable Diclaration */
	var site_path 					      = $("#site_path").val();
	a.file_path 					        = site_path;
	a.used_beacon_details_list 		= [];
	a.unused_beacon_details_list 	= [];
	a.beacon_details              = [];
  a.actionDetails               = [];
  a.Beacon                      = {};
  a.beaconDetails               = {};
  a.beacon_update_error_show    = true;
  a.showthisalso                = false;
  a.beaconPopShowing            = false;
  a.beacon_add_error_show       = false;
  a.add_button_clicked          = false;
  a.beacon_delete_error_show    = false;
  a.add_new_beacon              = false;
  a.update_beacon               = false;
  rs.updateBeacon               = false;
  a.suc                         = false;
  a.err                         = false;
  a.viewBeaconPopShowing        = false;
  a.viewusedproximity           = false;
  a.viewusedinteractive         = false;
  a.viewunusedproximity         = false;
  a.viewunusedinteractive       = false;
  a.changeActionForBeacon       = false;
  a.successmsg                  = "";
  a.errormsg                    = "";
  a.beaconType                  = "";
  a.PreviouslySelectedAction    = "";
  a.used_proximiti_beacon_list      = [];
  a.used_interactive_beacon_list    = [];
  a.unused_proximiti_beacon_list    = [];
  a.unused_interactive_beacon_list  = [];

  rs.scopePageTitle       = 'Beacons';
  rs.pageTitle            = '';

	/* Get list of all redeemar and there logo details*/
	function getUsedBeaconDetails(){
    	x.get("../admin/dashboard/usedbeacondetails").success(function(response){        
        	if(response.length){
            angular.forEach(response, function(beacon){
              beacon.used = true;
              if(beacon.beacon_type == 'proximity'){
                a.used_proximiti_beacon_list.push(beacon);
              } else {
                a.used_interactive_beacon_list.push(beacon);
              }
            });
          } else {
            a.used_proximiti_beacon_list      = [];
            a.used_interactive_beacon_list    = [];
          }
    	});
    }

    /* Get list of all unregistered logo details*/
    function getUnusedBeaconDetails(){
  		x.get("../admin/dashboard/unusedbeacondetails").success(function(response){        
  			if(response.length){
            angular.forEach(response, function(beacon){
              beacon.used = false;
              if(beacon.beacon_type == 'proximity'){
                a.unused_proximiti_beacon_list.push(beacon);
              } else {
                a.unused_interactive_beacon_list.push(beacon);
              }
            });
          } else {
            a.unused_proximiti_beacon_list    = [];
            a.unused_interactive_beacon_list  = [];
          }
  		});
    }

    /*Get List of all actions*/
    function getAllActionList (argument) {
      x.get("../admin/dashboard/actionlist").success(function(response){
        a.actionDetails = response;
        a.actionDetails2 = response;
      });
    }


    getUsedBeaconDetails();
    getUnusedBeaconDetails();
    getAllActionList();


    /* Beacons Area */

    a.addBeaconPopShow = function(beaconType){
      a.beaconType       = beaconType;
      a.beaconPopShowing = true;
      a.add_new_beacon   = true;
      a.update_beacon    = false;
    };

    a.hideAddBeaconDiv = function(){
      a.beaconPopShowing = false;
    };

    a.addBeacon = function(beaconType){
      a.add_button_clicked = true;
      var alphanumers = /^(0|[1-9][0-9]*)$/;
      if(beaconType == 'proximity'){
        if(a.Beacon.name !== undefined && a.Beacon.uuid !== undefined && a.Beacon.major !== undefined && a.Beacon.minor !== undefined && ( a.Beacon.color !== '' && a.Beacon.color !== undefined )){
          //if(a.Beacon.major > a.Beacon.minor){
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
              a.Beacon.beacon_type = a.beaconType;
              x.post("../admin/dashboard/addbeacon", a.Beacon).success(function(res){
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
          // } else {
          //   a.add_button_clicked = false;
          //   a.beacon_add_error_show = true;
          //   a.suc                   = false;
          //   a.err                   = true;
          //   a.successmsg = "";
          //   a.errormsg = "Major value should be grater than Minor value.";
          //   setTimeout(function(){
          //     a.beacon_add_error_show = false;
          //   },1000);
          // }
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
      } else {
        if(a.Beacon.name !== undefined && a.Beacon.identifier !== undefined && ( a.Beacon.category !== '' && a.Beacon.category !== undefined )){
          a.Beacon.beacon_type = a.beaconType;
          x.post("../admin/dashboard/addbeacon", a.Beacon).success(function(res){
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
      a.beaconType       = beacondata.beacon_type;
    };

    a.editBeacon = function(){
      a.edit_button_clicked = true;
      x.post("../admin/dashboard/updatebeacon",a.Beacon).success(function(response){
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
        x.post("../admin/dashboard/deletebeacon/"+id).success(function(response){
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

    a.view_beacon_data = function(beacon){
      a.viewBeaconPopShowing = true;
      a.beaconDetails = beacon;
    };

    a.hideViewBeaconDiv = function(){
      a.viewBeaconPopShowing = false;
    };

    a.remove_beacon_for_partner = function(id){
      x.get("../admin/dashboard/removeassignedpartnerforbeacon/"+id).success(function(res){
        if(res == 'success'){
          r.reload();
        } else {

        }
      });
    };

    a.change_beacon_action_for_partner = function(beacon){
      a.changeActionForBeacon   = true;
      a.SelectedAction          = "";
      a.SelectedBeacon          = beacon;
      if(beacon.beacon_type == 'interactive'){
        a.showthisalso = true;
      } else {
        a.showthisalso = false;
      }
    };

    a.HideActionPopup = function(){
      a.ShowNone = false;
      a.ShowofferList = false;
      a.ShowcampaignList = false;

      a.ShowNone2 = false;
      a.ShowofferList2 = false;
      a.ShowcampaignList2 = false;
      
      a.changeActionForBeacon = false;
    };

    a.getList = function(){
      a.SelectedAction = $("#SelectedAction").val();
      a.triggerDetails = [];

      if(a.SelectedAction == 2 || a.SelectedAction == 3) {
        x.get("../admin/dashboard/campaignbyuser/"+a.SelectedBeacon.redeemar_id).success(function(data){
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

    a.getAnotherList = function(){
      a.SelectedAnotherAction = $("#SelectedAnotherAction").val();
      a.triggerDetails2 = [];
      
      if(a.SelectedAnotherAction == 2 || a.SelectedAnotherAction == 3) {
        x.get("../admin/dashboard/campaignbyuser/"+a.SelectedBeacon.redeemar_id).success(function(data){
          a.ShowNone2 = false;
          a.ShowcampaignList2 = true;
          a.ShowofferList2 = false;
          a.triggerDetails2 = data;
        });
      } else {
        a.ShowNone2 = false;
        a.ShowofferList2 = false;
        a.ShowcampaignList2 = false;
      }
    };

    a.updateBeaconTrigger = function(beacon_type, beaconId){
      if(beacon_type == 'proximity'){
        if($("#SelectedCampaignTrigger").val() && !$("#SelectedOfferTrigger").val()){
          var offer_id = $("#SelectedCampaignTrigger").val();
        } else if($("#SelectedCampaignTrigger").val() && $("#SelectedOfferTrigger").val()) {
          var offer_id = $("#SelectedOfferTrigger").val();
        } else {
          var offer_id = "";
        }

        var updateTriggerDetails = { 
          'beacon_id': beaconId,
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

        if($("#SelectedCampaignTrigger2").val() && !$("#SelectedOfferTrigger2").val()){
          var offer_id2 = $("#SelectedCampaignTrigger2").val();
        } else if($("#SelectedCampaignTrigger2").val() && $("#SelectedOfferTrigger2").val()) {
          var offer_id2 = $("#SelectedOfferTrigger2").val();
        } else {
          var offer_id2 = "";
        }

        var updateTriggerDetails = { 
          'beacon_id': beaconId,
          'action_id': a.SelectedAction,
          'offer_id': offer_id,
          'action_id2': a.SelectedAnotherAction,
          'offer_id2': offer_id2,
          'beacon_type':'interactive'
        };
      }

      //console.log(JSON.stringify(updateTriggerDetails, null, 4));

      x.post("../admin/dashboard/updatebeaconaction", updateTriggerDetails).success(function(res){
        r.reload();
      });
    };

    a.getOfferList = function(SelectedCampaignTrigger){
      if(SelectedCampaignTrigger.length){
        x.get("../admin/dashboard/offersbyuser/"+a.SelectedBeacon.redeemar_id+"/"+SelectedCampaignTrigger).success(function(data){
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

    a.getOfferList2 = function(SelectedCampaignTrigger2){
      if(SelectedCampaignTrigger2.length){
        x.get("../admin/dashboard/offersbyuser/"+a.SelectedBeacon.redeemar_id+"/"+SelectedCampaignTrigger2).success(function(data){
          a.ShowNone2 = false;
          a.ShowofferList2 = true;
          a.ShowcampaignList2 = true;
          a.triggersInDetails2 = data;
        });
      } else {
          a.ShowNone2 = false;
          a.ShowofferList2 = false;
          a.ShowcampaignList2 = true;
      }
    };
}]);