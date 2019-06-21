<?php
if(MAINTENANCE_MODE) {
	if(isset($_SESSION['username'])) {
		session_start();
		session_destroy();
		header("Location: ".SITE_URL."/?page=".FRONT_PAGE);
	}
}
else {
	if($current_page!=FRONT_PAGE) {
		if(!isset($_SESSION['username'])) {
			header("Location:".SITE_URL);
		}	
	}
	else{
		if(isset($_SESSION['username'])) {
			if ($_SESSION['type'] == 'superreporting' || $_SESSION['type'] == 'reporting') {
				header("Location:".SITE_URL.'/?page=reports');  
			}elseif($_SESSION['type'] == 'dataentry' || $_SESSION['type'] == 'enquiry'){ 
				header("Location:?page=clients");
			}else {
				header("Location:".SITE_URL.'/?page=dashboard');
			}
		}
	} 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
	<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
	<title>Susu Mamas | Family and Youth Health Services</title>
	<meta name="description" content="Susu Mamas | Family Health Care Services" />
	<meta name="keywords" content="susu mamas, login, form, healthcare services, community" />
	<meta name="author" content="ThemeTribe" />

	<link rel="shortcut icon" href="<?php echo SITE_URL ?>/images/tab_logo.png"> 
	<script src="<?php echo SITE_URL ?>/library/jquery-ui-1.10.3/jquery-1.9.1.js" type="text/javascript"></script>
	<script src="<?php echo SITE_URL ?>/library/bootstrap/dist/js/bootstrap.js"></script>		
	<script src="<?php echo SITE_URL ?>/js/global_script.js"></script>

<?php if(isset($_GET['page']) && $_GET['page']!=FRONT_PAGE): ?>
	<!-- Bootstrap core CSS --> 
	<link href="<?php echo SITE_URL ?>/library/bootstrap/dist/css/bootstrap.css" type="text/css" rel="stylesheet">
	<link href="<?php echo SITE_URL ?>/library/bootstrap/assets/css/docs.css" type="text/css" rel="stylesheet">
	<!-- JQuery UI CSS --> 
	<link href="<?php echo SITE_URL ?>/library/jquery-ui-1.10.3/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />	
	<!-- Datepicker widget -->
	<script src="<?php echo SITE_URL ?>/library/jquery-ui-1.10.3/ui/jquery-ui.js" type="text/javascript"></script>
	<!-- Bootstrap core JS -->
	<!-- global script  -->
	<script src="<?php echo SITE_URL ?>/library/bootstrap/js/alert.js"></script>
	<script type="text/javascript">
	$(document).ready(function($) {
		$('#date_birth, #date_death, #datepicker3, #datepicker-review_date, #followup_date, #start_date, #end_date, #datepicker-malnu-review_date').datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd'
		}).on('keydown',function(){return false;
		}).on('hide', function(event) {
			event.preventDefault();
			event.stopPropagation();
		});
		$('#ui-datepicker-div').appendTo($('#newClientModal'));
	});
	</script>  
<?php endif; ?>
	<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL ?>/style.css?v=1.2" />
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="<?php //echo SITE_URL ?>/library/bootstrap/assets/js/html5shiv.js"></script>
	<script src="<?php //echo SITE_URL ?>/library/boostrap/assets/js/respond.min.js"></script>
	<![endif]-->	

	<!--[if lte IE 7]><style>.main{display:none;} .support-note .note-ie{display:block;}</style><![endif]-->

</head>
<body <?php body_class() ?>>