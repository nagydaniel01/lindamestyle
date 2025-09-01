<?php
    if ( ! function_exists('mytheme_ajax_submit_comment') ) {
        function mytheme_ajax_submit_comment() {
            try {
                // Only allow POST requests
                if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
                    wp_send_json_error([
                        'message' => __('Invalid request method.', TEXT_DOMAIN)
                    ], 405);
                }

                // Verify required fields exist
                if ( empty( $_POST['comment_post_ID'] ) || ! is_numeric( $_POST['comment_post_ID'] ) ) {
                    wp_send_json_error([
                        'message' => __('Invalid post ID.', TEXT_DOMAIN)
                    ], 400);
                }

                // Process comment submission
                $comment = wp_handle_comment_submission( wp_unslash( $_POST ) );
                if ( is_wp_error( $comment ) ) {
                    wp_send_json_error([
                        'message' => $comment->get_error_message()
                    ], 400);
                }

                if ( ! $comment || empty( $comment->comment_ID ) ) {
                    wp_send_json_error([
                        'message' => __('Comment could not be saved.', TEXT_DOMAIN),
                    ], 500);
                }

                // Set cookies
                $user = wp_get_current_user();
                do_action( 'set_comment_cookies', $comment, $user );

                // Calculate comment depth
                $comment_depth = 1;
                $comment_parent = $comment->comment_parent;
                while ( $comment_parent ) {
                    $comment_depth++;
                    $parent_comment = get_comment( $comment_parent );
                    $comment_parent = $parent_comment->comment_parent;
                }

                // Get max depth from WP Discussion Settings
                $max_depth = get_option( 'thread_comments_depth', 5 );

                // Set globals for template functions
                $GLOBALS['comment'] = $comment;
                $GLOBALS['comment_depth'] = $comment_depth;

                // Generate full comment HTML using your theme's callback
                ob_start();
                mytheme_comment( $comment, array(
                    'avatar_size' => 64,
                    'style'       => 'ol',
                    'max_depth'   => $max_depth
                ), $comment_depth );
                $comment_html = ob_get_clean();

                // Return comment HTML
                wp_send_json_success([
                    'comment'     => $comment_html,
                    'comment_id'  => $comment->comment_ID,
                    'message'     => __('Your comment has been sent successfully!', TEXT_DOMAIN)
                ], 200);

            } catch ( Exception $e ) {
                wp_send_json_error([
                    'message' => sprintf(__('Unexpected error: %s', TEXT_DOMAIN), $e->getMessage())
                ], 500);
            }
        }
        add_action( 'wp_ajax_ajaxcomments', 'mytheme_ajax_submit_comment' );
        add_action( 'wp_ajax_nopriv_ajaxcomments', 'mytheme_ajax_submit_comment' );
    }