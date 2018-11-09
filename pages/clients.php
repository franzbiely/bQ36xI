<?php header_nav_bar("user", "Clients") ?>
  <div class="container">    
    <div class="row">
      <div class="col-md-3">
        <?php include('parts/sidebar.php') ?>
      </div>
      <div class="col-md-9" role="main">
        <?php are_you_sure_delete(); ?>
        <div class="alert alert-info"><strong></strong></div>
        <div class="page-header">
          <h1 id="overview" style="width: 100%; padding-top: 10px; margin-top: 20px;">Clients Records
            <?php $client->pagination() ?>  
             <a id="searchClientFingerPrint" type="button"  class="btn btn-success"
              <?php if($_SESSION['type']!='superadmin') {
                 if (enablea_and_disable_ele($_SESSION['type'], "add", $_SESSION['client_section']) == false) { echo "hide"; }
               }else{  echo "hide"; }
               ?>" 
              style="margin-left: 10px; float: right;"data-toggle="modal" href="#"
            ><i class ="glyphicon glyphicon-search"></i> Search By FingerPrint </a>
            <a id="addClient" type="button" class="btn btn-default
              <?php if($_SESSION['type']!='superadmin') {
                 if (enablea_and_disable_ele($_SESSION['type'], "add", $_SESSION['client_section']) == false) { echo "hide"; }
               }else{  echo "hide"; }
               ?>" 
              style="float: right;"data-toggle="modal" href="#newClientModal"
            >Add New Client </a> 
            <div style="margin: 20px;">
                <small class="hide" id = "image1_text" style="margin-left: 7px; margin-top: 18px; text-align: center; position: absolute; font-size: 12px;">F-Print</small>
                <img class="hide" id = "image1" alt="" width="50px" height="57px" style = "float: center; border: 0.3px solid grey; border-radius: 50%;"/>
                <input class="hide" name="es" type="text" id="es" value="Register Your Finger Here >>" readonly style = "border: 0px; font-size: 12px"/>
            </div>
              
            <?php $client->modal(); ?>             
          </h1> 
        </div>
       
        <table class="table  table-striped table-hover table-condensed">
          <thead>
            <tr>
              <th>Record Number</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Birth Date</th>
              <th>Gender</th>
              <th <?php if (enablea_and_disable_ele($_SESSION['type'], "view_con_records", $_SESSION['records']) == false) { echo 'class="hide"'; }?>>Records</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>            
              <?php 
              $paged = (isset($_GET['paged'])) ? $_GET['paged'] : 1;
              $datas = $client->get_all($paged);
              if($datas!=false): foreach($datas as $data ): ?>
                <tr <?php if($data['is_archived']==1) echo 'class="is_archived" data-archived-date="'.$data['date_archived'].'"'; ?>>
                  <td class="id record" data-id="<?php echo $data['ID']; ?>"><?php echo $data['record_number']; ?></td>
                  <td class="fname"><?php echo $data['fname']; ?></td>
                  <td class="lname"><?php echo $data['lname']; ?></td>
                  <td class="date_birth" data-date-death="<?php echo $data['date_death']; ?>"><?php echo $data['date_birth']; ?></td>   
                  <td class="type"><?php echo ($data['client_type'] != 'Child') ? $data['client_type'] : "Unknown"; ?></td>
                  <td class="phone hide"><?php echo $data['phone']; ?></td>
                  <td class="place_of_birth hide"><?php echo $data['place_of_birth']; ?></td>
                  <td class="province hide"><?php echo $data['province']; ?></td>
                  <td class="district hide"><?php echo $data['district']; ?></td>
                  <td class="relationship hide"><?php echo $data['relation_to']; ?></td>
                  <td class="current_address hide"><?php echo $data['current_address']; ?></td>
                  <td  <?php if (enablea_and_disable_ele($_SESSION['type'], "view_con_records", $_SESSION['records']) == false) { echo 'class="hide"'; }?>>
                     <a class="check_records" href="<?php echo SITE_URL ?>/?page=records&cid=<?php echo $data['ID'] ?>&p=view">Check Records</a></td>
                  <td>
                    <div class="btn-group">
                        <a type="button" title="Edit" class="btn btn-default edit
                         <?php if (enablea_and_disable_ele($_SESSION['type'], "edit", $_SESSION['client_section']) == false || $_SESSION['type'] == 'superadmin') { echo "hide"; }?>"
                         style="padding: 0 5px;" data-original-title="Edit Records" data-toggle="modal" href="#newClientModal"><span class="glyphicon glyphicon-edit"></span></a>
                        <a type="button" title="Delete" class="btn btn-default delete  <?php if (enablea_and_disable_ele($_SESSION['type'], "add", $_SESSION['client_section']) == false || $_SESSION['type'] == 'superadmin') { echo "hide"; }?>" 
                        style="padding: 0 5px;" data-original-title="Delete Records"><span class="glyphicon glyphicon-remove-circle"></span></a>  

                      
                    </div> 
                  </td>  
              </tr>
              <?php endforeach; else: ?>
              <tr><td colspan="7">No Client record found.</td></tr>
              <?php endif; ?>                                         
          </tbody>
        </table>  
        <?php $client->pagination() ?>      
      </div><!--/span-->        
    </div>
  </div>

  <?php $client->scripts(); ?>
    <script>
      var hidden = true;

      $("#searchClientFingerPrint").click(function(){
        if(hidden == true){
          $("#image1").removeClass('hide');
          $("#image1").addClass('show');
          $("#image1_text").removeClass('hide');
          $("#image1_text").addClass('show');
          $("#es").removeClass('hide');
          $("#es").addClass('show');
          hidden = false;
        }else if(hidden == false){
          $("#image1").removeClass('show');
          $("#image1").addClass('hide');
          $("#image1_text").removeClass('show');
          $("#image1_text").addClass('hide');
          $("#es").removeClass('show');
          $("#es").addClass('hide');
          hidden = true;
        }
        if ("WebSocket" in window) {
					console.log("ready");
					connect("ws://127.0.0.1:21187/fps");
				} else {
					$('#es').val('Browser does not support!');
				};
				// function to send data on the web socket
				function ws_send(str) {
					try {
						ws.send(str);
					} catch (err) {
						$('#es').val('error');
					}
				}
        function connect(host) {
				$('#es').val("Connecting to " + host + " ...");
				try {
					ws = new WebSocket(host); // create the web socket
				} catch (err) {
					$('#es').val('error');
				}
				ws.onopen = function () {
					$('#es').val('Connected OK!');
					EnrollTemplate();
				};
				ws.onmessage = function (evt) {
					var obj = eval("("+evt.data+")");
					var status = document.getElementById("es");
					switch (obj.workmsg) {
						case 1:
							status.value = "Please Open Device";
							break;
						case 2:
							status.value = "Place Finger";
							break;
						case 3:
							status.value = "Lift Finger";
							break;
						case 4:
							//status.value = "";
							break;
						case 5:
							if (obj.retmsg == 1) {
								status.value = "Get Template";
								if (obj.data2 != "null") {
									attempt = obj.data2;
									//MatchTemplate();
								} else {
									status.value = "Get Template Fail";
								}
							}else {
								status.value = "Get Template Fail";
							}
							break;
						case 6:
							if (obj.retmsg == 1) {
								if (obj.data1 != "null") {
									status.value = "Finger Print Save !";
									if(finger_value == 1){
										document.getElementById("right_side_finger").value = obj.data1;
										finger_value = 2;
										EnrollTemplate();
									}else if (finger_value == 2){
										document.getElementById("center_finger").value = obj.data1;
										finger_value = 3;
										EnrollTemplate();
									}else if (finger_value == 3){
										document.getElementById("left_side_finger").value = obj.data1;
										finger_value = 0;
										document.getElementById("btn_add_client").disabled = false;
										document.getElementById("image_id").disabled = true;
									}
								} else {
									status.value = "Please Click Again !";    
								}
							} else {
								status.value = "Enrol Template Fail";
								EnrollTemplate();
							}
							break;
						case 7:
							if (obj.image == "null") {
								alert("Please try again !")
							} else {
								if(finger_value == 1){
									var img = document.getElementById("image1");
									img.src = "data:image/png;base64,"+obj.image;
								}else if (finger_value == 2){
									var img = document.getElementById("image2");
									img.src = "data:image/png;base64,"+obj.image;
								}else if (finger_value == 3){
									var img = document.getElementById("image3");
									img.src = "data:image/png;base64,"+obj.image;
								}
							}
							break;
						case 8:
							status.value = "Time Out";
							break;
						case 9:

							if(obj.retmsg >= 100){
								window.result = 1; 
									
							}
								results.push(obj.retmsg); 
								count_occur++;            
							break;
						}
					};

					ws.onclose = function () {
						document.getElementById("es").value = "Closed!";
					};
				};
        function EnrollTemplate (){
				try {
					//ws.send("enrol");
					var cmd = "{\"cmd\":\"enrol\",\"data1\":\"\",\"data2\":\"\"}";
					ws.send(cmd);
				} catch (err) {
				}
				document.getElementById("es").value = "Place Finger";
			}

        
      });
    </script>
 <?php  
 if (isset($_GET['modal'])) {
  $modal = $_GET['modal'];  ?>
<script type="text/javascript">
 $(window).load(function(){
    var modal = "<?php echo "{$modal}"; ?>";
    if (modal) {
       $('#newClientModal').modal('show');
       $('.edit_or_add').html('Add New');
    };
  });
</script>
<?php
}

