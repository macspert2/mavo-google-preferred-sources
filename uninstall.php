<?php
// Runs only when the plugin is deleted from the Plugins screen (not on deactivation).
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'gps_settings' );
