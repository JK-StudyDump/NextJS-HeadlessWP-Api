<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://glitchtech.eu
 * @since             1.0.0
 * @package           Wphh_Crud_api
 *
 * @wordpress-plugin
 * Plugin Name:       wphh-crudAPI
 * Plugin URI:        https://glitchtech.eu
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            GlitchTech
 * Author URI:        https://glitchtech.eu
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wphh-crud_api
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
define( 'WPHH_CRUD_API_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wphh-crud_api-activator.php
 */
function activate_wphh_crud_api() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wphh-crud_api-activator.php';
	Wphh_Crud_api_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wphh-crud_api-deactivator.php
 */
function deactivate_wphh_crud_api() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wphh-crud_api-deactivator.php';
	Wphh_Crud_api_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wphh_crud_api' );
register_deactivation_hook( __FILE__, 'deactivate_wphh_crud_api' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wphh-crud_api.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */ 
function run_wphh_crud_api() {

	$plugin = new Wphh_Crud_api();
	$plugin->run();
	
	$isRunning = true;
}	
run_wphh_crud_api();

if ($isRunning = true) {
	require plugin_dir_path( __FILE__ ) . 'api/wphh-crud_api-controller.php';
}