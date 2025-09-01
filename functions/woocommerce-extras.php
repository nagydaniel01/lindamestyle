<?php
    // Change WooCommerce image sizes programmatically
    if ( ! function_exists( 'custom_woocommerce_image_sizes' ) ) {
        /**
         * Customize WooCommerce product image sizes via filters.
         *
         * This snippet adjusts the dimensions for:
         * - Gallery thumbnails (below main product image)
         * - Single product main image
         * - Shop/category thumbnails
         *
         * After making changes, remember to regenerate thumbnails so the new sizes take effect.
         */
        function custom_woocommerce_image_sizes() {

            // Shop/category thumbnails
            add_filter( 'woocommerce_get_image_size_thumbnail', function( $size ) {
                return array(
                    'width'  => 400,
                    'height' => 400,
                    'crop'   => 1,
                );
            });
            
            // Single product main image
            add_filter( 'woocommerce_get_image_size_single', function( $size ) {
                return array(
                    'width'  => 800,
                    'height' => 800,
                    'crop'   => 1,
                );
            });

            // Gallery thumbnails (below main image)
            add_filter( 'woocommerce_get_image_size_gallery_thumbnail', function( $size ) {
                return array(
                    'width'  => 600,
                    'height' => 600,
                    'crop'   => 1,
                );
            });

        }
        add_action( 'after_setup_theme', 'custom_woocommerce_image_sizes', 10 );
    }

    if ( ! function_exists( 'custom_hu_address_format' ) ) {
        /**
         * Modify the WooCommerce address format for Hungary (HU) 
         * to display the company name first.
         *
         * @param array $formats Associative array of country address formats.
         * @return array Modified address formats with HU customized.
         */
        function custom_hu_address_format( $formats ) {
            // Set Hungarian address format with company first
            $formats['HU'] = "{company}\n{name}\n{postcode} {city}\n{address_1} {address_2}\n{country}";
            return $formats;
        }
        add_filter( 'woocommerce_localisation_address_formats', 'custom_hu_address_format' );
    }

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

    if ( ! function_exists( 'wc_loop_show_categories_list' ) ) {
        /**
         * Display linked product categories under the product title in the loop.
         *
         * Uses wc_get_product_category_list() to output a comma-separated list of
         * linked categories. Placed outside the main product link for valid HTML.
         *
         * @return void
         */
        function wc_loop_show_categories_list() {
            global $product;

            if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
                return;
            }

            // Get linked categories, comma-separated
            $cats = wc_get_product_category_list( $product->get_id(), ', ' );

            if ( $cats ) {
                echo '<div class="woocommerce-loop-product__categories">' . $cats . '</div>';
            }
        }
        add_action( 'woocommerce_after_shop_loop_item', 'wc_loop_show_categories_list', 5 );
    }
    
    if ( ! function_exists( 'show_product_sku_in_loop' ) ) {
        /**
         * Display product SKU in the WooCommerce product loop.
         *
         * @return void
         */
        function show_product_sku_in_loop() {
            global $product;

            if ( ! $product || ! is_a( $product, 'WC_Product' ) ) return;

            $sku = $product->get_sku();
            if ( $sku ) {
                echo '<p class="product-sku"><strong>' . esc_html__( 'SKU:', TEXT_DOMAIN ) . '</strong> ' . esc_html( $sku ) . '</p>';
            }
        }
        add_action( 'woocommerce_after_shop_loop_item_title', 'show_product_sku_in_loop', 15 );
    }

    if ( ! function_exists( 'show_product_stock_in_loop' ) ) {
        /**
         * Display product stock status in the WooCommerce product loop.
         *
         * Uses WooCommerce's public get_availability() method.
         *
         * @return void
         */
        function show_product_stock_in_loop() {
            global $product;

            if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
                return;
            }

            // Get availability array (includes text + CSS class)
            $availability = $product->get_availability();

            if ( ! empty( $availability['availability'] ) ) {
                echo '<p class="product-stock">' . esc_html( $availability['availability'] ) . '</p>';
            }
        }
        add_action( 'woocommerce_after_shop_loop_item_title', 'show_product_stock_in_loop', 20 );
    }

    if ( ! function_exists( 'show_product_attributes_in_loop' ) ) {
        /**
         * Display specific product attributes in the WooCommerce product loop.
         *
         * This function loops through a predefined list of attribute slugs (without the 'pa_' prefix)
         * and outputs their values under the product title in the shop/archive loop.
         * Only attributes marked as "Visible on product page" will be displayed.
         *
         * @return void
         */
        function show_product_attributes_in_loop() {
            global $product;

            if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
                return;
            }

            // Attributes to show (slugs without 'pa_' prefix for taxonomy attributes)
            $attributes_to_show = array( 'meret' ); // Add more slugs as needed

            $product_attributes = $product->get_attributes();

            echo '<div class="woocommerce-loop-product__attributes">';

            foreach ( $attributes_to_show as $slug ) {

                // Attempt with 'pa_' prefix first (for taxonomy attributes)
                $taxonomy_slug = 'pa_' . $slug;

                if ( isset( $product_attributes[ $taxonomy_slug ] ) ) {
                    $attribute = $product_attributes[ $taxonomy_slug ];
                } elseif ( isset( $product_attributes[ $slug ] ) ) { 
                    $attribute = $product_attributes[ $slug ]; // fallback to custom attribute
                } else {
                    continue; // attribute not found
                }

                // Only show if attribute is visible on the product page
                if ( ! $attribute->get_visible() ) {
                    continue;
                }

                $name = wc_attribute_label( $attribute->get_name() );

                // Get attribute values
                if ( $attribute->is_taxonomy() ) {
                    $values = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) );
                    $values = implode( ', ', $values );
                } else {
                    $values = $attribute->get_options();
                    $values = implode( ', ', $values );
                }

                echo '<p class="product-attribute"><strong>' . esc_html( $name ) . ':</strong> ' . esc_html( $values ) . '</p>';
            }

            echo '</div>';
        }
        add_action( 'woocommerce_after_shop_loop_item_title', 'show_product_attributes_in_loop', 25 );
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

    if ( ! function_exists( 'change_related_products_heading' ) ) {
        /**
         * Change the "Related products" heading text.
         *
         * @return string Custom heading text.
         */
        function change_related_products_heading() {
            return __( 'You will definitely like', TEXT_DOMAIN );
        }
        add_filter( 'woocommerce_product_related_products_heading', 'change_related_products_heading' );
    }

    if ( ! function_exists( 'change_cross_sells_heading' ) ) {
        /**
         * Change the "You may be interested in…" cross-sells heading text.
         *
         * @return string Custom heading text.
         */
        function change_cross_sells_heading() {
            return __( 'Customers also bought', TEXT_DOMAIN );
        }
        add_filter( 'woocommerce_product_cross_sells_products_heading', 'change_cross_sells_heading' );
    }

    if ( ! function_exists( 'change_upsells_heading' ) ) {
        /**
         * Change the "You may also like…" upsells heading text.
         *
         * @return string Custom heading text.
         */
        function change_upsells_heading() {
            return __( 'Handpicked for you', TEXT_DOMAIN );
        }
        add_filter( 'woocommerce_product_upsells_products_heading', 'change_upsells_heading' );
    }

    if ( ! function_exists( 'my_sticky_product_block' ) ) {
        /**
         * Display sticky product block after the single product summary.
         * This function loads the template part located at 'template-parts/blocks/block-product.php' and outputs it on single product pages.
         * 
         * @return void
         */
        function my_sticky_product_block() {
            get_template_part( 'template-parts/blocks/block', 'product' );
        }
        add_action( 'woocommerce_after_single_product_summary', 'my_sticky_product_block', 5 );
    }

    if ( ! function_exists( 'exclude_subscription_products_from_shop' ) ) {
        /**
         * Exclude subscription products from the main shop, product category, and product tag pages.
         *
         * This function modifies the WooCommerce main query on the frontend to exclude products
         * that belong to the "subscription" product type. It prevents subscription products from
         * appearing in the shop loop, category archives, and tag archives.
         *
         * @param WP_Query $query The WP_Query instance (passed by reference).
         *
         * @return void
         */
        function exclude_subscription_products_from_shop( $query ) {
            // Ensure this only runs on the main shop loop and frontend
            if ( ! is_admin() && $query->is_main_query() && ( is_shop() || is_product_category() || is_product_tag() ) ) {

                // Get the term for subscription products from the "product_type" taxonomy
                $subscription_term = get_term_by( 'slug', 'subscription', 'product_type' );

                if ( $subscription_term && ! is_wp_error( $subscription_term ) ) {
                    $tax_query = (array) $query->get( 'tax_query' );

                    // Exclude subscription products
                    $tax_query[] = array(
                        'taxonomy' => 'product_type',
                        'field'    => 'slug',
                        'terms'    => array( 'subscription' ),
                        'operator' => 'NOT IN',
                    );

                    $query->set( 'tax_query', $tax_query );
                }
            }
        }
        add_action( 'pre_get_posts', 'exclude_subscription_products_from_shop' );
    }