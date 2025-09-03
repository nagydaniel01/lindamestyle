<?php
    if ( ! function_exists( 'thank_you_query_shortcode' ) ) {
        /**
         * Shortcode to display data securely based on URL parameters:
         * - attendee_id: return attendee post title
         * - message_id: return stored form submission data (from transient)
         * Redirects to homepage if attendee_id is missing or invalid.
         *
         * Usage: [thank_you_query]
         *
         * @param array $atts Shortcode attributes (unused)
         * @return string Output
         */
        function thank_you_query_shortcode( $atts ) {

            // Ensure $_GET is available
            if ( ! isset( $_GET ) || ! is_array( $_GET ) ) {
                return wpautop( esc_html__( 'Error: No query parameters found.', TEXT_DOMAIN ) );
            }

            $query_vars = $_GET;

            // Handle attendee_id
            if ( isset( $query_vars['attendee_id'] ) && intval( $query_vars['attendee_id'] ) > 0 ) {

                $post_id = intval( $query_vars['attendee_id'] );
                $post    = get_post( $post_id );

                if ( $post && $post->post_type === 'attendee' ) {

                    // Fetch attendee meta
                    $attendee_name  = get_post_meta( $post_id, 'attendee_name', true );
                    $attendee_email = get_post_meta( $post_id, 'attendee_email', true );
                    $event_id       = get_post_meta( $post_id, 'event_id', true );

                    // Get event
                    $event_title = get_the_title( $event_id );
                    $event_link  = get_permalink( $event_id );

                    // Build message
                    $output = sprintf(
                        /* translators: 1: Attendee name, 2: Attendee email, 3: Event URL, 4: Event title */
                        wpautop( esc_html__( 'Hi %1$s (%2$s)! You are all set and registered for %4$s.', TEXT_DOMAIN ) ),
                        esc_html( $attendee_name ),
                        esc_html( $attendee_email ),
                        esc_url( $event_link ),
                        '<a href="' . esc_url( $event_link ) . '">' . esc_html( $event_title ) . '</a>'
                    );

                    $output .= sprintf(
                        /* translators: Welcome message after registration */
                        wpautop( esc_html__( 'Welcome to the event! We’re excited to have you join us!', TEXT_DOMAIN ) )
                    );

                    return $output;
                }

                // attendee_id invalid → redirect
                wp_safe_redirect( home_url() );
                exit;
            }

            // Handle message_id securely
            if ( isset( $query_vars['message_id'] ) ) {

                $message_id = sanitize_text_field( $query_vars['message_id'] );

                if ( empty( $message_id ) ) {
                    return wpautop( esc_html__( 'Error: Missing or invalid message ID.', TEXT_DOMAIN ) );
                }

                $data = get_transient( 'contact_form_' . $message_id );

                if ( ! $data || ! is_array( $data ) ) {
                    return wpautop( esc_html__( 'Invalid or expired link.', TEXT_DOMAIN ) );
                }

                // Define translatable labels for the keys
                $translations = [
                    'name'    => __( 'Name', TEXT_DOMAIN ),
                    'email'   => __( 'Email', TEXT_DOMAIN ),
                    'phone'   => __( 'Phone', TEXT_DOMAIN ),
                    'subject' => __( 'Subject', TEXT_DOMAIN ),
                    'message' => __( 'Message', TEXT_DOMAIN ),
                ];

                $output = '<ul class="list-unstyled">';
                foreach ( $data as $key => $value ) {

                    // Skip empty values
                    if ( empty( $value ) ) {
                        continue;
                    }

                    // Use translated label if available, fallback to ucfirst
                    $label = isset( $translations[ $key ] ) ? $translations[ $key ] : ucfirst( $key );

                    $output .= sprintf(
                        '<li><strong>%s:</strong> %s</li>',
                        esc_html( $label ),
                        wpautop( esc_html( $value ) ) // apply wpautop to each value
                    );
                }
                $output .= '</ul>';

                return $output;
            }

            return wpautop( esc_html__( 'No data available. Please use a valid link.', TEXT_DOMAIN ) );
        }
        add_shortcode( 'thank_you_query', 'thank_you_query_shortcode' );
    }
