"use strict"; //ReedemerController
var MyApp = angular.module("category-app", []);
MyApp.controller('CategoryController',["$scope", "PlaceholderTextService", "ngTableParams", "$filter", "$http", "fileUpload","$window",function(a, b, c, d, x, fu,hb) {
            a.dataLength={filtered:[]};
            a.root_base_url = root_base_url;
            a.currentPage = 0;
            a.pageSize = 5;
            a.selectedRootCatId = 0;
            a.selectedSubCatId = 0;

            // a.width = 300;
            // a.height = 300;
            a.statusOptions = [{name:'Active',id:1},{name:'Inactive',id:0}];
            var cat_id = $("#cat_id").val();

            var site_path=$("#site_path").val();
            var update_id =$("#update_id").val();
            // a.img_file_path = site_path; 
            a.setRootCatId = function(rootId){
																		if(rootId == a.selectedRootCatId){
																				a.selectedRootCatId = 0;
																		}else{
																				a.selectedRootCatId =  rootId;
																		}
                   a.selectedSubCatId = 0;
            }
            a.setSubCatId = function(subId){
																if(a.selectedSubCatId == subId){
																		a.selectedSubCatId = 0;
																}else{
                  a.selectedSubCatId =  subId;    
																}
            }
            
            a.deleteCategory = function(category){
														if(confirm('Are you sure?')){
                   x.get('../admin/dashboard/deletecategory/'+category.id).success(function(res){
                       if(res.status == 'error'){
                        a.error_msg = res.msg;
                       
                       }else{
                        a.succ_msg = res.msg;
                        a.getCatTree();
                       }
                       
                       if (a.$root && a.$root.$$phase != '$apply' && a.$root.$$phase != '$digest') {
                          a.$apply();
                          }
                        setTimeout(function(){
                        a.error_msg = '';
                        a.succ_msg = '';
                        if (a.$root && a.$root.$$phase != '$apply' && a.$root.$$phase != '$digest') {
                          a.$apply();
                          }
                        },10000);
                   });
														}
            }
            
            a.hideAddcatPopup = function(){
               a.cat = {};    
               a.showAddcategoryPopup = false;
            };
            
            a.showAddcatPopup = function(category_id){
                        if(category_id){
                          a.cat = {'parent_id':category_id};
                        }else{
                           a.cat = {};         
                        }
                        a.showAddcategoryPopup = true;
            }
            
            a.saveCat = function(category){
               x.post('../admin/dashboard/storecategory',category).success(function(res){
                       if(res.status == 'error'){
                        a.addpopup_error_msg = res.msg;
                       
                       }else{
                        a.succ_msg = res.msg;
                        a.getCatTree();
                         a.showAddcategoryPopup = false;
                       }
                       
                       if (a.$root && a.$root.$$phase != '$apply' && a.$root.$$phase != '$digest') {
                          a.$apply();
                          }
                        setTimeout(function(){
                        a.addpopup_error_msg = '';
                        a.succ_msg = '';
                        if (a.$root && a.$root.$$phase != '$apply' && a.$root.$$phase != '$digest') {
                          a.$apply();
                          }
                        },10000);                      
               });
            }
            
            a.showEditCatPopup = function(ecat){
                   a.ecat = ecat;
                   a.showEditcategoryPopup = true;     
            }
            
            a.hideEditcatPopup = function(){
                   a.ecat = {};
                   a.showEditcategoryPopup = false;
            }
            
            a.updateCat = function(category){
              x.post('../admin/dashboard/updatecategory',category).success(function(res){
                  if(res.status == 'error'){
                        a.editpopup_error_msg = res.msg;
                       
                       }else{
                        a.succ_msg = res.msg;
                        a.getCatTree();
                         a.showEditcategoryPopup = false;
                     }
                        
                       if (a.$root && a.$root.$$phase != '$apply' && a.$root.$$phase != '$digest') {
                          a.$apply();
                          }
                        setTimeout(function(){
                        a.editpopup_error_msg = '';
                        a.succ_msg = '';
                        if (a.$root && a.$root.$$phase != '$apply' && a.$root.$$phase != '$digest') {
                          a.$apply();
                          }
                        },10000);
                 
							});   
						}
						
						a.changeStatus = function(id){
								if(!id) { return false; }
								
								x.get('../admin/dashboard/swapcatstatus/'+id).success(function(res){
										 if(res.status == 'error'){
                        a.error_msg = res.msg;
                       
											}else{
											 a.succ_msg = res.msg;
											 a.getCatTree();
											}
                       
                      if (a.$root && a.$root.$$phase != '$apply' && a.$root.$$phase != '$digest') {
                        a.$apply();
                      }
											setTimeout(function(){
												a.error_msg = '';
												a.succ_msg = '';
												if (a.$root && a.$root.$$phase != '$apply' && a.$root.$$phase != '$digest') {
													a.$apply();
												}
											},10000);
								});
						}
           
            // Category, sub-cat listing
            $('#category_id').on('change',function(){
                var category_id = $(this).val();
                // var site_path=$("#site_path").val();           
                // alert(category_id);
                //return false;
                if(category_id){
                    $.ajax({
                        type:'POST',
                        url:'../admin/dashboard/subcategory/',
                        data:'parent_id='+category_id,
                        success:function(html){
                            //alert(site_path);
                             var new_html="<option value=''>----</option>";
                              for(var i=0; i<html.length; i++)
                              {
                                new_html+="<option value='"+html[i].id+"'>"+html[i].cat_name+"</option>";
                              }
                            //alert(JSON.stringify(new_html,null,4));
                            $('#subcat_id').html(new_html);
                        }

                    }); 
                }else{
                    $('#subcat_id').html('<option value="">Select state first</option>'); 
                }
            });

            // show all uploaded logo in admin panel 
            if($("#cat_id").val())
            {
                var new_cat_id = $("#cat_id").val();
            }
           
            a.new_cat_id=new_cat_id;
            a.category_details = {};

            a.getCatTree = function(){
                        x.get("../admin/dashboard/categories").success(function(data){
                                    a.categoriesTree  = data;
                                    if (a.$root && a.$root.$$phase != '$apply' && a.$root.$$phase != '$digest') {
                                    a.$apply();
                                    }
                        });
            };
            a.getCatTree();

          
            a.update_category=function(itemId,itemStatus){
               x.get("../admin/dashboard/categoryupdate/"+itemId+"/"+itemStatus).success(function(response){
                  a.status=response;                 
                  window.location.reload();             
               })
            }

            a.category_edit=function(itemId){
              $("#update_id").val(itemId);              
              var main_site_url=$("#main_site_url").val(); 
              var edit_url=main_site_url+'/admin/dashboard#/tables/category_edit/';    
              window.location.href = edit_url;             
            }

            a.edit_category=function(){              
              var main_site_url=$('#main_site_url').val();   
              var cat_name=$('#cat_name').val();
             
              //alert("V");
              //return false;
              a.cat_details= [{
                                'cat_name':cat_name,
                                'id':update_id
                              }];             
             // alert(JSON.stringify(a.cat_details,null,4));
             // return false;
              x.post(main_site_url+"/admin/dashboard/editcategory",a.cat_details).success(function(response){
                var main_site_url=$("#main_site_url").val();                              
                var redirect_url=main_site_url+'/admin/dashboard#/tables/category_list';
                
                //alert(JSON.stringify(response,null,4));
                //return false;
                if(response=='success')
                {
                  $("#update_id").val('');                  
                  $("#error_div").hide();
                  $("#show_message").slideDown();
                  $("#success_div").html("Data updated successfully. <br />Please wait,we will reload this page.");
                  $("#success_div").show(); 


                  setTimeout(function() {
                    window.location.href = redirect_url; 
                  }, 5000);
                }
                else if(response=='invalid_id')
                {
                  $("#error_div").hide();
                  $("#show_message").slideDown();
                  $("#error_div").html("Error occoure! Please try again.");
                  $("#error_div").show();
                  $("#success_div").hide();

                  setTimeout(function() { 
                    window.location.href = redirect_url; 
                  }, 2000);
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
            }

            a.delete_category=function(itemId){ 
             if(confirm("Are you sure?"))
             {
                var main_site_url=$('#main_site_url').val();

                $(".delete_row").hide();
                $("td#row_"+itemId).parent()
                .replaceWith('<tr><td colspan="5" class="center"><img src="'+main_site_url+'/images/loader.gif" /></td></tr>');               
                x.get(site_path+"admin/dashboard/deletecategory/"+itemId).success(function(response){
               //  alert(response);
                  if(response=='success')
                  {                   
                    $("#error_div").hide();
                    $("#show_message").slideDown();
                    $("#success_div").html("Data deleted successfully. <br />Please wait,we will reload this page.");
                    $("#success_div").show();              

                    setTimeout(function() { 
                      window.location.reload(); 
                    }, 5000);                                 
                  }
                  if(response=='subcat_exists')
                  { 
                    $("#error_div").hide();
                    $("#show_message").slideDown();
                    $("#error_div").html("Please remove all sub category under this category before delete this.");
                    $("#error_div").show();
                    $("#success_div").hide(); 
                    setTimeout(function() { 
                      window.location.reload(); 
                    }, 5000); 
                  }
                  if(response=='error')
                  { 
                    $("#error_div").hide();
                    $("#show_message").slideDown();
                    $("#error_div").html("Upable to delete category now. <br/> Please try after some time.");
                    $("#error_div").show();
                    $("#success_div").hide(); 
                    setTimeout(function() { 
                      window.location.reload(); 
                    }, 5000); 
                  }
               })
             }
            }

            a.set_cat_id = function(cat_id){
              // alert(cat_id);
              // return false;
              //a.dir_id=dir_id;
              $("#cat_id").val(cat_id);
               // x.get("../directory/category/"+dir_id).success(function(response){
                 // alert(JSON.stringify(response, null, 4)); 
               //   a.repo_details=response
                  //a.file_path=site_path
               // });
                // alert(cat_id);
                 a.cat_details= [{                               
                                'parent_id':cat_id,
                                'sub_cat':1
                              }]; 

                             // alert(JSON.stringify(a.cat_details, null, 4)); 
                              //return false;
                 x.post("../admin/dashboard/category",a.cat_details).success(function(category_data_response){              
                    // alert(JSON.stringify(category_data_response, null, 4)); 
                //     return false;
                     a.category_details = category_data_response;                
                     a.file_path=site_path; 
                     a.cat_id=cat_id;
                 });
            };

            a.add_reedemer=function(){                
              //alert(JSON.stringify(a.Redeemer, null, 4));
              if($("#company_name").val()=='')
              {
                    $("#error_div").hide();
                    $("#show_message").slideDown();
                    $("#error_div").html("Please insert company name.");
                    $("#error_div").show();
                    $("#success_div").hide();

                    $('#add_reedemer').prop('disabled', false);
                    $("#add_reedemer").text('Save user');  

                    return false;
               }
               else if($("#company_name").val()=='')
               {
                    $("#error_div").hide();
                    $("#show_message").slideDown();
                    $("#error_div").html("Please insert address.");
                    $("#error_div").show();
                    $("#success_div").hide();

                    $('#add_reedemer').prop('disabled', false);
                    $("#add_reedemer").text('Save user');  

                    return false;
               }
               else if($("#postal_code").val()=='')
               {
                    $("#error_div").hide();
                    $("#show_message").slideDown();
                    $("#error_div").html("Please insert address.");
                    $("#error_div").show();
                    $("#success_div").hide();

                    $('#add_reedemer').prop('disabled', false);
                    $("#add_reedemer").text('Save user');  

                    return false;
               }
               a.show_success_msg=false; 
               a.show_error_msg=false; 
               
               var url=$("#web_address").val();              

               var re = /^(http[s]?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/;
                if (!re.test(url)) { 
                    $("#error_div").hide();
                    $("#show_message").slideDown();
                    $("#error_div").html("Invalid web address.");
                    $("#error_div").show();
                    $("#success_div").hide();

                    $('#add_reedemer').prop('disabled', false);
                    $("#add_reedemer").text('Save user');   
                    return false;
                }

               x.post("../admin/dashboard/storereedemer", a.Redeemer).success(function(response){
                 
                  if(response=="success")
                  {                    
                    var main_site_url=$("#main_site_url").val();                                    
                    var redirect_url=main_site_url+'/admin/dashboard#/tables/list'; 

                    $("#error_div").hide();
                    $("#show_message").slideDown();
                    $("#success_div").html("Data inserted successfully. <br />Please wait,we will redirect you to listing page.");
                    $("#success_div").show();              

                    setTimeout(function() { 
                    window.location.href = redirect_url; 
                    }, 5000);
                  }
                  else if(response=="email_exists")
                  {
                    $("#error_div").hide();
                    $("#show_message").slideDown();
                    $("#error_div").html("Email already exists.Please try with diffrent email.");
                    $("#error_div").show();
                    $("#success_div").hide();

                    $('#add_reedemer').prop('disabled', false);
                    $("#add_reedemer").text('Save user');     
                  }
                  else if(response=="already_company_exists")
                  {
                    $("#error_div").hide();
                    $("#show_message").slideDown();
                    $("#error_div").html("Company already exists.");
                    $("#error_div").show();
                    $("#success_div").hide();

                    $('#add_reedemer').prop('disabled', false);
                    $("#add_reedemer").text('Save user');     
                  }
                  else
                  {
                      $("#error_div").hide();
                      $("#show_message").slideDown();
                      $("#error_div").html("Please insert all field.");
                      $("#error_div").show();
                      $("#success_div").hide();

                      $('#add_reedemer').prop('disabled', false);
                      $("#add_reedemer").text('Save user');     
                  }
               })
            }

            a.view_item=function(itemId){ 
                var main_site_url=$("#main_site_url").val();
                var url_link=main_site_url+'/admin/dashboard#/tables/view/';   

                window.location.href = url_link;  

                //x.post("../admin/dashboard/storereedemer", a.Redeemer).success(function(response){

                //});
            } 

            a.cancel_redirect=function(folder_name){ 
                var main_site_url=$("#main_site_url").val();
                $("#update_id").val(''); 
                var redirect_url=main_site_url+'/admin/dashboard#/'+folder_name+'/list';
                
                window.location.href = redirect_url; 
            } 

            a.cancel_category=function(folder_name){ 
                var main_site_url=$("#main_site_url").val();
                $("#update_id").val(''); 
                var redirect_url=main_site_url+'/admin/dashboard#/'+folder_name+'/category_list';
                
                window.location.href = redirect_url; 
            }
             
}]);
