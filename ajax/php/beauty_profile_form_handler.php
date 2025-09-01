<?php
    if ( ! function_exists('beauty_profile_form_handler') ) {
        /**
         * Handles the submission of the Beauty Profile form via AJAX.
         *
         * This function processes form data sent via POST, validates the nonce for security,
         * sanitizes the input, and saves it as user meta or via Advanced Custom Fields (if available).
         * Responds with JSON success or error messages.
         *
         * @return void Sends JSON response and exits.
         */
        function beauty_profile_form_handler() {
            try {
                // Ensure request is POST
                if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
                    wp_send_json_error([
                        'message' => __('Invalid request method.', TEXT_DOMAIN)
                    ], 405);
                }

                // Check if form data is present
                if ( empty($_POST['form_data']) ) {
                    wp_send_json_error([
                        'message' => __('No form data received.', TEXT_DOMAIN)
                    ], 400);
                }

                // Parse the serialized form data
                $form = [];
                if ( isset($_POST['form_data']) ) {
                    parse_str($_POST['form_data'], $form);
                }

                // Nonce check for security
                if ( ! isset($form['beauty_profile_form_nonce']) ||
                    ! wp_verify_nonce($form['beauty_profile_form_nonce'], 'beauty_profile_form_action') ) {
                    wp_send_json_error([
                        'message' => __('Invalid security token.', TEXT_DOMAIN)
                    ], 403);
                }

                // Validate user ID
                $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
                if ( ! $user_id ) {
                    wp_send_json_error([
                        'message' => __('Invalid user!', TEXT_DOMAIN)
                    ], 400);
                }

                // Process and save form fields
                foreach ($form as $key => $value) {
                    // Skip system fields
                    if ( in_array($key, ['action', 'user_id', 'beauty_profile_form_nonce', '_wp_http_referer']) ) {
                        continue;
                    }

                    // Sanitize input
                    $value = sanitize_text_field($value);

                    // Check for empty fields
                    if ( $value === '' ) {
                        wp_send_json_error([
                            'message' => sprintf(__('The %s field is empty!', TEXT_DOMAIN), $key)
                        ], 400);
                    }

                    // Save field via ACF or standard user meta
                    if ( function_exists('update_field') ) {
                        $saved = update_field($key, $value, 'user_' . $user_id);
                    } else {
                        $saved = update_user_meta($user_id, $key, $value);
                    }

                    // Handle saving errors
                    if ( is_wp_error($saved) ) {
                        wp_send_json_error([
                            'message' => sprintf(__('Saving the %s field failed: %s', TEXT_DOMAIN), $key, $saved->get_error_message())
                        ], 500);
                    }

                    if ( $saved === false ) {
                        wp_send_json_error([
                            'message' => sprintf(__('Saving the %s field failed, please try again.', TEXT_DOMAIN), $key)
                        ], 500);
                    }
                }

                // Send success response
                wp_send_json_success([
                    'message' => __('Data saved successfully!', TEXT_DOMAIN)
                ], 200);

            } catch ( Exception $e ) {
                // Catch any unexpected errors
                wp_send_json_error([
                    'message' => sprintf(__('Unexpected error: %s', TEXT_DOMAIN), $e->getMessage())
                ], 500);
            }
        }

        // Register AJAX handlers for logged-in and non-logged-in users
        add_action('wp_ajax_beauty_profile_form_handler', 'beauty_profile_form_handler');
        add_action('wp_ajax_nopriv_beauty_profile_form_handler', 'beauty_profile_form_handler');
    }
