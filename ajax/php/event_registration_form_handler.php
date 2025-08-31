<?php
    if ( ! function_exists('event_registration_form_handler') ) {
        function event_registration_form_handler() {
            try {
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
                if ( ! isset($form['event_registration_form_nonce']) ||
                    ! wp_verify_nonce($form['event_registration_form_nonce'], 'event_registration_form_action') ) {
                    wp_send_json_error(['message' => __('Invalid security token', TEXT_DOMAIN)], 400);
                }

                // Extract fields safely
                $event_id = isset($form['event_id']) ? intval($form['event_id']) : 0;
                $user_id  = get_current_user_id();
                $name     = isset($form['reg_name']) ? sanitize_text_field($form['reg_name']) : '';
                $email    = isset($form['reg_email']) ? sanitize_email($form['reg_email']) : '';

                // Validate inputs
                if ( ! $user_id ) {
                    wp_send_json_error(['message' => __('You must be logged in to register.', TEXT_DOMAIN)], 401);
                }

                if ( empty($name) || empty($email) ) {
                    wp_send_json_error(['message' => __('Name and email are required.', TEXT_DOMAIN)], 422);
                }

                if ( ! is_email($email) ) {
                    wp_send_json_error(['message' => __('Invalid email format.', TEXT_DOMAIN)], 422);
                }

                if ( ! $event_id ) {
                    wp_send_json_error(['message' => __('Invalid event ID.', TEXT_DOMAIN)], 400);
                }

                // Prevent duplicate registration
                $existing = get_posts([
                    'post_type'  => 'attendee',
                    'post_status'=> 'publish',
                    'meta_query' => [
                        'relation' => 'AND',
                        [
                            'key'   => 'event_id',
                            'value' => $event_id,
                        ],
                        [
                            'key'   => 'attendee_email',
                            'value' => $email,
                        ]
                    ]
                ]);

                if ( $existing ) {
                    wp_send_json_error(['message' => __('You are already registered for this event.', TEXT_DOMAIN)], 409);
                }

                // Create attendee post
                $registration_id = wp_insert_post([
                    'post_type'   => 'attendee',
                    'post_status' => 'publish',
                    'post_title'  => sprintf(__('Registration - %s (%s)', TEXT_DOMAIN), $name, $email),
                    'post_author' => $user_id,
                    'meta_input'  => [
                        'event_id'       => $event_id,
                        'attendee_name'  => $name,
                        'attendee_email' => $email,
                    ]
                ]);

                if ( is_wp_error($registration_id) ) {
                    wp_send_json_error([
                        'message' => sprintf(__('Registration failed: %s', TEXT_DOMAIN), $registration_id->get_error_message())
                    ], 500);
                }

                if ( $registration_id === false ) {
                    wp_send_json_error(['message' => __('Registration failed, please try again.', TEXT_DOMAIN)], 500);
                }

                // Success response
                wp_send_json_success(['message' => __('Successfully registered!', TEXT_DOMAIN)]);

            } catch ( Exception $e ) {
                wp_send_json_error([
                    'message' => sprintf(__('Unexpected error: %s', TEXT_DOMAIN), $e->getMessage())
                ], 500);
            }
        }

        add_action('wp_ajax_event_registration_form_handler', 'event_registration_form_handler');
        add_action('wp_ajax_nopriv_event_registration_form_handler', 'event_registration_form_handler');
    }
