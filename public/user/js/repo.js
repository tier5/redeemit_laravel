
"use strict";

var MyApp = angular.module("repo-app", ["ngFileUpload","angularUtils.directives.dirPagination","ngDragDrop"]);

MyApp.controller('RepoListController',["$scope", "$rootScope", "PlaceholderTextService", "ngTableParams", "$filter", "$http", "fileToUpload", "$route", function (a, rs, b, c, d, x, fu, $route) {          
    
    a.upload_repo_img_disabled  = false;
    a.upload_repo_img_btn       = "Upload";
    a.currentPage               = 0;
    a.pageSize                  = 1;
    a.showAddFolder             = false;
    a.folder_name               = "";
    a.directoryIndex            = 1;
    var site_path               = $("#site_path").val();
    a.activeDirMenu             = 0;
    a.copyToDir                 ="";
    a.file_path                 = site_path;
   
    a.showAllimages = function() {
      a.directoryIndex = 0;
      showAllimagesWithOrWithourDir();

    };

    a.showStoreimages = function(){
      a.directoryIndex = 1;
      showAllimagesWithOrWithourDir(a.directoryIndex);
    };

    a.get_image_by_dir_id = function(dir_id){
      a.directoryIndex = 0;
      showAllimagesWithOrWithourDir(dir_id);
    };

    a.setActiveClass = function(id){
        a.activeDirMenu = id;
        if(!id){
          a.activeDirMenu = 0;
        }
    };
    
    
    function showAllimagesWithOrWithourDir(dir) {
      if(dir == undefined || dir == 'undefined'){
        a.folder_name = "All Files";
        var url = "../directory/alllisting";
        // sessionStorage.setItem('selectedDirId', '0');
        rs.selectedDirId = '0';
      } else if(dir == 1) {
        a.folder_name = "Store Image";
        var url = "../directory/allstoreimages";
        // sessionStorage.setItem('selectedDirId', dir.id);
        rs.selectedDirId = '1';
      } else {
        a.folder_name = dir.original_name;
        var url = "../directory/alllisting/"+dir.id;
        // sessionStorage.setItem('selectedDirId', dir.id);
        rs.selectedDirId = dir.id;
      }

      x.get(url).success(function(response){
        a.repo_details = [];
        if(response.length){
          if(response[0].id != undefined){
            if(rs.selectedDirId == '1'){
              if(response[0].store_front_image_path != "" && response[0].brand_image_path != ""){
                var imageList = [
                {
                  'id' : 1,
                  'image_name': response[0].store_front_image,
                  'image_path': '../../'+response[0].store_front_image_path,
                  "img_dimension": "350x250"
                },
                {
                  'id' : 2,
                  'image_name': response[0].brand_image,
                  'image_path': '../../'+response[0].brand_image_path,
                  "img_dimension": "200x200"
                }];
              } else if(response[0].store_front_image_path != "" && response[0].brand_image_path == ""){
                var imageList = [
                {
                  'id' : 1,
                  'image_name': response[0].store_front_image,
                  'image_path': '../../'+response[0].store_front_image_path,
                  "img_dimension": "350x250"
                }];
              } else if(response[0].store_front_image_path == "" && response[0].brand_image_path != ""){
                var imageList = [{
                  'id' : 1,
                  'image_name': response[0].brand_image,
                  'image_path': '../../'+response[0].brand_image_path,
                  "img_dimension": "200x200"
                }];
              } else {
                var imageList = [];
              }
              a.repo_details = imageList;
            } else {
              a.repo_details = response;
            }
          } else {
            a.repo_details = [];
          }
        } else {
          a.repo_details = [];
        }
      });
    }

    function getAllDirectoryListOnly(){
      var url = "../directory/onlydirectorylist";
      a.setActiveClass();
      x.get(url).success(function(response){
        if(response.length){
          if(response[0].id != undefined){
            a.directory_details = response;
          } else {
            a.directory_details = [];
          }
        } else {
          a.directory_details = [];
        }
      });
    }

    getAllDirectoryListOnly();

    showAllimagesWithOrWithourDir();

    a.showAddFolderBox = function(){
      a.showAddFolder = true;
      a.directoryIndex = 0;
      setTimeout(function(){
        $("#addFolder").focus();
      },10);
    };

    a.blurUpdate = function(){
      var folder_name = $("#addFolder").val();
      if( folder_name != ""){
        var data = {
          "dir_name" : folder_name.trim(),
          "new_dir_id": 0
        };
        x.post("../directory/store",data).success(function(response){
          
          if(response=="success") {  
              a.showAddFolder = false;             
              getAllDirectoryListOnly();

              $("#error_div").hide();
              $("#show_message").slideDown();
              $("#success_div").html("Folder created successfully!");
              $("#success_div").show();
              setTimeout(function(){
                $("#success_div").hide();
              },5000);
                                                
          } else if(response=="error") {
              $("#error_div").hide();
              $("#show_message").slideDown();
              $("#error_div").html("Please insert all field.");
              $("#error_div").show();
              $("#success_div").hide(); 
              setTimeout(function(){
                $("#error_div").hide();
              },5000);                                 
          } else if(response=="folder_exists") {
              $("#error_div").hide();
              $("#show_message").slideDown();
              $("#error_div").html("Folder you enter already exists. Please try with diffrent name.");
              $("#error_div").show();
              $("#success_div").hide();
              setTimeout(function(){
                $("#error_div").hide();
              },5000);                                    
          }
        });
      }
    };

    a.showorhideme = function (index) {
      if (a.directoryIndex == index) {
        a.directoryIndex = 0;
      } else {
        a.directoryIndex = index;
      }
    };


    a.delete_folder = function(itemId){      
      if(confirm("Are you sure?"))
      { 
        x.get("../directory/delete/"+itemId).success(function(response){

          if(response=='success')
          {
            $route.reload();
          }
        })
      }
    };
    
    
    a.deleteRepoImg = function(repoId){
         if(confirm("Are you sure?")){
             x.get('../directory/deleterepo/' + repoId).success(function(response){
            if(response=='success')
              {
               showAllimagesWithOrWithourDir();
              }
            }); 
         }
    };

    a.openRename = function(index){
      a.directoryIndex = 0;
      if (a.renameOpenIndex == index) {
        a.renameOpenIndex = 0;
      } else {
        a.renameOpenIndex = index;
      }
    }

    
    a.renameDirName = function(id,newName){
       var dir = {'id':id};
        if(!id || !newName){
            return 0;
        }
        var newName = newName.trim();
        
        a.openRename(0);
        x.post('../directory/renamedir',{'id':id,'new_name':newName}).success(function(oldname){
            if(a.folder_name == oldname)  {
                a.folder_name = newName;
            }
            $route.reload();
        });
      
    }
    
    
    //drag & drop
    a.onOver = function(e) {
      angular.element(e.target).addClass("hover");
      a.copyToDir = angular.element(e.target).find('.dirnameSpan').html();
     
      if(a.copyToDir == undefined){
        a.copyToDir = 'All Files';
      }
      a.$apply();
      
    };

    a.onOut = function(e) {
      angular.element(e.target).removeClass("hover");
      a.copyToDir = '';
      a.$apply();
    };

    a.onDrop = function(e,u) {
        
      angular.element(e.target).removeClass("hover").addClass("done");
      var dirid = angular.element(e.target).attr('data-dirid');
      
      var repoid = angular.element(u.draggable).attr('data-repoid');
      a.copyToDir = '';
      
      if(repoid){
        x.post('../directory/changeaddress',{'dirid':dirid,'repoid':repoid}).success(function(response){
          if(response == 'success'){
            angular.element(u.draggable).remove();
            $route.reload();
          }else{
             angular.element(u.draggable).css('left','0').css('top','0');
          }
        });
      }
    };
    
    a.onStart = function(e,data){
      // console.log(e);
      // e.mydata = 'sample';
    }
   
}]);

