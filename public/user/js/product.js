
"use strict";

var MyApp = angular.module("product-app", ["ngFileUpload","angularUtils.directives.dirPagination"]);

MyApp.controller('ProductController',["$scope", "$rootScope", "PlaceholderTextService", "ngTableParams", "$filter", "$http", "fileToUpload", "$route",function (a, rs, b, c, d, x, fu, r) {          
    
    var site_path                   = $("#site_path").val();
    a.file_path                     = site_path;

    a.dataLength                    = {filtered:[]};
    a.cnames                        = [];
    a.product_details               = [];
    a.product_details.product_image = [];

    a.addproductbtn                 = "Save";
    a.updateProduct_disabled        = false;

    a.showPreview                   = false;
    a.cropMe                        = false;

    a.cropper                       = {};
    a.cropper.sourceImage           = null;
    a.cropper.croppedImage          = null;

    a.showOrHideMe                  = false;
    a.Product                       = {};
    a.ShowaddgroupDiv               = false;
    a.ShowaddgroupDivOnEdit         = false;

    a.showProductEditPopup          = false;
    a.popupShowing                  = false;
    a.repoDirTree                   = {};
    a.repos                         = {};
    a.selectedRepo                  = {};
    a.selectedRepoWhenAdding        = {};
    a.lftPnlSelectedDir             = 0;
    a.SelectedEditImagePid          = 0;
    a.dtext                         = "Done";
    rs.editProductId                = "";
    a.showMessages                  = false;


    rs.scopePageTitle       = 'Product';
    rs.pageTitle            = '';
    var isRunningAjax               = false;
    x.post("../group/grouplist").success(function(data){              
        a.product_group = data;
    });

    //Convert image into base64
    $('#product_image').on('change', function(e){
      $(this).base64img({
        url: e.target.files[0],
        result: '#product_image_encode'
      });
    });

    a.filter_table=function(groupId){ 
    
      x.post("../product/filterlist", groupId).success(function(data_response){              
        a.product_details = data_response; 
        a.file_path = site_path;  
      }); 
    }
    x.post("../product/list", update_id).success(function(data_response){              
        a.product_details = data_response; 
        a.file_path = site_path;
       // alert(a.get_group) ;

    });    
    //  console.log($('#main_site_url').val()) ;

    $("#file").on('change',function(){
      a.showPreview = true;
    });

    a.enable_cropping = function(){
      a.cropMe = true;
    };

    a.cancel_cropping = function(){
      a.cropMe = false;
    };     

    a.addProduct = function(){ 


      if($("#Product_name").val()=='' || $("#sell_price").val()=='' || $("#cost").val()=='' || $("#retail_price").val()=='') {
          $("#error_div").hide();
          $("#show_message").slideDown();
          $("#error_div").html("Please fill out all fields");
          $("#error_div").show();
          $("#success_div").hide();

          $('#add_product').prop('disabled', false);
          $("#add_product").text('Save');
          return false;
      } else {        
        var product_group=$("#product_group").val();
        var image_id=$("#image_id").val();
        
        a.product_details= [{
          'product_name':a.Product.Product_name,
          'sell_price':a.Product.sell_price,
          'cost':a.Product.cost,
          'retail_price':a.Product.retail_price,
          'product_group':product_group,
          'image_id':image_id
        }];

      // alert(JSON.stringify(a.product_details,null,4));
      // return false;   
        if(!isRunningAjax){
          isRunningAjax = true;
          x.post("../product/store",a.product_details).success(function(response){         
             var url = 'dashboard#/product/list';
             isRunningAjax = false;
             switch(response) {
              case 'success':
                      a.Product = {};
                      $("#error_div").hide();
                      $("#show_message").slideDown();
                      $("#success_div").html("Data inserted successfully. <br />Please wait,we will reload this page.");
                      $("#success_div").show();
                      
                      
                      r.reload();
                      break;
                  case 'image_not':
                      a.Product.product_image = "";
                      $("#error_div").hide();
                      $("#show_message").slideDown();
                      $("#error_div").html("Unable to upload image. Please try again.");
                      $("#error_div").show();
                      $("#success_div").hide();
                      return false;  
                      break;
                  default:
                      $("#error_div").hide();
                      $("#show_message").slideDown();
                      $("#error_div").html("Please insert all field.");
                      $("#error_div").show();
                      $("#success_div").hide();
                      break;
             }
             //break;
          });
        }
      }
    };

    // Function for deleting a Product
    a.delete_product=function(itemId){ 
    
      if(confirm("Are you sure?")) {    
        x.get("../product/delete/"+itemId).success(function(response){
          r.reload();
        });
      }
    };

    a.redirect_edit=function(itemId){
      // sessionStorage.setItem('editId', itemId);
      rs.editProductId = itemId;
      
      if(a.showProductEditPopup){
        a.showProductEditPopup = false;
      } else {
        a.showProductEditPopup = true;
        setTimeout(function(){
          if(rs.editProductId){
            var update_id = rs.editProductId;
            x.post("../product/list",update_id).success(function(data_response){            
                angular.forEach(data_response, function(data){
                  data.cost = parseInt(data.cost);
                  data.sell_price = parseInt(data.sell_price);
                  data.retail_price = parseInt(data.retail_price);
                });
                a.edit_product_details = data_response[0];
                a.select_group = data_response[0].product_group;
                //alert(JSON.stringify(data_response[0].product_group,null,4));
            });
          } else {
            rs.editProductId = "";
                  
            r.reload();
          }
        },100);
      }
    };

    a.setleftOpenDir = function(dirId){
        a.lftPnlSelectedDir = dirId;
    };


    a.updateProduct=function(){
      if(rs.editProductId){
        var update_id = rs.editProductId;
        a.edit_product= [{
          'product_name':a.edit_product_details.product_name,
          'sell_price':a.edit_product_details.sell_price,
          'cost':a.edit_product_details.cost,
          'retail_price':a.edit_product_details.retail_price,
          'id':update_id
        }];
      
        if(!isRunningAjax){
          isRunningAjax = true;
          x.post("../product/editproduct",a.edit_product).success(function(response){
            isRunningAjax = false;
            if(response=='success') {
              // alert("Hi Shan!");
              //return false;
              $("#popup_error_div").hide();
              $("#popup_show_message").slideDown();
              $("#popup_success_div").html("Updated successfully.");
              $("#popup_success_div").show(); 
  
              rs.editProductId = "";
              setTimeout(function(){
                r.reload();
              },100);
            } else {
              $("#popup_show_message").slideDown();
              $("#popup_error_div").html("Please fill out all fields");
              $("#popup_error_div").show();
              $("#popup_success_div").hide(); 
            }
            
          });
        }
      } else {
          rs.editProductId = "";
                  
          setTimeout(function(){
            r.reload();
          },100);
      }
    }

    a.showOrHideAddProductDiv = function() {
      if(a.showOrHideMe){
        a.showOrHideMe = false;
      } else {
        a.showOrHideMe = true;
        setTimeout(function(){
          $("#hide").click(function(){
            $(".my").hide();
          });
          $("#show").click(function(){
              $(".my").show();
          });
        },100);
      }
    };

    a.showOrHideAddProductDivOnCancel = function() {
      if(a.ShowaddgroupDiv){
        a.ShowaddgroupDiv = false;
      } else {
        a.ShowaddgroupDiv = true;
      }
    };

    a.showOrHideAddProductDivOnEdit = function() {
      if(a.ShowaddgroupDivOnEdit){
        a.ShowaddgroupDivOnEdit = false;
      } else {
        a.ShowaddgroupDivOnEdit = true;
      }
    };

    a.saveNewGroupName = function(t){
      if(t == 'edit'){
        var group_name = $('#newEditGroupName').val();
      } else if(t == 'add') {
        var group_name = $('#newGroupName').val();
      }
      var formData = new FormData();
      formData.append('group_name', group_name);
      $.ajax({
        url: "../group/addgroupajax", // Url to which the request is send
        type: "POST",             // Type of request to be send, called as method
        data:  formData, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
        contentType: false,       // The content type used when sending data to the server.
        cache: false,             // To unable request pages to be cached
        processData:false,        // To send DOMDocument or non processed data file it is set to false
        success: function(data)   // A function to be called if request succeeds
        { 
          if(data.message=="error")
          {
            $("#success_div").hide();
            $("#error_div").hide();
            $("#show_message").slideDown();
            $("#error_div").html(data.message_details);
            $("#error_div").show();
            setTimeout(function(){
              $("#error_div").hide();
            },3000);
          }
          else
          {
            $("#error_div").hide();
            $("#success_div").show();
            $("#show_message").slideDown();
            $("#success_div").html("Group added successfully!");
            $(".my").hide();
            $(".egroup").hide();
            setTimeout(function(){
              $("#success_div").hide();
            },3000);
            x.post("../group/grouplist").success(function(data){              
                a.product_group = data;
            });
          }
        }
      });
     
    };

    a.makeDefaultGroup = function(pid){
      var pdata = [{
        'product_id': pid
      }];
      x.post("../product/ungroup",pdata).success(function(data){
        if(data == 'success'){
            $("#error_div").hide();
            $("#success_div").show();
            $("#show_message").slideDown();
            $("#success_div").html("Product shifted to Default Group successfully!");
            setTimeout(function(){
              $("#success_div").hide();
              r.reload();
            },2000);
        } else {
            $("#success_div").hide();
            $("#error_div").hide();
            $("#show_message").slideDown();
            $("#error_div").html("Product cannot be ungrouped! Please login again and try again!");
            $("#error_div").show();
            setTimeout(function(){
              $("#error_div").hide();
            },3000);
        }
      });
    };


    a.showRepoModal = function(pid){
      a.popupShowing = true;
      a.showMessages = false;
      if(pid){
        a.SelectedEditImagePid = pid;
        a.getRepoImagesbyDirId(0);
        a.setleftOpenDir(0);
      } else {
        a.SelectedEditImagePid = 0;
      }
    };

    a.hidePopup = function(){
      console.log("in hidePopup");
      a.dtext = "Done";
      a.popupShowing = false;
    };

    a.editImagePopup = function(){
      a.dtext = "Updating..";
      var image_id=$("#image_id").val();
      var editProductData = {
        product_id: a.SelectedEditImagePid,
        selected_image: a.selectedRepo.directory_base_path
      };

      x.post("../product/updateproductimage",editProductData).success(function(res){
        console.log("res :: "+JSON.stringify(res, null, 4));
        if(res == "success"){
          r.reload();
        } else {
            a.showMessages = true;
            $("#success_div").hide();
            $("#error_div").hide();
            $("#show_message").slideDown();
            $("#error_div").html("Product cannot modify! Please login again and try again!");
            $("#error_div").show();
            setTimeout(function(){
              a.showMessages = false;
              $("#error_div").hide();
            },3000);
        }
      });

      a.popupShowing = false;
      a.dtext = "Done";
    };
   
   //$(document).on('click','.grp',function(e){
   //     
   //     var grp = $(this).attr('data-group');
   //     var grpArr = grp.split('-');
   //     
   //     //a.filter_table(grpArr[1]);
   //     $('.grp').removeClass('active');
   //     $('.' + grp).addClass('active');
   // });
   // setTimeout(function(){
   //     $('.grp:first').trigger('click');
   // },700);
    
    a.setGroup = function(groupId){
        a.groupId = groupId;
        a.filter_table(groupId);
    };


    a.arrangeGroupSlider = function(){
    setTimeout(function(){
        x.post("../group/grouplist").success(function(data){
            a.product_group = data;
            a.grouplen = data.length;        
        });//ajax

        //a.$apply();
    },500);
    };
    
    
    
    
    a.getRepoImagesbyDirId = function(dir_id) {
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
    
    a.getImagerepo();
    
    a.arrangeGroupSlider();

    a.checkMe = function(Product_name){
      var alphanumers = /^[a-zA-Z0-9]+$/;
      if(!alphanumers.test(Product_name)){
            $("#success_div").hide();
            $("#error_div").hide();
            $("#show_message").slideDown();
            $("#error_div").html("Product name can have only alphabets and numbers.");
            $("#error_div").show();
            setTimeout(function(){
              $("#error_div").hide();
            },3000);
      } else {
          $("#error_div").hide();
      }
    };
}]);