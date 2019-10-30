<div class="container" style="font-family: 'Lato', Calibri, Arial, sans-serif !important;">
	<?php if(MAINTENANCE_MODE) : ?>
		<div class="alert alert-warning" style="background: red; display:block;
padding: 10px;
border: 1px solid #eee;
z-index: 9;
text-align:center;
font-size: 18px;"><strong>Maintenance Mode </strong> We are currently on maintenance mode to provide better service. Check out later to use this system. Thank you!</div>
		<?php endif; ?>
  		
		<div class="row">
			<header>
				<div class="leftHead">
					<div class="headLogo">
						<img src="images/ssm_original.png" width="70" height="70" draggable="false" style="pointer-events: none;">	
					</div>
					<span class="headName">
						<h2>eCIS | Susu Mamas</h2><br/>
						<h4>
							<i>Family and Youth Health Services</i>
						</h4>
					</span>
				</div>
			<br class="clr">				
			</header> <!-- end header -->					
		</div>
		<div class="row">	
			<section class="main bg2" style="padding: 30px 30px;">
				<div class="innerMain">
					<div class="mainLeft">
						<div class="leftPane">
							<h1>eCIS <br /><span>Electronic Client Record System</span></h1>
							<p>Your Health. Our Primary Concern</p>
							<!-- <p>Enim dictumst est? Egestas in nec! Mattis amet, sociis porttitor. 
								Et adipiscing scelerisque porta, mus sociis dictumst. 
								Augue, ut et aenean penatibus, augue nunc rhoncus aenean pulvinar. 
								Vut. Scelerisque dolor cras penatibus amet urna cursus in aliquet phasellus 
								cras adipiscing tincidunt a a dis sociis! Eros augue turpis mattis turpis 
								scelerisque cursus parturient ac, egestas ac. </p> -->	
								
							<!--displays banner-->
							<?php if (preg_match('/testclients.susumamas.org.pg$/', $_SERVER['HTTP_HOST'])) { ?>
      							<div style="background:#EC7A7A;text-align:center ;width:30%;padding:5px;position:relative;right:0%;top:20px;z-index:10000;overflow:hidden"><h3>This is the TEST SITE <br />(with auto deploy. T3)</h3></div><?php
  							} ?>
						</div>
					</div>
					<div class="mainRight">
						<form class="form-2" method="post">
							<input type="hidden" name="class" value="user" />
							<input type="hidden" name="func" value="login" />
							<h1>
								<span class="log-in">Sign In </span> 	
								<?php if(has_error("USER-INVALID")) : ?>
									<span class="errorMsg">Invalid Login. Please try again.</span>
								<?php endif; ?>		
								<?php if(has_error("NO-PERMISSION")) : ?>
									<span class="errorMsg">Can't login. No Permissions has given yet.</span>
								<?php endif; ?>		
							</h1>										
							<p class="float">
								<label for="login"><i class="icon-user"></i>Username</label>
								<input type="text" name="username" id="username" value="" />
							</p>
							<p class="float">
								<label for="password"><i class="icon-lock"></i>Password</label>
								<input type="password" name="passwrd" id="passwrd" value="" />
							</p>
							<p class="float">
								<label for="office"><i class="icon-reorder"></i>Select Health Facility</label>
								<select name="office" id="office">
									<option value="0"></option>
									<?php					
									foreach($office->get_all() as $data ){ 
										?><option value="<?php echo $data['ID'] ?>"><?php echo $data['area_name'] ?></option><?php echo "\n";
									}
									?>
								</select>
								
							</p>
							<br/>
							<p class="clearfix">  
								<input type="submit" name="lgin" id="lgin" value="Login"/>
							</p>
						</form>​​
					</div>	
				</div>
			<br class="clr"/>			
			</section>				
		</div>	<!-- end section -->
	</div>

	<?php $user->script() ?>