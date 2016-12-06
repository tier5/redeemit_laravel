
"use strict";

var MyApp = angular.module("myoffer-app", ["angularUtils.directives.dirPagination"]);
MyApp.controller('MyofferController',["$scope", "PlaceholderTextService", "ngTableParams", "$filter", "$http", "$route",function (a, b, c, d, x, fu, r) {          
    localStorage.removeItem('pdid');
    a.dataLength={filtered:[]};
    a.cnames = [];
    a.myoffer_details = [];
    var site_path=$("#site_path").val();
   
    a.add_campaign_btn = "Save";
    a.add_campaign_disable =false;

    var list_url='dashboard#/myoffer/list';
    
    x.get("../promotion/list").success(function(myoffer_list){ 
     // console.log(JSON.stringify(myoffer_list,null,4));
      a.myoffer_details = myoffer_list;  
    });  

}]);