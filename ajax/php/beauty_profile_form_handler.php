<?php
    if ( ! function_exists('beauty_profile_form_handler') ) {
        /**
         * Handles AJAX submissions for the Beauty Profile form.
         *
         * This function processes POST requests submitted via AJAX for the Beauty Profile form.
         * It performs the following steps:
         *   1. Validates the request method and presence of form data.
         *   2. Parses and sanitizes form inputs.
         *   3. Verifies the security nonce.
         *   4. Validates user ID and required fields.
         *   5. Saves form fields as user meta or via Advanced Custom Fields (if available).
         *   6. Sends a notification email to the admin or user depending on who updated the profile.
         *   7. Returns a JSON response indicating success or failure.
         *
         * @return void Outputs a JSON response and terminates execution.
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

                // Determine who modified the profile
                $current_user_id = get_current_user_id();
                $user_info       = get_userdata($user_id);
                $user_email      = $user_info->user_email;
                $user_name       = $user_info->display_name;

                // Get admin email and validate
                $admin_email = get_option('admin_email');
                if ( ! $admin_email || ! is_email($admin_email) ) {
                    wp_send_json_error([
                        'message' => __('Admin email is not configured properly.', TEXT_DOMAIN)
                    ], 500);
                }

                // Prepare email headers
                $headers = [
                    'From: ' . get_bloginfo('name') . ' <' . $admin_email . '>',
                    'Reply-To: ' . $name . ' <' . $email . '>',
                    'Content-Type: text/html; charset=UTF-8'
                ];

                if ( $current_user_id === $user_id ) {
                    // User updated their own profile → notify admin
                    $subject_admin = __('A user updated their Beauty Profile', TEXT_DOMAIN);
                    $message_admin = sprintf(
                        __("User %s (ID: %d, Email: %s) has updated their Beauty Profile.", TEXT_DOMAIN),
                        $user_name,
                        $user_id,
                        $user_email
                    );

                    // Send the email
                    $sent = wp_mail($admin_email, $subject_admin, $message_admin, $headers);
                } else {
                    // Admin updated user profile → notify user
                    $subject_user = __('Your Beauty Profile has been updated', TEXT_DOMAIN);
                    $message_user = sprintf(
                        __("Hello %s,\n\nYour Beauty Profile was updated by an administrator.\n\nThank you!", TEXT_DOMAIN),
                        $user_name
                    );

                    // Send the email
                    $sent = wp_mail($user_email, $subject_user, $message_user, $headers);
                }

                // Handle email sending errors
                if ( ! $sent ) {
                    wp_send_json_error([
                        'message' => __('Message could not be sent. Please try again later.', TEXT_DOMAIN)
                    ], 500);
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
