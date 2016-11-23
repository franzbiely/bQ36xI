<?php if ($_SESSION['type'] == 'superadmin'): ?>
<?php header_nav_bar("random", "Permission Details","settings") ?>
  <div class="container">    
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <?php are_you_sure_delete(); ?>
        <div class="page-header">
          <h1 id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;">Permission Details
            <!-- Modal -->
            <?php //$user->modal("visit"); ?>  
          </h1> 
        </div>
        <?php $permission_user = $user->get_permission_selected_user(); ?>
        <?php $type_user = $user->get_user_type(); ?>
        <?php $client_section;$records;$search_client;$user;$district;$province;$province;
              $client_reports;$feeding_reports;$consultation_reports;$hc_access;$add_hc; 
        ?>
        <?php if ($permission_user != false) {
          foreach ($permission_user as $value) {
            $client_section = json_decode($value['client_section']);
            $records = json_decode($value['records']);
            $search_client = json_decode($value['search_client']);
            $user_permission = json_decode($value['user']);
            $district_permission = json_decode($value['district']);
            $province_permission = json_decode($value['province']);
            $clinic_permission = json_decode($value['clinic']);
            $client_reports = json_decode($value['client_reports']);
            $feeding_reports = json_decode($value['feeding_reports']);
            $consultation_reports = json_decode($value['consultation_reports']);
            $hc_access = $value['hc_access'];
            $add_hc = $value['add_hc'];
          }
        } ?>
        <?php 
          /* get user information */
          $user_info = $user->get_user_info();
          $user_fullname = $user_info[0]['fullname'];
          $user_username = $user_info[0]['username'];
          $user_hc = get_area_name(get_user_hc($_GET['ID']));
         ?>
        <div class="tblcontainer permission_schema" data-type="client">
        <form role="form" action="" method="post" class="permission_form"> 
          <input type="hidden" name="class" value="permission" />
          <input type="hidden" name="func" value="add_permission" />
           <input type="hidden" name="user_id" value="<?php echo $_GET['ID']; ?>" />
            <div class="col-md-4">
              <?php if ($_GET['ID'] != $_SESSION['id']): ?>
              For <?php echo "\"{$user_fullname}\" - ({$user_username}) of \"{$user_hc}\""; ?> <br>
               <label>Select Type</label>
                <select name="user_type"  class="form-control user_type_select">
                <option value="none">Select User Type</option>
                <?php $data = $type->get_all('user'); ?>
                <?php foreach ($data['value'] as $key => $data): ?>
                  <?php if ($data != 'superadmin'): ?>
                    <option value="<?php echo $data; ?>" <?php if($data == $type_user){ echo "selected"; } ?>><?php echo $data; ?></option>
                  <?php endif ?>
                <?php endforeach ?>
               <!--  -->
              </select>
              <?php endif ?>
            </div>
            
          <table class="table  table-striped table-hover table-condensed" style="position:relative;">
            <thead>
              <tr>
                <th>
                  <div style="background: #fff; position:absolute; width: 100%; height: 100%;top: 0px;z-index: 9;opacity: 0.7; left:0;"></div>
                  Permission Table</th>
                <th></th>
              </tr>
            </thead>
            <tbody>            
                  <tr>
                     <td class="client_section">Client Section</td>
                    <td>
                    <div class="btn-group">
                        <input type="checkbox" name="client_section[]" class="view_current_hc  client_option" value="view_current_hc" <?php if (isset($client_section)){ if (enablea_and_disable_ele($type, "view_current_hc", $client_section)) { echo "checked"; } }?> onclick="return false">View from current Health Facility<br>
                        <input type="checkbox" name="client_section[]" class="view_all_hc client_option" value="view_all_hc" <?php  if (isset($client_section)){ if (enablea_and_disable_ele($type, "view_all_hc", $client_section)) { echo "checked"; }}?> onclick="return false">View from all Health Facility<br>
                        <input type="checkbox" name="client_section[]" class="client_section_add client_option" value="add" <?php if (isset($client_section)){ if (enablea_and_disable_ele($type, "add", $client_section)) { echo "checked"; }}?> onclick="return false">Add<br>
                        <input type="checkbox" name="client_section[]" class="client_section_edit client_option"value="edit" <?php if (isset($client_section)){ if (enablea_and_disable_ele($type, "edit", $client_section)) { echo "checked"; }}?> onclick="return false">Edit <br>
                        <input type="checkbox" name="client_section[]" class="client_section_delete client_option"value="delete" <?php if (isset($client_section)){ if (enablea_and_disable_ele($type, "delete", $client_section)) { echo "checked=checked"; }}?> onclick="return false">Delete<br>
                        <input type="checkbox" name="client_section[]" class="assign_other_hc client_option" value="assign_other_hc" <?php if (isset($client_section)){ if (enablea_and_disable_ele($type, "assign_other_hc", $client_section)) { echo "checked=checked"; }}?> onclick="return false">Assign to other Health Facility  
                        
                        
                        <!--  hidden fields used to get data from disabled fields -->
                       <!--  <input type="hidden" name="client_section[]" class="view_current_hc  client_option" value="view_current_hc">
                        <input type="hidden" name="client_section[]" class="view_all_hc client_option" value="view_all_hc">
                        <input type="hidden" name="client_section[]" class="client_section_add client_option" value="add">
                        <input type="hidden" name="client_section[]" class="client_section_edit client_option"value="edit">
                        <input type="hidden" name="client_section[]" class="client_section_delete client_option"value="delete">
                        <input type="hidden" name="client_section[]" class="assign_other_hc client_option" value="assign_other_hc"> -->
                    </div> 
                    </td> 
                  </tr>   
                  <tr>
                     <td class="records">Records</td>
                    <td>
                    <div class="btn-group">
                        <input type="checkbox" name="records[]" class="view_con_records records_option" value="view_con_records" 
                        <?php if (isset($records)){ if (enablea_and_disable_ele($type, "view_con_records", $records)) { echo "checked"; }}?> onclick="return false">View Consultation Records<br>
                        <input type="checkbox" name="records[]" class="add_con_records records_option" value="add_con_records" 
                        <?php  if (isset($records)){if (enablea_and_disable_ele($type, "add_con_records", $records)) { echo "checked"; }}?> onclick="return false">Add Consultation Records<br>
                        <input type="checkbox" name="records[]" class="delete_con_records records_option" value="delete_con_records" 
                         <?php  if (isset($records)){if (enablea_and_disable_ele($type, "delete_con_records", $records)) { echo "checked"; }}?> onclick="return false">Delete Consultation Records
                        
                        <!--  hidden fields used to get data from disabled fields -->
                      <!--   <input type="hidden" name="records[]" class="view_con_records records_option" value="view_con_records">
                        <input type="hidden" name="records[]" class="add_con_records records_option" value="add_con_records">
                        <input type="hidden" name="records[]" class="delete_con_records records_option" value="delete_con_records"> -->
                    </div> 
                    </td> 
                  </tr>  
                  <tr>
                     <td class="search_client">Search Client</td>
                    <td>
                    <div class="btn-group">
                        <input type="checkbox" name="search_client[]" class="quick_search search_option" value="quick_search"
                         <?php  if(isset($search_client)){ if (enablea_and_disable_ele($type, "quick_search", $search_client)) { echo "checked"; }}?> onclick="return false">Quick Search<br>
                        <input type="checkbox" name="search_client[]" class="advanced_search search_option" value="advanced_search"
                         <?php if(isset($search_client)){ if (enablea_and_disable_ele($type, "advanced_search", $search_client)) { echo "checked"; }}?> onclick="return false">Addvanced Search
                        <input type="checkbox" name="search_client[]" class="search_other_hc search_option" value="search_other_hc" 
                         <?php if(isset($search_client)){ if (enablea_and_disable_ele($type, "search_other_hc", $search_client)) { echo "checked"; }}?> onclick="return false">Search for Other Health Facility
                        
                        
                        <!--  hidden fields used to get data from disabled fields -->
                        <!-- <input type="hidden" name="search_client[]"  value="quick_search">
                        <input type="hidden" name="search_client[]" class="advanced_search search_option" value="advanced_search">
                        <input type="hidden" name="search_client[]" class="search_other_hc search_option" value="search_other_hc"> -->
                    </div> 
                    </td> 
                  </tr>        
                   <tr>
                     <td class="user">User</td>
                    <td>
                    <div class="btn-group user">
                        <input type="checkbox" name="user[]" class="view_other_profile user_option" value="view_other_profile"
                         <?php if(isset($user_permission)){ if(enablea_and_disable_ele($type, "view_other_profile", $user_permission)) { echo "checked"; }}?> onclick="return false">View Other Profile<br>
                        <input type="checkbox" name="user[]" class="add_new_user user_option" value="add_new_user" 
                        <?php if(isset($user_permission)){ if (enablea_and_disable_ele($type, "add_new_user", $user_permission)) { echo "checked"; }}?> onclick="return false">Add New User<br>
                        <input type="checkbox" name="user[]" class="edit_personal_record user_option" value="edit_personal_record" 
                        <?php if(isset($user_permission)){ if (enablea_and_disable_ele($type, "edit_personal_record", $user_permission)) { echo "checked"; }}?> onclick="return false">Edit Personal Record<br>
                        <input type="checkbox" name="user[]" class="edit_other_record user_option" value="edit_other_record" 
                        <?php if(isset($user_permission)){ if (enablea_and_disable_ele($type, "edit_other_record", $user_permission)) { echo "checked"; }}?> onclick="return false">Edit Other User Record<br>
                        <input type="checkbox" name="user[]" class="delete_other_user user_option" value="delete_other_user" 
                        <?php if(isset($user_permission)){ if (enablea_and_disable_ele($type, "delete_other_user", $user_permission)) { echo "checked"; }}?> onclick="return false">Delete Other User<br>
                        <input type="checkbox" name="user[]" class="delete_personal_record user_option" value="delete_personal_record" 
                        <?php if(isset($user_permission)){ if (enablea_and_disable_ele($type, "delete_personal_record", $user_permission)) { echo "checked"; }}?> onclick="return false">Delete Personal Record<br>
                        <input type="checkbox" name="user[]" class="user_assign_other_hc user_option" value="assign_other_hc" 
                        <?php if(isset($user_permission)){ if (enablea_and_disable_ele($type, "assign_other_hc", $user_permission)) { echo "checked"; }}?> onclick="return false">Assign to Other Health Facility
                        
                        
                        <!--  hidden fields used to get data from disabled fields -->
                        <!-- <input type="hidden" name="user[]" class="view_other_profile user_option" value="view_other_profile">
                        <input type="hidden" name="user[]" class="add_new_user user_option" value="add_new_user">
                        <input type="hidden" name="user[]" class="edit_personal_record user_option" value="edit_personal_record">
                        <input type="hidden" name="user[]" class="edit_other_record user_option" value="edit_other_record">
                        <input type="hidden" name="user[]" class="delete_other_user user_option" value="delete_other_user">
                        <input type="hidden" name="user[]" class="delete_personal_record user_option" value="delete_personal_record">
                        <input type="hidden" name="user[]" class="user_assign_other_hc user_option" value="assign_other_hc"> -->
                    </div> 
                    </td> 
                  </tr> 
                  <tr>
                     <td class="district">District</td>
                    <td>
                    <div class="btn-group">
                        <input type="checkbox" name="district[]" class="district_view district_option_view" value="view" 
                        <?php if(isset($district_permission)){ if (enablea_and_disable_ele($type, "view", $district_permission)) { echo "checked"; }}?> onclick="return false">View<br>
                        <input type="checkbox" name="district[]" class="district_add district_option" value="add" 
                        <?php if(isset($district_permission)){ if (enablea_and_disable_ele($type, "add", $district_permission)) { echo "checked"; }}?> onclick="return false">Add<br>
                        <input type="checkbox" name="district[]" class="district_edit district_option" value="edit" 
                        <?php if(isset($district_permission)){ if (enablea_and_disable_ele($type, "edit", $district_permission)) { echo "checked"; }}?> onclick="return false">Edit<br>
                        <input type="checkbox" name="district[]" class="district_delete district_option" value="delete" 
                        <?php if(isset($district_permission)){ if (enablea_and_disable_ele($type, "delete", $district_permission)) { echo "checked"; }}?> onclick="return false">Delete <br>
                        <input type="checkbox" name="district[]" class="district_assign_other_loc district_option" value="assign_other_loc" 
                        <?php if(isset($district_permission)){ if (enablea_and_disable_ele($type, "assign_other_loc", $district_permission)) { echo "checked"; }}?> onclick="return false">Assign to another location<br>
                        
                        
                        <!--  hidden fields used to get data from disabled fields -->
                        <!-- <input type="hidden" name="district[]" class="district_view district_option" value="view">
                        <input type="hidden" name="district[]" class="district_add district_option" value="add">
                        <input type="hidden" name="district[]" class="district_edit district_option" value="edit">
                        <input type="hidden" name="district[]" class="district_delete district_option" value="delete">
                        <input type="hidden" name="district[]" class="district_assign_other_loc district_option" value="assign_other_loc"> -->
                    </div> 
                    </td> 
                  </tr> 
                  <tr>
                     <td class="province">Province</td>
                    <td>
                    <div class="btn-group">
                        <input type="checkbox" name="province[]" class="province_view province_option_view" value="view" 
                        <?php if(isset($province_permission)){ if (enablea_and_disable_ele($type, "view", $province_permission)) { echo "checked"; }}?> onclick="return false">View<br>
                        <input type="checkbox" name="province[]" class="province_add province_option" value="add" 
                        <?php if(isset($province_permission)){ if (enablea_and_disable_ele($type, "add", $province_permission)) { echo "checked"; }}?> onclick="return false">Add<br>
                        <input type="checkbox" name="province[]" class="province_edit province_option" value="edit" 
                        <?php if(isset($province_permission)){ if (enablea_and_disable_ele($type, "edit", $province_permission)) { echo "checked"; }}?> onclick="return false">Edit<br>
                        <input type="checkbox" name="province[]" class="province_delete province_option" value="delete" 
                        <?php if(isset($province_permission)){ if (enablea_and_disable_ele($type, "delete", $province_permission)) { echo "checked"; }}?> onclick="return false">Delete <br>
                        <input type="checkbox" name="province[]" class="province_assign_other_loc province_option" value="assign_other_loc" 
                        <?php if(isset($province_permission)){ if (enablea_and_disable_ele($type, "assign_other_hc", $province_permission)) { echo "checked"; }}?> onclick="return false">Assign to another location<br>
                        
                        
                         <!--  hidden fields used to get data from disabled fields -->
                       <!--  <input type="hidden" name="province[]" class="province_view province_option" value="view">
                        <input type="hidden" name="province[]" class="province_add province_option" value="add">
                        <input type="hidden" name="province[]" class="province_edit province_option" value="edit">
                        <input type="hidden" name="province[]" class="province_delete province_option" value="delete">
                        <input type="hidden" name="province[]" class="province_assign_other_loc province_option" value="assign_other_loc"> -->
                    </div> 
                    </td> 
                  </tr>  
                <!--   <tr>
                     <td class="llg">LLG</td>
                    <td>
                    <div class="btn-group">
                        <input type="checkbox" name="llg[]" class="llg_view" value="view">View<br>
                        <input type="checkbox" name="llg[]" class="llg_add" value="add">Add<br>
                        <input type="checkbox" name="llg[]" class="llg_edit" value="edit">Edit<br>
                        <input type="checkbox" name="llg[]" class="llg_delete" value="delete">Delete <br>
                        <input type="checkbox" name="llg[]" class="llg_assign_other_loc" value="assign_other_loc">Assign to another location<br>
                    </div> 
                    </td> 
                  </tr> -->  
                  <tr>
                     <td class="clinic">Clinic</td>
                    <td>
                      <div class="btn-group">
                        <input type="checkbox" name="clinic[]" class="clinic_view clinic_option_view" value="view" 
                        <?php if(isset($clinic_permission)){ if (enablea_and_disable_ele($type, "view", $clinic_permission)) { echo "checked"; }}?> onclick="return false">View<br>
                        <input type="checkbox" name="clinic[]" class="clinic_add clinic_option" value="add" 
                        <?php if(isset($clinic_permission)){ if (enablea_and_disable_ele($type, "add", $clinic_permission)) { echo "checked"; }}?> onclick="return false">Add<br>
                        <input type="checkbox" name="clinic[]" class="clinic_edit clinic_option" value="edit" 
                        <?php if(isset($clinic_permission)){ if (enablea_and_disable_ele($type, "edit", $clinic_permission)) { echo "checked"; }}?> onclick="return false">Edit<br>
                        <input type="checkbox" name="clinic[]" class="clinic_delete clinic_option" value="delete" 
                        <?php if(isset($clinic_permission)){ if (enablea_and_disable_ele($type, "delete", $clinic_permission)) { echo "checked"; }}?> onclick="return false">Delete <br>
                        <input type="checkbox" name="clinic[]" class="clinic_assign_other_loc clinic_option" value="assign_other_loc" 
                        <?php if(isset($clinic_permission)){ if (enablea_and_disable_ele($type, "assign_other_hc", $clinic_permission)) { echo "checked"; }}?> onclick="return false">Assign to another location<br>
                        
                   
                        <!--  hidden fields used to get data from disabled fields --> 
                      <!--   <input type="hidden" name="clinic[]" class="clinic_view clinic_option" value="view">
                        <input type="hidden" name="clinic[]" class="clinic_add clinic_option" value="add">
                        <input type="hidden" name="clinic[]" class="clinic_edit clinic_option" value="edit">
                        <input type="hidden" name="clinic[]" class="clinic_delete clinic_option" value="delete">
                        <input type="hidden" name="clinic[]" class="clinic_assign_other_loc clinic_option" value="assign_other_loc"> -->
                    </div>
                    </td> 
                  </tr>  
                 <!--  <tr>
                     <td class="hc">Health Facility</td>
                    <td>
                    <div class="btn-group">
                        <input type="checkbox" name="hc" value="view">View<br>
                        <input type="checkbox" name="hc" value="add">Add<br>
                        <input type="checkbox" name="hc" value="edit">Edit<br>
                        <input type="checkbox" name="hc" value="delete">Delete <br>
                        <input type="checkbox" name="hc" value="assign_other_loc">Assign to another location<br>
                    </div> 
                    </td> 
                  </tr>  -->
                   <tr>
                     <td class="client_reports">Client Reports</td>
                    <td>
                    <div class="btn-group">
                        <input type="checkbox" name="client_reports[]" class="client_reports_generate_current_hc client_report_option" value="generate_current_hc" 
                        <?php if (isset($client_reports)){ if (enablea_and_disable_ele($type, "generate_current_hc", $client_reports)) { echo "checked"; }}?> onclick="return false">Generate from Current Health Facility<br>
                        <input type="checkbox" name="client_reports[]" class="client_reports_generate_other_hc client_report_option" value="generate_other_hc" 
                        <?php if (isset($client_reportss)){ if (enablea_and_disable_ele($type, "generate_other_hc", $client_reports)) { echo "checked"; }}?> onclick="return false">Generate from Other Health Facility<br>
                        <input type="checkbox" name="client_reports[]" class="client_reports_generate_all_hc client_report_option" value="generate_all_hc" 
                        <?php if (isset($client_reports)){ if (enablea_and_disable_ele($type, "generate_all_hc", $client_reports)) { echo "checked"; }}?> onclick="return false">Generate from All Health Facility<br>
                        <input type="checkbox" name="client_reports[]" class="client_reports_export_csv client_report_option" value="export_csv" 
                        <?php if (isset($client_reports)){ if (enablea_and_disable_ele($type, "export_csv", $client_reports)) { echo "checked"; }}?> onclick="return false">Export CSV <br>
                        <input type="checkbox" name="client_reports[]" class="client_reports_export_excel client_report_option" value="export_excel" 
                        <?php if (isset($client_reports)){ if (enablea_and_disable_ele($type, "export_excel", $client_reports)) { echo "checked"; }}?> onclick="return false">Export Excel<br>
                        
                   
                        <!--  hidden fields used to get data from disabled fields --> 
                        <!-- <input type="hidden" name="client_reports[]" class="client_reports_generate_current_hc client_report_option" value="generate_current_hc">
                        <input type="hidden" name="client_reports[]" class="client_reports_generate_other_hc client_report_option" value="generate_other_hc">
                        <input type="hidden" name="client_reports[]" class="client_reports_generate_all_hc client_report_option" value="generate_all_hc">
                        <input type="hidden" name="client_reports[]" class="client_reports_export_csv client_report_option" value="export_csv">
                        <input type="hidden" name="client_reports[]" class="client_reports_export_excel client_report_option" value="export_excel"> -->
                    </div> 
                    </td> 
                  </tr>   
                  <tr>
                     <td class="consultation_reports">Consultation Reports</td>
                    <td>
                    <div class="btn-group">
                        <input type="checkbox" name="consultation_reports[]" class="consultation_generate_current_hc consultion_report_option" value="generate_current_hc" 
                        <?php if (isset($consultation_reports)){ if (enablea_and_disable_ele($type, "generate_current_hc", $consultation_reports)) { echo "checked"; }}?> onclick="return false">Generate from Current Health Facility<br>
                        <input type="checkbox" name="consultation_reports[]" class="consultation_generate_other_hc consultion_report_option" value="generate_other_hc" 
                        <?php if (isset($consultation_reports)){ if (enablea_and_disable_ele($type, "generate_other_hc", $consultation_reports)) { echo "checked"; }}?> onclick="return false">Generate from Other Health Facility<br>
                        <input type="checkbox" name="consultation_reports[]" class="consultation_generate_all_hc consultion_report_option" value="generate_all_hc" 
                        <?php if (isset($consultation_reports)){ if (enablea_and_disable_ele($type, "generate_all_hc", $consultation_reports)) { echo "checked"; }}?> onclick="return false">Generate from All Health Facility<br>
                        <input type="checkbox" name="consultation_reports[]" class="consultation_export_csv consultion_report_option" value="export_csv" 
                        <?php if (isset($consultation_reports)){ if (enablea_and_disable_ele($type, "export_csv", $consultation_reports)) { echo "checked"; }}?> onclick="return false">Export CSV <br>
                        <input type="checkbox" name="consultation_reports[]" class="consultation_export_excel consultion_report_option" value="export_excel" 
                        <?php if (isset($consultation_reports)){ if (enablea_and_disable_ele($type, "export_excel", $consultation_reports)) { echo "checked"; }}?> onclick="return false">Export Excel<br>
                    
                        <!--  hidden fields used to get data from disabled fields --> 
                        <!-- <input type="hidden" name="consultation_reports[]" class="consultation_generate_current_hc consultion_report_option" value="generate_current_hc">
                        <input type="hidden" name="consultation_reports[]" class="consultation_generate_other_hc consultion_report_option" value="generate_other_hc">
                        <input type="hidden" name="consultation_reports[]" class="consultation_generate_all_hc consultion_report_option" value="generate_all_hc">
                        <input type="hidden" name="consultation_reports[]" class="consultation_export_csv consultion_report_option" value="export_csv">
                        <input type="hidden" name="consultation_reports[]" class="consultation_export_excel consultion_report_option" value="export_excel"> -->
                    </div> 
                    </td> 
                  </tr>  
                  <tr>
                     <td class="feeding_reports">Feeding Reports</td>
                    <td>
                     <div class="btn-group">
                        <input type="checkbox" name="feeding_reports[]" class="feeding_generate_current_hc feeding_report_option" value="generate_current_hc"
                        <?php if(isset($feeding_reports)){ if (enablea_and_disable_ele($type, "generate_current_hc", $feeding_reports)) { echo "checked"; }}?> onclick="return false">Generate from Current Health Facility<br>
                        <input type="checkbox" name="feeding_reports[]" class="feeding_generate_other_hc feeding_report_option" value="generate_other_hc" 
                        <?php if(isset($feeding_reports)){ if (enablea_and_disable_ele($type, "generate_other_hc", $feeding_reports)) { echo "checked"; }}?> onclick="return false">Generate from Other Health Facility<br>
                        <input type="checkbox" name="feeding_reports[]" class="feeding_generate_all_hc feeding_report_option" value="generate_all_hc" 
                        <?php if(isset($feeding_reports)){ if (enablea_and_disable_ele($type, "generate_all_hc", $feeding_reports)) { echo "checked"; }}?> onclick="return false">Generate from All Health Facility<br>
                        <input type="checkbox" name="feeding_reports[]" class="feeding_export_csv feeding_report_option" value="export_csv" 
                        <?php if(isset($feeding_reports)){ if (enablea_and_disable_ele($type, "export_csv", $feeding_reports)) { echo "checked"; }}?> onclick="return false">Export CSV <br>
                        <input type="checkbox" name="feeding_reports[]" class="feeding_export_excel feeding_report_option" value="export_excel" 
                        <?php if(isset($feeding_reports)){ if (enablea_and_disable_ele($type, "export_excel", $feeding_reports)) { echo "checked"; }}?> onclick="return false">Export Excel<br>
                    
                        <!--  hidden fields used to get data from disabled fields --> 
                        <!-- <input type="hidden" name="consultation_reports[]" class="consultation_generate_current_hc consultion_report_option" value="generate_current_hc">
                        <input type="hidden" name="consultation_reports[]" class="consultation_generate_other_hc consultion_report_option" value="generate_other_hc">
                        <input type="hidden" name="consultation_reports[]" class="consultation_generate_all_hc consultion_report_option" value="generate_all_hc">
                        <input type="hidden" name="consultation_reports[]" class="consultation_export_csv consultion_report_option" value="export_csv">
                        <input type="hidden" name="consultation_reports[]" class="consultation_export_excel consultion_report_option" value="export_excel"> -->
                    </div>
                    </td> 
                  </tr>  
                  <tr>
                     <td class="hc_access">Health Facility Access</td>
                    <td>
                    <div class="btn-group hc">
                     <?php 
                      $_data = $office->get_all();
                    ?>
                    <input type="radio" class="hc_access_option hc_access_all" name="hc_access" value="0"
                    <?php if ($_SESSION['type'] == "superadmin" || $_SESSION['type'] == "superreporting") { echo "checked"; }?> <?php if($type_user != '') echo "disabled"; ?>>All<br>
      
                    <?php  
                    $office_id = $permission->get_office_id();
                    if($_data!=false): foreach($_data as $data ): ?>
                       
                          <input type="radio" name="hc_access" class="hc_access" value="<?php echo $data['ID']; ?>"
                             <?php if ( $office_id == $data['ID']): ?>
                             checked="checked"
                              <?php endif ?>
                           <?php if($type_user != '') echo "disabled"; ?>><?php echo $data['area_name']; ?><br>
                           <input type="hidden" name="hc_access" class="hc_access" value="<?php echo $data['ID']; ?>"
                             <?php if ( $office_id == $data['ID']): ?>
                             checked="checked"
                              <?php endif ?>
                           <?php if($type_user != '') echo "disabled"; ?>> 
                   <!--  <input type="hidden" name="hc_access" class="hc_access" value="<?php //echo $data['ID']; ?>"> -->
                    <?php endforeach; endif; ?>
                    </div> 
                    </td> 
                  </tr>  
                  <tr>
                    <td class="consultation_reports">Can Add Health Facility</td>
                    <td>
                    <div class="btn-group add_hc">
                        <input type="radio" name="add_hc" class="can_access_hc_0 can_access" value="0" onclick="return false">Yes<br>
                        <input type="radio" name="add_hc" class="can_access_hc_1 can_access"value="1" onclick="return false">No
                    </div> 
                    </td> 
                  </tr>  
                                    
            </tbody>
          </table> 
          <div class="alert alert-info"><strong></strong></div>
          <input style="margin-top: 20px;" type="submit" class="btn btn-success btn-default btn-submit
          <?php if ($_GET['ID'] == $_SESSION['id']){
            echo "hide";
          } ?>
          " id="btn_submit">
        </form> 
        </div>      
      </div><!--/span-->   
      <!-- && enablea_and_disable_ele($_SESSION['type'], "edit_personal_record", $_SESSION['user']) == false) -->     
    </div>
  </div>

  <?php $permission->script(); ?>
<?php else: ?>
  <?php if ($_SESSION['type'] == 'reporting' || $_SESSION['type'] == 'superreporting'): ?>
    <?php header("Location:?page=reports"); ?>
  <?php else: ?>
  <?php header("Location:?page=dashboard"); ?>
  <?php endif ?>
<?php endif ?>