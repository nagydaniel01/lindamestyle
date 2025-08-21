<?php
    global $product;
    if ( ! $product ) return;
?>
<div class="block block--product-sticky is-sticky">
    <div class="container">
        <div class="block__inner">
            <?php if ( has_post_thumbnail( $product->get_id() ) ) : ?>
                <?php echo get_the_post_thumbnail( $product->get_id(), 'thumbnail', array(
                    'class' => 'block__image wp-post-image',
                    'alt'   => $product->get_name(),
                    'decoding' => 'async'
                )); ?>
            <?php endif; ?>

            <p class="block__title"><?php echo esc_html( $product->get_name() ); ?></p>

            <p class="price">
                <?php echo $product->get_price_html(); ?>
            </p>

            <?php if ( wc_get_rating_html( $product->get_average_rating() ) ) : ?>
                <div class="block__rating">
                    <?php echo wc_get_rating_html( $product->get_average_rating() ); ?>
                    <span class="rating-count">(<?php echo $product->get_review_count(); ?>)</span>
                </div>
            <?php endif; ?>

            <div class="block__add-to-cart">
                <?php
                    switch ( $product->get_type() ) {

                        case 'simple':
                            if ( $product->is_purchasable() && $product->is_in_stock() ) :
                                ?>
                                <form class="cart" action="<?php echo esc_url( $product->add_to_cart_url() ); ?>" method="post" enctype="multipart/form-data">
                                    <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt">
                                        <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
                                    </button>
                                </form>
                            <?php
                            endif;
                            break;

                        case 'variable':
                        case 'grouped':
                            // Link to single product page for variable or grouped products
                            ?>
                            <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="button alt">
                                <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
                            </a>
                            <?php
                            break;

                        case 'external':
                            ?>
                            <a href="<?php echo esc_url( $product->get_product_url() ); ?>" target="_blank" rel="nofollow" class="button alt">
                                <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
                            </a>
                            <?php
                            break;
                    }
                ?>
            </div>
        </div>
    </div>
</div>