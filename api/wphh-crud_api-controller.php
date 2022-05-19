<?php 

/**
 * Fired right after plugin started
 *
 * @link       https://glitchtech.eu
 * @since      1.0.0
 *
 * @package    Wphh_Crud_api
 * @subpackage Wphh_Crud_api/api
 */

/**
 * Post Types
 * Todo: creation of post types, relations, endpoints and such
 */ 

// Auth Custom
require plugin_dir_path( __FILE__ ) . 'post-types/wphh-crud_api-auth.php';
// Post Type: task 
require plugin_dir_path( __FILE__ ) . 'post-types/wphh-crud_api-tasks.php';

// ----------

/*
add_action('init', function() {
    register_rest_route( 'wphh', '/test/(?P<wphh_param>\d+)', array(
        'methods' => 'GET',
        'callback' => 'wphh_api_test_callback'
    ));
});

function wphh_api_test_callback( $request ) {
    $response['status'] = 200;
    $response['success'] = true;
    $response['data'] = $request->get_param('wphh_param');
    return new WP_REST_Response( $response );
}
*/