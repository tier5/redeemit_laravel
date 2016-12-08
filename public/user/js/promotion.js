
"use strict";

var MyApp = angular.module("promotion-app", ["ngFileUpload", 'rzModule', 'ui.bootstrap']);

MyApp.controller('PromotionController',["$scope", "$rootScope", "$sce","PlaceholderTextService", "ngTableParams", "$filter", "$http", "fileToUpload","$route","$location",function (a, ra, s, b, c, d, x, fu, r,l) {          
    var site_path                   = $("#site_path").val();
    a.file_path                     = site_path;
    a.popupShowing                  = false;
    a.showproductorinventory        = false;
    a.selectedCampaign              = "";
    a.showAddCampaignDiv            = false;
    a.selected_validate_after_Hour  = 0;
    a.showOfferValidationTimeDiv    = false;
    a.categorryList                 = [];
    a.date_camp_start               = 0;
    a.openCat                       = 0;
    a.camp_start_selecte_date       = 0;
    a.validate_hour_txt             = 'Default 24 hours';
    a.Campaign                      = {};
    a.offerSelectedImagePath        = '';
    a.SelectedProductImage          = null;
    a.AllData                       = [];
    a.AllInventoryData              = {};
    a.totalProductAndInventoryCost  = 0;
    a.checkStatus                   = false;
    a.margin_markup_above           = false;
    a.margin_markup_equal           = false;
    a.margin_markup_poor            = false;
    a.markup                        = "00";
    a.grossMargin                   = "00";
    a.inventorymarkup               = "00";
    a.inventorygrossMargin          = "00";
    a.totalRetailPrice              = "00";
    a.opMargin                      = "00";
    a.opMarkup                      = "00";
    a.consumerPay                   = "00";
    a.discount                      = "00";
    a.OffValue                      = "00";
    a.SaveValue                     = "00";
    a.DiscountValue                 = "00";
    a.opMarginProfit                = "";
    a.opMarkupProfit                = "";
    a.tpaiv                         = "00";
    a.tpaisp                        = "00";
    a.DiscountValueText             = "$00 OFF";
    a.publishPossible               = false;
    a.selectedDiscount              = 1;
    a.repoDirTree                   = {};
    a.repos                         = {};
    a.selectedRepo                  = {};
    a.lftPnlSelectedDir             = 0;
    a.importRepoforChoiceObj        = {};
    a.importRepoforInventoryObj     = {};
    a.selectedDiscountText          = 1;
    a.offerValidated                = true;
    a.show_camp_date                = 'MM/DD/YY - MM/DD/YY';
    a.offerResponse                 = {'location':'','lat':'','lng':''};
    a.loggedUser                    = {};
    a.mapurl                        = '';
    a.offerPublishing               = false;
    a.promotion                     = {};
    a.offer_id                      = 0;  
    a.promotion.on_demand           = false;
    a.selected_validate_after_Hour  = 24;
    a.published                     = false;
    a.disableIt                     = false;
    a.showAddCampaignDiv            = false;
    a.add_campaign_disable          = false;
    a.editDiscountValue             = false;
    a.root_base_url                 = root_base_url;
    a.productAvailable              = false;
    a.inventoryAvailable            = false;
    a.largeText                     = true;
    a.savePossible                  = false;
    a.offerSaving                   = false;
    ra.scopePageTitle               = 'Offer Maker';
    ra.pageTitle                    = '';

    a.choices = [{
      'choiceid': 1,
      'productData':[],
      'noofproduct':1,
      'total_cost':0,
      'total_selling_price':0,
      'total_retail_price':0,
      'selectedImage':true
    }];

    a.inventorychoices = [{
      'inventorychoiceid': 1,
      'InventoryData':[],
      'noofinventory':1,
      'total_cost':0,
      'total_selling_price':0,
      'total_retail_price':0,
      'selectedImage':false
    }];

    a.validate_after_hour = {
      value: 0,
      options: {
        ceil: 24,
        floor: 0,
        showTicks: 4,
        onEnd: function(id, newValue, highValue, pointerType) {
          a.selected_validate_after_Hour = newValue;
          $('#selected_validate_after_Hour').trigger('change');
         // console.log(newValue);
        }
      }
    };


    
    function setDatePickers(){
      $("#more_information").tagit();
      var dateToday = new Date();

      $( "#c_s_date" ).datepicker({ 

        dateFormat:"mm/dd/yy",
        minDate: 0,
        beforeShow: function(d,i) {
          i.settings.minDate = a.date_camp_start;
          i.settings.maxDate = a.date_camp_end;
       },
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate() + 1);
            a.camp_start_selecte_date = dt;
        }
      });

      $( "#c_e_date" ).datepicker({
        dateFormat:"mm/dd/yy",
         beforeShow: function(d,i) {
            if(a.camp_start_selecte_date){
                i.settings.minDate = a.camp_start_selecte_date;
            }else{
                i.settings.minDate = a.date_camp_start;
            }
            i.settings.maxDate = a.date_camp_end;
            
        },
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate() - 1);
            $("#c_s_date").datepicker("option", "maxDate", dt);
        }
      });


      $( "#a_c_s_date" ).datepicker({ 
    
        dateFormat:"mm/dd/yy",
        minDate: dateToday,
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate() + 1);
            $("#a_c_e_date").datepicker("option", "minDate", dt);
        }
      });

      
      $( "#a_c_e_date" ).datepicker({
          dateFormat:"mm/dd/yy",
          minDate: dateToday,
          onSelect: function (selected) {
              var dt = new Date(selected);
              dt.setDate(dt.getDate() - 1);
              $("#a_c_s_date").datepicker("option", "maxDate", dt);
          }
      });
    }

    //call this when chaging dropdown
    a.get_campaign_details = function(){
      // var campaign_id = $("#campaign_id").val();

      x.post("../campaign/list",a.campaign_id).success(function(response){
        ra.campaignId = "";
        ra.campaignId = a.campaign_id;
        a.selectedCampaign = ra.campaignId;        
        var date_start_full = new Date(response[0].start_date);
        var date_end_full = new Date(response[0].end_date);

        a.promotion.c_s_date = a.date_camp_start = ( '0' + (date_start_full.getMonth()+1) ).slice( -2 ) + '/' + (( '0' +((date_start_full.getDate()).toString())) .slice( -2 ))  + '/' +  date_start_full.getFullYear();

        a.promotion.c_e_date = a.date_camp_end = ( '0' + (date_end_full.getMonth()+1) ).slice( -2 ) + '/' + (( '0' +((date_end_full.getDate()).toString())) .slice( -2 ))  + '/' +  date_end_full.getFullYear();

        //var date_camp_end = (date_end_full.getMonth() + 1) + '/' + date_end_full.getDate() + '/' +  date_end_full.getFullYear();

        //print campaign date
        a.show_camp_date = a.date_camp_start+' - '+a.date_camp_end;
      });
    };


    //Get all Campaign of this user
    function getCampaignList(){
      x.post("../campaign/list").success(function(response){ 
        a.campaign_list = response; 
        a.file_path = site_path;
        a.selectedCampaign = ra.campaignId;
        
        if(a.selectedCampaign){
          a.campaign_id = a.selectedCampaign;
          a.get_campaign_details();

          setTimeout(function(){
            setDatePickers();
          },200);  
        } else {
          a.campaign_id = a.campaign_list[0].id;
          a.get_campaign_details();

          setTimeout(function(){
            setDatePickers();
          },200);  
        }   
      });
    }

    a.showMyDiv = function(){
      if(a.showOfferValidationTimeDiv && !a.disableIt) {
        a.showOfferValidationTimeDiv = false;
      } else if(!a.disableIt) {
        a.showOfferValidationTimeDiv = true;
      }
    };
    
    a.getSelected = function(){
        var strHtml = '';
        angular.element('.check_class').each(function(){
            if(angular.element(this).is(':checked')){
                strHtml += ('<div class="valuecontent" data-value="'+angular.element(this).val()+'">'+angular.element(this).attr('data-subcatname')+'<i class="fa fa-times-circle-o  remove-btn" aria-hidden="true"></i></div>');
            }
        });
        angular.element('.selected-value').html(strHtml);
    };
    
    a.getSelectedCatIds = function(){
        var selCatIdsArr = [];
        angular.element('.check_class').each(function(){
                    if(angular.element(this).is(':checked')){
                        selCatIdsArr.push(angular.element(this).val());
                    }
            });
        return selCatIdsArr;
    }
    
    angular.element(document).on('click','.valuecontent',function(){
      angular.element('.check_class[value="'+angular.element(this).attr('data-value')+'"]').attr('checked',false);
      a.getSelected();
    });
    
    
    a.hourValidate = function(value){
        if(value){
            a.promotion.start_hour = value;
            a.selected_validate_after_Hour = value;

            if(value > 1 ){
              a.validate_hour_txt = 'Validate after ' + value +' hours';
            } else {
              a.validate_hour_txt = 'Validate after ' + value +' hour';
            }
            a.showOfferValidationTimeDiv = false;
            a.promotion.on_demand = false;
        } else {
            a.promotion.start_hour = value;
            a.validate_hour_txt = 'On Demand';
            a.promotion.on_demand = true;
            a.showOfferValidationTimeDiv = false;
        }
    };

    
    a.checkMyValue = function(){
      var alphanumers = /^\d+(\.\d{1,2})?$/;
      if(!alphanumers.test(a.discount)){
        $("#error_div").hide();
        $("#show_message").slideDown();
        $("#error_div").html("Discount value can have only numbers.");
        $("#error_div").show();
        setTimeout(function(){
          $("#error_div").hide();
        },3000);
      } else {
          $("#error_div").hide();
          a.setTotalRetailPrice();
          a.changeOnDiscountText();
      }
    };
    
    a.ondemand = function(demand){
        if(demand){
            a.hourValidate(0);
        }else{
            a.hourValidate(24);
        }
    };
    
    a.getUserinfo = function(){
      x.get('../user/userinfo').success(function(res){
          a.loggedUser = res;
          a.mapurl = s.trustAsResourceUrl("https://www.google.com/maps/embed/v1/view?key=AIzaSyD64Y-QDJdKxaRw1rs1rGJrkx7klW5B2Hs&zoom=12&center="+a.loggedUser.lat+","+a.loggedUser.lng);
      });
    };
   

    a.showAddCampaignForm = function(){
      a.showAddCampaignDiv = true;
      setTimeout(function(){
        var dateToday = new Date();
        $( "#popup_c_s_date" ).datepicker({ 

          dateFormat:"mm/dd/yy",
          minDate: dateToday,
          onSelect: function (selected) {
              var dt = new Date(selected);
              dt.setDate(dt.getDate() + 1);
              $("#popup_c_e_date").datepicker("option", "minDate", dt);
          }
        });

        $( "#popup_c_e_date" ).datepicker({
            dateFormat:"mm/dd/yy",
            minDate: dateToday,
            onSelect: function (selected) {
                var dt = new Date(selected);
                dt.setDate(dt.getDate() - 1);
                $("#popup_c_s_date").datepicker("option", "maxDate", dt);
            }
        });
      }, 100);
    };

    a.hideAddCampaignDiv = function(){
      a.showAddCampaignDiv = false;
    };

    a.addNewChoice = function() {
      var newItemNo = a.choices.length+1;
      var is_selected_choice = a.choices.length>0?false:true;
      var newChoiceCreate = {
        'choiceid': newItemNo,
        'productData':[],
        'noofproduct':1,
        'total_cost':0,
        'total_selling_price':0,
        'total_retail_price':0,
        'selectedImage':is_selected_choice
      };
      a.choices.push(newChoiceCreate);
    };
    
    a.addCampaign = function(){
      a.add_campaign_disable=true;
      a.cancel_redirect = true;
      

      var c_s_date_arr = $('#popup_c_s_date').val().split('/');
      var c_s_date = c_s_date_arr[2]+'-'+c_s_date_arr[0]+'-'+c_s_date_arr[1];
         
      var c_e_date_arr = $('#popup_c_e_date').val().split('/');
      var c_e_date = c_e_date_arr[2]+'-'+c_e_date_arr[0]+'-'+c_e_date_arr[1];

      a.Campaign.c_s_date = c_s_date; 
      a.Campaign.c_e_date = c_e_date;
      
      // console.log("a.Campaign :: "+JSON.stringify(a.Campaign));
      x.post("../campaign/addlogo",a.Campaign).success(function(response){
        if(response=='success')
        {
          a.add_campaign_btn="Saving ...";
          a.add_campaign_disable = true;

          $("#camp_popup_error_div").hide();
          $("#show_message").slideDown();
          $("#success_div").html("Data inserted successfully. <br />Please wait,we will redirect you to listing page.");
          $("#success_div").show();              
          getCampaignList();
          setTimeout(function(){
            $("#success_div").hide();
          },100);
          a.showAddCampaignDiv = false;
         
        }
        else
        {
          a.add_campaign_disable = false;
          $("#camp_popup_error_div").hide();
          $("#show_message").slideDown();
          $("#camp_popup_error_div").html("There are some issues with your internate connection. Please refresh the page.");
          $("#camp_popup_error_div").show();
          $("#success_div").hide();
          setTimeout(function(){
            $("#camp_popup_error_div").hide();
          },100);
        }
       
      });
    };

      
    a.removeChoice = function(choice) {
      if(!a.disableIt){
        var index = a.choices.indexOf(choice);
        var itWasSelected = false;
        if(a.choices[index].selectedImage){
          itWasSelected = true;
        }
        a.choices.splice(index, 1);
        a.choices = a.choices;
        
        if(itWasSelected ){
          if(a.choices.length){
            a.choices[0].selectedImage = true;
          } else {
            a.productAvailable = false;
            a.offerSelectedImagePath = "";
          }
          setTimeout(function(){
              a.offerSelectedImagePath = '';
              if (a.$root && a.$root.$$phase != '$apply' && a.$root.$$phase != '$digest') {
                  a.$apply();
              }
              angular.element('.choiceRadio:eq(0)').trigger('change');
          },200);
        }
        
        a.setMarkupAndMargin();
      }
    };

    a.productnochanged = function(choice){
      a.selectedChoice = choice;
      var index = a.choices.indexOf(choice);
      
      if(a.selectedChoice.productData ){
        a.choices[index].total_cost = parseFloat(a.selectedChoice.noofproduct * a.selectedChoice.productData[0].cost).toFixed(2);
        a.choices[index].total_selling_price = parseFloat(a.selectedChoice.noofproduct * a.selectedChoice.productData[0].sell_price).toFixed(2);
        a.choices[index].total_retail_price = parseFloat(a.selectedChoice.noofproduct * a.selectedChoice.productData[0].retail_price).toFixed(2);
        
        if(a.choices[index].total_cost.split('.')[1] == '00'){
          a.choices[index].total_cost = a.choices[index].total_cost.split('.')[0];
        }
        if(a.choices[index].total_selling_price.split('.')[1] == '00'){
          a.choices[index].total_selling_price = a.choices[index].total_selling_price.split('.')[0];
        }
        if(a.choices[index].total_retail_price.split('.')[1] == '00'){
          a.choices[index].total_retail_price = a.choices[index].total_retail_price.split('.')[0];
        }
        a.setMarkupAndMargin();
      }

      
    };

    a.fetchAllProduct = function(choiceid){
      x.get("../product/showlist").success(function(response){
        // console.log("response :: "+JSON.stringify(response, null, 4));
        if(response.length){
          for(var i =0; i <= response.length-1; i++){
            response[i].choiceid = choiceid;
          }
          a.AllData = response;
        } else {
          a.AllData = [];
        }
      });
      a.showproductorinventory = true;
    };

    a.hideMe = function(){
      a.showproductorinventory = false;
      a.AllData = [];
      a.AllInventoryData = {};
    };

    a.usethisProduct = function(productId, choiceid){
      a.productAvailable = true;
      x.get("../product/addselectedproduct/"+productId).success(function(res){
        angular.forEach(a.choices, function(value, key){
          var image_name = res[0].product_image.split("/");
          res[0].image_name = image_name[image_name.length-1];
          if(value.choiceid == choiceid){
            a.choices[key].productData = res;
            a.productnochanged(a.choices[key]);
            if(a.choices[key].selectedImage){
              a.offerSelectedImagePath = res[0].product_image;
              setTimeout(function(){
                  angular.element('.choiceRadio:eq(0)').trigger('change');
              },200);
            }
          }
        });
        a.setMarkupAndMargin();
      });
    };

    a.setMarkupAndMargin = function(){
      a.new_total_selling_price = 0;
      a.new_total_cost = 0;
      if(a.choices.length){
        angular.forEach(a.choices, function(value, key){
          // console.log("value :: "+value.choiceid+" , key :: "+key);
            a.new_total_cost = a.new_total_cost+parseFloat(value.total_cost);
            a.new_total_selling_price = a.new_total_selling_price+parseFloat(value.total_selling_price);
        });
        a.grossMargin = parseFloat(a.new_total_selling_price-a.new_total_cost);
        a.markup = ((parseFloat(a.grossMargin)/parseFloat(a.new_total_cost))*100).toFixed(2);
        a.grossMargin = a.grossMargin.toFixed(2);
      } else {
        a.grossMargin = '00';
        a.markup = '00';
      }
      a.markup = isNaN(a.markup)?'00':a.markup;
      a.setTotalRetailPrice();
    };
    
    
    a.showRepoModal = function(choice){
      a.popupShowing = true;
      if(choice.choiceid){
        a.importRepoforChoiceObj = choice;
      }else if(choice.inventorychoiceid){
        a.importRepoforInventoryObj = choice;
      }
    };

    a.hidePopup = function(){
      a.popupShowing = false;
      a.importRepoforChoiceObj = {};
      a.importRepoforInventoryObj = {};
       if (a.$root.$$phase != '$apply' && a.$root.$$phase != '$digest') {
            a.$apply();
        }
    };
    
    a.getRepoImagesbyDirId = function(dir_id) {
      //console.log(dir_id);
      var url = "../directory/repos/"+dir_id;

      x.get(url).success(function(response){
         a.repos = response;
      });
    };
    
    a.doSelectedRepo = function(file){
        //console.log(a.importRepoforInventoryObj);
        //console.log(a.importRepoforInventoryObj.choiceid);
        if(a.importRepoforChoiceObj.choiceid){
            a.selectedRepo = file;
            if(!a.importRepoforChoiceObj.productData[0]){
                a.importRepoforChoiceObj.productData[0] = {};
            }
            a.importRepoforChoiceObj.productData[0].product_image = file.directory_base_path;
            a.importRepoforChoiceObj.productData[0].image_name  = file.file_name;
            a.importRepoforChoiceObj.productData[0].is_file_exist  = file.is_file_exist;
            
            setTimeout(function(){
              angular.element('.choiceRadio:eq(0)').trigger('change');
            },200);
        }else if(a.importRepoforInventoryObj.inventorychoiceid){
            if(!a.importRepoforInventoryObj.InventoryData[0]){
                a.importRepoforInventoryObj.InventoryData[0] = {};
            }
            a.importRepoforInventoryObj.InventoryData[0].is_file_exist = file.is_file_exist
            a.importRepoforInventoryObj.InventoryData[0].inventory_image = file.directory_base_path
        }  
    };

    a.getImagerepo = function(){
        x.get("../directory/dirtree").success(function(data){
            a.repoDirTree = data;
        });
    };

    a.setleftOpenDir = function(dirId){
      a.lftPnlSelectedDir = dirId;
    };

    
    angular.element(document).on('change','.choiceRadio',function(){
      console.log('change prod radio');
      if(angular.element('.choiceRadio').filter(':checked').length){
        var choiceRadioIdArr = angular.element('.choiceRadio').filter(':checked').attr('id').split('-');
       
        var choiceRadioId = choiceRadioIdArr[1];
        if(ra.EditOfferData.published == "true"){
          a.offerSelectedImagePath = a.root_base_url+ra.EditOfferData.offer_large_image_path;
          a.published = true;
          a.disableIt = true;
        } else {
          a.offerSelectedImagePath = angular.element('#product_image_id'+choiceRadioId).attr('src');
          a.disableIt = false;
          a.published = false;
        }
  
        if(choiceRadioId){
            angular.forEach(a.inventorychoices,function(c,k){
              a.inventorychoices[k].selectedImage = false;
            });
            angular.forEach(a.choices,function(c,k){
                if(c.choiceid == choiceRadioId){
                    a.choices[k].selectedImage = true;
                }else{
                    a.choices[k].selectedImage = false;
                }
            });
            
           
        }else{
            console.log('No choiceRadioId' + choiceRadioId);
        }
        
        
        if (a.$root && a.$root.$$phase != '$apply' && a.$root.$$phase != '$digest') {
        a.$apply();
        }else{
            //setTimeout(function(){
            //    angular.element('.choiceRadio:eq(0)').trigger('change');
            //},200);
        }
      }
            
    });

    angular.element(document).on('change','.inventoryRadio',function(){
      
      if(angular.element('.inventoryRadio').filter(':checked').length){
        var inventoryRadioIdArr = angular.element('.inventoryRadio').filter(':checked').attr('id').split('-');
        var inventoryRadioId = inventoryRadioIdArr[1];
        if(ra.EditOfferData.published == "true"){
          a.offerSelectedImagePath = a.root_base_url+ra.EditOfferData.offer_large_image_path;
          a.published = true;
          a.disableIt = true;
        } else {
          a.offerSelectedImagePath = angular.element('#inventory_image_id'+inventoryRadioId).attr('src');
          a.disableIt = false;
          a.published = false;
        }
  
        if(inventoryRadioId){
  
            angular.forEach(a.choices,function(c,k){
              a.choices[k].selectedImage = false;
            });
  
            angular.forEach(a.inventorychoices,function(c,k){
                if(c.inventorychoiceid == inventoryRadioId){
                    a.inventorychoices[k].selectedImage = true;
                }else{
                    a.inventorychoices[k].selectedImage = false;
                }
            });
            
           
        }else{
            console.log('No inventoryRadioId' + inventoryRadioId);
        }
        
        
        if (a.$root && a.$root.$$phase != '$apply' && a.$root.$$phase != '$digest') {
        a.$apply();
        }else{
            //setTimeout(function(){
            //    angular.element('.choiceRadio:eq(0)').trigger('change');
            //},200);
        }
      }
            
    });

    /* for inventory */

    a.addNewInventoryChoice = function() {
      var newItemNo = a.inventorychoices.length+1;
      var is_selected_choice = false;
      var newChoiceCreate = {
        'inventorychoiceid': newItemNo,
        'InventoryData':[],
        'noofinventory':1,
        'total_cost':0,
        'total_selling_price':0,
        'total_retail_price':0,
        'selectedImage':is_selected_choice
      };
      a.inventorychoices.push(newChoiceCreate);
    };
      
    a.removeInventoryChoice = function(inventorychoice) {
      if( !a.disableIt ){
        var index = a.inventorychoices.indexOf(inventorychoice);
        a.inventorychoices.splice(index, 1);
        a.inventorychoices = a.inventorychoices;
        a.setInventoryMarkupAndMargin();
        a.setTotalRetailPrice();
        if(!a.inventorychoices.length){
          a.inventoryAvailable = false;
        }
      }
    };

    a.inventorynochanged = function(inventorychoice){
      a.selectedInventoryChoice = inventorychoice;
      var index = a.inventorychoices.indexOf(inventorychoice);
      a.inventorychoices[index].total_cost = parseFloat(a.selectedInventoryChoice.noofinventory * a.selectedInventoryChoice.InventoryData[0].cost).toFixed(2);
      a.inventorychoices[index].total_selling_price = parseFloat(a.selectedInventoryChoice.noofinventory * a.selectedInventoryChoice.InventoryData[0].sell_price).toFixed(2);
      a.inventorychoices[index].total_retail_price = parseFloat(a.selectedInventoryChoice.noofinventory * a.selectedInventoryChoice.InventoryData[0].retail_price).toFixed(2);
      if(a.inventorychoices[index].total_cost.split('.')[1] == '00'){
        a.inventorychoices[index].total_cost = a.inventorychoices[index].total_cost.split('.')[0];
      }
      if(a.inventorychoices[index].total_selling_price.split('.')[1] == '00'){
        a.inventorychoices[index].total_selling_price = a.inventorychoices[index].total_selling_price.split('.')[0];
      }
      if(a.inventorychoices[index].total_retail_price.split('.')[1] == '00'){
        a.inventorychoices[index].total_retail_price = a.inventorychoices[index].total_retail_price.split('.')[0];
      }
      a.setInventoryMarkupAndMargin();
    };

    a.fetchAllInventory = function(inventorychoiceid){
      x.post("../inventory/list").success(function(response){
        // console.log("response :: "+JSON.stringify(response, null, 4));
        if(response.length){
          for(var i =0; i <= response.length-1; i++){
            response[i].inventorychoiceid = inventorychoiceid;
          }
          a.AllInventoryData = response;
        } else {
          a.AllInventoryData = [];
        }
      });
      a.showproductorinventory = true;
      // $(".black-bg").show();
      // disableScroll();
    };

    a.usethisInventory = function(inventoryId, inventorychoiceid){
      a.inventoryAvailable = true;
      
      var inventory_id = inventoryId;
      var inventorychoiceid = inventorychoiceid;
      x.post("../inventory/inventorydetails",inventory_id).success(function(res){        
        // console.log("res :: "+JSON.stringify(res, null, 4));
        angular.forEach(a.inventorychoices, function(value, key){
          // console.log("value :: "+value.choiceid+" , key :: "+key);
          var image_name = res.inventory_image.split("/");
          res.image_name = image_name[image_name.length-1];
          if(value.inventorychoiceid == inventorychoiceid){
            if(a.inventorychoices[key].InventoryData[0]){
                a.inventorychoices[key].InventoryData[0] = {};
            }
            a.inventorychoices[key].InventoryData[0] = res;
            a.inventorynochanged(a.inventorychoices[key]);

            if(a.inventorychoices[key].selectedImage){
              a.offerSelectedImagePath = res.inventory_image;
              setTimeout(function(){
                  angular.element('.inventoryRadio:eq(0)').trigger('change');
              },200);
            }
          }
        });
        a.setInventoryMarkupAndMargin();
      });
    };

    a.setInventoryMarkupAndMargin = function(){
      a.new_total_inventory_selling_price = 0;
      a.new_total_inventory_cost = 0;
      if(a.inventorychoices.length){
        angular.forEach(a.inventorychoices, function(value, key){
          // console.log("value :: "+value.choiceid+" , key :: "+key);
            a.new_total_inventory_cost = a.new_total_inventory_cost+parseFloat(value.total_cost);
            a.new_total_inventory_selling_price = a.new_total_inventory_selling_price+parseFloat(value.total_selling_price);
        });
        a.inventorygrossMargin = parseFloat(a.new_total_inventory_selling_price-a.new_total_inventory_cost);
        a.inventorymarkup = ((parseFloat(a.inventorygrossMargin)/parseFloat(a.new_total_inventory_cost))*100).toFixed(2);
        a.inventorygrossMargin = a.inventorygrossMargin.toFixed(2);
      } else {
        a.inventorygrossMargin = '00';
        a.inventorymarkup = '00';
      }

      a.setTotalRetailPrice();
    };

    a.clickedOnMe = function(clicked){
      if(clicked.choiceid){
        var image_id = 'product_image_id'+clicked.choiceid;
        $("#inventory_or_product_image_id").val(image_id);
      } else if(clicked.inventorychoiceid){
        var image_id = 'inventory_image_id'+clicked.inventorychoiceid;
        $("#inventory_or_product_image_id").val(image_id);
      } else {
        console.log("none of the value is true");
      }
    };

    /* for inventory */
    a.setTotalRetailPrice = function(){
      a.totalRetailPrice = "00";
      a.tpaisp = "00";
      a.tpc = "00";
      a.tic = "00";
      a.tpsp = "00";
      a.tisp = "00";
      a.tpaic = "00";
      if(a.choices.length){
        angular.forEach(a.choices, function(productDetails){
          a.totalRetailPrice = parseFloat(a.totalRetailPrice)+parseFloat(productDetails.total_retail_price);
          a.tpc = parseFloat(a.tpc)+parseFloat(productDetails.total_cost);
          a.tpsp = parseFloat(a.tpsp)+parseFloat(productDetails.total_selling_price);
        });
      }
      if(a.inventorychoices.length){
        angular.forEach(a.inventorychoices, function(inventoryDetails){
          a.totalRetailPrice = parseFloat(a.totalRetailPrice)+parseFloat(inventoryDetails.total_retail_price);
          a.tic = parseFloat(a.tic)+parseFloat(inventoryDetails.total_cost);
          a.tisp = parseFloat(a.tisp)+parseFloat(inventoryDetails.total_selling_price);
        });
      }

      a.tpaisp = parseFloat(a.tpsp)+parseFloat(a.tisp);
      a.tpaisellingprice = parseFloat(a.tpsp)+parseFloat(a.tisp);
      a.tpaic = parseFloat(a.tpc)+parseFloat(a.tic);
      a.CalculateConsumerPay();
    };

    a.CalculateConsumerPay = function(){
      if( a.tpc || a.tic ){
        if(a.checkStatus){ 
          if(a.tic){
            if(a.selectedDiscount == 1){
              if(a.discount != ""){
                // a.consumerPay = parseFloat(a.tpaisp)-parseFloat(a.discount);
                var cisp = parseFloat(a.tisp)-parseFloat(a.discount);
                a.consumerPay = parseFloat(cisp+a.tpsp);
              } else {
                a.consumerPay = parseFloat(a.tpaisp);
              }

              a.OffValue = parseFloat(a.totalRetailPrice)-parseFloat(a.consumerPay);

              if(a.consumerPay){
                a.DiscountValue = Math.round(((parseFloat(a.OffValue)/parseFloat(a.totalRetailPrice))*100).toFixed(2));
              } else {
                a.DiscountValue = "00";
              }
              a.opMargin = parseFloat(a.consumerPay)-parseFloat(a.tpaic);
              if(a.opMargin){
                a.opMarkup = ((parseFloat(a.opMargin)/a.tpaic)*100).toFixed(2);
              } else {
                a.opMarkup = "00";
              }

              a.opMargin = a.opMargin.toFixed(2);
              a.consumerPay = a.consumerPay.toFixed(2);
              a.OffValue = Math.round(a.OffValue.toFixed(2));
              a.consumerPayValue('margin');
            } else if(a.selectedDiscount == 2){
              if(a.discount != ""){
                // a.consumerPay = parseFloat(a.tpaisp)-((parseFloat(a.discount)*parseFloat(a.tisp))/100);
                var cisp = parseFloat(a.tisp)-((parseFloat(a.discount)*parseFloat(a.tisp))/100);
                a.consumerPay = parseFloat(cisp+a.tpsp);
              } else {
                a.consumerPay = parseFloat(a.tpaisp);
              }

              a.OffValue = parseFloat(a.totalRetailPrice)-parseFloat(a.consumerPay);
              
              if(a.tpaisp){
                a.DiscountValue = Math.round((parseFloat((a.OffValue/a.totalRetailPrice))*100).toFixed(2));
              } else {
                a.DiscountValue = "00";
              }

              a.opMargin = parseFloat(a.consumerPay)-parseFloat(a.tpaic);
              
              if(a.opMargin){
                a.opMarkup = ((parseFloat(a.opMargin)/parseFloat(a.tpaic))*100).toFixed(2);
              } else {
                a.opMarkup = "00";
              }

              a.opMargin = a.opMargin.toFixed(2);
              a.consumerPay = a.consumerPay.toFixed(2);
              a.OffValue = Math.round(a.OffValue.toFixed(2));
              a.consumerPayValue('margin');
            }
          } else {
            if(a.selectedDiscount == 1){
              a.consumerPay = parseFloat(a.tpaisp);

              a.OffValue = parseFloat(a.totalRetailPrice)-parseFloat(a.consumerPay);

              if(a.consumerPay){
                a.DiscountValue = Math.round(((parseFloat(a.OffValue)/parseFloat(a.totalRetailPrice))*100).toFixed(2));
              } else {
                a.DiscountValue = "00";
              }
              a.opMargin = parseFloat(a.consumerPay)-parseFloat(a.tpaic);
              if(a.opMargin){
                a.opMarkup = ((parseFloat(a.opMargin)/a.tpaic)*100).toFixed(2);
              } else {
                a.opMarkup = "00";
              }

              a.opMargin = a.opMargin.toFixed(2);
              a.consumerPay = a.consumerPay.toFixed(2);
              a.OffValue = Math.round(a.OffValue.toFixed(2));
              a.consumerPayValue('margin');
            } else if(a.selectedDiscount == 2){
              a.consumerPay = parseFloat(a.tpaisp);

              a.OffValue = parseFloat(a.totalRetailPrice)-parseFloat(a.consumerPay);
              
              if(a.tpaisp){
                a.DiscountValue = Math.round((parseFloat((a.OffValue/a.totalRetailPrice))*100).toFixed(2));
              } else {
                a.DiscountValue = "00";
              }

              a.opMargin = parseFloat(a.consumerPay)-parseFloat(a.tpaic);
              
              if(a.opMargin){
                a.opMarkup = ((parseFloat(a.opMargin)/parseFloat(a.tpaic))*100).toFixed(2);
              } else {
                a.opMarkup = "00";
              }

              a.opMargin = a.opMargin.toFixed(2);
              a.consumerPay = a.consumerPay.toFixed(2);
              a.OffValue = Math.round(a.OffValue.toFixed(2));
              a.consumerPayValue('margin');
            }
          }

          a.changeOnDiscountText();
        } else {
          if(a.selectedDiscount == 1){
            if(a.discount != ""){
              a.consumerPay = parseFloat(a.tpaisp)-parseFloat(a.discount);
            } else {
              a.consumerPay = parseFloat(a.tpaisp);
            }

            a.OffValue = parseFloat(a.totalRetailPrice)-parseFloat(a.consumerPay);
            
            if(a.tpaisp){
              a.DiscountValue = Math.round((parseFloat((a.OffValue/a.totalRetailPrice)*100)).toFixed(2));
            } else {
              a.DiscountValue = "00";
            }
            a.opMargin = parseFloat(a.consumerPay)-parseFloat(a.tpaic);
            if(a.opMargin){
              a.opMarkup = ((parseFloat(a.opMargin)/parseFloat(a.tpaic))*100).toFixed(2);
            } else {
              a.opMarkup = "00";
            }

            a.opMargin = a.opMargin.toFixed(2);
            a.consumerPay = a.consumerPay.toFixed(2);
            a.OffValue = Math.round(a.OffValue.toFixed(2));
            a.consumerPayValue('margin');
          } else if(a.selectedDiscount == 2){
            if(a.discount != ""){
              a.consumerPay = parseFloat(a.tpaisp)-((parseFloat(a.discount)*parseFloat(a.tpaisp)/100));
            } else {
              a.consumerPay = parseFloat(a.tpaisp);
            }

            a.OffValue = parseFloat(a.totalRetailPrice)-parseFloat(a.consumerPay);
            
            if(a.tpaisp){
              a.DiscountValue = Math.round((parseFloat((a.OffValue/a.totalRetailPrice))*100).toFixed(2));
            } else {
              a.DiscountValue = "00";
            }
            a.opMargin = parseFloat(a.consumerPay)-parseFloat(a.tpaic);
            if(a.opMargin){
              a.opMarkup = ((parseFloat(a.opMargin)/parseFloat(a.tpaic))*100).toFixed(2);
            } else {
              a.opMarkup = "00";
            }
            
            a.opMargin = a.opMargin.toFixed(2);
            a.consumerPay = a.consumerPay.toFixed(2);
            a.OffValue = Math.round(a.OffValue.toFixed(2));
            a.consumerPayValue('margin');
          }

          a.changeOnDiscountText();
        }
        if(a.opMargin.split('.')[1] == '00'){
          a.opMargin = a.opMargin.split('.')[0];
        }
        if(a.opMarkup.split('.')[1] == '00'){
          a.opMarkup = a.opMarkup.split('.')[0];
        }
      } else {
        a.totalRetailPrice = "00";
        a.tpaisp = "00";
        a.tpc = "00";
        a.tic = "00";
        a.tpsp = "00";
        a.tisp = "00";
        a.tpaic = "00";
        a.consumerPay = "00";
        a.OffValue = "00";
        a.opMargin = "00";
        a.opMarkup = "00";
        a.DiscountValue = "00";
      }

      if((a.consumerPay.split('.')[0]).length > 5){
        if(a.consumerPay.split('.')[1] == '00'){
          a.consumerPay = a.consumerPay.split('.')[0];
        }
        a.largeText = false;
      } else {
        if(a.consumerPay.split('.')[1] == '00'){
          a.consumerPay = a.consumerPay.split('.')[0];
        }
        a.largeText = true;
      }
    };

    a.changeOnDiscountvalue = function(){
      a.checkMyValue();
      // a.consumerPayValue('margin');
    };

    a.changeOnDiscount = function(selectedOption){
      var discountValue = 0;
      //console.log("a.checkStatus :: "+a.checkStatus+" , a.discount :: "+a.discount);
      if(a.checkStatus){
        if(selectedOption == 1){
          if(a.discount != "00" && a.discount > 0 && a.tisp != "00" && a.tisp > 0){
            discountValue = ((parseFloat(a.discount)*parseFloat(a.tisp))/100).toFixed(2);
            a.discount = discountValue;
          } else {
            a.discount = 0;
          }
        } else {
          if(a.discount != "00" && a.discount > 0){
            discountValue = ((a.discount*100)/parseFloat(a.tisp)).toFixed(2);
            a.discount = discountValue;
          }
        }
      } else {
        if(selectedOption == 1 && a.tpaisp != "00" && a.tpaisp > 0){
          if(a.discount != "00" && a.discount > 0 && a.tpaisp != "00" && a.tpaisp > 0){
            discountValue = ((parseFloat(a.discount)*parseFloat(a.tpaisp))/100).toFixed(2);
            a.discount = discountValue;
          }
        } else {
          if(a.discount != "00" && a.discount > 0 && a.tpaisp != "00" && a.tpaisp > 0){
            discountValue = ((a.discount*100)/parseFloat(a.tpaisp)).toFixed(2);
            a.discount = discountValue;
          } else {
            a.discount = 0;
          }
        }
      }

      a.changeOnDiscountText();
    };

    a.changeOnDiscountText = function(){
      if(a.DiscountValue != "00"){
        if(a.selectedDiscount == 1){
          if(a.selectedDiscountText == 1){
            a.DiscountValueText = a.currcy_symbol+a.OffValue+" OFF";
          } else if(a.selectedDiscountText == 2){
            a.DiscountValueText = a.currcy_symbol+a.OffValue+" Discount";
          } else if(a.selectedDiscountText == 3){
            a.DiscountValueText = a.currcy_symbol+a.OffValue+" Savings";
          }
        } else {
          if(a.selectedDiscountText == 1){
            a.DiscountValueText = a.DiscountValue+"% OFF";
          } else if(a.selectedDiscountText == 2){
            a.DiscountValueText = a.DiscountValue+"% Discount";
          } else if(a.selectedDiscountText == 3){
            a.DiscountValueText = a.DiscountValue+"% Savings";
          }
        }
      } else {
        a.DiscountValueText = "$"+a.OffValue+" OFF";
      }
    };

    a.consumerPayValue = function(optionType){
      var alphanumers = /^\d+(\.\d{1,2})?$/;
      if(optionType){
        var currentProfitMargin = ((parseFloat(a.consumerPay)-parseFloat(a.tpaic))/parseFloat(a.consumerPay))*100;
        if(optionType == 'margin'){
          if(a.opMarginProfit !== ""){
            if(!alphanumers.test(a.opMarginProfit)){
              a.opMarginProfit = "";
              $("#error_msg_div").hide();
              $("#show_error_message").slideDown();
              $("#error_msg_div").html("Optional value can have only numbers.");
              $("#error_msg_div").show();
              setTimeout(function(){
                $("#error_msg_div").hide();
              },3000);
              return;
            } else {
              $("#error_msg_div").hide();
              var newSellingPrice = (parseFloat(a.tpaic)*100)/(100-parseFloat(a.opMarginProfit));
              if(newSellingPrice != a.tpaic){
                a.opMarkupProfit = (((parseFloat(newSellingPrice)-parseFloat(a.tpaic))/parseFloat(a.tpaic))*100).toFixed(2);
              } else {
                a.opMarkupProfit = 0;
              }
            }
          } else {
            a.opMarkupProfit = 0;
          }
        } else {
          if(a.opMarkupProfit !== ""){
            if(!alphanumers.test(a.opMarkupProfit)){
              a.opMarkupProfit = "";
              $("#error_msg_div").hide();
              $("#show_error_message").slideDown();
              $("#error_msg_div").html("Optional value can have only numbers.");
              $("#error_msg_div").show();
              setTimeout(function(){
                $("#error_msg_div").hide();
              },3000);
            } else {
              $("#error_msg_div").hide();
              var newSellingPrice = ((parseFloat(a.tpaic)*parseFloat(a.opMarkupProfit))+(100*parseFloat(a.tpaic)))/100;
              if(newSellingPrice != a.tpaic){
                a.opMarginProfit = (((parseFloat(newSellingPrice)-parseFloat(a.tpaic))/parseFloat(newSellingPrice))*100).toFixed(2);
              } else {
                a.opMarginProfit = 0;
              }
            }
          } else {
            a.opMarginProfit = 0;
          }
        }

        if(a.opMarginProfit <= currentProfitMargin && a.opMargin >=0){
          a.margin_markup_above = true;
          a.margin_markup_poor = false;
          a.publishPossible = true;
        } else if(optionType && a.tpaic != "00" && !a.opMarginProfit && a.opMargin >= 0 && a.consumerPay > 0) {
          a.margin_markup_above = true;
          a.margin_markup_poor = false;
          a.publishPossible = true;
        } else if((a.consumerPay > 0 && a.opMarginProfit <= currentProfitMargin) || (a.consumerPay > 0 && (!a.opMarginProfit || a.opMarginProfit == 0))){
          a.margin_markup_above = false;
          a.margin_markup_poor = true;
          a.publishPossible = true;
        }  else {
          a.margin_markup_above = false;
          a.margin_markup_poor = true;
          a.publishPossible = false;
        }
      } else {
        a.margin_markup_above = false;
        a.margin_markup_poor = true;
        a.publishPossible = false;
      }
    };
    
    a.savePosibilityChk = function(){
      if(a.campaign_id && a.offer_description && a.what_you_get){
        a.savePossible = true;
      }else{
        a.savePossible = false;
      }
    }

    
    
    a.publishOffer = function(e, btype){
      a.offerValidate();
      a.savePosibilityChk();
      if((btype == 'save' && !a.savePossible) || a.offerSaving){
        return false;
      }

      if(!a.publishPossible || !a.offerValidated || a.offerPublishing){
        if((a.offer_id && (btype == 'save')) || (!a.offer_id && (btype == 'save'))){
          a.offerPublishing = false;
          a.offerSaving = true;
        } else {
          return false;
        }
      } else if((a.publishPossible || a.offerValidated) && ((a.offer_id && (btype == 'save')) || (!a.offer_id && (btype == 'save')))){
        a.offerPublishing = false;
        a.offerSaving = true;
      } else {
        a.offerSaving = false;
        a.offerPublishing = true;
      }

      var offerDetailsData = {};
      //offer details
      var selectedCatIds = a.getSelectedCatIds();
      var offer_details = {
                            'campaign_id'   : a.campaign_id,
                            'categories'    : selectedCatIds,
                            'start_date'    : a.promotion.c_s_date?a.promotion.c_s_date:'',
                            'end_date'      : a.promotion.c_e_date?a.promotion.c_e_date:'',
                            'validated_hour': a.promotion.start_hour?a.promotion.start_hour:24,
                            'total_redmeem' : a.promotion.total_redeemar?a.promotion.total_redeemar:0,
                            'tags'          : a.promotion.tags?a.promotion.tags:0,
                            'offer_description': a.offer_description?a.offer_description:'',
                            'offer_on_demand': a.promotion.on_demand?1:0,
                            'offer_full_description' : a.what_you_get? a.what_you_get:''
                           };
     
      //Product data
      if(angular.element('.choiceRadio').filter(':checked').length){
      var choiceRadioIdArr = angular.element('.choiceRadio').filter(':checked').attr('id').split('-');     
      var choiceRadioId = choiceRadioIdArr[1];
      if(choiceRadioId){
        angular.forEach(a.inventorychoice,function(c,k){
          a.inventorychoices[k].selectedImage = false;
        });
        angular.forEach(a.choices,function(c,k){
            if(c.choiceid == choiceRadioId){
                a.choices[k].selectedImage = true;
            }else{
                a.choices[k].selectedImage = false;
            }
        });
       }
      }
      var product_data = a.choices;
      
      
      //Inventory Data
      if(angular.element('.inventoryRadio').filter(':checked').length){
      var inventoryRadioIdArr = angular.element('.inventoryRadio').filter(':checked').attr('id').split('-');
      var inventoryRadioId = inventoryRadioIdArr[1];
      
      if(inventoryRadioId){
          angular.forEach(a.choices,function(c,k){
            a.choices[k].selectedImage = false;
          });

          angular.forEach(a.inventorychoices,function(c,k){
              if(c.inventorychoiceid == inventoryRadioId){
                  a.inventorychoices[k].selectedImage = true;
              }else{
                  a.inventorychoices[k].selectedImage = false;
              }
          });
      }
      }
      var inventory_data = a.inventorychoices;
        
      //offer calculation
      var offer_calculation = {
                                'retails_value'         : a.totalRetailPrice?a.totalRetailPrice:0,
                                'is_only_for_inventory' : a.checkStatus?a.checkStatus:0,
                                'discount'              : a.discount?a.discount:0,
                                'value_calculate'       : a.selectedDiscount?a.selectedDiscount:1,
                                'optional_margin_profit_ratio': a.opMarginProfit?a.opMarginProfit:0,
                                'value_text'            : a.selectedDiscountText
                              };
                              
      //Consumer deal
      var consumer_deal = {
                            'pay_value' : a.consumerPay?a.consumerPay:0
                          };
      
      // craete offer object
      offerDetailsData = {
                            'offer_details' : offer_details,
                            'product_data'  : product_data,
                            'inventory_data': inventory_data,
                            'offer_calculation' : offer_calculation,
                            'consumer_deal' : consumer_deal,
                            'offer_id' : a.offer_id||0
                         };
       //console.log(offerDetailsData);
       offerDetailsData.published = 'false';
       if(btype == 'publish'){
        offerDetailsData.published = 'true';
       }
       console.log(offerDetailsData);
              
        x.post('../promotion/createoffer',offerDetailsData).success(function(res){
            a.offerResponse = res;
            
            if(res.status && res.status == 'success'){
                setTimeout(function(){
                    // l.path('dashboard');
                    //  if (a.$root.$$phase != '$apply' && a.$root.$$phase != '$digest') {
                    //         a.$apply();
                    //  }
                    ra.EditOfferData = {};
                    var url = '#/dashboard';
                    window.location = url;
                },2000);
                // if (a.$root.$$phase != '$apply' && a.$root.$$phase != '$digest') {
                //     a.$apply();
                // }
            } else {
                a.offerPublishing = false;
                a.offerSaving  = false;
            }
            
            
        });
      // offerDetailsData.campaign_id = ""
    };
    
    a.offerValidate = function(field){
        var err = false;
        var selectedCatIds = [];
        if(field == undefined){
            field = false;
        }
        angular.element('.err').remove();
       
        if(campaign_id == '' && (!field || field=='campaign_id')){
            angular.element('#campaign_id').parent('.form-group').append('<span class="err red">Select campaign</span>');
            err = true;
        }
        selectedCatIds = a.getSelectedCatIds();
     
        if(!selectedCatIds.length ){
            err = true;
            angular.element('.selectbox').parent('.form-group').append('<span class="err red">Select sub category</span>');
             
        }
        if(!a.offer_description && (!field || field=='offer_description')){
             angular.element('#offer_description').parent('.form-group').append('<span class="err red">Enter offer description</span>');
            err = true;
        }
        if(!a.what_you_get && (!field || field=='what_you_get')){
             angular.element('#what_you_get').parent('.form-group').append('<span class="err red">Enter what you get</span>');
            err = true;
        }
        
        if(err){
          a.savePossible = false;
        }else{
           a.savePossible = true;
        }

        if((!a.promotion.total_redeemar || a.promotion.total_redeemar < 1) && (!field || field=='total_redeemar')){
             angular.element('#total_redeemar').parents('.form-group').append('<span class="err red">Enter a value greater than 0</span>');
            err = true;
        }
        
        
        
        
        if(!err){
            a.offerValidated = true;
            return true;
        }else{
            a.offerValidated = false;
            return false;
        }
        
    };
    
    
    setTimeout(function(){
      a.getUserinfo();
      a.getImagerepo();
      a.savePosibilityChk();
    },2000);

    if(ra.EditOfferData.id != undefined){
      // console.log("ra.EditOfferData :: "+JSON.stringify(ra.EditOfferData, null, 4));
      setTimeout(function(){
        ra.campaignId = a.campaign_id = ra.EditOfferData.campaign_id;
      },200);
      a.promotion.c_s_date = ra.EditOfferData.c_s_date;
      a.promotion.c_e_date = ra.EditOfferData.c_e_date;
      a.promotion.start_hour = ra.EditOfferData.start_hour;
      a.promotion.tags = ra.EditOfferData.more_information;
      a.offer_description = ra.EditOfferData.offer_description;
      a.what_you_get = ra.EditOfferData.what_you_get;
      a.promotion.total_redeemar = ra.EditOfferData.max_redeemar;
      a.hourValidate(Number(ra.EditOfferData.validate_after));
      a.discount = "00";
      a.discount = ra.EditOfferData.discount;
      a.selectedDiscount = Number(ra.EditOfferData.value_calculate);
      a.opMarginProfit = Number(ra.EditOfferData.optional_margin_profit_ratio);
      a.offer_id = ra.EditOfferData.id;
      if(ra.EditOfferData.on_demand){
        a.promotion.on_demand = true;
      } else {
        a.promotion.on_demand = false;
      }
      a.selectedDiscountText = ra.EditOfferData.value_text;
      console.log(ra.EditOfferData);
      if(!ra.EditOfferData.published && ra.EditOfferData.published != 'false' ){
        a.offerSelectedImagePath = "";
        if(ra.EditOfferData.offer_medium_image_path != null) {
          a.offerSelectedImagePath = a.root_base_url+ra.EditOfferData.offer_large_image_path;
        }
        a.published = true;
      } else {
        a.published = false;
      }

      if(Number(ra.EditOfferData.is_only_on_inventory)){
          a.checkStatus = true;
          a.changeOnDiscountvalue();
      }

      if(ra.EditOfferData.productDetails.length){
        a.productAvailable = true;

        if(a.published){
          a.disableIt = true;
        } else {
          a.disableIt = false;
        }

        a.choices = [];
        var choiceid = 1;
       
        a.selectedImageOfferIndex = 1;
        angular.forEach(ra.EditOfferData.productDetails, function(productDetails){
          var productData = [{
              "id": productDetails.product_id,
              "product_name": productDetails.product_name,
              "product_image": "../../"+productDetails.image_path,
              "sell_price": productDetails.sell_price,
              "cost": productDetails.cost,
              "retail_price": productDetails.retail_price,
              "is_file_exist": true
          }];
          
          if(productDetails.selectedImage === true || productDetails.selectedImage === 'true'){
            a.offerSelectedImagePath = a.root_base_url + productDetails.image_path;
          }

          var total_cost = Math.round(parseFloat(productDetails.quantity)*parseFloat(productData[0].cost));
          var total_selling_price = Math.round(parseFloat(productDetails.quantity)*parseFloat(productData[0].sell_price));
          var total_retail_price = Math.round(parseFloat(productDetails.quantity)*parseFloat(productData[0].retail_price));
          
          var pdata = {
            'choiceid': choiceid,
            'productData':productData,
            'noofproduct':Number(productDetails.quantity),
            'total_cost':total_cost,
            'total_selling_price':total_selling_price,
            'total_retail_price':total_retail_price,
            'selectedImage':productDetails.selectedImage?true:false
          };

          a.choices.push(pdata);
          a.setMarkupAndMargin();
          choiceid++;
          
        });
        
        setTimeout(function(){
            angular.element('.choiceRadio:eq(0)').trigger('change');
        },2000);
      }

      if(ra.EditOfferData.inventoryDetails.length){
        a.productAvailable = true;
        a.inventorychoices = [];
        var inventorychoiceid = 1;
        angular.forEach(ra.EditOfferData.inventoryDetails, function(inventoryDetails){
          var inventoryData = [{
              "id": inventoryDetails.inventory_id,
              "inventory_name": inventoryDetails.inventory_name,
              "inventory_image": "../../"+inventoryDetails.image_path,
              "sell_price": inventoryDetails.sell_price,
              "cost": inventoryDetails.cost,
              "retail_price": inventoryDetails.retail_price,
              "is_file_exist": true
          }];
          var total_cost = Math.round(parseFloat(inventoryDetails.quantity)*parseFloat(inventoryData[0].cost));
          var total_selling_price = Math.round(parseFloat(inventoryDetails.quantity)*parseFloat(inventoryData[0].sell_price));
          var total_retail_price = Math.round(parseFloat(inventoryDetails.quantity)*parseFloat(inventoryData[0].retail_price));
          
          
          if(inventoryDetails.selectedImage === true || inventoryDetails.selectedImage === 'true'){
            console.log(inventoryDetails.image_path);
            a.offerSelectedImagePath = a.root_base_url + inventoryDetails.image_path;
          }
          
          var idata = {
            'inventorychoiceid': inventorychoiceid,
            'InventoryData':inventoryData,
            'noofinventory':Number(inventoryDetails.quantity),
            'total_cost':total_cost,
            'total_selling_price':total_selling_price,
            'total_retail_price':total_retail_price,
            'selectedImage':inventoryDetails.selectedImage?true:false
          };
          a.inventorychoices.push(idata);
          a.setInventoryMarkupAndMargin();
          inventorychoiceid++;
        });

        setTimeout(function(){
            angular.element('.inventoryRadio:eq(0)').trigger('change');
        },2500);
      }
      
      setTimeout(function(){
        x.post("../promotion/offercategories",{offer_id : a.offer_id}).success(function(res){
          
          angular.forEach(a.categorryList, function(catData){
            
            angular.forEach(catData.children, function(catDatachild){
              
              angular.forEach(res, function(resData){
                
                if(catDatachild.id == resData.cat_id){
                  catDatachild.checkedMe = true;
                }
              
              });
            
            });
          
          });

          a.categorryList = a.categorryList;
          setTimeout(function(){
            a.getSelected();
          },1000);
        });
        a.changeOnDiscountvalue();
      }, 300);
    };



    x.get('../promotion/categories').success(function(response){
      if(response.length){
        angular.forEach(response, function(res){
          res.children.checkedMe = false;
        });
        a.categorryList = response;
      } else {
        //a.logout();
      }
    });

    //Get all Campaign of this user
    getCampaignList();

    a.modifyInplace = function(discountValue){
      a.editDiscountValue   = true;
      a.editedDiscountValue = discountValue;
      setTimeout(function(){
        $("#editDiscountValue").focus();
      },100);
    };

    a.saveEditedData = function(editedDiscountValue){
      var alphanumers = /^\d+(\.\d{1,2})?$/;
      if(!alphanumers.test(editedDiscountValue)){
        $("#error_discount_div").hide();
        $("#show_discount_message").slideDown();
        $("#error_discount_div").html("Discount value can have only numbers.");
        $("#error_discount_div").show();
        setTimeout(function(){
          $("#error_discount_div").hide();
        },3000);
      } else {
          a.editDiscountValue  = false;
          $("#error_discount_div").hide();
          var currentRetailPrice = a.totalRetailPrice;
          var currentSellPrice = parseFloat(a.totalRetailPrice)-(parseFloat(parseFloat(a.totalRetailPrice)*parseFloat(editedDiscountValue))/100);
          var oldSellPrice = parseFloat(a.tpaisellingprice);
          if(currentSellPrice <= oldSellPrice){
            var currentDiscount = oldSellPrice - currentSellPrice;
            if(a.selectedDiscount == 1){
              a.discount = currentDiscount.toFixed(2);
              a.changeOnDiscountvalue();
            } else {
              var discountValue = ((currentDiscount*100)/parseFloat(a.tpaisellingprice)).toFixed(2);
              a.discount = discountValue;
              a.changeOnDiscountvalue();
            }
          } else if(!oldSellPrice) {
            $("#error_discount_div").hide();
            $("#show_discount_message").slideDown();
            $("#error_discount_div").html("Please add one product or inventory item first.");
            $("#error_discount_div").show();
            setTimeout(function(){
              $("#error_discount_div").hide();
            },3000);
          } else {
            $("#error_discount_div").hide();
            $("#show_discount_message").slideDown();
            $("#error_discount_div").html("Discount percentage can not lower than original discount value");
            $("#error_discount_div").show();
            setTimeout(function(){
              $("#error_discount_div").hide();
            },3000);
          }
      }
    };
}]);