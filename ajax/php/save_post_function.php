<?php
    function save_post_function() {
        // Security check
        if ( ! isset($_POST['post_id']) ) {
            wp_send_json_error(array('message' => __('Invalid request', TEXT_DOMAIN)));
        }

        $current_user_id = get_current_user_id();
        $save_post_id    = intval($_POST['post_id']);

        if ( ! $current_user_id ) {
            wp_send_json_error(array('message' => __('You must be logged in', TEXT_DOMAIN)));
        }

        // Get existing bookmarks from user meta
        $bookmarks = get_user_meta($current_user_id, 'user_bookmarks', true);

        if ( ! is_array($bookmarks) ) {
            $bookmarks = array();
        }

        // Toggle bookmark
        if ( in_array($save_post_id, $bookmarks) ) {
            // Remove post from bookmarks
            $bookmarks = array_diff($bookmarks, array($save_post_id));
        } else {
            // Add post to bookmarks
            $bookmarks[] = $save_post_id;
        }

        // Update user meta
        update_user_meta($current_user_id, 'user_bookmarks', $bookmarks);

        wp_send_json_success(array(
            'message' => __('The post saved successfully', TEXT_DOMAIN),
        ));
    }
    add_action('wp_ajax_save_post_function', 'save_post_function');
    add_action('wp_ajax_nopriv_save_post_function', 'save_post_function');
