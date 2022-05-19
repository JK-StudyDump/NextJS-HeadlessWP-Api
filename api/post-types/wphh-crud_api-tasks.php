<?php

/**
 * [Custom Post-type: Task]
 */

if ( !function_exists('wphh_task_post_type_callback') ) {
    function wphh_task_post_type_callback() {
        $args = array(
            'public' => true,
            'label' => __( 'Tasks', 'wphh-crud_api' ),
            'menu_icon' => 'dashicons-text',
            'supports' => [ 'custom-fields', 'title', 'editor' ],
            'show_in_graphql' => true,
        );
        register_post_type( 'task', $args );
    };
    add_action( 'init', 'wphh_task_post_type_callback' );
}

// Endpoints
    // GET
        // Get All
        add_action('init', function() {
            register_rest_route( 'wphh', '/tasks/', array(
                'methods' => 'GET',
                'callback' => 'wphh_api_get_callback',
                'permission_callback' => function() {
                    return current_user_can('edit_others_posts');
                }
            )); 
        });
        // Get Drafted
        add_action('init', function() {
            register_rest_route( 'wphh', '/tasks/drafted', array(
                'methods' => 'GET',
                'callback' => 'wphh_api_get_drafted_callback',
                'permission_callback' => function() {
                    return current_user_can('edit_others_posts');
                }
            ));
        });
        // Get Published
        add_action('init', function() {
            register_rest_route( 'wphh', '/tasks/published', array(
                'methods' => 'GET',
                'callback' => 'wphh_api_get_published_callback',
                'permission_callback' => function() {
                    return current_user_can('edit_others_posts');
                }
            ));
        });
        // Get Trashed
        add_action('init', function() {
            register_rest_route( 'wphh', '/tasks/trashed', array(
                'methods' => 'GET',
                'callback' => 'wphh_api_get_trashed_callback',
                'permission_callback' => function() {
                    return current_user_can('edit_others_posts');
                }
            ));
        });
        // Get by ID
        add_action('init', function() {
            register_rest_route( 'wphh', '/tasks/(?P<task_id>\d+)', array(
                'methods' => 'GET',
                'callback' => 'wphh_api_get_callback',
                'permission_callback' => function() {
                    return current_user_can('edit_others_posts');
                }
            )); 
        });
        // Get drafted by ID
        add_action('init', function() {
            register_rest_route( 'wphh', '/tasks/drafted/(?P<task_id>\d+)', array(
                'methods' => 'GET',
                'callback' => 'wphh_api_get_drafted_callback',
                'permission_callback' => function() {
                    return current_user_can('edit_others_posts');
                }
            ));
        });
        // Get published by ID
        add_action('init', function() {
            register_rest_route( 'wphh', '/tasks/published/(?P<task_id>\d+)', array(
                'methods' => 'GET',
                'callback' => 'wphh_api_get_published_callback',
                'permission_callback' => function() {
                    return current_user_can('edit_others_posts');
                }
            ));
        });
        // Get trashed by ID
        add_action('init', function() {
            register_rest_route( 'wphh', '/tasks/trashed/(?P<task_id>\d+)', array(
                'methods' => 'GET',
                'callback' => 'wphh_api_get_trashed_callback',
                'permission_callback' => function() {
                    return current_user_can('edit_others_posts');
                }
            ));
        });
    // POST
        // Create Task
        add_action('init', function() {
            register_rest_route( 'wphh', '/tasks/create', array(
                'methods' => 'POST',
                'callback' => 'wphh_api_create_task_callback',
                'permission_callback' => function() {
                    return current_user_can('edit_others_posts');
                }
            ));
        });
    // PUT
        // Update Task
        add_action('init', function() {
            register_rest_route( 'wphh', '/tasks/update/(?P<task_id>\d+)', array(
                'methods' => 'PUT',
                'callback' => 'wphh_api_update_task_callback',
                'permission_callback' => function() {
                    return current_user_can('edit_others_posts');
                }
            ));
        });
    // DELETE
        // Soft-Delete Task
        add_action('init', function() {
            register_rest_route( 'wphh', '/tasks/delete', array(
                'methods' => 'delete',
                'callback' => 'wphh_api_soft_delete_task_callback',
                'permission_callback' => function() {
                    return current_user_can('edit_others_posts');
                }
            ));
        });
        // Force-Delete Task
        add_action('init', function() {
            register_rest_route( 'wphh', '/tasks/force-delete', array(
                'methods' => 'delete',
                'callback' => 'wphh_api_force_delete_task_callback',
                'permission_callback' => function() {
                    return current_user_can('edit_others_posts');
                }
            ));
        });

