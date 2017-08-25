$(document).ready(function (){
	show_loading_in_pagination();
	show_loading_in_search();
	// autologout($);

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
	    if (idleTime > 4) { // 5 minutes
	        alert('You have been idle for longer than 5 minutes on the client database. \nPlease login again.');
	        window.location.href = '/?c=user&f=logout';
	    }
	}
}

