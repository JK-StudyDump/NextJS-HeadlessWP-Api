<?php

/**
 * [Custom Auth endpoints and callbacks]
 */
add_filter( 'send_password_change_email', '__return_false' );

// Endpoints
    // POST
        // CheckAuth
        add_action('init', function() {
            register_rest_route( 'wphh', '/auth', array(
                'methods' => 'POST',
                'callback' => 'wphh_api_authenticate_user_callback',
                // 'permission_callback' => function() {
                //     return current_user_can('edit_others_posts');
                // }
            ));
        });
        // Create/Update user
        add_action('init', function() {
            register_rest_route( 'wphh', '/auth/cu', array(
                'methods' => 'POST',
                'callback' => 'wphh_api_cu_user_callback',
                // 'permission_callback' => function() {
                //     return current_user_can('edit_others_posts');
                // }
            ));
        });
        // get user by ID
        add_action('init', function() {
            register_rest_route( 'wphh', '/auth/user/(?P<user_ID>\d+)', array(
                'methods' => 'GET',
                'callback' => 'wphh_api_get_user_callback',
                'permission_callback' => function() {
                    return current_user_can('edit_others_posts');
                }
            ));
        });
// Callbacks
    // Auth check
    function wphh_api_authenticate_user_callback( $request ) {
        $user['user_details'] = sanitize_text_field( $request->get_param( 'user_details' ) );

        $user_token = $user['user_details'];
        $user_details_decrypted = base64_decode($user['user_details']);
        $decoded_splits = explode("]!wphh![", $user_details_decrypted);

        $username = $decoded_splits[0];
        $password = $decoded_splits[1];
        $hash = wp_hash_password($password);

        remove_filter( 'determine_current_user', 'json_basic_auth_handler', 20 );
        
        $user = wp_authenticate( $username, $password );
        $hashCheck = wp_check_password($password, $hash, $user->ID);

        add_filter( 'determine_current_user', 'json_basic_auth_handler', 20 );

        $wp_json_basic_auth_error = true;

        $data = array(
            "hashCheck" => $hashCheck,
            "query" => $user_token,
            "ID" => $user->ID,
            "user_email" => $user->user_email,
            "user_nickname" => $user->user_nicename,
            "user_firstname" => $user->user_firstname,
            "user_lastname" => $user->user_lastname,
            "user_fullname" => $user->user_firstname . " " . $user->user_lastname,
            "checked_user" => $user
        );

        if ( !is_wp_error( $user ) ) {
            $response['status'] = 200;
            $response['success'] = true; 
            $response['message'] = "Authenticated";
            $response['data'] = [
                'user_status' => $data,
            ];
        } else {
            $response['status'] = 200;
            $response['success'] = false; 
            $response['message'] = "Couldn't authenticate";
        }
        return new WP_REST_Response( $response );
    }
    // Create/Edit user
    function wphh_api_cu_user_callback( $request ) {
        $user['user_details'] = sanitize_text_field( $request->get_param( 'user_details' ) );
        $method = sanitize_text_field( $request->get_param( 'api_method' ) );
        $user_ID = sanitize_text_field( $request->get_param( 'user_ID' ) );

        if (!$method) {
            $response['status'] = 200;
            $response['success'] = false; 
            $response['message'] = "api_method is missing";
            return new WP_REST_Response( $response );
        } else if ($method === "update_user" && $user_ID > 0) {
            $new_user_firstname = sanitize_text_field( $request->get_param( 'user_firstname' ) );
            $new_user_lastname = sanitize_text_field( $request->get_param( 'user_lastname' ) );
            $user_uname = sanitize_text_field( $request->get_param( 'user_uname' ) );
            $new_user_email = sanitize_text_field( $request->get_param( 'user_email' ) );
            $new_password = sanitize_text_field( $request->get_param( 'user_password' ) );

            $user_data = array(
                "ID" => $user_ID,
                "first_name" => $new_user_firstname,
                "last_name" => $new_user_lastname,
                "user_login" => $user_uname,
                "user_email" => $new_user_email,
                "user_pass" => $new_password,  
            );
            $this_user = wp_update_user($user_data);
            remove_filter( 'determine_current_user', 'json_basic_auth_handler', 20 );
            $user_checked = wp_authenticate( $user_uname, $new_password );
            add_filter( 'determine_current_user', 'json_basic_auth_handler', 20 );
            $wp_json_basic_auth_error = true;

            $user_checked_array = array(
                "ID" => $user_checked->ID,
                "user_email" => $user_checked->user_email,
                "user_login" => $user_checked->login,
                "user_nickname" => $user_checked->user_nicename,
                "user_fullname" => $user_checked->user_firstname . " " . $user->user_lastname,
                "user_role" => $user_checked->role
            );
        } 
        else if ($method === "create_user") {
            $new_user_firstname = sanitize_text_field( $request->get_param( 'user_firstname' ) );
            $new_user_lastname = sanitize_text_field( $request->get_param( 'user_lastname' ) );
            $user_uname = sanitize_text_field( $request->get_param( 'user_uname' ) );
            $new_user_email = sanitize_text_field( $request->get_param( 'user_email' ) );
            $new_password = sanitize_text_field( $request->get_param( 'user_password' ) );
            $new_user_role = sanitize_text_field( $request->get_param( 'user_role' ) );

            $user_data = array(
                "first_name" => $new_user_firstname,
                "last_name" => $new_user_lastname,
                "user_login" => $user_uname,
                "user_email" => $new_user_email,
                "user_pass" => $new_password,  
                "role" => $new_user_role     
            );

            remove_filter( 'determine_current_user', 'json_basic_auth_handler', 20 );
            $this_user = wp_insert_user($user_data);
            $user_checked = wp_authenticate( $user_uname, $new_password );
            add_filter( 'determine_current_user', 'json_basic_auth_handler', 20 );
            $wp_json_basic_auth_error = true;

            $user_checked_array = array(
                "ID" => $user_checked->ID,
                "user_email" => $user_checked->user_email,
                "user_login" => $user_checked->login,
                "user_nickname" => $user_checked->user_nicename,
                "user_fullname" => $user_checked->user_firstname . " " . $user->user_lastname,
                "user_role" => $user_checked->role
            );
        } else {
            $response['status'] = 500;
            $response['success'] = false; 
            $response['message'] = "Internal error: Missing Data in request";
            return new WP_REST_Response( $response );
        }

        $data = array(
            "isHashValid" => $hashCheck,
            "query" => $user_data,
            "checked_user" => $user_checked
        );

        if ( !is_wp_error( $user_checked ) ) {
            $response['status'] = 200;
            $response['success'] = true; 
            $response['message'] = "Authenticated";
            $response['method'] = $method;
            $response['data'] = [
                'user_status' => $data,
            ];
        } else {
            $response['status'] = 200;
            $response['success'] = false; 
            $response['message'] = "Couldn't authenticate";
            $response['error'] = $user_checked;
        }
        return new WP_REST_Response( $response );
    }
    // Create/Edit user
    function wphh_api_get_user_callback( $request ) {
        global $wpdb;

        $user_ID = sanitize_text_field( $request->get_param( 'user_ID' ) );

        if ($user_ID > 0) {
            $this_user = $user_ID;
            $query = "SELECT user_login,user_registered FROM {$wpdb->prefix}users WHERE ID = $this_user";
            $user_checked = $wpdb->get_results($query);
        } else {
            $response['status'] = 500;
            $response['success'] = false; 
            $response['message'] = "Internal error: Missing Data in request";
            return new WP_REST_Response( $response );
        }

        if ( !is_wp_error( $user_checked ) ) {
            $response['status'] = 200;
            $response['success'] = true; 
            $response['message'] = "Authenticated";
            $response['data'] = [
                'user' => $user_checked,
            ];
        } else {
            $response['status'] = 200;
            $response['success'] = false; 
            $response['message'] = "Couldn't authenticate";
            $response['error'] = $user_checked;
        }
        return new WP_REST_Response( $response );
    }
  
  