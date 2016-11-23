<?php header_nav_bar("cog", "General Settings", "settings") ?>

  <div class="container">    
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <form action="">
        <div class="col-md-9" role="main">
          <div class="page-header">
            <h1 id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;">General Settings
              <input type="submit" class="btn btn-primary" value="Save Settings" style="float:right" />   
            </h1> 
            
          </div>
          <div class="tblcontainer tblgeneralsettings" data-type="feeding">           
              <div class="form_group">
                <div class="col-xs-3">
                  <label>Display Count : </label>
                </div>
                <div class="col-xs-9">
                  <input class="form-control" type="text" name="display_count" />
                </div>
              </div>
          </div>      
        </div><!--/span-->
      </form>         
    </div>
  </div>

  <?php $type->scripts(); ?>
