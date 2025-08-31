<?php
    if ( ! function_exists('contact_form_handler') ) {
        function contact_form_handler() {
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
                if ( ! isset($form['contact_form_nonce']) ||
                    ! wp_verify_nonce($form['contact_form_nonce'], 'contact_form_action') ) {
                    wp_send_json_error(['message' => __('Invalid security token.', TEXT_DOMAIN)], 403);
                }

                // Extract fields safely
                $name    = isset($form['name']) ? sanitize_text_field($form['name']) : '';
                $email   = isset($form['email']) ? sanitize_email($form['email']) : '';
                $phone   = isset($form['phone']) ? sanitize_text_field($form['phone']) : '';
                $subject = isset($form['subject']) ? sanitize_text_field($form['subject']) : '';
                $message = isset($form['message']) ? sanitize_textarea_field($form['message']) : '';

                // Validate inputs
                if ( empty($name) || empty($email) || empty($subject) || empty($message) ) {
                    wp_send_json_error(['message' => __('All required fields must be filled out.', TEXT_DOMAIN)], 422);
                }

                if ( ! is_email($email) ) {
                    wp_send_json_error(['message' => __('Invalid email format.', TEXT_DOMAIN)], 422);
                }

                // Prepare email
                $admin_email = get_option('admin_email');
                if ( ! $admin_email || ! is_email($admin_email) ) {
                    wp_send_json_error(['message' => __('Admin email is not configured properly.', TEXT_DOMAIN)], 500);
                }

                $headers = [
                    'From: ' . get_bloginfo('name') . ' <' . $admin_email . '>',
                    'Reply-To: ' . $name . ' <' . $email . '>',
                    'Content-Type: text/html; charset=UTF-8'
                ];
                
                $mail_subject = sprintf(
                    __('[%s] New message: %s', TEXT_DOMAIN), // Adding site name in brackets
                    get_bloginfo('name'),
                    $subject
                );

                // Split message into lines, remove empty lines, and wrap each in <p>
                $message_lines = array_filter(preg_split("/\r\n|\n|\r/", $message), function($line) {
                    return trim($line) !== '';
                });

                $formatted_message = implode('', array_map(function($line) {
                    return '<p>' . esc_html($line) . '</p>';
                }, $message_lines));

                // Prepare email body
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

                // Try sending the email
                $sent = wp_mail($admin_email, $mail_subject, $mail_message, $headers);

                if ( ! $sent ) {
                    wp_send_json_error(['message' => __('Message could not be sent. Please try again later.', TEXT_DOMAIN)], 500);
                }

                // Success response
                wp_send_json_success(['message' => __('Your message has been sent successfully!', TEXT_DOMAIN)]);

            } catch ( Exception $e ) {
                wp_send_json_error([
                    'message' => sprintf(__('Unexpected error: %s', TEXT_DOMAIN), $e->getMessage())
                ], 500);
            }
        }

        add_action('wp_ajax_contact_form_handler', 'contact_form_handler');
        add_action('wp_ajax_nopriv_contact_form_handler', 'contact_form_handler');
    }
