<?php
    if ( ! function_exists('contact_form_handler') ) {
        /**
         * Handles AJAX submissions for the contact form.
         *
         * This function processes POST requests submitted via AJAX for the contact form.
         * It performs the following steps:
         *   1. Validates the request method and presence of form data.
         *   2. Parses and sanitizes form inputs.
         *   3. Verifies the security nonce.
         *   4. Validates required fields, email format, and privacy policy consent.
         *   5. Prepares and sends an HTML email to the site admin.
         *   6. Stores the message temporarily in a WordPress transient for 15 minutes.
         *   7. Returns a JSON response indicating success or failure.
         *
         * @return void Outputs a JSON response and terminates execution.
         */
        function contact_form_handler() {
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
                if ( ! isset($form['contact_form_nonce']) ||
                    ! wp_verify_nonce($form['contact_form_nonce'], 'contact_form_action') ) {
                    wp_send_json_error([
                        'message' => __('Invalid security token.', TEXT_DOMAIN)
                    ], 403);
                }

                // Extract and sanitize form fields
                $name    = isset($form['name']) ? sanitize_text_field($form['name']) : '';
                $email   = isset($form['email']) ? sanitize_email($form['email']) : '';
                $phone   = isset($form['phone']) ? sanitize_text_field($form['phone']) : '';
                $subject = isset($form['subject']) ? sanitize_text_field($form['subject']) : '';
                $message = isset($form['message']) ? sanitize_textarea_field($form['message']) : '';
                $privacy = isset($form['privacy_policy']) ? sanitize_text_field($form['privacy_policy']) : '';

                // Validate required fields
                if ( empty($name) || empty($email) || empty($subject) || empty($message) ) {
                    wp_send_json_error([
                        'message' => __('All required fields must be filled out.', TEXT_DOMAIN)
                    ], 422);
                }

                // Validate email format
                if ( ! is_email($email) ) {
                    wp_send_json_error([
                        'message' => __('Invalid email format.', TEXT_DOMAIN)
                    ], 422);
                }

                // Validate privacy policy consent
                if ( empty($privacy) || $privacy !== 'on' ) {
                    wp_send_json_error([
                        'message' => __('You must agree to the privacy policy.', TEXT_DOMAIN)
                    ], 422);
                }

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

                // Prepare email subject with site name
                $mail_subject = sprintf(
                    __('[%s] New message: %s', TEXT_DOMAIN),
                    get_bloginfo('name'),
                    $subject
                );

                // Format message lines into HTML paragraphs
                $message_lines = array_filter(preg_split("/\r\n|\n|\r/", $message), function($line) {
                    return trim($line) !== '';
                });

                $formatted_message = implode('', array_map(function($line) {
                    return '<p>' . esc_html($line) . '</p>';
                }, $message_lines));

                // Prepare email message
                $mail_message = sprintf(
                    '<strong>Name:</strong> %s<br/>
                    <strong>Email:</strong> %s<br/>
                    <strong>Phone:</strong> %s<br/>
                    <strong>Subject:</strong> %s<br/>
                    %s',
                    esc_html($name),
                    esc_html($email),
                    esc_html($phone),
                    esc_html($subject),
                    $formatted_message
                );

                // Send the email
                $sent = wp_mail($admin_email, $mail_subject, $mail_message, $headers);

                // Handle email sending errors
                if ( ! $sent ) {
                    wp_send_json_error([
                        'message' => __('Message could not be sent. Please try again later.', TEXT_DOMAIN)
                    ], 500);
                }

                // Generate a unique message ID and store message in a transient for 15 minutes
                // Useful for debugging, logging, or displaying confirmation later: get_transient('contact_form_' . $message_id).
                $message_id = time() . wp_generate_password(8, false, false);
                set_transient( 'contact_form_' . $message_id, [
                    'name'    => $name,
                    'email'   => $email,
                    'phone'   => $phone,
                    'subject' => $subject,
                    'message' => $message,
                ], 15 * MINUTE_IN_SECONDS ); // expires after 15 mins
                
                // Success response
                wp_send_json_success([
                    'message'      => __('Your message has been sent successfully!', TEXT_DOMAIN),
                    'redirect_url' => esc_url( trailingslashit( home_url('/thank-you') ) ),
                    'message_id'   => $message_id
                ], 200);

            } catch ( Exception $e ) {
                // Catch any unexpected errors
                wp_send_json_error([
                    'message' => sprintf(__('Unexpected error: %s', TEXT_DOMAIN), $e->getMessage())
                ], 500);
            }
        }

        // Register AJAX handlers for logged-in and non-logged-in users
        add_action('wp_ajax_contact_form_handler', 'contact_form_handler');
        add_action('wp_ajax_nopriv_contact_form_handler', 'contact_form_handler');
    }
