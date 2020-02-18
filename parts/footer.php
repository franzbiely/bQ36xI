	<div class="container">
		<div class="row">	
			<footer>
				<ul>
					<li><a href="#" style="pointer-events: none;">eCIS &copy 2019</a> <span id="system_version">V3.41</span></li>			
					<li><a href="/changelog.txt">Changelog</a></li>
				</ul>
				<div style="color: purple; float: right; font-size: 11px;">
					<strong style="display:none;">Time : <?php echo date("d/m/Y h:ia") ?></strong>
					DBUser : <strong><?php echo DBUSER ?></strong> | 
					DBName : <strong><?php echo DBNAME ?></strong>
				</div>
				<br class="clr"/>
									
			</footer>	
		</div>
	</div>

	<?php loader_modal(); ?>
</body>
</html>