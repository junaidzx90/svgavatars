<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.fiverr.com/junaidzx90
 * @since             1.0.0
 * @package           Svgavatars
 *
 * @wordpress-plugin
 * Plugin Name:       Svg Avatars
 * Plugin URI:        https://www.fiverr.com
 * Description:       SVG Avatars Generator WordPress plugin, please use <code>[svgavatars]</code> for output.
 * Version:           1.0.3
 * Author:            Developer Junayed
 * Author URI:        https://www.fiverr.com/junaidzx90
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       svgavatars
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SVGAVATARS_VERSION', '1.0.3' );
define( 'SVGAVATARS_URL', plugin_dir_url( __FILE__ ) );
define( 'SVGAVATARS_PATH', plugin_dir_path( __FILE__ ) );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-svgavatars-activator.php
 */
function activate_svgavatars() {
	
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-svgavatars-deactivator.php
 */
function deactivate_svgavatars() {
	
}

register_activation_hook( __FILE__, 'activate_svgavatars' );
register_deactivation_hook( __FILE__, 'deactivate_svgavatars' );

/**
 * The core php plugin functionality,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'public/svgatarts.public.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_svgavatars() {

	$plugin = new Svgavatars();
	$plugin->run();

}
run_svgavatars();
