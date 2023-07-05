<h2 class="nav-tab-wrapper">
	<?php
	//if ($current_user->user_login =="spotlight") { ?>
		<a href="?page=ss-toolkit-menu&tab=tools" class="nav-tab <?php echo $active_tab == 'tools' ? 'nav-tab-active' : ''; ?>">Tools</a>
		<a href="?page=ss-toolkit-menu&tab=wp-config" class="nav-tab <?php echo $active_tab == 'wp-config' ? 'nav-tab-active' : ''; ?>">WP-Config</a>
		<?php	if(get_option('ss_smtp_mail') == 1){ ?>
		<a href="?page=ss-toolkit-menu&tab=mail" class="nav-tab <?php echo $active_tab == 'mail' ? 'nav-tab-active' : ''; ?>">Mail</a>
		<?php } ?>
		<?php	if(get_option('ss_cookie_button.DELETE') == 1){ ?>
		<a href="?page=ss-toolkit-menu&tab=cookie" class="nav-tab <?php echo $active_tab == 'cookie' ? 'nav-tab-active' : ''; ?>">Cookie Notification</a>
		<?php } ?>
		<?php	if(get_option('ss_scripts') == 1){ ?>
		<a href="?page=ss-toolkit-menu&tab=scripts" class="nav-tab <?php echo $active_tab == 'scripts' ? 'nav-tab-active' : ''; ?>">Scripts</a>
		<?php } ?>
	<?php //} ?>
	<!-- <a href="?page=ss-toolkit-menu&tab=welcome" class="nav-tab <?php //echo $active_tab == 'welcome' ? 'nav-tab-active' : ''; ?>">Welcome</a> -->
</h2>	