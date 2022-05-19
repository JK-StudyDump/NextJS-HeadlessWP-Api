<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://glitchtech.eu
 * @since      1.0.0
 *
 * @package    Wphh_Crud_api
 * @subpackage Wphh_Crud_api/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wphh_Crud_api
 * @subpackage Wphh_Crud_api/includes
 * @author     GlitchTech <dev@glitchtech.eu>
 */
class Wphh_Crud_api_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wphh-crud_api',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
