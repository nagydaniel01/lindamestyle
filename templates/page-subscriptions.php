<?php /* Template Name: Subscriptions Page */ ?>

<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php get_header( 'shop' ); ?>

<main class="page page--default page--archive page--archive-product page--subscriptions">
    <div class="container">
        <div class="woocommerce-breadcrumb-wrapper">
            <?php do_action( 'woocommerce_before_main_content' ); ?>
        </div>

        <header class="woocommerce-products-header">
            <h1 class="woocommerce-products-header__title page-title"><?php the_title(); ?></h1>
            <?php do_action( 'woocommerce_archive_description' ); ?>
        </header>
        <?php
            if ( ! function_exists( 'wc_get_products' ) ) {
                return;
            }

            $paged             = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
            $ordering          = WC()->query->get_catalog_ordering_args();
            $products_per_page = apply_filters( 'loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page() );

            $get_products = wc_get_products( array(
                'type'       => array( 'subscription' ),
                'status'     => 'publish',
                'limit'      => $products_per_page,
                'page'       => $paged,
                'orderby'    => $ordering['orderby'],
                'order'      => $ordering['order'],
                'return'     => 'ids',
                'paginate'   => true,
                'visibility' => 'catalog',
            ) );

            // Set WooCommerce loop props
            wc_set_loop_prop( 'current_page', $paged );
            wc_set_loop_prop( 'is_paginated', true );
            wc_set_loop_prop( 'page_template', get_page_template_slug() );
            wc_set_loop_prop( 'per_page', $products_per_page );
            wc_set_loop_prop( 'total', $get_products->total );
            wc_set_loop_prop( 'total_pages', $get_products->max_num_pages );

            if ( $get_products->products ) : 
                do_action( 'woocommerce_before_shop_loop' ); 

                woocommerce_product_loop_start();

                foreach ( $get_products->products as $product_id ) {
                    $post_object = get_post( $product_id );
                    setup_postdata( $GLOBALS['post'] =& $post_object );
                    do_action( 'woocommerce_shop_loop' );
                    wc_get_template_part( 'content', 'product' );
                }
                wp_reset_postdata();

                woocommerce_product_loop_end();

                do_action( 'woocommerce_after_shop_loop' ); // pagination
            else :
                do_action( 'woocommerce_no_products_found' );
            endif;
        ?>

        <?php do_action( 'woocommerce_after_main_content' ); ?>
    </div>
</main>

<?php get_footer( 'shop' ); ?>