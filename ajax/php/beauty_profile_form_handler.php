<?php
    if ( ! function_exists('beauty_profile_form_handler') ) {
        function beauty_profile_form_handler() {
            try {
                // Ensure request is POST
                if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
                    wp_send_json_error(['message' => __('Invalid request method.', TEXT_DOMAIN)], 405);
                }

                // Check if form data is present
                if ( empty($_POST['form_data']) ) {
                    wp_send_json_error(['message' => __('No form data received.', TEXT_DOMAIN)], 400);
                }

                // Parse the serialized form data
                $form = [];
                if ( isset($_POST['form_data']) ) {
                    parse_str($_POST['form_data'], $form);
                }

                // Nonce check
                if ( ! isset($form['beauty_profile_form_nonce']) ||
                    ! wp_verify_nonce($form['beauty_profile_form_nonce'], 'beauty_profile_form_action') ) {
                    wp_send_json_error(['message' => __('Invalid security token.', TEXT_DOMAIN)], 400);
                }

                // Check user ID
                $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
                if ( ! $user_id ) {
                    wp_send_json_error(['message' => __('Invalid user!', TEXT_DOMAIN)], 400);
                }

                // Process form fields
                foreach ($form as $key => $value) {
                    if ( in_array($key, ['action', 'user_id', 'beauty_profile_form_nonce', '_wp_http_referer']) ) {
                        continue;
                    }

                    $value = sanitize_text_field($value);

                    // Empty field check
                    if ( $value === '' ) {
                        wp_send_json_error([
                            'message' => sprintf(__('The %s field is empty!', TEXT_DOMAIN), $key)
                        ], 400);
                    }

                    // Save field
                    if ( function_exists('update_field') ) {
                        $saved = update_field($key, $value, 'user_' . $user_id);
                    } else {
                        $saved = update_user_meta($user_id, $key, $value);
                    }

                    // Error handling with is_wp_error()
                    if ( is_wp_error($saved) ) {
                        wp_send_json_error([
                            'message' => sprintf(
                                __('Saving the %s field failed: %s', TEXT_DOMAIN),
                                $key,
                                $saved->get_error_message()
                            )
                        ], 500);
                    }

                    if ( $saved === false ) {
                        wp_send_json_error([
                            'message' => sprintf(__('Saving the %s field failed, please try again.', TEXT_DOMAIN), $key)
                        ], 500);
                    }
                }

                // Success response
                wp_send_json_success(['message' => __('Data saved successfully!', TEXT_DOMAIN)]);

            } catch ( Exception $e ) {
                wp_send_json_error([
                    'message' => sprintf(__('Unexpected error: %s', TEXT_DOMAIN), $e->getMessage())
                ], 500);
            }
        }

        add_action('wp_ajax_beauty_profile_form_handler', 'beauty_profile_form_handler');
        add_action('wp_ajax_nopriv_beauty_profile_form_handler', 'beauty_profile_form_handler');
    }
