<?php

/**
 * [Custom Post-type: Project]
 */

if ( !function_exists('wphh_project_post_type_callback') ) {
    function wphh_project_post_type_callback() {
        $args = array(
            'public' => true,
            'label' => __( 'Project', 'wphh-crud_api' ),
            'menu_icon' => 'dashicons-networking',
            'show_in_rest' => true,
            'show_in_graphql' => true,
          	'graphql_single_name' => 'project',
          	'graphql_plural_name' => 'projects'
        );
        register_post_type( 'project', $args );
    };
    add_action( 'init', 'wphh_project_post_type_callback' );
}

add_action('init', function() {
    register_rest_route( 'wphh', '/project/', array(
        'methods' => 'GET',
        'callback' => 'wphh_api_get_allProjects_callback'
    ));
});

function wphh_api_get_allProjects_callback( $request ) {
    $projects = get_posts( [ 'post_type' => 'project', 'post_status' => 'publish' ] );
    wp_reset_postdata();

    if ( count($projects) > 0 ) {
        $response['status'] = 200;
        $response['success'] = true; 
        $response['data'] = $projects;
    } else {
        $response['status'] = 200;
        $response['success'] = false; 
        $response['message'] = "No projects found";
    }
    
    return new WP_REST_Response( $response );
}