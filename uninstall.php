<?php // Let's clean up after ourselfs. 
//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit (); 

delete_option('blogg100_options');