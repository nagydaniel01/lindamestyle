<?php
    /**
     * Post Views Tracking & Admin Display
     *
     * Tracks post views, prevents duplicate counts per user (via cookie),
     * and displays view counts in the WP admin for all public post types.
     */

    // Prevent prefetching to keep counts accurate
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

    if (!function_exists('wp_set_post_views')) {
        /**
         * Increment the view count for a post.
         *
         * @param int $post_id Post ID.
         * @return void
         */
        function wp_set_post_views($post_id) {
            if (empty($post_id) || !is_numeric($post_id)) {
                return;
            }

            $count_key = '_post_views_count';
            $count     = (int) get_post_meta($post_id, $count_key, true);

            $count++;
            update_post_meta($post_id, $count_key, $count);
        }
    }

    if (!function_exists('wp_track_post_views')) {
        /**
         * Track post views for single posts only.
         * Prevents multiple counts from the same user via cookie.
         *
         * @param int|null $post_id Optional. Post ID to track. Defaults to current post.
         * @return void
         */
        function wp_track_post_views($post_id = null) {
            if (!is_single()) {
                return;
            }

            // Skip counting for logged-in admins
            if (is_user_logged_in() && current_user_can('manage_options')) {
                return;
            }

            if (empty($post_id)) {
                global $post;
                if (!isset($post->ID) || !is_numeric($post->ID)) {
                    return;
                }
                $post_id = (int) $post->ID;
            }

            $cookie_name  = 'viewed_posts';
            $viewed_posts = [];

            // Read cookie if it exists
            if (isset($_COOKIE[$cookie_name]) && is_string($_COOKIE[$cookie_name])) {
                $viewed_posts = array_filter(array_map('intval', explode(',', $_COOKIE[$cookie_name])));
            }

            // Only increment if not already viewed in this session
            if (!in_array($post_id, $viewed_posts, true)) {
                wp_set_post_views($post_id);

                $viewed_posts[] = $post_id;
                $cookie_value   = implode(',', $viewed_posts);

                // Set cookie for 1 day
                setcookie($cookie_name, $cookie_value, time() + DAY_IN_SECONDS, COOKIEPATH ?: '/', COOKIE_DOMAIN ?: '', is_ssl(), true);
                $_COOKIE[$cookie_name] = $cookie_value; // Ensure consistency in current request
            }
        }
        add_action('wp_head', 'wp_track_post_views');
    }

    if (!function_exists('wp_get_post_views')) {
        /**
         * Get the number of views for a post.
         *
         * @param int $post_id Post ID.
         * @return int Number of views.
         */
        function wp_get_post_views($post_id) {
            if (empty($post_id) || !is_numeric($post_id)) {
                return 0;
            }

            $count_key = '_post_views_count';
            $count     = get_post_meta($post_id, $count_key, true);

            return (int) ($count ?: 0);
        }
    }

    if (!function_exists('wp_add_post_views_column')) {
        /**
         * Add a "Views" column to all public post types in the admin.
         *
         * @param array $columns Existing columns.
         * @return array Modified columns.
         */
        function wp_add_post_views_column($columns) {
            $columns['post_views'] = __('View');
            return $columns;

            /*
            $new_columns = [];

            foreach ($columns as $key => $value) {
                $new_columns[$key] = $value;

                // Insert 'post_views' column right after 'title' column
                if ($key === 'title') {
                    $new_columns['post_views'] = __('Views', TEXT_DOMAIN);
                }
            }

            return $new_columns;
            */
        }
    }

    if (!function_exists('wp_render_post_views_column')) {
        /**
         * Render the "Views" column in the admin.
         *
         * @param string $column  Column name.
         * @param int    $post_id Post ID.
         * @return void
         */
        function wp_render_post_views_column($column, $post_id) {
            if ($column === 'post_views') {
                echo esc_html(number_format_i18n(wp_get_post_views($post_id)));
            }
        }
    }

    if (!function_exists('wp_make_post_views_sortable')) {
        /**
         * Make the "Views" column sortable in the admin.
         *
         * @param array $columns Sortable columns.
         * @return array Modified sortable columns.
         */
        function wp_make_post_views_sortable($columns) {
            $columns['post_views'] = 'post_views';
            return $columns;
        }
    }

    if (!function_exists('wp_sort_post_views_column')) {
        /**
         * Adjust query for sorting by views.
         *
         * @param WP_Query $query The WP_Query instance (passed by reference).
         * @return void
         */
        function wp_sort_post_views_column($query) {
            if (!is_admin() || !$query->is_main_query()) {
                return;
            }

            if ($query->get('orderby') === 'post_views') {
                $query->set('meta_key', '_post_views_count');
                $query->set('orderby', 'meta_value_num');
            }
        }
        add_action('pre_get_posts', 'wp_sort_post_views_column');
    }

    if (!function_exists('wp_register_post_views_admin_columns')) {
        /**
         * Register admin column filters and actions for all public post types.
         *
         * @return void
         */
        function wp_register_post_views_admin_columns() {
            $post_types = get_post_types(['public' => true], 'names');

            foreach ($post_types as $post_type) {
                add_filter("manage_{$post_type}_posts_columns", 'wp_add_post_views_column');
                add_action("manage_{$post_type}_posts_custom_column", 'wp_render_post_views_column', 10, 2);
                add_filter("manage_edit-{$post_type}_sortable_columns", 'wp_make_post_views_sortable');
            }
        }
        add_action('admin_init', 'wp_register_post_views_admin_columns');
    }