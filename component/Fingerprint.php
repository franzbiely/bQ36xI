<?php
// error_reporting(1);
class Fingerprint extends DB {
    public $number_of_specimen;
    public $number_of_batch;
	public function __construct() {
        parent::__construct();
        $this->number_of_specimen = 5;
        $this->number_of_batch = 5; // keep this as 1 always, if this will be changed, you must change the fetch to fetchAll below in getbatch(), and other else. please trace.
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
                <img class="fingerprint-search-container" id="fingerprint-search-preview" alt="" width="150" height="150" />
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
            ?><input type="hidden" class="fingerprint_fields" name="fingerprint_<?php echo $x ?>" id="fingerprint_<?php echo $x ?>" /><?php
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
            let number_of_batch = <?php echo $this->number_of_batch ?>;
            let ws;
            let x = 0;
            let checked_cid = 0;
            // Search purpose 
            var hidden = true;
            var attempt = null;
            let fingerprint_type = '';
            window.recordfound = false;
            $(document).ready(function () {
                if ("WebSocket" in window) {
                    connect("ws://127.0.0.1:21187/fps");
                } else {
                    console.log('Browser does not support!');
                };
                $("#fingerprint_start").on('click', function () {
                    EnrollTemplate();
                })
                $("#searchClientFingerPrint").on('click',function () {
                    if (hidden == true) {
                        $("#image_div").fadeIn('slow');
                        $("#image_div").removeClass('hide');
                        $("#image_div").addClass('show');
                        $(".fingerprint-search-container").removeAttr('src')
                        hidden = false;
                        fingerprint_type = 'search'
                        GetTemplate();
                    } else if (hidden == false) {
                        $("#image_div").fadeIn('slow');
                        $("#image_div").removeClass('show');
                        $("#image_div").addClass('hide');
                        hidden = true;
                    }
                });
            });
            function connect(host) {
                $('#fingerprint-status').html("Connecting to " + host + " ...");
                try {
                    if(!ws || ws.readyState === ws.CLOSED) {
                        ws = new WebSocket(host); // create the web socket
                        $('#fingerprint-status').html('Websocket created')
                    }
                } catch (err) {
                    $('#fingerprint-status').html('Error on connect');
                }
                ws.onopen = function () {
                    $('#fingerprint-status, #fingerprint-search-status').html('Connected OK!');
                }
                ws.onmessage = function(evt) {
                    var obj = eval("(" + evt.data + ")");
                    const statusDOM = $('#fingerprint-status, #fingerprint-search-status');
                    console.log(obj.workmsg)
                    switch (obj.workmsg) {
                        case 1:
                            statusDOM.html("Please Open Device");
                            break;
                        case 2:
                            if(fingerprint_type!=="search"){
                                if (finger_value < number_of_specimen) {
                                    if (passed) {
                                        passed = false;
                                        $('.fingerprint-preview').css({ opacity: (finger_value / number_of_specimen) })
                                        $('.fingerprint-percentage').html(Math.floor((finger_value / number_of_specimen) * 100) + '%')
                                    }
                                    else {
                                        $('.fingerprint-preview').css({ opacity: (finger_value / number_of_specimen + 0.1) })
                                    }
                                }
                                document.getElementById("fingerprint_start").disabled = true;
                            }
                            statusDOM.html('Place Right Index Finger')
                            break;
                        case 3:
                            $('.fingerprint-preview').css({ opacity: (finger_value / number_of_specimen) })
                            $('.fingerprint-percentage').html(Math.floor((finger_value / number_of_specimen) * 100) + 1 + '%')
                            statusDOM.html("Lift Finger");
                            break;
                        case 4:
                            console.log('No changes on case 4')
                            break;
                        case 5:
                            if (obj.retmsg == 1) {
                                results = [];
                                $('#fingerprint-search-status').html("Searching Client...!");
                                
                                if(obj.data1 != 'null') {
                                    attempt = obj.data1;
                                }
                                if (obj.data2 != "null") {
                                    attempt = obj.data2;
                                }
                                MatchTemplate(x);
                            } else {
                                statusDOM.html("Get Template Fail");
                            }
                            break;
                        case 6:
                            if (obj.retmsg == 1) {
                                if (obj.data1 != "null") {
                                    finger_value++;
                                    dummy_finger_value = finger_value;
                                    $("#fingerprint_" + finger_value).val(obj.data1);
                                    $('.fingerprint-preview').css({ opacity: (finger_value / number_of_specimen) })
                                    $('.fingerprint-percentage').html(Math.floor((finger_value / number_of_specimen) * 100) + '%')
                                    if (finger_value < number_of_specimen) {
                                        passed = true;
                                        EnrollTemplate();
                                        break;
                                    }
                                    else {
                                        if (document.getElementById("btn_add_client")) {
                                            document.getElementById("btn_add_client").disabled = false;
                                        }

                                        document.getElementById("fingerprint_start").disabled = true;
                                        $('#fingerprint-status').html("Fingerprint Ready");
                                        ws.close();
                                        finger_value = 0;
                                    }
                                }
                            } else {
                                $('#fingerprint-status').html("Enroll Template Fail");
                                EnrollTemplate();
                            }
                            break;
                        case 7:
                        console.log('ON 7', obj.image)
                            if (obj.image != "null") {
                                dummy_finger_value = dummy_finger_value + 0.2;
                                $('.fingerprint-preview').css({ opacity: (finger_value / number_of_specimen + 0.1) })
                                $('.fingerprint-percentage').html(Math.floor((finger_value / number_of_specimen) * 100) + 1 + '%')
                                var img = document.getElementById("fingerprint-search-preview");
                                img.src = "data:image/png;base64,"+obj.image;
                            }
                            break;
                        case 8:
                            console.log('timeout')
                            statusDOM.html("Time Out");
                            $('.fingerprint-search-container').removeAttr('src')
                            document.getElementById("fingerprint_start").disabled = false;
                            setTimeout(function() {
                                $('#searchClientFingerPrint').trigger('click')
                            }, 1500)
                            break;
                        case 9:
                            console.log('I am here', obj.retmsg)
                            if (obj.retmsg >= 60) {
                                console.log('Record Found. Redirecting... please wait..')
                                window.recordfound = true;
                                console.log('case 9 after window.recordfound=true')
                                recordFound(statusDOM)
                                console.log('case 9 after recordFound')
                            }
                            else {
                                console.log(obj.retmsg)
                            }
                            break;
                    }
                }
                ws.onclose = function() {
                    window.recordfound = false;
                    $('#fingerprint-status, #fingerprint-search-status').html("Closed!");
                }
            };
            function recordFound(statusDOM) {
                statusDOM.html("Record Found. Redirecting... please wait..");
                window.recordFound = false;
                setTimeout(function() {
                    window.location.href = "?page=records&cid=" +checked_cid+ "&p=view";
                }, 1000)
            }
            function EnrollTemplate() {
                $('#fingerprint-status').html('Place Right Index Finger')
                try {
                    var cmd = "{\"cmd\":\"enrol\",\"data1\":\"\",\"data2\":\"\"}";
                    ws.send(cmd);
                    document.getElementById("fingerprint_start").disabled = true;
                } catch (err) {
                    $('#fingerprint-status').html('Something is wrong')
                }
            }
            function GetTemplate() {

                try {
                    var cmd = "{\"cmd\":\"capture\",\"data1\":\"\",\"data2\":\"\"}";
                    ws.send(cmd);
                    console.log('GetTemplate')
                } catch (err) {
                    console.log('error', err)
                    $('#fingerprint-search-status').html('Something is wrong')
                }
                if(!window.recordfound) {
                    setTimeout(function() {
                        $('#fingerprint-search-status').html('Place Right Index Finger')
                    }, 1000)
                }
                
            }
            function MatchTemplate(x) {
                console.log('MatchTemplate')
                $.ajax({
                    url: 'ajax.php?c=Fingerprint&f=getbatch&p=' + x + '&i=' + number_of_batch,
                    // url: 'ajax.php?c=Fingerprint&f=getBatchLength',
                    type: 'get',
                    dataType: 'json',
                    success: function(ret) {
                        console.log('success')
                        for(let y=0; y<ret.length; y++) {
                            console.log('in loop', y)
                            if(!window.recordfound) {
                                console.log('in if')
                                try {
                                    var cmd = "{\"cmd\":\"setdata\",\"data1\":\"" + attempt + "\",\"data2\":\"" +  "\"}";
                                    ws.send(cmd);
                                    var cmd = "{\"cmd\":\"setdata\",\"data1\":\"" + "\",\"data2\":\"" + ret[y]['finger_data'] + "\"}";
                                    ws.send(cmd);
                                    var cmd = "{\"cmd\":\"match\",\"data1\":\"\",\"data2\":\"\"}";
                                    checked_cid = ret[y].client_id;
                                    ws.send(cmd);
                                    console.log('above if(window.recordfound) {')
                                    if(window.recordfound) {
                                        recordFound(statusDOM)
                                    }
                                } catch (err) {
                                    console.log('Err',err)
                                }
                            }
                        }
                        console.log('window record found', window.recordfound)
                        if(!window.recordfound) {
                            if(ret.length > 0){
                                x+=5
                                MatchTemplate(x);
                            }
                        }
                        // last iteration
                        if(ret.length === 0){
                            if(!window.recordfound) {
                                $('#fingerprint-search-status').html('Finger scan not recorded')
                            }
                        }
                        
                        console.log(ret)
                    }
                    // success: function (batchLength) {
                    //     let hasData = false;
                    //     for (let x = 0; x < batchLength; x++) {
                    //         if(window.recordfound) {
                    //             recordFound($('#fingerprint-status, #fingerprint-search-status'))
                    //             break;
                    //         }
                    //         $.ajax({
                    //             url: 'ajax.php?c=Fingerprint&f=getbatch&p=' + x + '&i=' + number_of_batch,
                    //             type: 'get',
                    //             dataType: 'json',
                    //             success: function (ret) {
                    //                 if(!window.recordfound) {
                    //                     try {
                    //                         var cmd = "{\"cmd\":\"setdata\",\"data1\":\"" + attempt + "\",\"data2\":\"" +  "\"}";
                    //                         ws.send(cmd);
                    //                         var cmd = "{\"cmd\":\"setdata\",\"data1\":\"" + "\",\"data2\":\"" + ret['finger_data'] + "\"}";
                    //                         ws.send(cmd);
                    //                         var cmd = "{\"cmd\":\"match\",\"data1\":\"\",\"data2\":\"\"}";
                                            
                    //                         ws.send(cmd);
                    //                         checked_cid = ret.client_id;
                    //                         if(!window.recordfound) {
                    //                             if(x===(batchLength-1)) {
                    //                                 $('#fingerprint-search-status').html('Finger scan not recorded')
                    //                             }
                    //                             else {
                    //                                 $('#fingerprint-search-status').html('Searched ' + (number_of_batch * (x + 1)/5) + ' items');
                    //                             }
                    //                         }
                    //                     } catch (err) {
                    //                         console.log('Err',err)
                    //                     }
                    //                 }
                    //             }
                    //         });
                    //     }
                    // }
                });
            }
            
        </script>
        <?php
    }
    function getbatch() {
        $query = "SELECT * FROM tbl_fingerprint ORDER BY ID ASC LIMIT ".$_GET['p'].",".$_GET['i'];
        $stmt = $this->query($query,['']);
        $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo  json_encode($array);
    }
    function getBatchLength() {
        $query = "SELECT COUNT(*) as ret FROM tbl_fingerprint";
        $stmt = $this->query($query,['']);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        $ret = $array['ret'] / $this->number_of_batch;
        echo $ret;
        exit();
    }
}

?>