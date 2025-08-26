<?php
    function update_bookmark_list_function() {
        $current_user_id = get_current_user_id();

        if ( ! $current_user_id ) {
            wp_send_json_error(array('message' => __('You must be logged in', TEXT_DOMAIN)));
        }

        $post_type    = ['post', 'knowledge_base'];
        $bookmark_ids = get_user_meta($current_user_id, 'user_bookmarks', true);

        if (empty($bookmark_ids) || !is_array($bookmark_ids)) {
            $bookmark_ids = array(0); // no posts
        }

        $args = array(
            'post_type'      => $post_type,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'post__in'       => $bookmark_ids,
            'orderby'        => 'post__in', // preserve saved order
        );

        $post_query = new WP_Query($args);

        ob_start();
        $query_args = array(
            'query'              => $post_query,
            'card_type'          => 'post',
            'number_of_columns'  => 2,
        );
        get_template_part('template-parts/blocks/block', 'query', $query_args);
        $html = ob_get_clean();

        wp_send_json_success(array('html' => $html));
    }
    add_action('wp_ajax_update_bookmark_list_function', 'update_bookmark_list_function');
    add_action('wp_ajax_nopriv_update_bookmark_list_function', 'update_bookmark_list_function');