MyApp.filter('startFrom', function() {
    return function(input, start) {
        start = +start; //parse to int
        return input.slice(start);
    }
});

// MyApp.controller('RepoAddFolderController',["$scope", "PlaceholderTextService", "ngTableParams", "$filter", "$http", "fileToUpload", "$route", function (a, b, c, d, x, fu, $route) {          
    
//     a.upload_repo_img_disabled = false;
//     a.upload_repo_img_btn = "Upload File";

//     if(sessionStorage.getItem('pdid')) {
//       a.new_dir_id = sessionStorage.getItem('pdid');
//     } else {
//       a.new_dir_id = 0;
//     }


//     a.add_folder = function(){
//         if(sessionStorage.getItem('pdid')) {
//           a.repodetails.new_dir_id = sessionStorage.getItem('pdid');
//         } else {
//           a.repodetails.new_dir_id = 0;
//         }
        
//         // console.log("a.repodetails :: "+JSON.stringify(a.repodetails, null, 4));
//         x.post("../directory/store",a.repodetails).success(function(response){
          
//           if(response=="success") {               
//               var redirect_url='dashboard#/repository/list';

//               $("#error_div").hide();
//               $("#show_message").slideDown();
//               $("#success_div").html("Data inserted successfully. <br />Please wait,we will redirect you to listing page.");
//               $("#success_div").show();              

              
//               window.location.href = redirect_url;
                                                
