<?php
    $ajax_dir = get_template_directory() . '/ajax/php';

    if ( file_exists( $ajax_dir ) && is_dir( $ajax_dir ) ) {
        include_files_recursively( $ajax_dir );
    } else {
        error_log( 'Directory does not exist: ' . $ajax_dir );
    }

    if ( ! function_exists( 'enqueue_save_post_ajax_scripts' ) ) {
        function enqueue_save_post_ajax_scripts() {
            $script_rel_path = '/ajax/js/save_post_ajax.js';
            $script_path = get_template_directory() . $script_rel_path;
            $script_uri = get_template_directory_uri() . $script_rel_path;

            // Check if the script file actually exists before enqueuing
            if ( file_exists( $script_path ) ) {
                // Enqueue the script with jQuery as a dependency, to be loaded in the footer
                wp_enqueue_script( 'save_post_ajax_script', $script_uri, array( 'jquery' ), null, true );

                // Localize script for use in JS
                wp_localize_script( 'save_post_ajax_script', 'save_post_ajax_object', array( 
                    'ajax_url' => admin_url( 'admin-ajax.php' ) 
                ) );
            } else {
                // Log an error if the script file doesn't exist
                error_log( 'Script file does not exist: ' . $script_path );
            }
        }

        add_action( 'wp_enqueue_scripts', 'enqueue_save_post_ajax_scripts' );
    }