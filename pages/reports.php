<?php header_nav_bar("list-alt", "Reports") ?>

  <div class="container bs-docs-container">
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <div class="row">
          <div class="panel-group">
            <div class="page-header">
              <h1>Reports Management</h1>
            </div>
              <?php
              if( strtotime('now') < strtotime('2018-04-01') ) :
                echo "<h1 style='line-height:1.5; color: grey;'><center>Sorry, reporting will be re-enabled <br />on the 1st of April, 2018.</center></h1>";
              else :
              ?>
              <div class="col-md-6">

                <div class="list-group">
                  <a class="list-group-item" href="?page=client_reports">
                    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-user" style="margin-right: 10px;"></span>Clients</h4>
                    <p class="list-group-item-text">shows client reports by clinic, Health Facility, District and Province.</p>
                  </a>
                  <a class="list-group-item" href="?page=consultation_reports">
                    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-phone-alt" style="margin-right: 10px;"></span>Consultation</h4>
                    <p class="list-group-item-text">shows consultation reports by clinic, Health Facility, District and Province.</p>
                  </a>
                  <a class="list-group-item" href="?page=feeding_reports">
                    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-list-alt" style="margin-right: 10px;"></span>Feeding</h4>
                    <p class="list-group-item-text">Shows Feeding Reports by Clinic, Health Facility, District and Province.</p>
                  </a>
                  <a class="list-group-item" href="?page=hb_level_reports">
                    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-indent-left" style="margin-right: 10px;"></span>HB Level</h4>
                    
                  </a>

                </div>
              </div>
              <div class="col-md-6">
                <div class="list-group">                    
                  <a class="list-group-item" href="?page=coming_soon">
                    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-sort-by-order-alt" style="margin-right: 10px;"></span>Child Count</h4>
                    <!-- <p class="list-group-item-text">Sed et a quis turpis nisi auctor non tempor aenean nec in tristique urna, augue velit, in, nunc!</p> -->
                  </a>
                  <a class="list-group-item" href="?page=coming_soon">
                    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-folder-close" style="margin-right: 10px;"></span>Archive</h4>
                    <!-- <p class="list-group-item-text">Sed et a quis turpis nisi auctor non tempor aenean nec in tristique urna, augue velit, in, nunc!</p> -->
                  </a>
                  <a class="list-group-item" href="?page=additional_reports">
                    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-plus" style="margin-right: 10px;"></span>Additional</h4>
                    <p class="list-group-item-text">Shows start date and end of report and options to select Locations by Clinic, Health Facility, 
                        District and Province and type of report.</p>

                  </a>
                  <a class="list-group-item" href="?page=catchment_reports">
                    <h4 class="list-group-item-heading"><span class="glyphicon glyphicon-plus" style="margin-right: 10px;"></span>Catchment</h4>
                    <p class="list-group-item-text">Shows catchment report.</p>
                  </a>
                </div>
              </div>
              <?php
              endif; 
              ?>
            </div>       
          </div>
      </div><!--/span-->           
    </div>
  </div>

    