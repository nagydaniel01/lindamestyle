<?php
    if ( ! function_exists( 'add_phpinfo_admin_page' ) ) {
        /**
         * Register the PHP Info page in the WordPress admin menu.
         *
         * @return void
         */
        function add_phpinfo_admin_page() {
            // Only allow access for administrators
            if ( current_user_can('administrator') ) {
                add_menu_page(
                    'PHP Info',             // Page title
                    'PHP Info',             // Menu title
                    'manage_options',       // Capability
                    'custom-phpinfo',       // Menu slug
                    'render_phpinfo_page',  // Callback
                    'dashicons-info',       // Icon
                    99                      // Position
                );
            }
        }

        //add_action('admin_menu', 'add_phpinfo_admin_page');
    }

    if ( ! function_exists( 'render_phpinfo_page' ) ) {
        /**
         * Display the PHP Info admin page content.
         *
         * Outputs the full result of phpinfo() with minimal custom styling for better admin integration.
         *
         * @return void
         */
        function render_phpinfo_page() {
            if ( ! current_user_can('administrator') ) {
                wp_die( __('You do not have sufficient permissions to access this page.'), 403 );
            }

            // Capture the phpinfo output
            ob_start();
            phpinfo();
            $phpinfo = ob_get_clean();

            // Inject custom styles to override default phpinfo() styles
            echo '<style>
                html, body { background-color: #fff !important; color: #000 !important; }
                .h { background-color: #99c !important; }
                .e { background-color: #ccf !important; }
                .v { background-color: #ddd !important; }
            </style>';

            // Output the full phpinfo HTML
            echo $phpinfo;
        }
    }
