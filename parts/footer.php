	<div class="container">
		<div class="row">	
			<footer>
				<ul>
					<li><a href="#" style="pointer-events: none;">Susu Mamas &copy 2017</a> <span id="system_version">v3.5</span></li>			
					<li><a href="#">Documentation</a></li>
					<li><a href="/changelog.txt">Changelog</a></li>
					<li><a href="#">Official Website</a></li>
					<li class="last"><a href="#">Contact Us</a></li>
					<?php if (preg_match('/testclients.susumamas.org.pg$/', $_SERVER['HTTP_HOST'])) { ?>
						<li style="color: purple;">
						DBUser : <strong><?php echo DBUSER ?></strong> | DBName : <strong><?php echo DBNAME ?></strong>
						</li>
						<?php

				} ?>
				</ul>

				<br class="clr"/>
									
			</footer>	
		</div>
	</div>

	<?php loader_modal(); ?>
</body>
</html>