
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
               ?>
              style="margin-left: 10px; float: right;"data-toggle="modal" href="#"
            ><i class ="glyphicon glyphicon-search"></i> Search By FingerPrint </a>
            <a id="addClient" type="button" class="btn btn-default
              <?php if($_SESSION['type']!='superadmin') {
                  if (enablea_and_disable_ele($_SESSION['type'], "add", $_SESSION['client_section']) == false) { echo "hide"; }
               }else{  echo "hide"; }
               ?>" 
              style="float: right;"data-toggle="modal" href="#newClientModal"
            >Add New Client </a> 
            <div  id = "image_div" class="row hide">
              <div class="searching-left">
                  <input name="finger_search" id="finger_search" type="text" id="right_side_finger" value="" readonly style = " display: none; border: 0px; font-size: 12px"/>
                  <img class="" id = "image1" alt="" width="80px" height="87px" style = "margin-left: 8px; float: center; border: 0.3px solid grey; border-radius: 50%;"/>
              </div>
              <div class="searching-right">
                <input class="" name="ess" type="text" id="ess" value="Register Your Finger Here >>" readonly />
              </div>
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
        <?php $client->pagination(); ?>      
      </div><!--/span-->        
    </div>
  </div>

  <?php $client->scripts(); ?>
    <script>
      var hidden = true;
      var attempt = null;
      window.result = 0;
      var result_id = null;
      var results = [];
      var count_occur = -1;
      var client_id;

      $("#searchClientFingerPrint").click(function(){

        if(hidden == true){
          $("#image_div").fadeIn('slow');
          $("#image_div").removeClass('hide');
          $("#image_div").addClass('show');
          hidden = false;
        }else if(hidden == false){
          $("#image_div").fadeIn('slow');
          $("#image_div").removeClass('show');
          $("#image_div").addClass('hide');
          hidden = true;
        }
        if ("WebSocket" in window) {
					console.log("ready");
					connect("ws://127.0.0.1:21187/fps");
				} else {
					$('#ess').val('Browser does not support!');
				};
				function ws_send(str) {
					try {
						ws.send(str);
					} catch (err) {
						$('#ess').val('error');
					}
				}
        function connect(host) {
  				  $('#ess').val("Connecting to " + host + " ...");
    				try {
    					ws = new WebSocket(host); // create the web socket
              console.log('Websocket created')
    				} catch (err) {
    					$('#ess').val('error');
    				}
  				ws.onopen = function () {
  					$('#ess').val('Connected OK!');
  					EnrollTemplate1();
  				};
  				ws.onmessage = function (evt) {
  					var obj = eval("("+evt.data+")");
  					var status = document.getElementById("ess");
  					switch (obj.workmsg) {
  						case 1:
  							status.value = "Please Open Device";
  							break;
  						case 2:
  							status.value = "Place Right Index Finger";
  							break;
  						case 3:
  							status.value = "Lift Finger";
  							break;
  						case 5:
  							if (obj.retmsg == 1) {
                  results = [];
  								status.value = "Searching Client...!";
  								if (obj.data2 != "null") {
  									attempt = obj.data2;
  									MatchTemplate();
  								} else {
  									status.value = "Get Template Fail";
  								}
  							}else {
  								status.value = "Get Template Fail";
  							}
  							break;
  						case 7:
  							if (obj.image == "null") {
  								alert("Please try again !")
  							} else {
  									var img = document.getElementById("image1");
  									img.src = "data:image/png;base64,"+obj.image;
  							}
  							break;
  						case 8:
  							status.value = "Time Out";
                var img = document.getElementById("image1");
                img.src = "";
                setInterval(function (){
                    EnrollTemplate1();
                  },2000);
  							break;
  						case 9:
  							if(obj.retmsg >= 100){
  								window.result = 1; 
  							}
                
  								results.push(obj.retmsg); 
                  console.log('result called', results)
  								count_occur++;            
  							break;
  						}
  					};
  					ws.onclose = function () {
  						document.getElementById("ess").value = "Closed!";
  					};
  				};
			  });
			function EnrollTemplate1(){
        console.log('EnrollTemplate1');
				try {
          var cmd = "{\"cmd\":\"capture\",\"data1\":\"\",\"data2\":\"\"}";
					ws.send(cmd);
          document.getElementById("ess").value = "Place Right Index Finger";
				} catch (err) {
          console.log('Something is wrong', err);
        }
			}
      function MatchTemplate () {
        $.ajax({
          url: '/ajax.php?capture=capture_data',
          type: 'get',
          dataType: 'json',
          success:function(response){
            content = response;
            $.each(response, function(key, value){
              var str = value["finger_data"];
              var concat = "";
              var count = 0;
              var client_id = value["client_id"];
              for( x = 0; x < str.length; x++){
                if(str.charAt(x) != " "){
                  concat += str.charAt(x);
                }else{
                  concat += '+';
                }
              }
              try {
                var cmd = "{\"cmd\":\"setdata\",\"data1\":\"" + attempt + "\",\"data2\":\""  + "\"}";
                ws.send(cmd);
                var cmd = "{\"cmd\":\"setdata\",\"data1\":\"" + "\",\"data2\":\"" + concat + "\"}";
                ws.send(cmd);
                var cmd = "{\"cmd\":\"match\",\"data1\":\"\",\"data2\":\"\"}";
                  ws.send(cmd);
                  console.log('Result', window.result);
                } catch (err) {}	
            });
          }
        });
        timer = setInterval(function(){
          if(window.result == 1){
            console.log('Result success')
            window.result = 0;
            let prevMatch = 0;
            let prevIndex = -1;
            for(x = 0; x < results.length; x++){
              if(prevMatch < results[x]){
                prevMatch = results[x];
                prevIndex = x;
              }
            }
            window.location.href = "?page=records&cid=" +content[results.indexOf(100)].client_id+ "&p=view";
            clearInterval(timer);
          }else{
            alert("The system does not recognize the fingerprint. You may need to TRY AGAIN or register your fingerprint.");
            clearInterval(timer);
            EnrollTemplate1();
          }           
        }, 500);  
      }
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

