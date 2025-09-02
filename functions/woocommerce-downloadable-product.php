<?php
    if ( ! class_exists('WooCommerce') ) return;

    if ( ! function_exists('custom_add_to_cart_or_download_button') ) {
        /**
         * Replace 'Add to Cart' button with download button if the user has purchased the product.
         *
         * @param string     $button  Original add-to-cart button HTML.
         * @param WC_Product $product WooCommerce product object.
         * @return string
         */
        function custom_add_to_cart_or_download_button($button, $product) {
            if ( ! $product instanceof WC_Product ) return $button;
            return function_exists('get_download_button') ? get_download_button($product) ?: $button : $button;
        }

        add_filter('woocommerce_loop_add_to_cart_link', 'custom_add_to_cart_or_download_button', 10, 2);
    }

    if ( ! function_exists('replace_single_product_buttons') ) {
        /**
         * Replace price and add-to-cart button on single product page if user already purchased.
         *
         * @return void
         */
        function replace_single_product_buttons() {
            global $product;

            if ( ! $product instanceof WC_Product ) return;

            if ( is_user_logged_in() && function_exists('has_user_purchased_product') && has_user_purchased_product($product->get_id()) ) {
                remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
                remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
                add_action('woocommerce_single_product_summary', 'output_download_button', 31);
            }
        }

        add_action('woocommerce_single_product_summary', 'replace_single_product_buttons', 29);
    }

    if ( ! function_exists('output_download_button') ) {
        /**
         * Echo download button for a product.
         *
         * @return void
         */
        function output_download_button() {
            global $product;

            if ( ! $product instanceof WC_Product ) return;
            if ( function_exists('get_download_button') ) {
                echo get_download_button($product);
            }
        }
    }

    if ( ! function_exists('get_download_button') ) {
        /**
         * Return download button HTML if user has purchased the product, otherwise false.
         *
         * @param WC_Product $product WooCommerce product object.
         * @return string|false
         */
        function get_download_button($product) {
            if ( ! is_user_logged_in() || ! $product->is_downloadable() ) return false;
            if ( ! function_exists('get_valid_download_url') ) return false;

            $download_url = get_valid_download_url($product->get_id());
            if ( ! $download_url ) return false;

            return sprintf(
                '<a class="button" href="%s" download="download"><span>%s</span></a>',
                esc_url($download_url),
                esc_html__('Download', TEXT_DOMAIN)
            );
        }
    }

    if ( ! function_exists('has_user_purchased_product') ) {
        /**
         * Check if the current user has purchased a product.
         *
         * @param int $product_id Product ID.
         * @return bool
         */
        function has_user_purchased_product($product_id) {
            if ( ! function_exists('wc_customer_bought_product') ) return false;
            return wc_customer_bought_product('', get_current_user_id(), $product_id);
        }
    }

    if ( ! function_exists('get_valid_download_url') ) {
        /**
         * Get a valid download URL for purchased product.
         *
         * @param int $product_id Product ID.
         * @return string Download URL or empty string.
         */
        function get_valid_download_url($product_id) {
            if ( ! function_exists('wc_get_customer_available_downloads') ) return '';

            $downloads = wc_get_customer_available_downloads(get_current_user_id());
            foreach ($downloads as $download) {
                if ( $download['product_id'] !== $product_id ) continue;

                $access_expires      = $download['access_expires'] ?? null;
                $downloads_remaining = $download['downloads_remaining'] ?? null;

                if ( ($access_expires && strtotime($access_expires) <= time()) || ($downloads_remaining === 0) ) {
                    continue;
                }

                return $download['download_url'] ?? '';
            }

            return '';
        }
    }

    if ( ! function_exists('show_purchase_notice') ) {
        /**
         * Show notice if the user has already purchased the product.
         *
         * @return void
         */
        function show_purchase_notice() {
            global $product;

            if ( ! is_user_logged_in() || ! $product instanceof WC_Product ) return;

            $user            = wp_get_current_user();
            $product_id      = $product->get_id();
            $download_url    = function_exists('get_valid_download_url') ? get_valid_download_url($product_id) : '';
            $add_to_cart_url = wc_get_cart_url() . '?add-to-cart=' . $product_id;

            if ( function_exists('has_user_purchased_product') && has_user_purchased_product($product_id) ) {
                if ( $download_url ) {
                    wc_print_notice(
                        sprintf(
                            __('Hey %s, you\'ve purchased this in the past.', TEXT_DOMAIN),
                            esc_html($user->first_name)
                        ),
                        'notice'
                    );
                } else {
                    wc_print_notice(
                        sprintf(
                            __('Hey %s, you\'ve purchased this before, but the file is no longer available. <a href="%s" class="button">Buy Again</a>', TEXT_DOMAIN),
                            esc_html($user->first_name),
                            esc_url($add_to_cart_url)
                        ),
                        'notice'
                    );
                }
            }
        }

        add_action('woocommerce_before_single_product', 'show_purchase_notice', 10);
    }
