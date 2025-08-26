<?php
    if ( ! function_exists( 'hide_downloads_tab_my_account' ) ) {
        /**
         * Hide downloads tab in My Account if customer has no downloads.
         */
        function hide_downloads_tab_my_account( $items ) {
            $downloads = ! empty( WC()->customer ) ? WC()->customer->get_downloadable_products() : false;
            $has_downloads = (bool) $downloads;

            if ( ! $has_downloads ) {
                unset( $items['downloads'] );
            }

            return $items;
        }
        add_filter( 'woocommerce_account_menu_items', 'hide_downloads_tab_my_account', 9999 );
    }

    if ( ! function_exists( 'my_account_orders_filter_by_status' ) ) {
        /**
         * Filter My Account orders by status from URL parameter.
         */
        function my_account_orders_filter_by_status( $args ) {
            if ( isset( $_GET['status'] ) && ! empty( $_GET['status'] ) ) {
                $args['status'] = array( sanitize_text_field( $_GET['status'] ) );
            }
            return $args;
        }
        add_filter( 'woocommerce_my_account_my_orders_query', 'my_account_orders_filter_by_status' );
    }

    if ( ! function_exists( 'my_account_orders_filters' ) ) {
        /**
         * Display order status filters in My Account orders page.
         */
        function my_account_orders_filters() {
            echo '<p>' . esc_html__( 'Filter by:', TEXT_DOMAIN ) . ' ';
            $customer_orders = 0;

            foreach ( wc_get_order_statuses() as $slug => $name ) {
                $status_orders = count( wc_get_orders( [
                    'status'   => $slug,
                    'customer' => get_current_user_id(),
                    'limit'    => -1,
                ] ) );

                if ( $status_orders > 0 ) {
                    $name_esc = esc_html( $name );
                    if ( isset( $_GET['status'] ) && $_GET['status'] === $slug ) {
                        echo '<b>' . $name_esc . ' (' . $status_orders . ')</b><span class="delimit"> - </span>';
                    } else {
                        echo '<a href="' . esc_url( add_query_arg( 'status', $slug, wc_get_endpoint_url( 'orders' ) ) ) . '">' . $name_esc . ' (' . $status_orders . ')</a><span class="delimit"> - </span>';
                    }
                }

                $customer_orders += $status_orders;
            }

            if ( isset( $_GET['status'] ) && ! empty( $_GET['status'] ) ) {
                echo '<a href="' . esc_url( remove_query_arg( 'status' ) ) . '">' . sprintf( esc_html__( 'All statuses (%d)', TEXT_DOMAIN ), $customer_orders ) . '</a>';
            } else {
                echo '<b>' . sprintf( esc_html__( 'All statuses (%d)', TEXT_DOMAIN ), $customer_orders ) . '</b>';
            }

            echo '</p>';
        }
        add_action( 'woocommerce_before_account_orders', 'my_account_orders_filters' );
    }

    if ( ! function_exists( 'my_account_orders_filter_by_status_pagination' ) ) {
        /**
         * Fix My Account orders pagination for filtered orders.
         */
        function my_account_orders_filter_by_status_pagination( $url, $endpoint, $value, $permalink ) {
            if ( 'orders' === $endpoint && isset( $_GET['status'] ) && ! empty( $_GET['status'] ) ) {
                return add_query_arg( 'status', sanitize_text_field( $_GET['status'] ), $url );
            }
            return $url;
        }

        add_action( 'woocommerce_before_account_orders', function() {
            add_filter( 'woocommerce_get_endpoint_url', 'my_account_orders_filter_by_status_pagination', 9999, 4 );
        });
    }

    if ( ! function_exists( 'custom_my_account_endpoint_titles' ) ) {
        /**
         * Change WooCommerce My Account page title dynamically
         */
        function custom_my_account_endpoint_titles( $title, $id ) {
            // Run only on frontend, My Account page, and main queried object
            if ( is_account_page() && ! is_admin() && get_queried_object_id() === $id && is_user_logged_in()) {
                
                global $wp_query;

                // List of endpoints and their custom titles
                $titles = array(
                    'dashboard'       => __( 'Dashboard', 'woocommerce' ),
                    'orders'          => __( 'Orders', 'woocommerce' ),
                    'downloads'       => __( 'Downloads', 'woocommerce' ),
                    'edit-address'    => __( 'Address', 'woocommerce' ),
                    'edit-account'    => __( 'Account details', 'woocommerce' ),
                    'customer-logout' => __( 'Logout', 'woocommerce' ),

                    // WooCommerce Subscriptions
                    'subscriptions'      => __( 'My Subscriptions', 'woocommerce-subscriptions' ),
                    'view-subscription'  => __( 'Subscription Details', 'woocommerce-subscriptions' ),

                    // WooCommerce Memberships
                    'members-area'       => __( 'My Memberships', 'woocommerce-memberships' ),
                    'view-membership'    => __( 'Membership Details', 'woocommerce-memberships' ),
                );

                foreach ( $titles as $endpoint => $endpoint_title ) {
                    if ( isset( $wp_query->query_vars[ $endpoint ] ) ) {
                        return $endpoint_title;
                    }
                }

                // Default title for My Account root (with username if logged in)
                $current_user = wp_get_current_user();
                $first_name   = $current_user->first_name ?? '';
                $last_name    = $current_user->last_name ?? '';
                $display_name = $current_user->display_name ?? '';
                $user_name    = $display_name ? $display_name : $first_name;

                return sprintf( __( 'Hello %s!', TEXT_DOMAIN ), $user_name );
            }

            return $title;
        }
        add_filter( 'the_title', 'custom_my_account_endpoint_titles', 10, 2 );
    }

    if ( ! function_exists( 'display_last_login_before_account_navigation' ) ) {
        // Display last login and memberships before My Account navigation
        function display_last_login_before_account_navigation() {
            if ( is_account_page() && is_user_logged_in() ) {

                $current_user = wp_get_current_user();
                $current_user_id = $current_user->ID;
                $last_login   = get_user_meta( $current_user_id, 'last_login', true );

                // Display last login
                if ( ! empty( $last_login ) ) {
                    $formatted_date = date_i18n( 'Y. F d., H:i', strtotime( $last_login ) );
                    echo '<p class="my-account-last-login">' . esc_html__( 'Your last login was on:', TEXT_DOMAIN ) . ' ' . esc_html( $formatted_date ) . '</p>';
                }

                // Display memberships as notices
                if ( class_exists( 'WC_Memberships' ) ) {
                    echo '<div class="woocommerce-notices-wrapper">';
                    $memberships = wc_memberships_get_user_active_memberships( $current_user_id );

                    if ( empty( $memberships ) ) {
                        wc_add_notice( __( 'You have no active membership.', 'woocommerce-memberships' ), 'notice' );
                    } else {
                        foreach ( $memberships as $membership ) {
                            $plan_name = $membership->get_plan()->get_name();
                            wc_add_notice( sprintf( __( 'Your active membership plan: %s', 'woocommerce-memberships' ), esc_html( $plan_name ) ), 'notice' );
                        }
                    }

                    // Print all notices immediately
                    wc_print_notices();
                    echo '</div>';
                }
            }
        }
        //add_action( 'woocommerce_before_account_navigation', 'display_last_login_before_account_navigation', 5 );
    }