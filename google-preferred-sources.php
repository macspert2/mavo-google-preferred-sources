<?php
/**
 * Plugin Name:       Google Preferred Sources Button
 * Plugin URI:        https://developers.google.com/search/docs/appearance/preferred-sources
 * Description:       Displays a Google Preferred Sources call-to-action button below posts and as a footer widget, with an explanatory tooltip, fully localized for French, English, and German (Polylang).
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      8.0
 * Author:            Mavo
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       google-preferred-sources
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GPS_VERSION', '1.0.0' );
define( 'GPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GPS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GPS_PLUGIN_FILE', __FILE__ );

require_once GPS_PLUGIN_DIR . 'includes/class-gps-i18n.php';
require_once GPS_PLUGIN_DIR . 'includes/class-gps-render.php';
require_once GPS_PLUGIN_DIR . 'includes/class-gps-settings.php';
require_once GPS_PLUGIN_DIR . 'includes/class-gps-widget.php';
require_once GPS_PLUGIN_DIR . 'includes/class-gps-shortcode.php';
require_once GPS_PLUGIN_DIR . 'includes/class-gps-plugin.php';

register_activation_hook( __FILE__, array( 'GPS_Settings', 'activate' ) );

GPS_Plugin::get_instance();
