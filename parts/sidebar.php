        <div class="bs-sidebar hidden-print affix" role="complementary" style="top: 45px;">
          <div class="panel-group" id="accordion">
            <h4><a style="color: #2c3e50; text-decoration: none; font-weight: bold; pointer-events: none;">Welcome, 
              <?php echo $_SESSION['username'];?> <small>(<?php echo $_SESSION['type'] ?>)</small>
            </br>
            <?php if ($_SESSION['type'] != 'superadmin' && $_SESSION['type'] != 'superreporting'): ?>
              <?php echo $_SESSION['area_name']; ?> Health Facility
            <?php endif ?>
           </a></h4>   
            <a type="button" class="btn btn-primary" style="width: 100%;" href="<?php echo SITE_URL ?>/?c=user&f=logout">Log Out</a>
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                 <!--  <a class="accordion" data-parent="#accordion" style="text-decoration: none; pointer-events: none;">
                    <span class="glyphicon glyphicon-forward"></span>Quick Search
                  </a> -->
                </h4>
              </div>

              <div class="panel-body">
               <div class="col-lg-6" style="width: 100%; padding: 0">
                <div class="input-group box_quick_search">
                  <form name="frmsearch" id="frmsearch" method="POST" action="?page=search">

                    <input type="text" name="quick_search" class="form-control" placeholder="Type your search here.."
                     <?php if ($_SESSION['type'] != 'superadmin' && $_SESSION['type'] != 'superreporting' && $_SESSION['type'] != 'reporting'): ?>
                        <?php if (enablea_and_disable_ele($_SESSION['type'], "quick_search", $_SESSION['search_client'])) {
                        echo "enabled";
                        }else{ echo "disabled"; } 
                        /* make element disabled or enabled based on permission access. */
                        ?>
                      <?php else: ?>
                        <?php echo 'disabled'; ?>
                     <?php endif ?>
                      >
                    <span class="input-group-btn">
                      <button type="submit" class="btn btn-default btnsubmit btn_submit_quick" onclick="$(this).closest('form').submit();"
                        <?php if ($_SESSION['type'] != 'superadmin' && $_SESSION['type'] != 'superreporting' && $_SESSION['type'] != 'reporting'): ?>
                        <?php if (enablea_and_disable_ele($_SESSION['type'], "quick_search", $_SESSION['search_client'])) {
                        echo "enabled";
                        }else{ echo "disabled"; } 
                        /* make element disabled or enabled based on permission access. */
                        ?>
                      <?php else: ?>
                        <?php echo 'disabled'; ?>
                     <?php endif ?>
                          >
                        <span class="glyphicon glyphicon-search"></span>
                      </button>
                    </span>
                  </form>
                  </div><!-- /input-group -->
                </div><!-- /.col-lg-6 -->
              </div>
            </div>
            <a href="?page=clients&modal=add" class="btn btn-default addNewCli" style="width: 100%;" 
               <?php if ($_SESSION['type'] != 'superadmin' && $_SESSION['type'] != 'superreporting' && $_SESSION['type'] != 'reporting'): ?>
                  <?php if (enablea_and_disable_ele($_SESSION['type'], "quick_search", $_SESSION['search_client'])) {
                  echo "enabled";
                  }else{ echo "disabled"; } 
                  /* make element disabled or enabled based on permission access. */
                  ?>
                <?php else: ?>
                  <?php echo 'disabled'; ?>
               <?php endif ?>
            >Add Client</a>
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" style="text-decoration: none; pointer-events: none;">
                    <span class="glyphicon glyphicon-bookmark"></span>Advanced Search
                  </a>
                </h4>
              </div>
              <div id="collapseThree" class="panel-collapse">
                <div class="panel-body">
                  <form method="POST" action="?page=search">
                    <div class="form-group">
                      <input type="text" class="form-control" id="fname" name="fname" placeholder="Enter First Name"
                        <?php if ($_SESSION['type'] != 'superadmin' && $_SESSION['type'] != 'superreporting' && $_SESSION['type'] != 'reporting'): ?>
                        <?php if (enablea_and_disable_ele($_SESSION['type'], "quick_search", $_SESSION['search_client'])) {
                        echo "enabled";
                        }else{ echo "disabled"; } 

                        /* make element disabled or enabled based on permission access. */
                        ?>
                      <?php else: ?>
                        <?php echo 'disabled'; ?>
                     <?php endif ?>
                      >
                    </div>
                    <div class="form-group">
                      <input type="text" class="form-control" id="lname" name="lname" placeholder="Enter Last Name"
                       <?php if ($_SESSION['type'] != 'superadmin' && $_SESSION['type'] != 'superreporting' && $_SESSION['type'] != 'reporting'): ?>
                        <?php if (enablea_and_disable_ele($_SESSION['type'], "quick_search", $_SESSION['search_client'])) {
                        echo "enabled";
                        }else{ echo "disabled"; } 
                        /* make element disabled or enabled based on permission access. */
                        ?>
                      <?php else: ?>
                        <?php echo 'disabled'; ?>
                     <?php endif ?>
                      >
                    </div>
                    <div class="form-group">
                      <input type="text" class="form-control" id="recnum" name="record_number" placeholder="Enter Record Number"
                      <?php if ($_SESSION['type'] != 'superadmin' && $_SESSION['type'] != 'superreporting' && $_SESSION['type'] != 'reporting'): ?>
                        <?php if (enablea_and_disable_ele($_SESSION['type'], "quick_search", $_SESSION['search_client'])) {
                        echo "enabled";
                        }else{ echo "disabled"; } 
                        /* make element disabled or enabled based on permission access. */
                        ?>
                      <?php else: ?>
                        <?php echo 'disabled'; ?>
                     <?php endif ?>
                        >
                    </div>
                    <div class="form-group">
                      <select class="form-control" name="clinicloc"
                      <?php if ($_SESSION['type'] != 'superadmin' && $_SESSION['type'] != 'superreporting' && $_SESSION['type'] != 'reporting'): ?>
                        <?php if (enablea_and_disable_ele($_SESSION['type'], "quick_search", $_SESSION['search_client'])) {
                        echo "enabled";
                        }else{ echo "disabled"; } 
                        /* make element disabled or enabled based on permission access. */
                        ?>
                      <?php else: ?>
                        <?php echo 'disabled'; ?>
                     <?php endif ?>
                        >
                        <option value="">Choose Clinic Location</option>
                        <?php                 
                        foreach($clinic->get_all() as $data ){ 
                          ?><option value="<?php echo $data['ID']; ?>"><?php echo $data['clinic_name']; ?></option><?php echo "\n";
                        }
                        ?>  
                      </select>
                    </div>
                    <div class="form-group">
                      <select class="form-control" name="district"
                       <?php if ($_SESSION['type'] != 'superadmin' && $_SESSION['type'] != 'superreporting' && $_SESSION['type'] != 'reporting'): ?>
                        <?php if (enablea_and_disable_ele($_SESSION['type'], "quick_search", $_SESSION['search_client'])) {
                        echo "enabled";
                        }else{ echo "disabled"; } 
                        /* make element disabled or enabled based on permission access. */
                        ?>
                      <?php else: ?>
                        <?php echo 'disabled'; ?>
                     <?php endif ?>
                        >
                        <option value="">Choose District</option>
                        <?php                 
                          foreach($district->get_all() as $data ){ 
                            ?><option value="<?php echo $data['ID']; ?>"><?php echo $data['area_name']; ?></option><?php echo "\n";
                          }
                          ?>
                      </select>
                    </div>      
                    <div class="form-group">
                      <select class="form-control" name="client_type"
                        <?php if ($_SESSION['type'] != 'superadmin' && $_SESSION['type'] != 'superreporting' && $_SESSION['type'] != 'reporting'): ?>
                        <?php if (enablea_and_disable_ele($_SESSION['type'], "quick_search", $_SESSION['search_client'])) {
                        echo "enabled";
                        }else{ echo "disabled"; } 
                        /* make element disabled or enabled based on permission access. */
                        ?>
                      <?php else: ?>
                        <?php echo 'disabled'; ?>
                     <?php endif ?>
                        >
                        <option value="">Choose Client Gender</option>
                        <?php $_data = $type->get_all('client');
                        if($_data!=false): foreach($_data['value'] as $data ): ?>
                          <option value="<?php echo $data ?>"><?php echo $data ?></option>  
                        <?php endforeach; endif; ?>
                      </select>
                    </div>           
                    <input type="submit" value="Search" style="width: 100%; margin-top: 5px;" class="btn btn-success btnsubmit btn_submit_search" name="search" 
                       <?php if ($_SESSION['type'] != 'superadmin' && $_SESSION['type'] != 'superreporting' && $_SESSION['type'] != 'reporting'): ?>
                        <?php if (enablea_and_disable_ele($_SESSION['type'], "quick_search", $_SESSION['search_client'])) {
                        echo "enabled";
                        }else{ echo "disabled"; } 
                        /* make element disabled or enabled based on permission access. */
                        ?>
                      <?php else: ?>
                        <?php echo 'disabled'; ?>
                     <?php endif ?>
                        />
                  </form>
                </div>
              </div>
            </div>
            <?php $client->scripts(); ?>
          </div>              
        </div>