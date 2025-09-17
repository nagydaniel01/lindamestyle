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

                    $google_url = get_add_to_calendar_url( $event_id );
                    $ics_url    = get_add_to_calendar_ics( $event_id );

                    if ( $google_url ) {
                        $output .= '<p><a href="' . esc_url( $google_url ) . '" target="_blank" rel="noopener noreferrer" class="button add-to-calendar">';
                        $output .= esc_html__( 'Add to Google Calendar', 'your-text-domain' );
                        $output .= '</a></p>';
                    }

                    if ( $ics_url ) {
                        $output .= '<p><a href="' . esc_url( $ics_url ) . '" download class="button add-to-calendar">';
                        $output .= esc_html__( 'Add to Apple / Outlook Calendar', 'your-text-domain' );
                        $output .= '</a></p>';
                    }

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

    if ( ! function_exists( 'custom_wc_registration_form_shortcode' ) ) {
        /**
         * Registration Form Shortcode
         *
         * Displays only the WooCommerce registration form.
         * If the user is logged in, shows a message instead.
         *
         * @return string HTML output for registration form or message.
         */
        function custom_wc_registration_form_shortcode() {
            if ( is_user_logged_in() ) {
                return '<p>' . esc_html__( 'You are already registered.', 'woocommerce' ) . '</p>';
            }

            ob_start();

            do_action( 'woocommerce_before_customer_login_form' );

            $html = wc_get_template_html( 'myaccount/form-login.php' );

            if ( empty( $html ) ) {
                return '<p>' . esc_html__( 'Registration form not available.', 'woocommerce' ) . '</p>';
            }

            libxml_use_internal_errors( true );

            $dom = new DOMDocument();
            $dom->encoding = 'utf-8';

            $loaded = $dom->loadHTML(
                '<?xml encoding="utf-8" ?>' . $html,
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
            );

            libxml_clear_errors();

            if ( ! $loaded ) {
                return '<p>' . esc_html__( 'Error loading registration form.', 'woocommerce' ) . '</p>';
            }

            $xpath = new DOMXPath( $dom );
            $form  = $xpath->query( '//form[contains(@class,"register")]' );
            $form  = $form->item( 0 );

            if ( $form ) {
                echo $dom->saveHTML( $form );
            } else {
                echo '<p>' . esc_html__( 'Registration form not found.', 'woocommerce' ) . '</p>';
            }

            return ob_get_clean();
        }
        add_shortcode( 'custom_wc_registration_form', 'custom_wc_registration_form_shortcode' );
    }

    if ( ! function_exists( 'custom_wc_login_form_shortcode' ) ) {
        /**
         * Login Form Shortcode
         *
         * Displays only the WooCommerce login form.
         * If the user is logged in, shows a message instead.
         *
         * @return string HTML output for login form or message.
         */
        function custom_wc_login_form_shortcode() {
            if ( is_user_logged_in() ) {
                return '<p>' . esc_html__( 'You are already logged in.', 'woocommerce' ) . '</p>';
            }

            ob_start();

            do_action( 'woocommerce_before_customer_login_form' );

            woocommerce_login_form( [
                'redirect' => wc_get_page_permalink( 'myaccount' ),
            ] );

            return ob_get_clean();
        }
        add_shortcode( 'custom_wc_login_form', 'custom_wc_login_form_shortcode' );
    }

    if ( ! function_exists( 'custom_wc_redirect_logged_in_users' ) ) {
        /**
         * Redirect Logged-In Users Away From Login/Registration Shortcodes
         *
         * If a logged-in user tries to access a page containing
         * the login or registration shortcodes, redirect them
         * to the "My Account" page instead.
         *
         * @return void
         */
        function custom_wc_redirect_logged_in_users() {
            if ( ! is_user_logged_in() || ! is_page() ) {
                return;
            }

            global $post;

            if ( ! $post instanceof WP_Post ) {
                return;
            }

            $content = $post->post_content;

            if ( has_shortcode( $content, 'custom_wc_login_form' ) || has_shortcode( $content, 'custom_wc_registration_form' ) ) {
                wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
                exit;
            }
        }
        add_action( 'template_redirect', 'custom_wc_redirect_logged_in_users' );
    }