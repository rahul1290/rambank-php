<!DOCTYPE html>
<html lang="en">

<head><meta http-equiv="Content-Type" content="text/html; charset=euc-jp">

  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Shri Ram Nam Bank</title>

  <!-- Custom fonts for this template-->
  <link href="<?php echo base_url('assets');?>/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="<?php echo base_url('assets');?>/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion toggled" id="accordionSidebar" style="background-image: linear-gradient(180deg,#df671e 10%,#f00 100%);">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo base_url('Admin_ctrl/users');?>">
        <div class="sidebar-brand-icon rotate-n-15">
          <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Jai Shri RAM <sup>+</sup></div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="<?php echo base_url('users'); ?>">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Users</span>
		</a>
		<?php /*
		<a class="nav-link" href="<?php echo base_url('ballot'); ?>">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Ballot</span>
		</a>
		*/ ?>
		
		<a class="nav-link" href="<?php echo base_url('app_images'); ?>">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>App Images</span>
		</a>
		
		<a class="nav-link" href="<?php echo base_url('users/logout'); ?>">
          <i class="fas fa-fw fa-sign-out-alt"></i>
          <span>Logout</span>
		</a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider">

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>


        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <span class="h3 mb-4 text-gray-800">Users List</span>
         <span class="text-danger"><a href="javascript:void(0);" id="add">Add More</a></span>
         <span class="float-right">
                 <input type="text" placeholder="Search" id="search_text" value="<?php echo $this->uri->segment(4); ?>"/>
                 <input type="button" value="Search" id="search"/>
         </span>
         <a href="javascript:void(0);" id="event">Event</a>
          <div class="table-responsive">
              <table id="example" class="table table-striped table-bordered" style="width:100%">
                  <thead>
                        <th>Action</th>
                      <th>S.No.</th>
                      <th>Account No.</th>
                      <!--th>Profile Photo</th-->
                      <th>Name</th>
                      <th>User Name</th>
                      <th>Gender</th>
                      <th>Contact No.</th>
                      <th>EMail Id</th>
                      <th>Password</th>
                      <th>Address</th>
                      <th>Previous</th>
                      <th>Current Month</th>
                      <th>Today</th>
                      <th>Total</th>
                      <th>Grand Total<br/><small>(Previous+Total)</small></th>
                      
                  </thead>
                  <tbody>
                      <?php $c = 1; foreach($users as $u){ ?>
                          <tr>
                              <td>
                                  <!--a target="_blank" href="<?php //echo base_url('user-detail');?>/<?php //echo $u['account_no']; ?>">detail</a-->
                                  <a href="javascript:void(0);" class="edit" 
                                  data-total="<?php echo $u['feeds']; ?>" data-today="<?php echo $u['today'];?>" data-month="<?php echo $u['this_month']; ?>" data-name="<?php echo $u['name']; ?>" data-u_name="<?php echo $u['u_name']; ?>" data-account_no="<?php echo $u['account_no']; ?>" data-contact_no="<?php echo $u['mobile_no'];?>" data-password="<?php echo $u['password']; ?>" data-address="<?php echo $u['address']; ?>" data-gender="<?php echo $u['gender'];?>" data-previous="<?php echo $u['previous']; ?>"><i class="fas fa-fw fa-pencil-alt"></i></a>
                                  <a href="javascript:void(0);" class="delete" data-account_no="<?php echo $u['account_no']; ?>"><i class="fa fa-trash"></i></a>
                                  <a href="javascript:void(0);" class="firebase" data-token="<?php echo $u['device_token']; ?>" data-account_no="<?php echo $u['account_no']; ?>"><i class="fa fa-fire"></i></a>
                              </td>
                              <td><?php echo $c; ?></td>
                              <td><?php echo $u['account_no']; ?></td>
                              <!--td><img src=""></td-->
                              <td><?php echo $u['name']; ?></td>
                              <td><?php echo $u['u_name']; ?></td>
                              <td><?php echo $u['gender']; ?></td>
                              <td><?php echo $u['mobile_no']; ?></td>
                              <td><?php echo $u['mail_id']; ?></td>
                              <td><?php echo $u['password']; ?></td>
                              <td><?php echo $u['address']; ?></td>
                              <td><?php echo $u['previous']; ?></td>
                              <td><?php echo $u['this_month']; ?></td>
                              <td><?php echo $u['today']; ?></td>
                              <td><?php echo $u['feeds']; ?></td>
                              <td><?php echo $u['total']; ?></td>
                              
                          </tr>
                      <?php $c = $c + 1; } ?>
                  </tbody>
              </table>
              <div class="row justify-content-center">
                        <input type="button" class="btn mr-2 btn-secondary nav_btn" data-draw="<?php echo ($draw - 1); ?>" value="Prev" <?php if(!$has_prev){ echo "disabled"; } ?> >
                    
                        <input type="button" class="btn btn-secondary nav_btn" data-draw="<?php echo ($draw+1); ?>" value="Next" <?php if(!$has_next){ echo "disabled"; } ?>>
                    
              </div>
          </div>   
        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; Your Website 2019</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  
  
  <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Devotee</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <form>
              <input type="hidden" id="account_no" value="">
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="name">
                </div>
              </div>
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">User Name</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="u_name">
                </div>
              </div>
              
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Contact No.</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="contact_no">
                </div>
              </div>
              
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Password</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="password">
                </div>
              </div>
              
              
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">address</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="address">
                </div>
              </div>
              
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Gender</label>
                <div class="col-sm-10">
                  <input type="radio" name="gender" value="MALE" id="gm">MALE
                  <input type="radio" name="gender" value="FEMALE" id="gf">FEMALE
                </div>
              </div>
              
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Previous</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="previous" value="0">
                </div>
              </div>
              
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Month</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="current_month" value="0">
                </div>
              </div>
              
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Today</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="today" value="0">
                </div>
              </div>
			  
			  <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Total Feed</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="total_feed" value="0">
                </div>
              </div>
              
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="submit" class="btn btn-primary">Submit</button>
        <button style="display:none;" type="button" id="update" class="btn btn-primary">update</button>
      </div>
    </div>
  </div>
</div>


<!-- Firebase -->
<div class="modal fade" id="firebaseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="firebaseModalLabel">Send personal notification</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <form>
              <input type="hidden" id="firebase_account_no" value="">
              <input type="hidden" id="firebase_token" value="">
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Message Title</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="firebase_title">
                </div>
              </div>
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Message Body</label>
                <div class="col-sm-10">
                  <textarea class="form-control" id="firebase_body"></textarea>
                </div>
              </div>
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="firebase_submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</div>


<!-- Firebase Event -->
<div class="modal fade" id="firebaseEventModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="firebaseEventModalLabel">Send personal notification</h5>
        <button type="button" class="eventclose" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <form>
              <div class="form-group row">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Message Body</label>
                <div class="col-sm-10">
                  <textarea class="form-control" id="firebase_event_body"></textarea>
                </div>
              </div>
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="firebase_event_submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</div>
<!--   -->
  <!-- Bootstrap core JavaScript-->
  <script src="<?php echo base_url('assets');?>/vendor/jquery/jquery.min.js"></script>
  <script src="<?php echo base_url('assets');?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="<?php echo base_url('assets');?>/vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="<?php echo base_url('assets');?>/js/sb-admin-2.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
  <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
       // $('#example').DataTable();
       
       $(document).on('click','.nav_btn',function(){
           console.log('sdf');
           let draw = $(this).data('draw');
           let searchTxt = $('#search_text').val();
           //window.location.href = "<?php echo base_url('admin_ctrl/users/');?>"+ draw +'/'+ searchTxt;
           
           window.location.replace("<?php echo base_url('admin_ctrl/users/');?>"+ draw +'/'+ searchTxt);
       });
       
       $(document).on('click','#search',function(){
           let searchTxt = $('#search_text').val();
           
           window.location.href = "<?php echo base_url('admin_ctrl/users/1');?>"+'/'+ searchTxt;
       });
        
        $(document).on('click','#add',function(){
            $('#exampleModal').modal({
                keyboard: false,
                backdrop : 'static'
            });
        });
        
        $(document).on('click','.edit',function(){
            $('#account_no').val($(this).data('account_no'));
            $('#name').val($(this).data('name'));
            $('#u_name').val($(this).data('u_name'));
            $('#contact_no').val($(this).data('contact_no'));
            $('#password').val($(this).data('password'));
            $('#address').val($(this).data('address'));
            $('#previous').val($(this).data('previous'));
            $('#current_month').val($(this).data('month'));
            $('#today').val($(this).data('today'));
			$('#total_feed').val($(this).data('total'));
            if($(this).data('gender') == 'MALE'){
                $('#gm').prop('checked', true);
            } else {
                $('#gf').prop('checked', true);
            }
            $('#exampleModalLabel').html('Update Dedvotee');
            $('#submit').hide();
            $('#update').show();
            
            $('#exampleModal').modal({
                keyboard: false,
                backdrop : 'static'
            });
            
        });
        
        $(document).on('click','#submit',function(){
            $.ajax({
              type: "POST",
              url: "<?php echo base_url('admin_ctrl/add_devotee');?>",
              dataType: "json",
              data: {
                  'name'	: $('#name').val(),
                  'u_name' : $('#u_name').val(),
                  'contact_no' : $('#contact_no').val(),
                  'password' : $('#password').val(),
                  'address' : $('#address').val(),
                  'gender' : $("input[name='gender']:checked").val(),
                  'previous' : $('#previous').val(),
				  'month' : $('#current_month').val(),
                  'today' : $('#today').val(),
				  'total_feed' : $('#total_feed').val()
              },
              success: function(response){
                  if(response.status == 200){
                      alert('Devotee Added Successfully.');
                      location.reload(true);
                  } else {
                      
                  }
              },
              
            });
        });
        
        
        $(document).on('click','#update',function(){
            $.ajax({
              type: "POST",
              url: "<?php echo base_url('admin_ctrl/update_devotee');?>",
              dataType: "json",
              data: {
            	  'name' : $('#name').val(),
                  'u_name' : $('#u_name').val(),
                  'contact_no' : $('#contact_no').val(),
                  'password' : $('#password').val(),
                  'address' : $('#address').val(),
                  'gender' : $("input[name='gender']:checked").val(),
                  'previous' : $('#previous').val(),
                  'month' : $('#current_month').val(),
                  'today' : $('#today').val(),
				  'total_feed' : $('#total_feed').val(),
                  'account_no' : $('#account_no').val()
              },
              success: function(response){
                  if(response.status == 200){
                      alert('Devotee Updated successfully.');
                      location.reload(true);
                  }
              }
            });
        });
        
        
        $(document).on('click','.delete',function(){
            var account_no = $(this).data('account_no');
            var c = confirm('Are you sure?');
            if(c){
                $.ajax({
                  type: "POST",
                  url: "<?php echo base_url('admin_ctrl/delete_devotee');?>",
                  dataType: "json",
                  data: {
                      'account_no' : account_no
                  },
                  success: function(response){
                      if(response.status == 200){
                          alert('User not deleted! Contact to Super Admin.');
                          location.reload();
                      } else{
                          alert('Users deleted Successfully.');
                      }
                  }
                });
            }
        });
        
        $(document).on('click','.firebase',function(){
            $('#firebase_account_no').val($(this).data('account_no'));
            $('#firebase_token').val($(this).data('token'));
            
            $('#firebaseModal').modal({
                keyboard: false,
                backdrop : 'static'
            });
        });
        
        $(document).on('click','#firebase_submit',function(){
            // Create notification message
            var message = {
                to: $('#firebase_token').val(),
                notification:{
                    title: $('#firebase_title').val(),
                    body: $('#firebase_body').val(),
                    // icon: "https://your-site.com/icon.png",
                    // click_action: "https://your-site.com"
                }
            };
            console.log('Message===>',message);
        
            // Send notification using AJAX
            $.ajax({
                type: 'POST',
                url: 'https://fcm.googleapis.com/fcm/send',
                headers: {
                    Authorization: 'Bearer AAAAashnTpw:APA91bGMJnMlFeMgrN530rP2-sZbmK8R70M4jSH9Tj2f-C5tHiosV9qm2nfZwNx0NkDNBtv0kA8lczra6VpYeuidqFhV_sAcI_o-TgSdmgTO5lgVxv_EDYkcSJY1jro55FxoQ4WRT_f-',
                    'Content-Type': 'application/json; charset=UTF-8'
                },
                data: JSON.stringify(message),
                success: function(response) {
                    alert('Notification send successfully.');
                    console.log("Notification sent successfully:", response);
                    $('#firebaseModal').modal('toggle');
                    
                },
                error: function(error) {
                    console.error("Error sending notification:", error);
                }
            });
        });
        
        
        
        ///////////////////////////////////Event////////////////////////////////////////
        $(document).on('click','#event',function(){
            $('#firebaseEventModal').modal({
                keyboard: false,
                backdrop : 'static'
            });
        });
        
        $(document).on('click','#firebase_event_submit',function(){
            let too;
            $.ajax({
                type: 'GET',
                async : false,
                url: 'http://gyanodayvidyaniketan.com/rambank/Admin_ctrl/device_tokens',
                success: function(response) {
                    too = response.map((item)=>{
                        return item.device_token        
                   })
                },
            })
            
            
            var message = {
                registration_ids: too,
                notification:{
                    title: 'Event',
                    body: $('#firebase_event_body').val(),
                    // icon: "https://your-site.com/icon.png",
                    // click_action: "https://your-site.com"
                }
            };
            console.log('Message===>',message);
        
            // Send notification using AJAX
            $.ajax({
                type: 'POST',
                url: 'https://fcm.googleapis.com/fcm/send',
                headers: {
                    Authorization: 'Bearer AAAAashnTpw:APA91bGMJnMlFeMgrN530rP2-sZbmK8R70M4jSH9Tj2f-C5tHiosV9qm2nfZwNx0NkDNBtv0kA8lczra6VpYeuidqFhV_sAcI_o-TgSdmgTO5lgVxv_EDYkcSJY1jro55FxoQ4WRT_f-',
                    'Content-Type': 'application/json; charset=UTF-8'
                },
                data: JSON.stringify(message),
                success: function(response) {
                    alert('Notification send successfully.');
                    console.log("Notification sent successfully:", response);
                    $('#firebaseEventModal').modal('toggle');
                    
                },
                error: function(error) {
                    console.error("Error sending notification:", error);
                }
            });
        });
    });
    
    
</script>
</body>

</html>
