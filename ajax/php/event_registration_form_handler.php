<?php
    if ( ! function_exists('event_registration_form_handler') ) {
        /**
         * Handles AJAX event registration form submissions.
         *
         * This function validates POST requests, verifies the security nonce,
         * sanitizes and validates user inputs, prevents duplicate registrations,
         * creates an 'attendee' custom post type entry, and returns a JSON response.
         *
         * @return void Sends JSON response and exits.
         */
        function event_registration_form_handler() {
            try {
                // Ensure the request method is POST
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

                // Parse serialized form data into an associative array
                $form = [];
                if ( isset($_POST['form_data']) ) {
                    parse_str($_POST['form_data'], $form);
                }

                // Nonce verification for security
                if ( ! isset($form['event_registration_form_nonce']) ||
                    ! wp_verify_nonce($form['event_registration_form_nonce'], 'event_registration_form_action') ) {
                    wp_send_json_error([
                        'message' => __('Invalid security token', TEXT_DOMAIN)
                    ], 403);
                }

                // Extract and sanitize form fields
                $event_id = isset($form['event_id']) ? intval($form['event_id']) : 0;
                $user_id  = get_current_user_id();
                $name     = isset($form['reg_name']) ? sanitize_text_field($form['reg_name']) : '';
                $email    = isset($form['reg_email']) ? sanitize_email($form['reg_email']) : '';
                $privacy  = isset($form['reg_privacy_policy']) ? sanitize_text_field($form['reg_privacy_policy']) : '';

                /*
                // Validate user
                if ( ! $user_id ) {
                    wp_send_json_error([
                        'message' => __('You must be logged in to register.', TEXT_DOMAIN)
                    ], 401);
                }
                */

                // Validate required fields
                if ( empty($name) || empty($email) ) {
                    wp_send_json_error([
                        'message' => __('Name and email are required.', TEXT_DOMAIN)
                    ], 422);
                }

                // Validate email format
                if ( ! is_email($email) ) {
                    wp_send_json_error([
                        'message' => __('Invalid email format.', TEXT_DOMAIN)
                    ], 422);
                }

                // Validate event ID
                if ( ! $event_id ) {
                    wp_send_json_error([
                        'message' => __('Invalid event ID.', TEXT_DOMAIN)
                    ], 400);
                }

                // Validate privacy policy consent
                if ( empty($privacy) || $privacy !== 'on' ) {
                    wp_send_json_error([
                        'message' => __('You must agree to the privacy policy.', TEXT_DOMAIN)
                    ], 422);
                }

                // Prevent duplicate registration
                $existing = get_posts([
                    'post_type'  => 'attendee',
                    'post_status'=> 'publish',
                    'meta_query' => [
                        'relation' => 'AND',
                        [
                            'key'   => 'event_id',
                            'value' => $event_id
                        ],
                        [
                            'key'   => 'attendee_email',
                            'value' => $email
                        ]
                    ]
                ]);

                if ( $existing ) {
                    wp_send_json_error([
                        'message' => __('You are already registered for this event.', TEXT_DOMAIN)
                    ], 409);
                }

                // Create attendee post
                $registration_id = wp_insert_post([
                    'post_type'   => 'attendee',
                    'post_status' => 'publish',
                    'post_title'  => sprintf(
                        __('[Event ID: %d] %s (%s)', TEXT_DOMAIN),
                        $event_id,
                        $name,
                        $email
                    ),
                    'post_author' => $user_id,
                    'meta_input'  => [
                        'event_id'       => $event_id,
                        'attendee_name'  => $name,
                        'attendee_email' => $email
                    ]
                ]);

                // Handle insertion errors
                if ( is_wp_error($registration_id) ) {
                    wp_send_json_error([
                        'message' => sprintf(__('Registration failed: %s', TEXT_DOMAIN), $registration_id->get_error_message())
                    ], 500);
                }

                if ( $registration_id === false ) {
                    wp_send_json_error([
                        'message' => __('Registration failed, please try again.', TEXT_DOMAIN)
                    ], 500);
                }

                // Success response
                wp_send_json_success([
                    'message' => __('Successfully registered!', TEXT_DOMAIN)
                ], 200);

            } catch ( Exception $e ) {
                // Catch unexpected errors
                wp_send_json_error([
                    'message' => sprintf(__('Unexpected error: %s', TEXT_DOMAIN), $e->getMessage())
                ], 500);
            }
        }

        // Register AJAX handlers for logged-in and non-logged-in users
        add_action('wp_ajax_event_registration_form_handler', 'event_registration_form_handler');
        add_action('wp_ajax_nopriv_event_registration_form_handler', 'event_registration_form_handler');
    }
