<?php
    if ( ! function_exists( 'quantity_plus_sign' ) ) {
        /**
         * Output the plus button after the quantity input field.
         */
        function quantity_plus_sign() {
            global $product;

            // Skip if product is sold individually
            if ( $product && $product->is_sold_individually() ) {
                return;
            }

            echo get_quantity_plus_sign();
        }

        /**
         * Returns the HTML for the plus button.
         *
         * @return string
         */
        function get_quantity_plus_sign() {
            return '<button type="button" class="btn btn-primary btn-sm plus">
                        <svg class="icon icon-plus">
                            <use xlink:href="#icon-plus"></use>
                        </svg>
                    </button>';
        }

        add_action( 'woocommerce_after_quantity_input_field', 'quantity_plus_sign' );
    }

    if ( ! function_exists( 'quantity_minus_sign' ) ) {
        /**
         * Output the minus button before the quantity input field.
         */
        function quantity_minus_sign() {
            global $product;

            // Skip if product is sold individually
            if ( $product && $product->is_sold_individually() ) {
                return;
            }

            echo get_quantity_minus_sign();
        }

        /**
         * Returns the HTML for the minus button.
         *
         * @return string
         */
        function get_quantity_minus_sign() {
            return '<button type="button" class="btn btn-primary btn-sm minus">
                        <svg class="icon icon-minus">
                            <use xlink:href="#icon-minus"></use>
                        </svg>
                    </button>';
        }

        add_action( 'woocommerce_before_quantity_input_field', 'quantity_minus_sign' );
    }

    if ( ! function_exists( 'add_cod_fee_dynamic_translatable' ) ) {
        /**
         * Add a dynamic, translatable COD fee to the cart.
         *
         * @param WC_Cart $cart WooCommerce cart object.
         */
        function add_cod_fee_dynamic_translatable( $cart ) {
            if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
                return;
            }

            $chosen_payment_method = WC()->session->get( 'chosen_payment_method' );
            $available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

            if ( isset( $available_gateways[ $chosen_payment_method ] ) ) {
                $gateway = $available_gateways[ $chosen_payment_method ];

                $total_fee = 100; // Total amount including tax
                $taxable   = wc_tax_enabled(); // Apply tax if taxes are enabled
                $tax_class = ''; // Default tax class

                if ( $taxable ) {
                    // Get tax rate for the selected class
                    $tax_rates = WC_Tax::get_rates( $tax_class );
                    $first_rate = reset($tax_rates); // Get first rate safely
                    $rate = isset($first_rate['rate']) ? floatval($first_rate['rate']) : 0;

                    // Calculate pre-tax fee so total
                    $fee_amount = ($rate > 0) ? $total_fee / (1 + $rate / 100) : $total_fee;
                } else {
                    // Not taxable, just total
                    $fee_amount = $total_fee;
                }

                $fee_name = sprintf( __( '%s díja', TEXT_DOMAIN ), $gateway->get_title() );
                $cart->add_fee( $fee_name, $fee_amount, $taxable, $tax_class );
            }
        }
        add_action( 'woocommerce_cart_calculate_fees', 'add_cod_fee_dynamic_translatable' );
    }
