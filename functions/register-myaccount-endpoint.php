<?php
        /**
         * Modify the My Account menu items by adding custom links.
         *
         * @param array $menu_links Existing menu items.
         * @return array Modified menu items with additional custom links.
         */
    if ( ! function_exists( 'my_account_menu_items' ) ) {
        function my_account_menu_items( $menu_links ) {
            $new_items = array(
                'beauty-profile'    => __('Beauty profile', 'woocommerce'),
                'bookmarks'         => __('Bookmarks', 'woocommerce'),
                'email-marketing'   => __('Newsletter', 'woocommerce')
            );
            
            // Add custom items to the My Account menu.
            $menu_links = array_slice( $menu_links, 0, 1, true ) + $new_items + array_slice( $menu_links, 1, NULL, true );

            return $menu_links;
        }

        add_filter( 'woocommerce_account_menu_items', 'my_account_menu_items', 40 );
    }

    /**
     * Register new endpoints for custom sections in the My Account page.
     *
     * @return void
     */
    if ( ! function_exists( 'add_endpoints' ) ) {
        function add_endpoints() {
            // Register custom endpoints for each new section.
            add_rewrite_endpoint( 'beauty-profile', EP_ROOT | EP_PAGES );
            add_rewrite_endpoint( 'bookmarks', EP_ROOT | EP_PAGES );
            add_rewrite_endpoint( 'email-marketing', EP_ROOT | EP_PAGES );
        }

        add_action( 'init', 'add_endpoints' );
    }

    /**
     * Display the content for the 'Beauty profile' section in My Account.
     *
     * @return void
     */
    if ( ! function_exists( 'beauty_profile_endpoint_content' ) ) {
        function beauty_profile_endpoint_content() {
            // Include custom template for 'Beauty profile' endpoint.
            wc_get_template( 'myaccount/beauty-profile.php' );
        }

        add_action( 'woocommerce_account_beauty-profile_endpoint', 'beauty_profile_endpoint_content' );
    }

    /**
     * Display the content for the 'Bookmarks' section in My Account.
     *
     * @return void
     */
    if ( ! function_exists( 'bookmarks_endpoint_content' ) ) {
        function bookmarks_endpoint_content() {
            // Include custom template for 'Bookmarks' endpoint.
            wc_get_template( 'myaccount/bookmarks.php' );
        }

        add_action( 'woocommerce_account_bookmarks_endpoint', 'bookmarks_endpoint_content' );
    }

    /**
     * Display the content for the 'Newsletter' section in My Account.
     *
     * @return void
     */
    if ( ! function_exists( 'email_marketing_content' ) ) {
        function email_marketing_content() {
            // Include custom template for 'Newsletter' endpoint.
            wc_get_template( 'myaccount/email-marketing.php' );
        }

        add_action( 'woocommerce_account_email-marketing_endpoint', 'email_marketing_content' );
    }