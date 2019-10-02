<?php
// error_reporting(1);
class Fingerprint extends DB {
    public $number_of_specimen;
	public function __construct() {
        parent::__construct();
        $this->number_of_specimen = 6;
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
            let finger_value = 1;
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
                        console.log(obj.workmsg)
						switch (obj.workmsg) {
							case 1:
								alert("Please Open Device");
								break;
							case 2:
								if (finger_value < number_of_specimen){
                                    $('#fingerprint-status').html('Press')
								}
								document.getElementById("fingerprint_start").disabled = true;
								break;
							case 3:
                                $('#fingerprint-status').html("Press and Lift");
								break;
							case 6:
                                console.log('on case 6', obj.retmsg)
								if (obj.retmsg == 1) {
									if (obj.data1 != "null") {
                                        if(finger_value < number_of_specimen) {
                                            $('.fingerprint-preview').css({opacity: (finger_value/number_of_specimen)})
                                            $('.fingerprint-percentage').html(Math.floor((finger_value/number_of_specimen)*100) + '%')
                                        }
                                        if(finger_value <= number_of_specimen) {
                                            EnrollTemplate();
                                            $("#fingerprint_"+finger_value).val( obj.data1 );
                                            finger_value++;
                                            break;
                                        }
                                        else {
                                            $('.fingerprint-preview').css({opacity: 1})
                                            $('.fingerprint-percentage').html('100%')
                                            document.getElementById("btn_add_client").disabled = false;
                                            document.getElementById("fingerprint_start").disabled = true;
                                            $('#fingerprint-status').html("Fingerprint Ready"); 
                                            ws.onclose(); 
                                        }
									} else {
										$('#fingerprint-status').html("Please Press Again");    
									}
								} else {
									$('#fingerprint-status').html("Enroll Template Fail");
									EnrollTemplate();
								}
								break;
							case 7:
								if (obj.image == "null") {
									alert("Please try again.")
								} else {
                                    // console.log('on case 7', obj.image)
                                    // $("#fingerprint_"+finger_value).val( obj.data1 );
                                            
									// if(finger_value == 1){
									// 	var img = document.getElementById("image11");
									// 	img.src = "data:image/png;base64,"+obj.image;
									// }else if (finger_value == 2){
									// 	var img = document.getElementById("image2");
									// 	img.src = "data:image/png;base64,"+obj.image;
									// }else if (finger_value == 3){
									// 	var img = document.getElementById("image3");
									// 	img.src = "data:image/png;base64,"+obj.image;
									// }
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
							// $('#fingerprint-status').html("Closed!");
						};
                };
                function EnrollTemplate(){
                    $('#fingerprint-status').html('Place finger')
                    try {
                        var cmd = "{\"cmd\":\"enrol\",\"data1\":\"\",\"data2\":\"\"}";
                        ws.send(cmd);
                        document.getElementById("fingerprint_start").disabled = true;
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
            });
        </script>
        <?php
    }
}

?>