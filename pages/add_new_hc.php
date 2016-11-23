<?php header_nav_bar("home", "Add new HC","settings") ?>

  <div class="container">    
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <div class="alert alert-info"><strong></strong></div>
        <div class="page-header">
          <h1 id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;">Add Health Center         
        		 
        		<!-- Hide, don't delete this -->
        		<!-- <a id="addClient" type="button" class="btn btn-default" style="float: right;"data-toggle="modal" href="#newClientModal">Add New Health Center</a>  -->
        		             <!-- Modal -->
            <?php //$office->modal() ?>
          </h1> 
        </div>
       <div class="col-md-6">
          <form role="form" action="" method="post">
          <input type="hidden" name="class" value="office" />
          <input type="hidden" name="func" value="add" />
          <div class="form-group">
            <label for="area_name"> Health Facility</label>
            <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="area_name" name="area_name" placeholder="Enter Health Center Name" required>
          </div>               
          <div class="form-group">
            <label for="province_name"> Health Facility Primary Address</label>
            <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="address1" name="office_address" placeholder="Enter Health Center Primary Address" required>
          </div>       
          <?php /*                       
          <div class="form-group">
            <label for="office_description">Office Secondary Address</label>
            <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="address2" placeholder="Enter Office Secondary Address" name="address2">
          </div> 
          */ ?>
          <div class="form-group">
            <label for="office"> Health Facility Primary Phone Number</label>
            <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="phone1" name="contact" placeholder="Enter Health Center Primary Phone Number" >
          </div>
          <div class="form-group">
            <label for="area_name">Province</label>
            <select class="form-control" name="province" id="province" onchange="javascript: populate_districts(this)" required>
              <option>Select Province</option>
              <?php                 
              foreach($office->get_province() as $data ){ 
                ?><option value="<?php echo $data['ID']; ?>"><?php echo $data['area_name']; ?></option><?php echo "\n";
              }
              ?>  
            </select>
          </div> 
           <div class="div-district">
            <div class="form-group district-form" id="district-form">
            <label for="area_name">District</label>
            <select class="form-control district_select" name="district" id="district" required>
             
              <div class="district_option">
                 <option>Select District</option>
                <?php                 
               /* foreach($district->get_all() as $data ){ 
                  ?><option value="<?php echo $data['ID']; ?>"><?php echo $data['area_name']; ?></option><?php echo "\n";
                }*/
                ?>  
              </div>
            </select>
            <div class="alert alert-warning no-distirct"><strong></strong></div>  
          </div> 
          </div> 
          <?php /*  
          <div class="form-group">
            <label for="office">Office Secondary Phone Number</label>
            <input type="text" autocapitalize="off" autocorrect="off" class="form-control" id="phone1" name="phone2" placeholder="Enter Office Secondary Phone Number">
          </div>  
          */ ?>                                                                                  
          <input style="margin-top: 20px;" type="submit" class="btn btn-success btn-default">
        </form>
       </div>       
      </div><!--/span-->        
    </div>
  </div>

  <?php $office->scripts(); ?>
