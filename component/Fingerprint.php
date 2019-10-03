<?php
// error_reporting(1);
class Fingerprint extends DB {
    public $number_of_specimen;
	public function __construct() {
        parent::__construct();
        $this->number_of_specimen = 5;
    }
    public function search_starter() {
        ?>
        <a id="searchClientFingerPrint" type="button"  class="btn btn-success"
              <?php if($_SESSION['type']!='superadmin') {
                 if (enablea_and_disable_ele($_SESSION['type'], "add", $_SESSION['client_section']) == false) { echo "hide"; }
               }else{  echo "hide"; }
               ?>
              style="margin-left: 10px; float: right;"data-toggle="modal" href="#"
            ><i class ="glyphicon glyphicon-search"></i> Search By FingerPrint </a>
        <?php
    }
    public function search_preview() {
        ?>
        <div id = "image_div" class="row hide">
        
            <div class="form-group fingerprint-preview-container">
                <input name="finger_search" id="finger_search" type="text" id="right_side_finger" value="" readonly style = " display: none; border: 0px; font-size: 12px"/>
                <img class="fingerprint-search-container" id="image1" alt="" width="150" height="150" />
                <span id="fingerprint-search-status"></span>
            </div>
            
        </div>
        <?php
    }
    public function register_starter() {
        ?>
            <input class="btn btn-primary" style="text-align: center; font-size: 13px;" type="button" id="fingerprint_start" value="Click this to register finger print !" />
        <?php
    }
    public function register_fields() {
        for($x=1;$x<=$this->number_of_specimen;$x++) {
            ?><input type="hidden" name="fingerprint_<?php echo $x ?>" id="fingerprint_<?php echo $x ?>" /><?php
        }
    }
    public function register_preview() {
        ?>
            <div class="form-group fingerprint-preview-container">
                <span class="fingerprint-preview"></span>
                <span class="fingerprint-percentage"></span>
                <span id="fingerprint-status"></span>
            </div>
        <?php
    }
    public function fpupdate($clientID) {
        $query = "SELECT * FROM tbl_fingerprint WHERE client_id = '{$clientID}'";
		$stmt = $this->query($query,array());
        $_all_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if(count($_all_data) > 0) {
            for($x=1;$x<=$this->number_of_specimen;$x++) {
                $this->update($_POST['fingerprint_'.$x], $_all_data[$x-1]['ID'], $_all_data[$x-1]['client_id'] );
            }
		}
		else {
            $this->fpsave($clientID);
		}
    }
    public function fpsave($clientID) {
        for($x=1;$x<=$this->number_of_specimen;$x++) {
            $this->save([
                'client_id' => $clientID,
                'finger_data' => $_POST['fingerprint_'.$x]
            ], array(), "tbl_fingerprint", "lastInsertId");
        }
    }
    public function clean_data(&$_data) {
        for($x=1;$x<=$this->number_of_specimen;$x++) {
            unset($_data['fingerprint_'.$x]);
        }
    }
    public function scripts() {
        ?>
        <script>
            let passed = false;
            let finger_value = 0;
            let dummy_finger_value = 0;
            let number_of_specimen = <?php echo $this->number_of_specimen ?>;
            
            $(document).ready(function(){
                function ws_send(str) {
					try {
						ws.send(str);
					} catch (err) {
						console.log('Error');
					}
                }
                function connect(host) {
					$('#fingerprint-status').html("Connecting to " + host + " ...");
					try {
						ws = new WebSocket(host); // create the web socket
						$('#fingerprint-status').html('Websocket created')
					} catch (err) {
						$('#fingerprint-status').html('Error on connect');
					}
					ws.onopen = function () {
					    $('#fingerprint-status').html('Connected OK!');
						EnrollTemplate();
					};
					ws.onmessage = function (evt) {
                        var obj = eval("("+evt.data+")");
                        console.log('finger_value', finger_value)
						switch (obj.workmsg) {
							case 1:
								alert("Please Open Device");
								break;
							case 2:
								if (finger_value < number_of_specimen){
                                    if(passed) {
                                        passed = false;
                                        $('.fingerprint-preview').css({opacity: (finger_value/number_of_specimen)})
                                        $('.fingerprint-percentage').html(Math.floor((finger_value/number_of_specimen)*100) + '%')
                                    }
                                    else {
                                        dummy_finger_value = dummy_finger_value+0.2;
                                        $('.fingerprint-preview').css({opacity: (finger_value/number_of_specimen + 0.1)})
                                        $('.fingerprint-percentage').html(Math.floor((dummy_finger_value/number_of_specimen)*100) + '%')
                                    
                                    }
                                    
                                    $('#fingerprint-status').html('Place Right Thumb')
                                }
								document.getElementById("fingerprint_start").disabled = true;
								break;
							case 3:
                                $('#fingerprint-status').html("Lift Finger");
								break;
                            case 4:
                                console.log('No changes on case 4')
                                break;
                            case 5:
                                if (obj.retmsg == 1) {
                                    $('#fingerprint-status').html("Get Template OK");
                                } else {
                                    $('#fingerprint-status').html("Get Template Fail");
                                }
                                break;
							case 6:
								if (obj.retmsg == 1) {
                                    if (obj.data1 != "null") {
                                        finger_value++;
                                        dummy_finger_value = finger_value;
                                        $("#fingerprint_"+finger_value).val( obj.data1 );
                                        $('.fingerprint-preview').css({opacity: (finger_value/number_of_specimen)})
                                        $('.fingerprint-percentage').html(Math.floor((finger_value/number_of_specimen)*100) + '%')
                                            
                                        if(finger_value < number_of_specimen) {
                                            passed=true;
                                            EnrollTemplate();
                                            break;
                                        }
                                        else {
                                            if(document.getElementById("btn_add_client")) {
                                                document.getElementById("btn_add_client").disabled = false;
                                            }
                                            
                                            document.getElementById("fingerprint_start").disabled = true;
                                            $('#fingerprint-status').html("Fingerprint Ready"); 
                                            ws.onclose();
                                            finger_value=0; 
                                        }
									} else {
                                        // $('#fingerprint-status').html("Please Press Again");    
									}
								} else {
									$('#fingerprint-status').html("Enroll Template Fail");
									EnrollTemplate();
								}
								break;
							case 7:
                                console.log('Preview')
								if (obj.image == "null") {
									alert("Please try again.")
								} else {
                                    if(!passed) {
                                        dummy_finger_value = dummy_finger_value+0.2;
                                        $('.fingerprint-preview').css({opacity: (finger_value/number_of_specimen + 0.1)})
                                        $('.fingerprint-percentage').html(Math.floor((dummy_finger_value/number_of_specimen) *100) + '%')
                                    }
								}
								break;
							case 8:
                                $('#fingerprint-status').html("Time Out");
								document.getElementById("fingerprint_start").disabled = false;
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
							$('#fingerprint-status').html("Ready");
						};
                };
                function EnrollTemplate(){
                    $('#fingerprint-status').html('Place Right Thumb')
                    try {
                        var cmd = "{\"cmd\":\"enrol\",\"data1\":\"\",\"data2\":\"\"}";
                        ws.send(cmd);
                        document.getElementById("fingerprint_start").disabled = true;
                    } catch (err) {
                        $('#fingerprint-status').html('Something is wrong')
                    }
                }
                function GetTemplate() {
                    $('#fingerprint-status').html('Place Right Thumb')
                    try {
                        var cmd = "{\"cmd\":\"capture\",\"data1\":\"\",\"data2\":\"\"}";
                        ws.send(cmd);
                    } catch (err) {
                        $('#fingerprint-status').html('Something is wrong')
                    }
                }
                
                $("#fingerprint_start").on('click',function() {
                    if ("WebSocket" in window) {
                        console.log("WebSocket Ready");
                        connect("ws://127.0.0.1:21187/fps");
                    } else {
                        console.log('Browser does not support!');
                    };
                })

                // var hidden = true;
                // var attempt = null;
                // window.result = 0;
                // var result_id = null;
                // var results = [];
                // $("#searchClientFingerPrint").click(function(){
                //     if(hidden == true){
                //         $("#image_div").fadeIn('slow');
                //         $("#image_div").removeClass('hide');
                //         $("#image_div").addClass('show');
                //         hidden = false;
                //     }else if(hidden == false){
                //         $("#image_div").fadeIn('slow');
                //         $("#image_div").removeClass('show');
                //         $("#image_div").addClass('hide');
                //         hidden = true;
                //     }
                //     if ("WebSocket" in window) {
                //         console.log("WebSocket Ready");
                //         search_connect("ws://127.0.0.1:21187/fps");
                //     } else {
                //         console.log('Browser does not support!');
                //     };
                // });
            });
        </script>
        <?php
    }
}

?>