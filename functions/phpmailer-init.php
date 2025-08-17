<?php
    if ( ! function_exists( 'mailtrap' ) ) {
        /**
         * Configure PHPMailer to use Mailtrap SMTP for local email testing.
         *
         * @param PHPMailer $phpmailer The PHPMailer instance to configure.
         * 
         * @return void
         */
        function mailtrap( $phpmailer ) {
            $phpmailer->isSMTP();
            $phpmailer->Host       = 'sandbox.smtp.mailtrap.io';
            $phpmailer->SMTPAuth   = true;
            $phpmailer->Port       = 2525;
            $phpmailer->Username   = '03e7fa1f5124ab';
            $phpmailer->Password   = '4be51df465bc15';
        }

        // Uncomment to activate Mailtrap config
        //add_action( 'phpmailer_init', 'mailtrap' );
    }

    if ( ! function_exists( 'rd_mailtrap' ) ) {
        /**
         * Configure PHPMailer to use an alternative Mailtrap SMTP account for local email testing.
         *
         * @param PHPMailer $phpmailer The PHPMailer instance to configure.
         * 
         * @return void
         */
        function rd_mailtrap( $phpmailer ) {
            $phpmailer->isSMTP();
            $phpmailer->Host       = 'sandbox.smtp.mailtrap.io';
            $phpmailer->SMTPAuth   = true;
            $phpmailer->Port       = 2525;
            $phpmailer->Username   = 'e259dd697fd18f';
            $phpmailer->Password   = '67e22e4ddb9475';
        }

        // Uncomment to activate Mailtrap config
        add_action( 'phpmailer_init', 'rd_mailtrap' );
    }

    if ( ! function_exists( 'log_mailer_errors' ) ) {
        /**
         * Logs email sending errors to a log file.
         *
         * @param WP_Error $wp_error The WordPress error object.
         */
        function log_mailer_errors( WP_Error $wp_error ) {
            $log_file = ABSPATH . 'mail.log'; // Absolute path to log file
    
            // Format the error message with timestamp
            $error_message = sprintf(
                "[%s] Mailer Error: %s\n",
                date('d-M-Y H:i:s T'),
                $wp_error->get_error_message()
            );
    
            // Attempt to append the error message to the log file
            try {
                if (!file_put_contents($log_file, $error_message, FILE_APPEND | LOCK_EX)) {
                    error_log('Unable to write to mail log file: ' . $log_file);
                }
            } catch ( Exception $e ) {
                error_log('Exception caught while logging mailer error: ' . $e->getMessage());
            }
        }
    
        add_action('wp_mail_failed', 'log_mailer_errors', 10, 1);
    }
    