$(document).ready(function (){
	show_loading_in_pagination();
	show_loading_in_search();
	// autologout($);
	alert();
	//----------------finger print demo start
	 // test if the browser supports web sockets
	 if ("WebSocket" in window) {
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

	// connect to the specified host
	function connect(host) {

		$('#es').val('Connecting to " + host + " ...');
		try {
			ws = new WebSocket(host); // create the web socket
		} catch (err) {
			$('#es').val('error');
		}

		ws.onopen = function () {
			$('#es').val('Connected OK!');
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
						status.value = "Get Template OK";
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
				case 6:
					if (obj.retmsg == 1) {
						status.value = "Enrol Template OK";
						if (obj.data1 != "null") {
							var user = prompt("Enter your Name: ");
							if(user != null){
								$.ajax({
									url: 'fpengine.php?fpengine=store&key='+obj.data1+'&name='+user,
									method: 'get',
									success: function(response){
										console.log(response);
									}
								});
							}
						} else {
						status.value = "Enrol Template Fail";    
						}
					} else {
						status.value = "Enrol Template Fail";
					}
					break;
				case 7:
					if (obj.image == "null") {
					} else {
						var img = document.getElementById("imgDiv");
						img.src = "data:image/png;base64,"+obj.image;
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

});

function EnrollTemplate() {
	try {
		//ws.send("enrol");
		var cmd = "{\"cmd\":\"enrol\",\"data1\":\"\",\"data2\":\"\"}";
		ws.send(cmd);
	} catch (err) {
	}
	document.getElementById("es").value = "Place Finger";
}

function GetTemplate() {
	try {
		//ws.send("capture");
		var cmd = "{\"cmd\":\"capture\",\"data1\":\"\",\"data2\":\"\"}";
		ws.send(cmd);
		console.log(cmd)
	} catch (err) {
	}
	document.getElementById("es").value = "Place Finger";
}

function MatchTemplate() {
	var timer = null;
	var prev = null;
	var content = null;
	results = [];

	$.ajax({
		url: 'fpengine.php?fpengine=show',
		dataType: 'json',
		success:function(response){
			content = response;
			$.each(response, function(key, value){
				var str = value.data;
				var concat = "";
				var count = 0;
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
					var cmd = "{\"cmd\":\"setdata\",\"data1\":\"" +data2 "\",\"\":\"" + concat + "\"}";
					ws.send(cmd);
					var cmd = "{\"cmd\":\"match\",\"data1\":\"\",\"data2\":\"\"}";
					ws.send(cmd);
					console.log(window.result);
				} catch (err) {
					
				}	
		});

		}
	});

	timer = setInterval(function(){
				if(window.result == 1){
					window.result = 0;

					let prevMatch = 0;
					let prevIndex = -1;
					
					for(x = 0; x < results.length; x++){
						if(prevMatch < results[x]){
							prevMatch = results[x];
							prevIndex = x;
						}
					}

					$(".login-name").html(content[prevIndex].name);
					console.log('Results', results, content[prevIndex]);
					clearInterval(timer);
					// GetTemplate();
				}else{
					console.log("PLease try again!");

					clearInterval(timer);
					GetTemplate();
				}           
			}, 500);  


}
	//----------------finger print demo end

});

function show_loading_in_search(){
	$('.btn_submit_quick').click(function (){
		show_loader($);
	});
	$('.btn_submit_search').click(function (){
		show_loader($);
	});
	
}
function show_loading_in_pagination(){
	$('.next').click(function (){
		show_loader($);
	});
	$('.prev').click(function (){
		show_loader($);
	});
}
function disbled_submit_button(_this){
	$(_this).prop('disabled', true);
}
function resetForm() {
	/* empty form */
   $("#newClientModal input[type='text']").val('');
   $("#newClientModal input[type='password']").val('');
   $("#newClientModal input[type='email']").val('');
   //Code added by Joe [to set the reset the value of datetime-local and display to none the child type]
   $("#newClientModal input[type='datetime-local']").val('');
   $("#newClientModal option").prop('selected', false);
   $("#newClientModal .showonchildtype").css("display", "none");
   
   var consultation_modal_box = $(".consultation_modal_box");
   consultation_modal_box.find('#datepicker3').val('');
   consultation_modal_box.find('#clinic_id').val('');
   consultation_modal_box.find('#referral_id').val('');
   consultation_modal_box.find('#datepicker-review_date').val('');
   consultation_modal_box.find('#referral_time').val('');
   consultation_modal_box.find(':checkbox').attr('checked', false);
   // End added code here
}

function show_loader($,element_force_hide){

	$("#loader_modal").modal({
		show 	: true,
		backdrop: 'static',
  		keyboard: true
	});
	
	if(element_force_hide!=''){
		$(element_force_hide).addClass('tobehind');
	}
}
function close_loader($,element_force_hide){
	setTimeout(function(){
		$("#loader_modal").modal('hide');
		if(element_force_hide!=''){
			$(element_force_hide).removeClass('tobehind');
		}
	},1000)
	
}
function getUrlVars() {
    var vars = {};
    var parts =window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[ decodeURIComponent(key)] = decodeURIComponent(value);
    });
    return vars;
}

//Code added by Joe [global function "timestampToDateTimeLocal" for javascript to convert timestamp value to datetimelocal format ex.2015-04-08T08:00:0]
function timestampToDateTimeLocal (timestamp) {
	var date = new Date(timestamp*1000);
	var year = date.getFullYear();
	var month = date.getMonth() + 1;
	month = (month<10) ? '0' + month : month;
	var day = date.getDate();
	day = (day<10) ? '0' + day : day;
	var hours = date.getHours();
	hours = (hours<10) ? '0' + hours : hours;
	var minutes = date.getMinutes();
	minutes = (minutes<10) ? '0' + minutes : minutes;
	var seconds = date.getSeconds();
	seconds = (seconds<10) ? '0' + seconds : seconds;
	return year + "-" + month + "-" + day + "T" + hours + ":" + minutes + ":" + seconds;
}

function autologout($) {
	console.log('callme');
	// timer for 3 minutes idle
    var idleTime = 0;
    var submitted = false;

    var idleInterval = setInterval(timerIncrement, 60000); // 1 minute
    $(this).mousemove(function(e) { //Zero the idle timer on mouse movement and keypress.
        idleTime = 0;
    });
    $(this).keypress(function(e) {
        idleTime = 0;
    });
    function timerIncrement() {
	    idleTime = idleTime + 1;
	    if (idleTime > 10) { // 11 minutes
	        alert('You have been idle for longer than 10 minutes on the client database. \nPlease login again.');
	        window.location.href = '/?c=user&f=logout';
	    }
	}
}