// Callbacks
    
    // GET
        // Get task/tasks
        function wphh_api_get_callback( $request ) {
            $task_id = $request->get_param('task_id');
                if ( empty( $task_id ) ) {
                    $allTasksArray = [];
                    $tasks = get_posts( [ 'post_type' => 'task', 'post_status' => array('draft', 'publish', 'trash'), 'numberposts' => -1 ] );
                    foreach (array_reverse($tasks) as $task) { 
                            array_push($allTasksArray, [ 
                                'task_data' => wp_get_single_post( $task->ID ), 
                                'task_meta' => get_post_meta( $task->ID ) 
                            ]);
                    }

                    if ( count($tasks) > 0 ) {
                        $response['status'] = 200;
                        $response['success'] = true; 
                        // $response['from'] = $sourceURLArray;
                        // $response['token'] = $token;
                        $response['data'] = $allTasksArray;
                    } else {
                        $response['status'] = 200;
                        $response['success'] = false; 
                        // $response['from'] = $sourceURLArray;
                        // $response['token'] = $token;
                        $response['message'] = "No tasks found";
                    }
                } else {
                    if ( $task_id > 0 ){
                        $post = wp_get_single_post( $task_id );
                        $post_meta = get_post_meta( $task_id );

                        if ( !empty( $post ) ) {
                            $response['status'] = 200;
                            $response['success'] = true; 
                            // $response['from'] = $sourceURLArray;
                            // $response['token'] = $token;
                            $response['data'] = [
                                'task_data' => $post,
                                'task_meta' => $post_meta,
                            ];
                        } else {
                            $response['status'] = 200;
                            $response['success'] = false; 
                            // $response['from'] = $sourceURLArray;
                            // $response['token'] = $token;
                            $response['message'] = "No task found";
                            $response['requested_ID'] = $task_id;
                        }
                    }
                }
            return new WP_REST_Response( $response );
        }
        // Get drafted task/tasks
        function wphh_api_get_drafted_callback( $request ) {
            $task_id = $request->get_param('task_id');
                if ( empty( $task_id ) ) {
                    $allTasksArray = [];
                    $tasks = get_posts( [ 'post_type' => 'task', 'post_status' => 'draft', 'numberposts' => -1 ] );
                    foreach (array_reverse($tasks) as $task) { 
                            array_push($allTasksArray, [ 
                                'task_data' => wp_get_single_post( $task->ID ), 
                                'task_meta' => get_post_meta( $task->ID ) 
                            ]);
                    }

                    if ( count($tasks) > 0 ) {
                        $response['status'] = 200;
                        $response['success'] = true; 
                        // $response['from'] = $sourceURLArray;
                        // $response['token'] = $token;
                        $response['data'] = $allTasksArray;
                    } else {
                        $response['status'] = 200;
                        $response['success'] = false; 
                        // $response['from'] = $sourceURLArray;
                        // $response['token'] = $token;
                        $response['message'] = "No tasks found";
                    }
                } else {
                    if ( $task_id > 0 ){
                        $post = wp_get_single_post( $task_id );
                        $post_meta = get_post_meta( $task_id );

                        if ( !empty( $post ) ) {
                            $response['status'] = 200;
                            $response['success'] = true; 
                            // $response['from'] = $sourceURLArray;
                            // $response['token'] = $token;
                            $response['data'] = [
                                'task_data' => $post,
                                'task_meta' => $post_meta,
                            ];
                        } else {
                            $response['status'] = 200;
                            $response['success'] = false; 
                            // $response['from'] = $sourceURLArray;
                            // $response['token'] = $token;
                            $response['message'] = "No task found";
                            $response['requested_ID'] = $task_id;
                        }
                    }
                }
            return new WP_REST_Response( $response );
        }
        // Get published task/tasks
        function wphh_api_get_published_callback( $request ) {
            $task_id = $request->get_param('task_id');
                if ( empty( $task_id ) ) {
                    $allTasksArray = [];
                    $tasks = get_posts( [ 'post_type' => 'task', 'post_status' => 'publish', 'numberposts' => -1 ] );
                    foreach (array_reverse($tasks) as $task) { 
                            array_push($allTasksArray, [ 
                                'task_data' => wp_get_single_post( $task->ID ), 
                                'task_meta' => get_post_meta( $task->ID ) 
                            ]);
                    }

                    if ( count($tasks) > 0 ) {
                        $response['status'] = 200;
                        $response['success'] = true; 
                        // $response['from'] = $sourceURLArray;
                        // $response['token'] = $token;
                        $response['data'] = $allTasksArray;
                    } else {
                        $response['status'] = 200;
                        $response['success'] = false; 
                        // $response['from'] = $sourceURLArray;
                        // $response['token'] = $token;
                        $response['message'] = "No tasks found";
                    }
                } else {
                    if ( $task_id > 0 ){
                        $post = wp_get_single_post( $task_id );
                        $post_meta = get_post_meta( $task_id );

                        if ( !empty( $post ) ) {
                            $response['status'] = 200;
                            $response['success'] = true; 
                            // $response['from'] = $sourceURLArray;
                            // $response['token'] = $token;
                            $response['data'] = [
                                'task_data' => $post,
                                'task_meta' => $post_meta,
                            ];
                        } else {
                            $response['status'] = 200;
                            $response['success'] = false; 
                            // $response['from'] = $sourceURLArray;
                            // $response['token'] = $token;
                            $response['message'] = "No task found";
                            $response['requested_ID'] = $task_id;
                        }
                    }
                }
            return new WP_REST_Response( $response );
        }
        // Get trashed task/tasks
        function wphh_api_get_trashed_callback( $request ) {
            $task_id = $request->get_param('task_id');
                if ( empty( $task_id ) ) {
                    $allTasksArray = [];
                    $tasks = get_posts( [ 'post_type' => 'task', 'post_status' => 'trash', 'numberposts' => -1 ] );
                    foreach (array_reverse($tasks) as $task) { 
                            array_push($allTasksArray, [ 
                                'task_data' => wp_get_single_post( $task->ID ), 
                                'task_meta' => get_post_meta( $task->ID ) 
                            ]);
                    }

                    if ( count($tasks) > 0 ) {
                        $response['status'] = 200;
                        $response['success'] = true; 
                        // $response['from'] = $sourceURLArray;
                        // $response['token'] = $token;
                        $response['data'] = $allTasksArray;
                    } else {
                        $response['status'] = 200;
                        $response['success'] = false; 
                        // $response['from'] = $sourceURLArray;
                        // $response['token'] = $token;
                        $response['message'] = "No tasks found";
                    }
                } else {
           if ( $task_id > 0 ){
                        $post = wp_get_single_post( $task_id );
                        $post_meta = get_post_meta( $task_id );

                        if ( !empty( $post ) ) {
                            $response['status'] = 200;
                            $response['success'] = true; 
                            // $response['from'] = $sourceURLArray;
                            // $response['token'] = $token;
                            $response['data'] = [
                                'task_data' => $post,
                                'task_meta' => $post_meta,
                            ];
                        } else {
                            $response['status'] = 200;
                            $response['success'] = false; 
                            // $response['from'] = $sourceURLArray;
                            // $response['token'] = $token;
                            $response['message'] = "No task found";
                            $response['requested_ID'] = $task_id;
                        }
                    }
                }
            return new WP_REST_Response( $response );
        }
    // POST
        function wphh_api_create_task_callback( $request ) {
            $task['post_title'] = sanitize_text_field( $request->get_param( 'task_title' ) );
            $task['post_name'] = sanitize_text_field( strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->get_param( 'task_title' )))) );
            $task['post_content'] = sanitize_textarea_field( $request->get_param( 'task_content' ) );

            if ($request->get_param('meta_task_project') == "") {
                $task['meta_input'] = [
                    'project' => sanitize_text_field( "No Project" ),
                ];
            } else {
                $task['meta_input'] = [
                    'project' => sanitize_text_field( $request->get_param( 'meta_task_project' ) ),
                ];
            }

            $task['post_status'] = 'publish';
            $task['post_type'] = 'task';

            $posted_task = wp_insert_post( $task );

            if ( !is_wp_error( $posted_task ) ) {
                $response['status'] = 200;
                $response['success'] = true; 
                $response['message'] = "Task created";
                $response['data'] = [
                    'task_data' => get_post( $posted_task ),
                    'task_meta' => get_post_meta( $posted_task ),
                ];
            } else {
                $response['status'] = 200;
                $response['success'] = false; 
                $response['message'] = "Couldn't create task";
            }
            return new WP_REST_Response( $response );
        }
    // PUT
        function wphh_api_update_task_callback( $request ) {
            $task_id = $request->get_param('task_id');

            if ( $task_id > 0 ) {
                $task['ID'] = $task_id;
                $task['post_title'] = sanitize_text_field( $request->get_param( 'task_title' ) );
                $task['post_name'] = sanitize_text_field( strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->get_param( 'task_title' )))) );
                $task['post_content'] = sanitize_textarea_field( $request->get_param( 'task_content' ) );
                $task['meta_input'] = [ 
                    'project' => sanitize_text_field( $request->get_param( 'task_meta_project' )  ),
                ];
                $task['post_status'] = sanitize_text_field( $request->get_param( 'task_status' )  );
                $task['post_type'] = 'task';

                if ($task['post_status'] === "") {
                    $task['post_status'] = "draft";
                }

                $posted_task = wp_update_post( $task, true );

                if ( !is_wp_error( $posted_task ) ) {
                    $response['status'] = 200;
                    $response['success'] = true; 
                    $response['message'] = "Task updated";
                    $response['data'] = get_post( $posted_task );
                } else {
                    $response['status'] = 200;
                    $response['success'] = false; 
                    $response['message'] = "Couldn't update task";
                    $response['requested_ID'] = $task_id;
                }
            } else {
                $response['status'] = 200;
                $response['success'] = false; 
                $response['message'] = "Couldn't find task";
                $response['requested_ID'] = $task_id;
            }
            return new WP_REST_Response( $response );
        }
        // DELETE
            // Soft-Delete
            function wphh_api_soft_delete_task_callback( $request ) {
                $task_id = $request->get_param('task_id');

                if ( $task_id > 0 ) {
                    $deleted_task = wp_trash_post( $task_id, false );

                    if ( !is_wp_error( $posted_task ) ) {
                        $response['status'] = 200;
                        $response['success'] = true; 
                        $response['message'] = "Task soft-deleted";
                        $response['data'] = get_post( $posted_task );
                    } else {
                        $response['status'] = 200;
                        $response['success'] = false; 
                        $response['message'] = "Couldn't delete task, there was an error.";
                        $response['requested_ID'] = $task_id;
                    }
                } else {
                    $response['status'] = 200;
                    $response['success'] = false; 
                    $response['message'] = "Couldn't delete task, ID is required.";
                    $response['requested_ID'] = $task_id;
                }
                return new WP_REST_Response( $response );
            }
            // Force-Delete
            function wphh_api_force_delete_task_callback( $request ) {
                $task_id = $request->get_param('task_id');

                if ( $task_id > 0 ) {
                    $deleted_task = wp_delete_post( $task_id, false );

                    if ( !is_wp_error( $posted_task ) ) {
                        $response['status'] = 200;
                        $response['success'] = true; 
                        $response['message'] = "Task force-deleted";
                        $response['data'] = get_post( $posted_task );
                    } else {
                        $response['status'] = 200;
                        $response['success'] = false; 
                        $response['message'] = "Couldn't delete task, there was an error.";
                        $response['requested_ID'] = $task_id;
                    }
                } else {
                    $response['status'] = 200;
                    $response['success'] = false; 
                    $response['message'] = "Couldn't delete task, ID is required.";
                    $response['requested_ID'] = $task_id;
                }
                return new WP_REST_Response( $response );
            }