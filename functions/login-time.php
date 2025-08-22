<?php
    if ( ! function_exists( 'set_last_login' ) ) {
        /**
         * Set the last login time for the user.
         *
         * @param string $login The username of the user who logged in.
         * @return void
         */
        function set_last_login( $login ) {
            $user               = get_user_by( 'login', $login );
            $current_login_time  = get_user_meta( $user->ID, 'current_login', true );

            if ( ! empty( $current_login_time ) ) {
                update_user_meta( $user->ID, 'last_login', $current_login_time );
                update_user_meta( $user->ID, 'current_login', current_time( 'mysql' ) );
            } else {
                update_user_meta( $user->ID, 'current_login', current_time( 'mysql' ) );
                update_user_meta( $user->ID, 'last_login', current_time( 'mysql' ) );
            }
        }

        add_action( 'wp_login', 'set_last_login' );
    }

    if ( ! function_exists( 'get_last_login' ) ) {
        /**
         * Retrieve the last login time for the user.
         *
         * @param int $user_id The ID of the user to retrieve the last login for.
         * @return string Formatted date string of last login time.
         */
        function get_last_login( $user_id ) {
            $last_login  = get_user_meta( $user_id, 'last_login', true );
            $date_format = 'Y-m-d H:i';

            if ( wp_is_mobile() ) {
                $the_last_login = date( "M j, y, g:i a", strtotime( $last_login ) );
            } else {
                $the_last_login = mysql2date( $date_format, $last_login, false );
            }

            return $the_last_login;
        }
    }
