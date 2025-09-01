<?php
    if ( ! function_exists('update_bookmark_list_handler') ) {
        /**
         * Handles AJAX requests to update the user's bookmarked posts list.
         *
         * This function retrieves the bookmarks of the currently logged-in user,
         * queries the corresponding posts, renders them using a template part,
         * and returns the HTML via JSON response.
         *
         * @return void Sends JSON response and exits.
         */
        function update_bookmark_list_handler() {
            $current_user_id = get_current_user_id();

            // Ensure the user is logged in
            if ( ! $current_user_id ) {
                wp_send_json_error([
                    'message' => __('You must be logged in', TEXT_DOMAIN)
                ], 401);
            }

            // Define post types to include
            $post_type    = ['post', 'knowledge_base'];

            // Retrieve bookmarked post IDs from user meta
            $bookmark_ids = get_user_meta($current_user_id, 'user_bookmarks', true);

            // If no bookmarks, set a placeholder ID to avoid query errors
            if ( empty($bookmark_ids) || ! is_array($bookmark_ids) ) {
                $bookmark_ids = [0];
            }

            // Query bookmarked posts
            $args = [
                'post_type'      => $post_type,
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'post__in'       => $bookmark_ids,
                'orderby'        => 'post__in', // preserve saved order
            ];

            $post_query = new WP_Query($args);

            // Capture the template output
            ob_start();
            $query_args = [
                'query'             => $post_query,
                'card_type'         => 'post',
                'number_of_columns' => 2,
            ];
            get_template_part('template-parts/blocks/block', 'query', $query_args);
            $html = ob_get_clean();

            // Return the rendered HTML as JSON
            wp_send_json_success(['html' => $html]);
        }

        // Register AJAX handlers for logged-in and non-logged-in users
        add_action('wp_ajax_update_bookmark_list_handler', 'update_bookmark_list_handler');
        add_action('wp_ajax_nopriv_update_bookmark_list_handler', 'update_bookmark_list_handler');
    }
