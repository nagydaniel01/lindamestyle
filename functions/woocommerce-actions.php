<?php
    /**
     * WooCommerce Customizations
     * This file contains customizations for WooCommerce.
     */

    // Optional: Disable all default WooCommerce stylesheets
    add_filter('woocommerce_enqueue_styles', '__return_empty_array');

    // Remove WooCommerce breadcrumb
    //remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

    // Remove WooCommerce sidebar
    remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

    // Remove default WooCommerce wrappers
    remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

    // ---------------------------------------------
    // Add blocks to shop and product single
    // ---------------------------------------------

    if (!function_exists('custom_woocommerce_output_content_wrapper')) {
        /**
         * Output the opening wrapper for WooCommerce content.
         */
        function custom_woocommerce_output_content_wrapper() {
            if (is_shop() || is_product_category()) {
                echo '<main class="page page--archive page--archive-product"><section class="section section--archive section--archive-products"><div class="container">';
            } elseif (is_singular('product')) {
                echo '<main class="page page--single page--single-product">';
            }
        }
        add_action('woocommerce_before_main_content', 'custom_woocommerce_output_content_wrapper', 10);
    }

    if (!function_exists('custom_woocommerce_output_content_wrapper_end')) {
        /**
         * Output the closing wrapper for WooCommerce content.
         */
        function custom_woocommerce_output_content_wrapper_end() {
            if (is_shop() || is_product_category()) {
                echo '</div></section></main>';
            } elseif (is_singular('product')) {
                echo '</main>';
            }
        }
        add_action('woocommerce_after_main_content', 'custom_woocommerce_output_content_wrapper_end', 10);
    }

    if (!function_exists('custom_woocommerce_single_product_main_wrapper')) {
        /**
         * Wraps the single product main content in a custom section and container.
         */
        function custom_woocommerce_single_product_main_wrapper() {
            echo '<section class="section section--product-main"><div class="container"><div class="section__inner">';
        }
        add_action('woocommerce_before_single_product_summary', 'custom_woocommerce_single_product_main_wrapper', 5);
    }

    if (!function_exists('custom_woocommerce_single_product_main_wrapper_end')) {
        /**
         * Closes the custom section wrapper added around the single product main content.
         */
        function custom_woocommerce_single_product_main_wrapper_end() {
            echo '</div></div></section>';
        }
        add_action('woocommerce_after_single_product_summary', 'custom_woocommerce_single_product_main_wrapper_end', 5);
    }

    if (!function_exists('custom_wrap_woocommerce_breadcrumbs')) {
        /**
         * Output the opening wrapper for WooCommerce breadcrumbs
         *
         * @return void
         */
        function custom_breadcrumb_wrapper_start() {
            if (is_shop() || is_product_category()) {
                echo '<div class="woocommerce-breadcrumb-wrapper">';
            } elseif (is_singular('product')) {
                echo '<div class="woocommerce-breadcrumb-wrapper"><div class="container">';
            }
        }
        add_action( 'woocommerce_before_main_content', 'custom_breadcrumb_wrapper_start', 15 );

        /**
         * Output the closing wrapper for WooCommerce breadcrumbs
         *
         * @return void
         */
        function custom_breadcrumb_wrapper_end() {
            if (is_shop() || is_product_category()) {
                echo '</div>';
            } elseif (is_singular('product')) {
                echo '</div></div>';
            }
        }
        add_action( 'woocommerce_before_main_content', 'custom_breadcrumb_wrapper_end', 20 );
    }

    if (!function_exists('custom_woocommerce_notices_wrapper')) {
        /**
         * Output opening wrapper for WooCommerce notices
         *
         * @return void
         */
        function custom_woocommerce_notices_wrapper() {
            echo '<div class="container">';
        }
        //add_action('woocommerce_before_shop_loop', 'custom_woocommerce_notices_wrapper', 5);
        add_action('woocommerce_before_single_product', 'custom_woocommerce_notices_wrapper', 5);
    }

    if (!function_exists('custom_woocommerce_notices_wrapper_end')) {
        /**
         * Output closing wrapper for WooCommerce notices
         *
         * @return void
         */
        function custom_woocommerce_notices_wrapper_end() {
            echo '</div>';
        }
        //add_action('woocommerce_before_shop_loop', 'custom_woocommerce_notices_wrapper_end', 15);
        add_action('woocommerce_before_single_product', 'custom_woocommerce_notices_wrapper_end', 15);
    }

    add_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10); // Default notices
    add_action('woocommerce_before_single_product', 'woocommerce_output_all_notices', 10); // Default notices

    if (!function_exists('custom_woocommerce_catalog_ordering_wrapper')) {
        /**
         * Output opening wrapper for WooCommerce result count and ordering dropdown
         *
         * @return void
         */
        function custom_woocommerce_catalog_ordering_wrapper() {
            echo '<div class="woocommerce-tools">';
        }
        add_action('woocommerce_before_shop_loop', 'custom_woocommerce_catalog_ordering_wrapper', 10);
    }

    if (!function_exists('custom_woocommerce_catalog_ordering_wrapper_end')) {
        /**
         * Output closing wrapper for WooCommerce result count and ordering dropdown
         *
         * @return void
         */
        function custom_woocommerce_catalog_ordering_wrapper_end() {
            echo '</div>';
        }
        add_action('woocommerce_before_shop_loop', 'custom_woocommerce_catalog_ordering_wrapper_end', 35);
    }

    // ---------------------------------------------
    // Add blocks to product card
    // ---------------------------------------------

    // Product wrapper

    if (!function_exists('custom_product_wrapper')) {
        /**
         * Open a wrapper around each WooCommerce product in the loop
         */
        function custom_product_wrapper() {
            echo '<div class="woocommerce-loop-product">';
        }
        add_action('woocommerce_before_shop_loop_item', 'custom_product_wrapper', 5);
    }

    if (!function_exists('custom_product_wrapper_end')) {
        /**
         * Close the wrapper around each WooCommerce product in the loop
         */
        function custom_product_wrapper_end() {
            echo '</div>';
        }
        add_action('woocommerce_after_shop_loop_item', 'custom_product_wrapper_end', 20);
    }

    // Body wrapper

    if ( ! function_exists( 'custom_woocommerce_loop_body_wrapper' ) ) {
        /**
         * Open a wrapper around the WooCommerce product body.
         */
        function custom_woocommerce_loop_body_wrapper() {
            echo '<div class="woocommerce-loop-product__body">';
        }
        add_action( 'woocommerce_shop_loop_item_title', 'custom_woocommerce_loop_body_wrapper', 1 );
    }

    if ( ! function_exists( 'custom_woocommerce_loop_body_wrapper_end' ) ) {
        /**
         * Close the wrapper around the WooCommerce product body.
         */
        function custom_woocommerce_loop_body_wrapper_end() {
            echo '</div>';
        }
        add_action( 'woocommerce_after_shop_loop_item', 'custom_woocommerce_loop_body_wrapper_end', 1 );
    }

    // Image wrapper

    if ( ! function_exists( 'custom_woocommerce_loop_image_wrapper' ) ) {
        /**
         * Open a wrapper around the WooCommerce product image.
         */
        function custom_woocommerce_loop_image_wrapper() {
            echo '<div class="woocommerce-loop-product__image">';
        }
        add_action( 'woocommerce_before_shop_loop_item_title', 'custom_woocommerce_loop_image_wrapper', 1 );
    }

    if ( ! function_exists( 'custom_woocommerce_loop_image_wrapper_end' ) ) {
        /**
         * Close the wrapper around the WooCommerce product image.
         */
        function custom_woocommerce_loop_image_wrapper_end() {
            echo '</div>';
        }
        add_action( 'woocommerce_before_shop_loop_item_title', 'custom_woocommerce_loop_image_wrapper_end', 20 );
    }

    // Rating wrapper

    if ( ! function_exists( 'custom_woocommerce_loop_rating_wrapper' ) ) {
        /**
         * Outputs opening wrapper div for rating in WooCommerce product loop.
         */
        function custom_woocommerce_loop_rating_wrapper() {
            echo '<div class="woocommerce-loop-product__rating-wrapper">';
        }
        add_action( 'woocommerce_after_shop_loop_item_title', 'custom_woocommerce_loop_rating_wrapper', 4 );
    }

    if ( ! function_exists( 'custom_woocommerce_loop_rating_wrapper_end' ) ) {
        /**
         * Outputs closing wrapper div for rating in WooCommerce product loop.
         */
        function custom_woocommerce_loop_rating_wrapper_end() {
            echo '</div>';
        }
        add_action( 'woocommerce_after_shop_loop_item_title', 'custom_woocommerce_loop_rating_wrapper_end', 6 );
    }

    // Add to cart wrapper

    if ( ! function_exists( 'custom_woocommerce_loop_add_to_cart_wrapper' ) ) {
        /**
         * Open a wrapper around the WooCommerce add-to-cart button.
         */
        function custom_woocommerce_loop_add_to_cart_wrapper() {
            echo '<div class="woocommerce-loop-product__add-to-cart-wrapper">';
        }
        add_action( 'woocommerce_after_shop_loop_item', 'custom_woocommerce_loop_add_to_cart_wrapper', 9 );
    }

    if ( ! function_exists( 'custom_woocommerce_loop_add_to_cart_wrapper_end' ) ) {
        /**
         * Close the wrapper around the WooCommerce add-to-cart button.
         */
        function custom_woocommerce_loop_add_to_cart_wrapper_end() {
            echo '</div>';
        }
        add_action( 'woocommerce_after_shop_loop_item', 'custom_woocommerce_loop_add_to_cart_wrapper_end', 11 );
    }

    // ---------------------------------------------
    // Add blocks to product single
    // ---------------------------------------------

    if (!function_exists('custom_woocommerce_single_product_gallery_wrapper')) {
        /**
         * Output opening wrapper for the product gallery
         *
         * @return void
         */
        function custom_woocommerce_single_product_gallery_wrapper() {
            echo '<div class="gallery entry-gallery">';
        }
        add_action('woocommerce_before_single_product_summary', 'custom_woocommerce_single_product_gallery_wrapper', 6);
    }

    if (!function_exists('custom_woocommerce_single_product_gallery_wrapper_end')) {
        /**
         * Output closing wrapper for the product gallery
         *
         * @return void
         */
        function custom_woocommerce_single_product_gallery_wrapper_end() {
            echo '</div>';
        }
        add_action('woocommerce_before_single_product_summary', 'custom_woocommerce_single_product_gallery_wrapper_end', 21);
    }

    if (!function_exists('custom_woocommerce_gallery_thumbnail_size')) {
        /**
         * Change the WooCommerce gallery thumbnail size to match main image size.
         *
         * @param array $size Array of width, height, and crop settings for the gallery thumbnail.
         * @return array Modified gallery thumbnail size
         */
        function custom_woocommerce_gallery_thumbnail_size($size) {
            return array(
                'width'  => 600,
                'height' => 600,
                'crop'   => 1,
            );
        }
        add_filter('woocommerce_get_image_size_gallery_thumbnail', 'custom_woocommerce_gallery_thumbnail_size');
    }

    if (!function_exists('custom_woocommerce_move_product_tabs')) {
        /**
         * Moves the product tabs into a Bootstrap container.
         * Checks if WooCommerce tabs function exists before removing/re-adding.
         */
        function custom_woocommerce_move_product_tabs() {
            if (function_exists('woocommerce_output_product_data_tabs')) {
                // Remove default WooCommerce tabs output
                remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);

                // Re-add tabs wrapped in a container
                add_action('woocommerce_after_single_product_summary', 'custom_woocommerce_move_product_tabs_inner', 10);
            }
        }

        /**
         * Inner function to output WooCommerce tabs inside a container.
         */
        if (!function_exists('custom_woocommerce_move_product_tabs_inner')) {
            function custom_woocommerce_move_product_tabs_inner() {
                echo '<section class="section section--product-tabs"><div class="container">';
                woocommerce_output_product_data_tabs();
                echo '</div></section>';
            }
        }

        add_action('after_setup_theme', 'custom_woocommerce_move_product_tabs', 11);
    }

    if (!function_exists('custom_woocommerce_pagination_icons')) {
        /**
         * Replaces WooCommerce pagination arrows with custom SVG icons.
         *
         * @param array $args Pagination arguments.
         * @return array Modified pagination arguments.
         */
        function custom_woocommerce_pagination_icons($args) {
            $args['prev_text'] = '<svg class="icon icon-chevron-left"><use xlink:href="#icon-chevron-left"></use></svg>';
            $args['next_text'] = '<svg class="icon icon-chevron-right"><use xlink:href="#icon-chevron-right"></use></svg>';
            return $args;
        }
        add_filter('woocommerce_pagination_args', 'custom_woocommerce_pagination_icons');
        add_filter('woocommerce_comment_pagination_args', 'custom_woocommerce_pagination_icons');
    }

    if (!function_exists('custom_woocommerce_single_product_sections')) {
        /**
         * Displays flexible content sections on the single product page.
         * Checks for ACF 'sections' field for the current product,
         * then falls back to 'product_page_sections' options field.
         */
        function custom_woocommerce_single_product_sections() {
            if (function_exists('have_rows')) {
                if (have_rows('sections')) {
                    while (have_rows('sections')) {
                        the_row();
                        get_template_part('template-parts/sections/section-' . get_row_layout());
                    }
                } elseif (have_rows('product_page_sections', 'option')) {
                    while (have_rows('product_page_sections', 'option')) {
                        the_row();
                        get_template_part('template-parts/sections/section-' . get_row_layout());
                    }
                } else {
                    the_content();
                }
            }
        }
        add_action('woocommerce_after_single_product_summary', 'custom_woocommerce_single_product_sections', 30);
    }
    
    if ( ! function_exists( 'refresh_offcanvas_minicart_fragments' ) ) {
        /**
         * Refresh minicart and cart count via AJAX fragments.
         *
         * This function ensures that both the minicart contents and the cart item count
         * are refreshed dynamically after products are added to the cart via AJAX.
         *
         * @param array $fragments An array of HTML fragments to refresh with AJAX.
         * @return array Modified fragments array including cart count and minicart wrapper.
         */
        function refresh_offcanvas_minicart_fragments( $fragments ) {

            // Cart count fragment
            ob_start();
            ?>
            <span class="cart_contents_count">
                <?php echo WC()->cart->get_cart_contents_count(); ?>
            </span>
            <?php
            $fragments['.cart_contents_count'] = ob_get_clean();

            // Minicart wrapper fragment
            ob_start();
            ?>
            <div class="woocommerce-mini-cart__wrapper">
                <?php woocommerce_mini_cart(); ?>
            </div>
            <?php
            $fragments['.woocommerce-mini-cart__wrapper'] = ob_get_clean();

            return $fragments;
        }
        add_filter( 'woocommerce_add_to_cart_fragments', 'refresh_offcanvas_minicart_fragments' );
    }