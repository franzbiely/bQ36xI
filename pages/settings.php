<?php if ($_SESSION['type'] == 'superadmin'): ?>
<div class="navbar navbar-fixed-top navbar-inverse" role="navigation">
    <div class="container">
      <div class="row">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo SITE_URL ?>/?page=dashboard" title="Susu Mamas | Dashboard">Susu Mamas</a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="<?php echo SITE_URL ?>/?page=reports"><span class="glyphicon glyphicon-cog" style="margin-right: 10px;"></span>Settings</a></li>
          </ul>
        </div><!-- /.nav-collapse -->
      </div><!-- /.row -->
    </div><!-- /.container -->
  </div><!-- /.navbar -->

  <div class="container bs-docs-container">
    <div class="row">
      <div class="col-md-3">
         <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <div class="row">
          <div class="panel-group">
            <div class="page-header">
              <h1>Settings</h1>
            </div>
              <div class="col-md-6">
                <div class="list-group">
                  <a class="list-group-item" href="?page=client_type">
                    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-user" style="margin-right: 10px;"></span>Client Gender</h4>
                    <!-- <p class="list-group-item-text">Sed et a quis turpis nisi auctor non tempor aenean nec in tristique urna, augue velit, in, nunc!</p> -->
                  </a>
                  <a class="list-group-item" href="?page=clinic_type">
                    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-home" style="margin-right: 10px;"></span>Clinic Type</h4>
                    <!-- <p class="list-group-item-text">Sed et a quis turpis nisi auctor non tempor aenean nec in tristique urna, augue velit, in, nunc!</p> -->
                  </a>
                  <a class="list-group-item" href="?page=feeding_type">
                    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-cutlery" style="margin-right: 10px;"></span>Feeding Type</h4>
                    <!-- <p class="list-group-item-text">Sed et a quis turpis nisi auctor non tempor aenean nec in tristique urna, augue velit, in, nunc!</p> -->
                  </a>
                  <a class="list-group-item" href="?page=report_settings">
                    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-list-alt" style="margin-right: 10px;"></span>Notifications</h4>
                    <!-- <p class="list-group-item-text">Sed et a quis turpis nisi auctor non tempor aenean nec in tristique urna, augue velit, in, nunc!</p> -->
                  </a>
                </div>
              </div>
                <div class="col-md-6">
                  <div class="list-group">                    
                    <a class="list-group-item hide" href="?page=followup_type">
                      <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-pushpin" style="margin-right: 10px;"></span>Followup Type</h4>
                      <!-- <p class="list-group-item-text">Sed et a quis turpis nisi auctor non tempor aenean nec in tristique urna, augue velit, in, nunc!</p> -->
                    </a>
                    <a class="list-group-item" href="?page=visit_type">
                      <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-briefcase" style="margin-right: 10px;"></span>Visit Type</h4>
                      <!-- <p class="list-group-item-text">Sed et a quis turpis nisi auctor non tempor aenean nec in tristique urna, augue velit, in, nunc!</p> -->
                    </a>
                    <a class="list-group-item" href="?page=user_type">
                      <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-th-large" style="margin-right: 10px;"></span>User Type</h4>
                      <!-- <p class="list-group-item-text">Sed et a quis turpis nisi auctor non tempor aenean nec in tristique urna, augue velit, in, nunc!</p> -->
                    </a>
                    <a class="list-group-item" href="?page=permission_schema">
                      <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-random" style="margin-right: 10px;"></span>Permission Schema</h4>
                      <!-- <p class="list-group-item-text">Sed et a quis turpis nisi auctor non tempor aenean nec in tristique urna, augue velit, in, nunc!</p> -->
                    </a>
                    <!--  <a class="list-group-item" href="?page=add_new_hc">
                      <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-home" style="margin-right: 10px;"></span>Health Center</h4>
                      <p class="list-group-item-text">Sed et a quis turpis nisi auctor non tempor aenean nec in tristique urna, augue velit, in, nunc!</p>
                    </a> -->

                    <!-- 
                    FOR FUTURE VERSION
                    <a class="list-group-item" href="?page=general_settings">
                      <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-cog" style="margin-right: 10px;"></span>General Settings</h4>
                      <p class="list-group-item-text">Sed et a quis turpis nisi auctor non tempor aenean nec in tristique urna, augue velit, in, nunc!</p>
                    </a> -->
                  </div>
                </div>
              </div>       
            </div>
        </div><!--/span-->           
      </div>
    </div>
    <?php else: ?>
  <?php if ($_SESSION['type'] == 'reporting' || $_SESSION['type'] == 'superreporting'): ?>
    <?php header("Location:?page=reports"); ?>
  <?php else: ?>
  <?php header("Location:?page=dashboard"); ?>
  <?php endif ?>
<?php endif ?>