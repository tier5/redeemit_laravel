"use strict";

var MyApp = angular.module("profile-app", []);
MyApp.controller('ProfileController',["$scope", "$rootScope", "$http", "$route",function (a, rs, x, r) {
	
	/* All Variable Diclaration */
	var site_path 			= $("#site_path").val();
    a.profileData 			= [];
    a.file_path 			= site_path;
    a.disableIt 			= false;
    a.saving 				= false;


    rs.scopePageTitle       = 'Profile';
    rs.pageTitle            = '';    

    x.get('../user/show').success(function(res){
    	// console.log("res :: "+JSON.stringify(res, null, 4));
    	res[0].mobile = parseInt(res[0].mobile);
    	a.profileData = res[0];
    	a.profileData.cat_name = res[0].cat_name
    	a.selectedSubCat = res[0].subcat_id;
    });

    a.updateUderData = function(){
    	if(!a.disableIt){
    		a.disableIt = true;
    		a.saving 	= true;
    		// console.log("a.changepass :: "+JSON.stringify(a.changepass, null, 4));
    		if(a.changepass){
    			if((a.changepass.new_password == a.changepass.confirm_user_password) && (a.changepass.new_password.length >= 6)){
    				a.changepassdata = {
    					'old_pass': a.changepass.password,
    					'new_pass': a.changepass.new_password
    				}
    			} else {
    				a.changepassdata = {};
    			}
    		} else {
    			a.changepassdata = {};
    		}

    		a.profileData.changepssData = a.changepassdata;

    		console.log("a.profileData :: "+JSON.stringify(a.profileData, null, 4));

    		x.post('../user/updatedata', a.profileData).success(function(res){
    			if(res == 'success'){
		    		a.disableIt = false;
		    		a.saving 	= false;
		    		r.reload();
    			} else {

    			}
    		});


    	} else {
    		return false;
    	}
    };
}]);