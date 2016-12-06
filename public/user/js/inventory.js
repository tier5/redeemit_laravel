
"use strict";

var MyApp = angular.module("inventory-app", ["ngFileUpload","angularUtils.directives.dirPagination"]);

MyApp.controller('InventoryController',["$scope", "$rootScope", "PlaceholderTextService", "ngTableParams", "$filter", "$http", "fileToUpload", "$route",function (a, rs, b, c, d, x, fu, r) {          
    
    a.dataLength                        = { filtered: [] };
    a.cnames                            = [];
    a.inventory_details                 = [];
    a.inventory_details.inventory_image = [];
    a.addinventorybtn                   = "Save";
    a.updateInventory_disabled          = false;
    a.showOrHideMe                      = false;
    a.Inventory                         = {};
    a.showInventoryPopup                = false;
    a.edit_inventory_details            = [];
    a.popupShowing                  = false;
    a.repoDirTree                   = {};
    a.repos                         = {};
    a.selectedRepo                  = {};
    a.lftPnlSelectedDir             = 0;
    a.selectedRepoWhenAdding        = {};
    a.dtext                         = "Done";
    rs.editId                       = "";
    
    var isRunningAjax = false;
    var site_path                       = $("#site_path").val();
    
    x.post("../inventory/list", update_id).success(function(data_response){              
        a.inventory_details = data_response; 
        a.file_path=site_path;  
    });         
    
    a.addInventory = function(){
      if(!a.Inventory.inventory_name || !a.Inventory.sell_price || !a.Inventory.cost || !a.Inventory.retail_price
         ) {
          $("#error_div").hide();
          $("#show_message").slideDown();
          $("#error_div").html("Please fill out all fields");
          $("#error_div").show();
          $("#success_div").hide();

          $('#add_inventory').prop('disabled', false);
          $("#add_inventory").text('Save');
          return false;
      } else {
    
          if(!a.Inventory.sku_number){
            a.Inventory.sku_number = "";
          }

          var file = {            
            'inventory_data' : a.Inventory,
            'image_id' : $("#image_id").val()
          };
          if(!isRunningAjax){
            x.post("../inventory/store",file).success(function(response){
              isRunningAjax = false;
              r.reload();
            });
          }
      }
    };

    // Function for deleting a Inventory
    a.delete_inventory=function(itemId){ 
    
      if(confirm("Are you sure?")) {    
        x.get("../inventory/delete/"+itemId).success(function(response){
          r.reload();
        });
      }
    };

    a.redirect_edit=function(itemId){
      // sessionStorage.setItem('editId', itemId);
      rs.editId = itemId;
      if(a.showInventoryPopup){
        a.showInventoryPopup = false;
      } else {
        a.showInventoryPopup = true;
        if(itemId){
          // var update_id = JSON.parse(sessionStorage.getItem('editId'));
          x.post("../inventory/list",itemId).success(function(data_response){
              angular.forEach(data_response, function(data){
                data.cost = parseInt(data.cost);
                data.sell_price = parseInt(data.sell_price);
                data.retail_price = parseInt(data.retail_price);
              });
              a.edit_inventory_details = data_response[0];
          });
        }
      }
    }

    a.showOrHideAddInventoryDiv = function(){
      if(a.showOrHideMe){
        a.showOrHideMe = false;
      } else {
        a.showOrHideMe = true;
      }
    }
    
   

    a.updateInventory=function(){
      // var update_id = JSON.parse(sessionStorage.getItem('editId'));
      
      if(  a.edit_inventory_details.inventory_name !== ''
         && a.edit_inventory_details.sell_price !== ''
         && a.edit_inventory_details.cost !== ''
         && a.edit_inventory_details.retail_price !== ''
         ) {
                
        a.edit_inventory = [{
  
          'inventory_name': a.edit_inventory_details.inventory_name,
          'sell_price'    : a.edit_inventory_details.sell_price,
          'cost'          : a.edit_inventory_details.cost,
          'retail_price'  : a.edit_inventory_details.retail_price,
          'sku_number'    : a.edit_inventory_details.sku_number,
          'id'            : rs.editId
  
        }];
        
        if(!isRunningAjax){
          isRunningAjax = true;
          x.post("../inventory/editinventory",a.edit_inventory).success(function(response){
            isRunningAjax = false;
            if(response=='success') {
              //return false;
              $("#popup_error_div").hide();
              $("#popup_show_message").slideDown();
              $("#popup_success_div").html("Updated successfully.");
              $("#popup_success_div").show(); 
    
              rs.editId = "";
              
              setTimeout(function(){
                r.reload();
              },1000);
            } else {
              $("#popup_error_div").hide();
              $("#popup_show_message").slideDown();
              $("#popup_error_div").html("Please fill out all fields");
              $("#popup_error_div").show();
              $("#popup_success_div").hide();
            }
          });
        }
      }else{
          $("#popup_error_div").hide();
          $("#popup_show_message").slideDown();
          $("#popup_error_div").html("Please fill out all fields");
          $("#popup_error_div").show();
          $("#popup_success_div").hide();
          return false;
      }
    }

    a.showRepoModal = function(inventoryid){
      a.popupShowing = true;
      if(inventoryid){
        a.SelectedEditImagePid = inventoryid;
        a.getRepoImagesbyDirId(0);
        a.setleftOpenDir(0)
      } else {
        a.SelectedEditImagePid = 0;
      }
    };

    a.hidePopup = function(){
      a.dtext = "Done";
      a.popupShowing = false;
    };
    
    a.getRepoImagesbyDirId = function(dir_id) {
       // console.log(dir_id);
      var url = "../directory/repos/"+dir_id;

      x.get(url).success(function(response){
         a.repos = response;
      });
    };
    
    a.doSelectedRepo = function(file){
        a.selectedRepo = file;
        if(!a.SelectedEditImagePid){
          a.selectedRepoWhenAdding = file;
        }
    };

    
    a.getImagerepo = function(){
        x.get("../directory/dirtree").success(function(data){
            a.repoDirTree = data;
        });
    };
    
    a.editImagePopup = function(){
      // console.log("in editImagePopup with pid :: "+a.SelectedEditImagePid);
      a.dtext = "Updating..";
      var image_id=$("#image_id").val();
      var editInventoryData = {
        inventory_id: a.SelectedEditImagePid,
        selected_image: a.selectedRepo.directory_base_path
      };

      x.post("../inventory/updateinventoryimage",editInventoryData).success(function(res){
        console.log("res :: "+JSON.stringify(res, null, 4));
        if(res == "success"){
          r.reload();
        } else {
            $("#success_div").hide();
            $("#error_div").hide();
            $("#show_message").slideDown();
            $("#error_div").html("Product cannot modify! Please login again and try again!");
            $("#error_div").show();
            setTimeout(function(){
              $("#error_div").hide();
            },3000);
        }
      });

      a.popupShowing = false;
      a.dtext = "Done";
    };

    a.setleftOpenDir = function(dirId){
        a.lftPnlSelectedDir = dirId;
    };

    a.getImagerepo();

    a.checkMe = function(Inventory_name){
      var alphanumers = /^[a-zA-Z0-9]+$/;
      if(!alphanumers.test(Inventory_name)){
            $("#success_div").hide();
            $("#error_div").hide();
            $("#show_message").slideDown();
            $("#error_div").html("Inventory name can have only alphabets and numbers.");
            $("#error_div").show();
            setTimeout(function(){
              $("#error_div").hide();
            },3000);
      } else {
          $("#error_div").hide();
      }
    };
   
}]);