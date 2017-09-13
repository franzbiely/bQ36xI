<?php header_nav_bar("dashboard", "Dashboard") ?>

  <div class="container bs-docs-container">
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <div class="row">
          <div class="panel-group">
            <div class="page-header">
              <h1>Select Destination</h1>
            </div>
              <div class="col-md-6">
                <div class="list-group">
                  <?php if ($_SESSION['type'] == 'superadmin'): ?>
                      <a class="list-group-item <?php if (enablea_and_disable_ele($_SESSION['type'], "view", $_SESSION['province']) == false) { echo "hide"; }?>" href="?page=province">
                      <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-plane" style="margin-right: 10px;"></span>Province</h4>
                      <p class="list-group-item-text">This sections allows you to add a new Province for the new clinic.</p>
                    </a> 
                     <a class="list-group-item" href="?page=offices">
                        <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-globe" style="margin-right: 10px;"></span>Health Facility</h4>
                        <p class="list-group-item-text">This section allows you to add a new Susu Mamas Health Facility.</p>
                      </a>
                    <?php else: ?>
                      <a class="list-group-item <?php if (enablea_and_disable_ele($_SESSION['type'], "view_current_hc", $_SESSION['client_section']) == false) { echo "hide"; }?>" 
                        href="?page=clients"> <h4 class="list-group-item-heading">
                        <span class="glyphicon glyphicon-user" style="margin-right: 10px;"></span>Clients</h4>
                        <p class="list-group-item-text">This section allows you to add, delete and update clients records and consultation records.</p>
                      </a>
                      <a class="list-group-item <?php if ($_SESSION['type'] == 'superadmin') { echo "hide"; }?>" href="?page=reports">
                      <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-list-alt" style="margin-right: 10px;"></span>Reports</h4>
                      <p class="list-group-item-text">This sections allows to your generate reports. Reports for end of the month, 
                        feeding report, consultation reports, archive, additional reports and etc.</p>
                    </a>
					 
				
                  <?php endif ?>
				  <?php if ($_SESSION['type'] == 'admin'): ?> 
				  	<a class="list-group-item" href="?page=catchment">
                        <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-globe" style="margin-right: 10px;"></span>Manage Catchment Areas</h4>
                        <p class="list-group-item-text">This section allows you to manage catchment areas.</p>
                    </a>
				  <?php endif ?>
                </div>
              </div>
                <div class="col-md-6">
                  <div class="list-group">  
                    <?php if ($_SESSION['type'] == 'superadmin'): ?>
                      <a class="list-group-item <?php if (enablea_and_disable_ele($_SESSION['type'], "view", $_SESSION['district']) == false) { echo "hide"; }?>
                      " href="?page=districts">
                      <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-send" style="margin-right: 10px;"></span>Districts</h4>
                      <p class="list-group-item-text">This section allows you to add new districts for the new clinic.</p>
                    </a>
                      <a class="list-group-item" href="?page=settings">
                        <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-cog" style="margin-right: 10px;"></span>Settings</h4>
                        <p class="list-group-item-text">This section allows you to add a new client gender, feeding type, visiting type, and clinic type.</p>
                      </a>
                    <?php else: ?>
                       <a class="list-group-item <?php if (enablea_and_disable_ele($_SESSION['type'], "view", $_SESSION['clinic']) == false) { echo "hide"; }?>" href="?page=clinics">
                        <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-home" style="margin-right: 10px;"></span>Clinics</h4>
                        <p class="list-group-item-text">This section allows you to include a new clinic.</p>
                      </a>
                      <a class="list-group-item" href="?page=offices">
                        <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-globe" style="margin-right: 10px;"></span>Health Facility</h4>
                        <p class="list-group-item-text">This section allows you to add a new Susu Mamas Health Facility.</p>
                      </a>
                     <!--  <a class="list-group-item" href="?page=settings">
                        <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-cog" style="margin-right: 10px;"></span>Settings</h4>
                        <p class="list-group-item-text">This section allows you to add a new client gender, feeding type, visiting type, and clinic type.</p>
                      </a> -->
                    <?php endif ?>
                  </div>
                </div>
              </div>       
            </div>
        </div><!--/span-->           
      </div>
    </div>