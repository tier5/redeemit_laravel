"use strict";

var MyApp = angular.module("campaign-app", ["ngFileUpload","angularUtils.directives.dirPagination"]);
MyApp.controller('CampaignAddController',["$scope", "$rootScope", "PlaceholderTextService", "ngTableParams", "$filter", "$http", "fileToUpload","$route",function (a, rs, b, c, d, x, fu, r) {
    a.dataLength            = {filtered:[]};
    a.cnames                = [];
    a.campaign_details      = [];
    a.Campaignedit          = [];
    a.Campaign              = {};
    var site_path           = $("#site_path").val();
    a.add_campaign_btn      = "Save";
    a.add_campaign_disable  = false;
    a.campaignItemIndex     = 0;
    a.pagination            = {};
    a.root_base_url         = root_base_url;
    //hide add campaign div on page load
    a.showAddCampaignDiv    = false;
    a.enableordisable       = false;
    a.showCampaignEditPopup = false;
    a.statOffer             = {};
    a.CurDate               = "";
    rs.pageNo               = 1;
    rs.campaignId           = "";
    rs.scopePageTitle       = 'Campaign';
    rs.pageTitle            = '';
    
    x.post("../campaign/list").success(function(data_response){     
      //console.log(JSON.stringify(data_response,null,4))    ;
        if(data_response.length){
          a.campaign_details = data_response; 
          a.file_path = site_path;
        } else {
          a.campaign_details = data_response;
        }
    });

    a.addCampaign = function(){
      a.add_campaign_disable=true;
      a.cancel_redirect = true;
      
      if($('#c_s_date').val() == '' || $('#c_e_date').val() == '' || $("#c_name").val() == '')
      {
          $("#error_div").hide();
          $("#show_message").slideDown();
          $("#error_div").html("Please insert all field.");
          $("#error_div").show();
          $("#success_div").hide();

          a.add_campaign_disable = false;
      } else {

          var c_s_date_arr = $('#c_s_date').val().split('/');
          var c_s_date = c_s_date_arr[2]+'-'+c_s_date_arr[0]+'-'+c_s_date_arr[1];
             
          var c_e_date_arr = $('#c_e_date').val().split('/');
          var c_e_date = c_e_date_arr[2]+'-'+c_e_date_arr[0]+'-'+c_e_date_arr[1];

          a.Campaign.c_s_date = c_s_date; 
          a.Campaign.c_e_date = c_e_date;
          
          x.post("../campaign/addlogo",a.Campaign).success(function(response){
            if(response=='success')
            {
              a.add_campaign_btn="Saving ...";
              a.add_campaign_disable = true;

              $("#error_div").hide();
              $("#show_message").slideDown();
              $("#success_div").html("Data inserted successfully. <br />Please wait,we will redirect you to listing page.");
              $("#success_div").show();              

              setTimeout(function(){
                r.reload();
              },100);
             
            }
            else
            {
              $("#error_div").hide();
              $("#show_message").slideDown();
              $("#error_div").html("There are some issues with your internate connection. Please refresh the page.");
              $("#error_div").show();
              $("#success_div").hide(); 
            }
           
          });
      }
       a.setCarausalTarget();
    };

    function getAllOffer(campaignId) {
      //console.log(campaignId);
      x.post("../campaign/offerbyid",campaignId).success(function(response){
       // console.log("res :: "+JSON.stringify(response, null, 4));
        a.allOffer = response;
        a.allOfferList = [];
        angular.forEach(a.allOffer, function(result, index) {
          // console.log("response :: "+JSON.stringify(result, null, 4));
          var currentd = new Date();
          var dt1 = result.end_date.split(' '),
              dt2 = dt1[0].split('-');
          var current_month = currentd.getMonth()+1; 
          var cd  = [];
          cd.push(currentd.getFullYear());
          cd.push('0'+current_month);
          cd.push(currentd.getDate());

          var one = new Date(cd[0],cd[1],cd[2]),
              two = new Date(dt2[0], dt2[1], dt2[2]);

          var millisecondsPerDay = 1000 * 60 * 60 * 24;
          var millisBetween = two.getTime() - one.getTime();
          var days = millisBetween / millisecondsPerDay;

          result.remaining_days = Math.floor(days);
          if(result.value_calculate == 1){
            result.discountvalue = Math.round(result.retails_value-result.pay_value);
          } else {
            result.discountvalue = Math.round(((result.retails_value-result.pay_value)*100)/result.retails_value);
          }
          a.allOfferList.push(result);
        });
        //console.log("a.allOfferList :: "+JSON.stringify(a.allOfferList, null, 4));
      });
    }

    // Function for deleting a campaign
    a.delete_campaign=function(itemId){     
      if(confirm("Are you sure?"))
      {  
        var main_site_url=$("#main_site_url").val();       
        x.get("../campaign/delete/"+itemId).success(function(response){
            r.reload();
        });
      }
    };

    a.redirect_edit=function(itemId, pageNo){
      // sessionStorage.removeItem("pageNo");
      // sessionStorage.removeItem("EditCampaignId");
      rs.pageNo = "";
      rs.campaignId = "";
      rs.pageNo =  pageNo;
      rs.campaignId = itemId;
      
      if(a.showCampaignEditPopup){
        a.showCampaignEditPopup = false;
      } else {
        a.showCampaignEditPopup = true;
        setTimeout(function(){
          if(rs.campaignId){
            var update_id = rs.campaignId;
          } else {
            var update_id = " ";
            r.reload(); 
          }
          var dateToday = new Date();
          $( "#c_s_e_date" ).datepicker({ 

            dateFormat:"mm/dd/yy",
            minDate: dateToday,
            onSelect: function (selected) {
                var dt = new Date(selected);
                dt.setDate(dt.getDate() + 1);
                $("#c_e_e_date").datepicker("option", "minDate", dt);
            }
          });

          $( "#c_e_e_date" ).datepicker({
              dateFormat:"mm/dd/yy",
              minDate: dateToday,
              onSelect: function (selected) {
                  var dt = new Date(selected);
                  dt.setDate(dt.getDate() - 1);
                  $("#c_s_e_date").datepicker("option", "maxDate", dt);
              }
          });

          x.post("../campaign/list",update_id).success(function(data_response){         
              if(data_response.length){
                a.campaign = data_response[0]; 
                a.c_s_e_date = a.campaign.start_date.split('-')[1]+'/'+a.campaign.start_date.split('-')[2]+'/'+a.campaign.start_date.split('-')[0];
                a.c_e_e_date = a.campaign.end_date.split('-')[1]+'/'+a.campaign.end_date.split('-')[2]+'/'+a.campaign.end_date.split('-')[0];
              } else {
                a.campaign = data_response;
              }
          });
        }, 100);
      }

    };

    a.editCampaignDone = function (){

      var c_s_date_arr  = $('#c_s_e_date').val().split('/');
      var c_s_date      = c_s_date_arr[2]+'-'+c_s_date_arr[0]+'-'+c_s_date_arr[1];
         
      var c_e_date_arr  = $('#c_e_e_date').val().split('/');
      var c_e_date      = c_e_date_arr[2]+'-'+c_e_date_arr[0]+'-'+c_e_date_arr[1];
      var c_name        = a.campaign.campaign_name;

      if(rs.campaignId){
        var update_id   = rs.campaignId;
      } else {
        var update_id   = " ";
        r.reload(); 
      }
      //alert(c_s_date);
      a.campaign_details = [{
        'start_date':c_s_date,
        'end_date':c_e_date,
        'campaign_name':c_name,
        'id':update_id
      }];

      // console.log("Campaign :: "+JSON.stringify(a.campaign_details, null, 4));
      if(c_name && c_s_date) {
          
        x.post("../campaign/editcampaign",a.campaign_details).success(function(response){
          console.log("res :: "+JSON.stringify(response, null, 4));
          if(response=='success')
          {
            r.reload();
          }
          else if(response=='invalid_id')
          {
            $("#error_div").hide();
            $("#show_message").slideDown();
            $("#error_div").html("Error occoure! Please try again.");
            $("#error_div").show();
            $("#success_div").hide();
            setTimeout(function(){
              r.reload();
            },100);
      
          }
          else
          {
            $("#error_div").hide();
            $("#show_message").slideDown();
            $("#error_div").html("Please insert all field.");
            $("#error_div").show();
            $("#success_div").hide(); 
          }
          
        });
      } else {
        $("#error_div").hide();
        $("#show_message").slideDown();
        $("#error_div").html("Please insert all field.");
        $("#error_div").show();
        $("#success_div").hide();
      }
    }

    a.cancel_redirect=function(){
      r.reload(); 
    };

    a.showOrHideAllOffer = function (index) {
      a.allOfferList = [];
      a.statOffer = {};
        if (a.campaignItemIndex == index) {
          a.campaignItemIndex = 0;
          rs.pageNo = "";
        } else {
          getAllOffer(index);
          a.campaignItemIndex = index;
        }
         a.setCarausalTarget();
       
        
      //console.log(index);
      //console.log(a.campaignItemIndex);
    };

    if(rs.pageNo && rs.campaignId){
      a.pagination.current = rs.pageNo;
      a.showOrHideAllOffer(rs.campaignId);
    } else {
      a.pagination.current = 1;
    }

    a.addNewOffer = function(camId){
      rs.campaignId = camId;
      rs.EditOfferData = {};
      rs.scopePageTitle = 'Offer Maker';
      var url = 'dashboard#/promotion/create';
      window.location = url;
    };

    a.delete_offer= function(itemId) {
      if(confirm("Are you sure?")) {
        x.post("../promotion/softdeloffer",itemId).success(function(data_item){ 
          var edit_url = 'dashboard#/campaign/list/';    
          window.location = edit_url;
        });       
      } 
    };

    a.$watch('pagination.current', function(val){
      a.currentPageChanged = val;
      //console.log("val :: "+val);
    });

    // show or hide add campaign div method
    a.showAddCampaign = function(){
      a.showAddCampaignDiv = true;
      a.enableordisable = true;
      setTimeout(function(){
        var dateToday = new Date();
        $( "#c_s_date" ).datepicker({ 

          dateFormat:"mm/dd/yy",
          minDate: dateToday,
          onSelect: function (selected) {
              var dt = new Date(selected);
              dt.setDate(dt.getDate() + 1);
              $("#c_e_date").datepicker("option", "minDate", dt);
          }
        });

        $( "#c_e_date" ).datepicker({
            dateFormat:"mm/dd/yy",
            minDate: dateToday,
            onSelect: function (selected) {
                var dt = new Date(selected);
                dt.setDate(dt.getDate() - 1);
                $("#c_s_date").datepicker("option", "maxDate", dt);
            }
        });
      }, 100);
    };

    a.hideCampaignDiv = function(){
      a.showAddCampaignDiv = false;
      a.enableordisable = false;
    };
    
     a.setCarausalTarget = function(){
         setTimeout(function(){
         angular.element('.carousel-control').each(function(){
           var caroId = angular.element(this).parent().parent('.carousel').attr('id');
           console.log('caroId='+caroId);
           angular.element(this).attr('data-target','#'+caroId);
         });
         },1000);
     };//setCarausalTarget
    
     a.setCarausalTarget();
     
     a.setStatOffer = function(offer){

        var date = new Date();
        var day = date.getDate();
        var monthIndex = date.getMonth();
        var year = date.getFullYear();
        var monthNames = [
          "January", "February", "March",
          "April", "May", "June", "July",
          "August", "September", "October",
          "November", "December"
        ];

        a.CurDate = monthNames[monthIndex] + ' ' + day + ', ' + year;
        var offerDetails = offer;
        var totalCost = 0;
        var profit = 0;
        var totalProfit = 0;
        var totalPayValue = 0;
        angular.forEach(offer.productDetails, function(product){
          totalCost = parseFloat(totalCost) + (parseFloat(product.cost)*product.quantity);
        });

        angular.forEach(offer.inventoryDetails, function(inventory){
          totalCost = parseFloat(totalCost) + (parseFloat(inventory.cost)*inventory.quantity);
        });

        profit = parseFloat(offer.pay_value)-parseFloat(totalCost);
        totalProfit = parseFloat(profit)*offer.validatedcount;
        totalPayValue = parseFloat(offer.pay_value)*offer.validatedcount;
        a.statOffer = offer;
        a.statOffer.totalProfit = totalProfit;
        a.statOffer.totalPayValue = totalPayValue;
     };

     a.unsetStatOffer = function(){
        a.statOffer = {};
     };

    a.editOffer = function(offer){
      // console.log("offer :: "+JSON.stringify(offer, null, 4));
      // localStorage.removeItem('editOfferData');
      // localStorage.setItem('editOfferData', offer);
      rs.EditOfferData = offer;
      rs.scopePageTitle = 'Offer Maker';
      var url = '#/promotion/create';
      window.location = url;
    };

    a.deleteDraftOffer = function(offerID){
      console.log("offerID :: "+offerID);
      x.post("../promotion/deleteoffer",{'offer_id': offerID}).success(function(res){
        if(res == 'success'){
          r.reload();
        } else {
          console.log("error");
        }
      });
    };
}]);


