"use strict";

var MyApp = angular.module("partner-app", []);
MyApp.controller('PartnerListController',["$scope", "$rootScope", "$http", "$route",function (a, rs, x, r) {
	
	/* All Variable Diclaration */
	var site_path 			= $("#site_path").val();
    a.cnames 				= [];
    a.logo_details 			= [];
    a.all_logo_details		= [];
    a.currentPage 			= 0;
    a.pageSize 				= 5;
    a.file_path 			= site_path;
    a.partnerItemIndex 		= 0;
    a.selectedPartner 		= 1;
    a.sm 					= false;
	a.msg 					= "";
    a.workingfine 			= false;
    a.showLoader 			= 0;
    a.showDeleteLoader 		= 0;
    a.uploadMe 				= false;
    rs.partnerID 			= "";
    a.all_logo_details 		= [];
    a.image_data 			= "";
    a.editPartner 			= false;
    a.partnerDetails 		= {};
    a.selectedCategory 		= "";
    a.cat_list 				= [];
    a.selectedSubCategory 	= "";
    a.updatingData 			= false;
    a.workingfine1 			= false;
	a.sm1					= false;
    a.addPartner			= false;
    a.addingData 			= false;
    a.newPartnerDetails 	= {};
    a.new_redeemar_details 	= [];
    a.showLogosDeleteLoader = 0;
    a.editLogo 				= false;
    rs.logoID 				= 0;
    a.noNewLogo 			= true;
    a.selectedLogoCategory 		= 0;
	a.selectedLogoSubCategory 	= 0;

    a.width 				= 300;
    a.height 				= 300;

    $("#pre").hide();
    $("#crop_it_again").hide();

	/* Get list of all redeemar and there logo details*/
	x.post("../admin/dashboard/reedemarlistandlogo").success(function(data_response){              
		a.reedemar_details = data_response;
  		angular.forEach(a.reedemar_details, function(redeemar){
	  		angular.forEach(redeemar.logo_details, function(logoData){
	  			if(logoData.default_logo == "1"){
	  				redeemar.default_logo_image = logoData.logo_name;
	  				redeemar.rating = logoData.tracking_rating;
	  			}
	  		});
	  	});    
    });

    /* Get list of all unregistered logo details*/
	x.get("../admin/dashboard/unusedlogos").success(function(data_response){              
		if(data_response.length){
			a.reedemar_logo_details = data_response; 
		} else {
			a.reedemar_logo_details = [];
		}
    });

    a.showAllLogo = function(index){
        if (a.partnerItemIndex == index) {
          a.partnerItemIndex = 0;
        } else {
          a.partnerItemIndex = index;
          angular.forEach(a.reedemar_details,function(details){
          	if(details.id == a.partnerItemIndex){
          		a.logo_details = details.logo_details;
          		angular.forEach(a.logo_details, function(logo){
		  			if(logo.action_id == "1"){
		  				logo.trigger = "Go To Brand Dashboard";
		  			} else if(logo.action_id == "2") {
		  				logo.trigger = "Go To a Campaign";
		  			} else if(logo.action_id == "3") {
		  				logo.trigger = "Go To a Single Offer";
		  			} else if(logo.action_id == "4") {
		  				logo.trigger = "Validate Offer";
		  			} else {
		  				logo.trigger = "Go To Brand Dashboard";
		  			}
		  		});
          	}
          });
        }
    };


    setTimeout(function() { 
        x.get("../cron/updaterating").success(function(data_new){
            x.post("../admin/dashboard/reedemarlistandlogo").success(function(data_response){              
				a.reedemar_details = data_response;
		  		angular.forEach(a.reedemar_details, function(redeemar){
			  		angular.forEach(redeemar.logo_details, function(logoData){
			  			if(logoData.default_logo == "1"){
			  				redeemar.default_logo_image = logoData.logo_name;
			  				redeemar.rating = logoData.tracking_rating;
			  			}
			  		});
			  	});    
		    });

		    x.get("../admin/dashboard/unusedlogos").success(function(data_response){              
				if(data_response.length){
					a.reedemar_logo_details = data_response; 
				} else {
					a.reedemar_logo_details = [];
				}
		    });
        });
    }, 10000);

    a.get_cat = function(){
    	x.post("../admin/dashboard/category").success(function(category_data_response){              
            a.cat_list = category_data_response;             
        });
    }

    a.get_subcat = function(){
    	a.data = {};
    	if(rs.partnerID){
	    	a.data = {
	    		'parent_id' : a.partnerDetails.cat_id
	    	};
	    } else if(rs.logoID){
	    	a.data = {
	    		'parent_id' : a.logoData.cat_id
	    	};
	    } else {
	    	if($("#selected_cat").val()){
    			var cat_id = $("#selected_cat").val().split(':')[1];
	    	} else {
	    		var cat_id = $("#selected_logo_cat").val().split(':')[1];
	    	}

	    	a.data = {
	    		'parent_id' : cat_id
	    	};
	    }

        x.post('../admin/dashboard/subcategory', a.data).success(function(subcat_data_response){              
            a.subcat_details = subcat_data_response;          
        });
    };

    a.editPartnerDetails = function(redeemer_id){
    	rs.partnerID 	= "";
    	rs.partnerID 	= redeemer_id;
    	a.editPartner 	= true;
    	
    	angular.forEach(a.reedemar_details, function(res){
    		if(res.id == redeemer_id){
    			a.partnerDetails = res;
    			a.partnerDetails.mobile = parseInt(a.partnerDetails.mobile);

		    	a.selectedSubCategory 	= a.partnerDetails.cat_id;
		    	a.selectedCategory 		= a.partnerDetails.subcat_id;

		    	a.data = {
		    		'parent_id' : a.partnerDetails.cat_id
		    	};
    		}
    	});

		a.get_cat();

	    a.get_subcat();
		
    };

    a.addNewLogo = function(redeemer_id){
    	rs.partnerID = "";
    	if(!a.uploadMe) {
    		a.uploadMe = !a.uploadMe;
    	}
    	rs.partnerID = redeemer_id;
    };

    a.deletePartnerLogo = function(redeemer_id, logo_id){
    	if(confirm("Are you sure?")) {
	    	a.showLoader 			= logo_id;
	    	a.showLogosDeleteLoader = logo_id;
	    	var data 			= {
	    		'redeemer_id' 	: redeemer_id?redeemer_id:null,
	    		'logo_id' 		: logo_id
	    	};

	    	x.post("../partner/deletelogo", data).success(function(res){
	    		if(res == 'success'){
	    			a.showUploadloader 	= false;
				    a.sm 				= true;
				    a.workingfine 		= true;
				    a.msg 				= "Data deleted successfully";
				    setTimeout(function(){
	    				r.reload();
				    },3000);
	    		} else {
	    			a.showUploadloader 	= false;
			    	a.msg 				= "Problem occurd when deleting logo";
	    			a.sm 				= true;
				    a.workingfine 		= false;

				    setTimeout(function(){
				    	a.msg 			= "";
					    a.workingfine 	= false;
		    			a.sm 			= false;
				    },3000);
	    		}
	    	});
	    }
    };

    a.hideMe = function(){
    	a.uploadMe 		= false;
    	a.editPartner 	= false;
    	a.addPartner	= false;
    	a.editLogo 		= false;
    	rs.partnerID 	= "";
    };

    
    function OnGetFile (id) {
        var file = null;

    	if($(id)[0].files){
    		file = $(id)[0].files[0] || null;
    	}

        var reader = new FileReader();
        reader.readAsDataURL(file, "UTF-8");
        reader.onload = function (e) {
            a.image_data = "";
            a.image_data = e.target.result;
        };
        reader.onerror = function (e) {
           console.log(e.target.error);
           // $("#result").val(e.target.error);
        };
    }

    a.imageChanged = function(){
    	var file = $("#new_logo_name")[0].files[0];
    	var ext = $('#new_logo_name').val().split('.').pop().toLowerCase();
        var file_size = $('#new_logo_name')[0].files[0].size;

        if($.inArray(ext, ['jpg','jpeg','png','gif']) == -1) {

            a.showUploadloader 	= false;
	    	a.msg 				= "Please upload only .jpg /.jpeg /.png /.gif image.";
			a.sm 				= true;
		    a.workingfine 		= false;

		    setTimeout(function(){
		    	a.msg 			= "";
			    a.workingfine 	= false;
    			a.sm 			= false;
		    },3000);
            return false;
        } else if(file_size > 2097152) { 
            a.showUploadloader 	= false;
	    	a.msg 				= "Please upload only .jpg /.jpeg /.png /.gif image within 2MB.";
			a.sm 				= true;
		    a.workingfine 		= false;

		    setTimeout(function(){
		    	a.msg 			= "";
			    a.workingfine 	= false;
    			a.sm 			= false;
		    },3000);
            return false;
        } else {
        	OnGetFile("#new_logo_name");
        }
    };

    a.uploadFile = function(){
    	// console.log("rs.partnerID :: "+rs.partnerID);
    	OnGetFile("#new_logo_name");
    	var file = $("#new_logo_name")[0].files[0];
    	
        a.savingLogo = true;
        // End validation
        angular.forEach(a.reedemar_details, function(partnerDetails){
        	if(partnerDetails.id == rs.partnerID){
        		// console.log("partnerDetails :: "+JSON.stringify(partnerDetails, null, 4));
        		a.all_logo_details = [{
        			'redeemar_id' 	: rs.partnerID,
        			'logo_text' 	: partnerDetails.company_name,
		            'image_name' 	: file.name,
		            'category_id' 	: partnerDetails.cat_id,
		            'subcat_id' 	: partnerDetails.subcat_id,                    
		            'contact_email' : partnerDetails.email,
		            'company_name' 	: partnerDetails.company_name,
		            'first_name' 	: partnerDetails.first_name,
		            'last_name' 	: partnerDetails.last_name,
		            'address' 		: partnerDetails.address,
		            'city' 			: partnerDetails.city,
		            'state' 		: partnerDetails.state,
		            'postal_code' 	: partnerDetails.zipcode,
		            'web_address' 	: partnerDetails.web_address,
		            'mobile' 		: partnerDetails.mobile,
		            'image_data' 	: a.image_data,
		            'image_type' 	: file.type
		        }];
        	}
        });

        x.post("../admin/dashboard/addlogo",a.all_logo_details).success(function(response_back){

            a.savingLogo = false;
            if(response_back.response=="success")
            {
            	rs.partnerID = "";
                r.reload();
            }
            if(response_back.response=="image_problem")
            {
                a.showUploadloader 	= false;
		    	a.msg 				= "Unable to upload your image. Please try with a diffrent image.";
				a.sm 				= true;
			    a.workingfine 		= false;

			    setTimeout(function(){
			    	a.msg 			= "";
				    a.workingfine 	= false;
	    			a.sm 			= false;
			    },3000);
	            return false;                                      

            }
            
       }).error(function(){
            a.savingLogo = false;
            a.showUploadloader 	= false;
	    	a.msg 				= "Unable to upload your image. Please try with a diffrent image.";
			a.sm 				= true;
		    a.workingfine 		= false;

		    setTimeout(function(){
		    	a.msg 			= "";
			    a.workingfine 	= false;
    			a.sm 			= false;
		    },3000);
        });
    };

	a.updatePartnerDetails = function(){
		a.updatingData = true;
		x.post("../admin/dashboard/updatepartnerdata", a.partnerDetails).success(function(res){
			// console.log("res :: "+JSON.stringify(res, null, 4));
			if(res == 'success'){
				r.reload();
			} else {
				a.updatingData = false;
		    	a.msg 				= "Unable to modify data.";
				a.sm1 				= true;
			    a.workingfine1		= false;

			    setTimeout(function(){
			    	a.msg 			= "";
				    a.workingfine1 	= true;
	    			a.sm1			= false;
			    },3000);
			}
		});
	};

	a.addRedeemar = function(){
		rs.logoID 		= 0;
		rs.partnerID 	= 0;
		a.addPartner 	= true;
		a.get_cat();
	};

	a.addLogoChanged = function(){
		var file = $("#new_partner_logo_name")[0].files[0];
    	var ext = $('#new_partner_logo_name').val().split('.').pop().toLowerCase();
        var file_size = $('#new_partner_logo_name')[0].files[0].size;

        if($.inArray(ext, ['jpg','jpeg','png','gif']) == -1) {

	    	a.msg 				= "Please upload only .jpg /.jpeg /.png /.gif image.";
			a.sm1 				= true;

		    setTimeout(function(){
		    	a.msg 			= "";
			    a.workingfine 	= false;
    			a.sm1 			= false;
		    },3000);
		    return false;
        } else {
        	OnGetFile("#new_partner_logo_name");
        }
	};

	a.saveRedeemar = function(){
		a.addingData = true;
		OnGetFile("#new_partner_logo_name");
    	var file = $("#new_partner_logo_name")[0].files[0];

    	a.new_redeemar_details = [{
			'logo_text' 	: a.newPartnerDetails.company_name,
            'image_name' 	: file.name,
            'category_id' 	: a.newPartnerDetails.cat_id,
            'subcat_id' 	: a.newPartnerDetails.subcat_id,                    
            'email' 		: a.newPartnerDetails.email,
            'company_name' 	: a.newPartnerDetails.company_name,
            'first_name' 	: a.newPartnerDetails.first_name,
            'last_name' 	: a.newPartnerDetails.last_name,
            'address' 		: a.newPartnerDetails.address,
            'city' 			: a.newPartnerDetails.city,
            'state' 		: a.newPartnerDetails.state,
            'postal_code' 	: a.newPartnerDetails.zipcode,
            'web_address' 	: a.newPartnerDetails.web_address,
            'mobile' 		: a.newPartnerDetails.mobile,
            'image_data' 	: a.image_data,
            'image_type' 	: file.type
        }];

        // console.log(JSON.stringify(a.new_redeemar_details, null, 4));

        x.post("../admin/dashboard/storereedemer", a.new_redeemar_details).success(function(res){
        	if(res == 'success'){
        		r.reload();
        	} else {
        		a.msg 				= "Oops. Something went wrong. Please try again later.";
				a.sm1 				= true;

			    setTimeout(function(){
			    	a.msg 			= "";
	    			a.sm1 			= false;
			    },3000);
			    return false;
        	}
        });

	};

	a.deletePartnerDetails=function(partner_id){ 
		if(confirm("Are you sure?")) {
			a.showDeleteLoader = partner_id;
			var data = {
				'redeemar_id' : partner_id
			};

			x.post("../admin/dashboard/deletereedemer", data).success(function(response){
			  if(response == 'success'){
			  	r.reload();
			  } else {
				  	a.msg = "Oops. Something went wrong. Please try again later.";
					a.sm1 = true;

				    setTimeout(function(){
				    	a.msg = "";
		    			a.sm1 = false;
				    },3000);
				    return false;
			  }
			});
		}
	};

	a.update_status = function(partner_id, status){
		var data = {
			'redeemar_id' 	: partner_id,
			'status' 		: status
		};

		x.post("../admin/dashboard/statusupdate", data).success(function(response){
			if(response == 'success'){
				r.reload();
			} else {
				a.msg = "Oops. Something went wrong. Please try again later.";
				a.sm = true;

			    setTimeout(function(){
			    	a.msg = "";
	    			a.sm = false;
			    },3000);
			    return false;
			}              
		});
	};

	a.editLogosDetails = function(logodata){
		a.editLogo = true;
		rs.logoID = logodata.id;
		logodata.mobile = parseInt(logodata.mobile);
		a.logoData = logodata;

    	a.selectedLogoCategory 		= a.logoData.cat_id;
		a.selectedLogoSubCategory 	= a.logoData.subcat_id;

    	a.data = {
    		'parent_id' : a.logoData.cat_id
    	};

		a.get_cat();

	    a.get_subcat();
		console.log("logodata :: "+JSON.stringify(logodata, null, 4));
	};

	a.updatePartnerLogoDetails = function(){
		a.updatingData = true;
		
		if(!a.noNewLogo){
			OnGetFile("#edit_new_partner_logo");
    		var file = $("#edit_new_partner_logo")[0].files[0];

	    	a.logoData.image_name = file.name;
	    	a.logoData.image_data = a.image_data;
	    	a.logoData.image_type = file.type;
	    	a.logoData.image_changed = true;
		} else {
			a.logoData.image_changed = false;
		}

		x.post("../admin/dashboard/updatepartnerlogodata", a.logoData).success(function(res){
			// console.log("res :: "+JSON.stringify(res, null, 4));
			if(res == 'success'){
				r.reload();
			} else {
				a.updatingData = false;
		    	a.msg 				= "Unable to modify data.";
				a.sm1 				= true;
			    a.workingfine1		= false;

			    setTimeout(function(){
			    	a.msg 			= "";
				    a.workingfine1 	= true;
	    			a.sm1			= false;
			    },3000);
			}
		});
	};

	a.change_status = function(logo_id, status){
		var data = {
			'logo_id' 		: logo_id,
			'status' 		: status
		};

		x.post("../admin/dashboard/logostatusupdate", data).success(function(response){
			if(response == 'success'){
				r.reload();
			} else {
				a.msg = "Oops. Something went wrong. Please try again later.";
				a.sm = true;

			    setTimeout(function(){
			    	a.msg = "";
	    			a.sm = false;
			    },3000);
			    return false;
			}              
		});
	};

	a.uploadNewLogo = function(){
		a.noNewLogo = false;
	};

	a.cancelUploadNewLogo = function(){
		a.noNewLogo = true;
	};

	a.updateLogoChanged = function(){
		var file = $("#edit_new_partner_logo")[0].files[0];
    	var ext = $('#edit_new_partner_logo').val().split('.').pop().toLowerCase();
        var file_size = $('#edit_new_partner_logo')[0].files[0].size;

        if($.inArray(ext, ['jpg','jpeg','png','gif']) == -1) {

	    	a.msg 				= "Please upload only .jpg /.jpeg /.png /.gif image.";
			a.sm1 				= true;

		    setTimeout(function(){
		    	a.msg 			= "";
			    a.workingfine 	= false;
    			a.sm1 			= false;
		    },3000);
		    return false;
        } else {
        	OnGetFile("#edit_new_partner_logo");
        }
	};
}]);