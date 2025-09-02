<?php
    if ( ! class_exists( 'WC_Subscriptions' ) ) return;

    /**
     * Redirect to checkout when a subscription product is added to the cart.
     * Works for both single product pages (PHP redirect) and product loops (AJAX redirect).
     */

    if ( ! function_exists( 'my_force_subscription_checkout_redirect' ) ) {
        /**
         * Force redirect to checkout when a subscription product is in the cart
         * or is the product just added.
         *
         * @param string $url Default redirect URL.
         * @return string Checkout URL or default URL.
         */
        function my_force_subscription_checkout_redirect( $url ) {
            if ( ! function_exists( 'wc_get_checkout_url' ) ) {
                return $url;
            }

            $checkout_url = wc_get_checkout_url();

            /**
             * Helper closure: check if a product is a subscription type.
             *
             * @param int $product_id WooCommerce product ID.
             * @return bool
             */
            $is_subscription_product = function( $product_id ) {
                if ( empty( $product_id ) ) {
                    return false;
                }

                // Use WooCommerce Subscriptions API if available
                if ( function_exists( 'wcs_is_subscription' ) && wcs_is_subscription( $product_id ) ) {
                    return true;
                }

                // Fallback: check type manually
                $product = wc_get_product( $product_id );
                if ( ! $product ) {
                    return false;
                }

                return in_array(
                    $product->get_type(),
                    [ 'subscription', 'variable-subscription', 'subscription_variation' ],
                    true
                );
            };

            // If the cart already has a subscription -> redirect
            if ( WC()->cart ) {
                foreach ( WC()->cart->get_cart() as $item ) {
                    if (
                        $is_subscription_product( $item['product_id'] )
                        || $is_subscription_product( $item['variation_id'] ?? 0 )
                    ) {
                        return $checkout_url;
                    }
                }
            }

            // Otherwise, check the product just added
            $added_product_id = absint( $_REQUEST['add-to-cart'] ?? 0 );
            if ( $is_subscription_product( $added_product_id ) ) {
                return $checkout_url;
            }

            return $url;
        }
        add_filter( 'woocommerce_add_to_cart_redirect', 'my_force_subscription_checkout_redirect', 10 );
    }

    if ( ! function_exists( 'my_subscription_ajax_redirect_script' ) ) {
        /**
         * Inject JavaScript to handle redirect after AJAX add-to-cart in product loops.
         */
        function my_subscription_ajax_redirect_script() {
            if ( ! function_exists( 'wc_get_checkout_url' ) ) {
                return;
            }
            ?>
            <script type="text/javascript">
            (function($){
                var checkoutUrl = "<?php echo esc_url( wc_get_checkout_url() ); ?>";

                // Listen for WooCommerce's AJAX add-to-cart event
                $( document.body ).on( 'added_to_cart', function( event, fragments, cart_hash, $button ) {
                    if( !$button || !$button.length ) return;

                    var productId = $button.data('product_id');
                    if( !productId ) return;

                    // AJAX check if the product is a subscription
                    $.post('<?php echo esc_url( admin_url('admin-ajax.php') ); ?>', {
                        action: 'my_check_if_subscription',
                        product_id: productId
                    }, function(response){
                        if( response && response.is_subscription ){
                            window.location.href = checkoutUrl;
                        }
                    });
                });
            })(jQuery);
            </script>
            <?php
        }
        add_action( 'wp_footer', 'my_subscription_ajax_redirect_script' );
    }

    if ( ! function_exists( 'my_check_if_subscription' ) ) {
        /**
         * AJAX callback: check if the given product is a subscription type.
         */
        function my_check_if_subscription() {
            $product_id      = absint( $_POST['product_id'] ?? 0 );
            $is_subscription = false;

            if ( $product_id ) {
                $product = wc_get_product( $product_id );
                if ( $product ) {
                    $is_subscription = in_array(
                        $product->get_type(),
                        [ 'subscription', 'variable-subscription', 'subscription_variation' ],
                        true
                    );
                }
            }

            wp_send_json( [ 'is_subscription' => $is_subscription ] );
        }
        add_action( 'wp_ajax_my_check_if_subscription', 'my_check_if_subscription' );
        add_action( 'wp_ajax_nopriv_my_check_if_subscription', 'my_check_if_subscription' );
    }