//           } else if(response=="error") {
//               $("#error_div").hide();
//               $("#show_message").slideDown();
//               $("#error_div").html("Please insert all field.");
//               $("#error_div").show();
//               $("#success_div").hide();                                  
//           } else if(response=="folder_exists") {
//               $("#error_div").hide();
//               $("#show_message").slideDown();
//               $("#error_div").html("Folder you enter already exists. Please try with diffrent name.");
//               $("#error_div").show();
//               $("#success_div").hide();                                  
//           }
//         });
//     };
   
// }]);

MyApp.controller('RepoAddImageController',["$scope", "$rootScope", "PlaceholderTextService", "ngTableParams", "$filter", "$http", "fileToUpload", "$route", function (a, rs, b, c, d, x, fu, $route) {          
    
    a.upload_repo_img_btn   = "Upload File";
    a.cnames                = [];
    a.image_name            = "";
    a.smallImageCropDiv     = true;
    a.mideumImageCropDiv    = false;
    a.largeImageCropDiv     = false;
    a.data_url_set          = "";
    a.layersDetailedList    = [];
    a.showAddLayerButton    = false;
    a.showDeleteLayerButton = false;
    rs.counter  = 0;
    a.showLayers            = false;
    var site_path           = $("#site_path").val();
    a.file_path             = site_path;
    a.frontImageCropDiv     = false;
    a.brandImageCropDiv     = false;
    $("#error_div").hide();
    $("#success_div").hide();

    a.wth = 318;
    a.hgt = 222;

    if(rs.selectedDirId){
      a.selectedFolder = rs.selectedDirId;
      if(rs.selectedDirId == 1){
        a.ImageType = 'front_image';
      } else {
        a.ImageType = 'small_image';
      }
    } else {
      a.selectedFolder = '0';
    }

    x.get("../directory/alllisting/0").success(function(response){
      var rootOne = {
        id : '0',
        file_name: 'Root Folder',
        directory: '1'
      };

      var storeImage = {
        id : '1',
        file_name: 'Store Image',
        directory: '1'
      };

      response.push(rootOne);
      response.push(storeImage);
      a.cnames = response;
      angular.forEach(a.cnames, function(res){
        if(res.id.toString() === a.selectedFolder.toString()){
          a.SelectedFolderName = res.file_name;
        }
      });
    });

    a.uploadFirstImage = function(){
      $("#add_layer").click();
    };

    a.imageTypeChanged = function(ImageType){
      a.ImageType = ImageType;

      switch(a.ImageType){
        case 'small_image':
          a.smallImageCropDiv   = true;
          a.mideumImageCropDiv  = false;
          a.largeImageCropDiv   = false;
          a.frontImageCropDiv   = false;
          a.brandImageCropDiv   = false;
          a.wth = 227;
          a.hgt = 157;
          break;
        case 'medium_image':
          a.smallImageCropDiv   = false;
          a.largeImageCropDiv   = false;
          a.frontImageCropDiv   = false;
          a.brandImageCropDiv   = false;
          a.mideumImageCropDiv  = true;
          a.wth = 318 ;
          a.hgt = 222 ;
          break;
        case 'large_image':
          a.smallImageCropDiv   = false;
          a.mideumImageCropDiv  = false;
          a.frontImageCropDiv   = false;
          a.brandImageCropDiv   = false;
          a.largeImageCropDiv   = true;
          a.wth = 919 ;
          a.hgt = 439 ;
          break;
        case 'front_image':
          a.smallImageCropDiv   = false;
          a.mideumImageCropDiv  = false;
          a.largeImageCropDiv   = false;
          a.brandImageCropDiv   = false;
          a.frontImageCropDiv   = true;
          a.wth = 350 ;
          a.hgt = 250 ;
          break;
        case 'brand_image':
          a.smallImageCropDiv   = false;
          a.mideumImageCropDiv  = false;
          a.largeImageCropDiv   = false;
          a.frontImageCropDiv   = false;
          a.brandImageCropDiv   = true;
          a.wth = 200 ;
          a.hgt = 200 ;
          break;
      }

      if(a.layersDetailedList.length){
        setDivArea();
        setTimeout(function(){
          add_layer();
        },30);
      } else {
        setDivArea();
      }
    };

    function setDivArea(){
      setTimeout(function(){
        $(".inner_con").css({"width":a.wth,"height":a.hgt,"position":"absolute","overflow":"hidden","background":"white"});
        $('.inner_con').draggable( {"containment": "parent"});
      },10);
    }


    $("#add_layer").on("change",function(){
      importImage();
    });

    function add_layer()
    {
      for(var i = 0; i <= a.layersDetailedList.length-1; i++){
        var j = i+1;
        $('#draggable'+j).draggable();
        $('#resizable'+j).resizable({
            aspectRatio: true,
            minHeight: 50,
            minWidth: 50,
            handles: 'ne, se, sw, nw'
        });
      }
    }

    function importImage(){
      setDivArea();
      var file    = document.querySelector('input[type=file]').files[0];
      var reader  = new FileReader();

      reader.addEventListener("load", function () {
        a.showAddLayerButton = true;
        a.showDeleteLayerButton = true;
        rs.counter++;
        var LayerDetails = {
          layerId: rs.counter,
          layer_name: "draggable"+rs.counter,
          image_data: reader.result
        };
        a.layersDetailedList.push(LayerDetails);
      }, false);

      if (file) {
        setTimeout(function(){
          var liId = '#'+a.ImageType+'1';
          angular.element(liId).triggerHandler('click');
        },200);
        
        reader.readAsDataURL(file);
      }
    }


    a.save_image = function(){
        $(".resizable").addClass("white_border");
        $(".resizable .ui-resizable-handle").css({"background":"none","border":"none"});
        html2canvas($(".inner_con"), {
          onrendered: function(canvas) {
            var theCanvas = canvas;
            var dataUrl = canvas.toDataURL();
            var img = a.ImageType;
            $('#'+img).attr("src",dataUrl);
          }
        });
    };

    a.showAllLayers = function(){
      if(a.showLayers){
        a.showLayers = false;
      } else {
        a.showLayers = true;
      }
    };

    a.getSelectedLayer = function(layer){
      a.SelectedLayerId = layer;
    };

    a.deleteLayer = function(layer){
      var index = a.layersDetailedList.indexOf(layer);
      a.layersDetailedList.splice(index, 1);
      a.layersDetailedList = a.layersDetailedList;
      rs.counter = rs.counter - 1;
    };

    a.upload_repo_img = function(){
      if(a.selectedFolder == 1){

        if(a.selectedFolder == null ||  a.selectedFolder == "? undefined:undefined ?") {
          $("#error_div").hide();
          $("#show_message").slideDown();
          $("#error_div").html("Please insert required fields.");
          $("#error_div").show();
          $("#success_div").hide();
          setTimeout(function(){
            $("#error_div").hide();
          },5000);
        } else {
          if( $("#front_image").attr("src") == '../images/no-image-black.png' && $("#brand_image").attr("src") == '../images/no-image-black.png' ) {

            $("#error_div").hide();
            $("#show_message").slideDown();
            $("#error_div").html("Please crop all types of image.");
            $("#error_div").show();
            $("#success_div").hide();

            setTimeout(function(){
              $("#error_div").hide();
            },5000);

          } else if($("#front_image").attr("src") != '../images/no-image-black.png' && $("#brand_image").attr("src") == '../images/no-image-black.png') {

            var data = {
              'dir_id' : a.selectedFolder,
              'image_name' : image_name,
              'front_image' : $("#front_image").attr("src"),
              'image_type' : "image/jpg"
            };
          } else if($("#front_image").attr("src") == '../images/no-image-black.png' && $("#brand_image").attr("src") != '../images/no-image-black.png') {

            var data = {
              'dir_id' : a.selectedFolder,
              'image_name' : image_name,
              'brand_image' : $("#brand_image").attr("src"),
              'image_type' : "image/jpg"
            };
          } else {

            var data = {
              'dir_id' : a.selectedFolder,
              'image_name' : image_name,
              'front_image' : $("#front_image").attr("src"),
              'brand_image' : $("#brand_image").attr("src"),
              'image_type' : "image/jpg"
            };
          }


          console.log(data);
          a.upload_repo_img_btn = "Uploading ...";
          x.post('../directory/uploadstoreimage', data).success(function(response){
              console.log(response);
              //alert(JSON.stringify(response,null,4));
              switch(response.status) {
                case 'success':
                    var redirect_url='dashboard#/repository/list';
                    window.location = redirect_url; 
                    break;
                case 'already_exists':
                    $("#error_div").hide();
                    $("#show_message").slideDown();
                    $("#error_div").html("Image with same name already exists. <br /> Please try with a diffrent name.");
                    $("#error_div").show();
                    $("#success_div").hide();

                    setTimeout(function(){
                      $("#error_div").hide();
                    },5000);
                    break;
              }
          });
        }
      } else {
        var image_name = $("#image_name").val().replace(/[^a-zA-Z0-9]/g, '-');
        var data = {
          'dir_id' : a.selectedFolder,
          'image_name' : image_name,
          'small_image' : $("#small_image").attr("src"),
          'medium_image' : $("#medium_image").attr("src"),
          'large_image' : $("#large_image").attr("src"),
          'image_type' : "image/png"
        };
        console.log("data :: "+JSON.stringify(data, null, 4));
        if(data.dir_id == null ||  data.dir_id == "? undefined:undefined ?" || data.image_name == null || data.image_name == '' ) {
          $("#error_div").hide();
          $("#show_message").slideDown();
          $("#error_div").html("Please insert required fields.");
          $("#error_div").show();
          $("#success_div").hide();
          setTimeout(function(){
            $("#error_div").hide();
          },5000);
        } else {
          if( data.small_image == '../images/no-image-black.png' || data.large_image == '../images/no-image-black.png' ) {

            $("#error_div").hide();
            $("#show_message").slideDown();
            $("#error_div").html("Please crop all types of image.");
            $("#error_div").show();
            $("#success_div").hide();

            setTimeout(function(){
              $("#error_div").hide();
            },5000);

          } else {
              console.log(data);
              a.upload_repo_img_btn = "Uploading ...";
              x.post('../directory/upload', data).success(function(response){
                  console.log(response);
                  //alert(JSON.stringify(response,null,4));
                  switch(response.status) {
                    case 'success':
                        var redirect_url='dashboard#/repository/list';
                        window.location = redirect_url; 
                        break;
                    case 'already_exists':
                        $("#error_div").hide();
                        $("#show_message").slideDown();
                        $("#error_div").html("Image with same name already exists. <br /> Please try with a diffrent name.");
                        $("#error_div").show();
                        $("#success_div").hide();

                        setTimeout(function(){
                          $("#error_div").hide();
                        },5000);
                        break;
                  }
              });
          }
        }
      } 
    };


    a.changeSelectedDirectory = function(){
      rs.selectedDirId = "";
      rs.selectedDirId =  a.selectedFolder;
      angular.forEach(a.cnames, function(res){
        if(res.id.toString() === a.selectedFolder.toString()){
          a.SelectedFolderName = res.file_name;

          if(a.selectedFolder == 1){
            a.ImageType = 'front_image';
          } else {
            a.ImageType = 'small_image';
          }
          a.imageTypeChanged(a.ImageType);
        }
      });
    };

    a.imageTypeChanged(a.ImageType);

    rs.past_val = "";

    $( "#slider_data" ).slider({
      // orientation: "vertical",
      range: "min",
      value: 1,
      min: 0,
      max: 360,
      slide: function( event, ui ) {
       var curr_dv = $("#layers").attr("curr_layer");
        if(rs.past_val == "")
        {
          rs.past_val  =  ui.value;
          // console.log(  sessionStorage.past_val);
         // $("#image").cropper("rotate", sessionStorage.past_val);
         $('#'+curr_dv).css({'-webkit-transform' : 'rotate('+ui.value+'deg)',
                 '-moz-transform' : 'rotate('+ui.value+'deg)',
                 '-ms-transform' : 'rotate('+ui.value+'deg)',
                 'transform' : 'rotate('+ui.value+'deg)'});

        }else
        {
          var lst = ui.value -  rs.past_val;
          console.log(lst);
          //$("#image").cropper("rotate", lst);
         
         $('#'+curr_dv).css({'-webkit-transform' : 'rotate('+ui.value+'deg)',
                 '-moz-transform' : 'rotate('+ui.value+'deg)',
                 '-ms-transform' : 'rotate('+ui.value+'deg)',
                 'transform' : 'rotate('+ui.value+'deg)'});

          rs.past_val = ui.value;
        }
      }
    });

    $("#lock_outer_section").click(function(){
      $( ".inner_con" ).draggable( "disable" );
    });

    $("#unlock_outer_section").click(function(){
      $( ".inner_con" ).draggable( "enable" );
    });


    $("#layers").on( "click", 'div', function(){
      var layer_name = $(this).attr('layer_name');
      $(".resizable").addClass("white_border");
      $(".resizable .ui-resizable-handle").css({"background":"none","border":"none"});
      // console.log($("#"+layer_name).children().children());
      $("#"+layer_name).children().children().removeClass("white_border");
      $("#"+layer_name).find(".ui-resizable-handle").each(function(){
         $(this).css({ "background": "#f5dc58","border" : "1px solid #FFF"});
      });


      $("#layers").attr("curr_layer",layer_name);
      $( ".draggable" ).draggable( "disable" );
      $("#"+layer_name).draggable( "enable" );
      // //$("#"+layer_name).children().children().css({ "background": "#f5dc58","border" : "1px solid #FFF"});
    });


}]);


