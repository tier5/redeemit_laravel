"use strict";

var MyApp = angular.module("beacon-request-app", []);
MyApp.controller('BeaconsRequestController',["$scope", "$rootScope", "$http", "$route",function (a, rs, x, r) {
	
	/* All Variable Diclaration */
	var site_path 				= $("#site_path").val();
	a.file_path 				= site_path;
	a.allRequest 				= [];
	a.assignedData 				= {};
	a.assignBeaconPopShowing 	= false;
	a.assign_button_clicked 	= false;
	a.beacon_assign_error_show 	= false;
	a.beacon_delete_error_show 	= false;
	a.suc 						= false;
	a.err 						= false;
	a.requestItemIndex 			= 0;

    rs.scopePageTitle       = 'Beacon Request';
    rs.pageTitle            = '';

    x.get('../admin/dashboard/allbeaconrequest').success(function(res){
    	if(res.length){
    		a.allRequest = res;
    	} else {
    		a.allRequest = [];
    	}
    });

    a.assign_beacon = function(req, reqId){
		a.assignBeaconPopShowing = true;
		a.assignedData.partnerName = req.partner.company_name;
		a.assignedData.partnerId = req.partner.id;
		angular.forEach(req.all_request, function(single_request){
			if(single_request.id == reqId){
				a.assignedData.beacon_type  = single_request.beacon_type;
				a.assignedData.reqId 		= reqId;
				x.get('../user/unassignedbeacon/'+a.assignedData.beacon_type).success(function(response){
					a.assignedData.beacons = [];
					if(response.length){
						a.assignedData.beacons = response;
					}
				});
			}
		});
    };

    a.hideAssignBeaconDiv = function(){
      a.assignBeaconPopShowing = false;
    };

    a.assignBeaconDone = function(assignedData){
    	a.assign_button_clicked = true;
    	console.log(JSON.stringify(assignedData, null, 4));
    	var data = {
    		'redeemar_id' : assignedData.partnerId,
    		'beacon_type' : assignedData.beacon_type,
    		'beacon_id'   : assignedData.beacon_id,
    		'reqId'	      : assignedData.reqId
    	};

    	x.post('../admin/dashboard/assignbeacon', data).success(function(res){
    		
    		if(res == 'success'){
    			a.beacon_assign_error_show 	= true;
				a.suc = true;
				a.err = false;
				a.successmsg = "Beacon assigned successfully";
				setTimeout(function(){
	    			r.reload();
				},1000);
    		} else {
    			a.beacon_assign_error_show = true;
				a.suc = false;
				a.err = true;
				a.errormsg = "Could not assign beacon! Please try again.";
				setTimeout(function(){
	    			a.beacon_assign_error_show 	= false;
					a.suc = false;
    				a.assign_button_clicked		= false;
				},1000);
    		}
    	});
    };

    a.showAllRequest = function(reqId){
    	if (a.requestItemIndex == reqId) {
          a.requestItemIndex = 0;
        } else {
          a.requestItemIndex = reqId;
      	}
    };

    a.delete_beacon_request = function(partnerId){
    	console.log(partnerId);
    	$("#beacon-all-request-deleting-"+partnerId).hide();
    	$("#beacon-all-request-"+partnerId).show();
    	x.get('../admin/dashboard/deleteallrequest/'+partnerId).success(function(res){
    		if(res == 'success'){
    			r.reload();
    		} else {
				a.beacon_delete_error_show 	= true;
				a.err = true;
				a.errormsg = "Could not delete record! Please try again after refreshing the page!";
    		}
    	});
    };

    a.delete_beacon_single_request = function(reqId){
    	$("#beacon-single-request-deleting-"+reqId).hide();
    	$("#beacon-single-request-"+reqId).show();
    	x.get('../admin/dashboard/deletesinglerequest/'+reqId).success(function(res){
    		if(res == 'success'){
    			r.reload();
    		} else {
				a.beacon_delete_error_show 	= true;
				a.err = true;
				a.errormsg = "Could not delete record! Please try again after refreshing the page!";
    		}
    	});
    };

}]);