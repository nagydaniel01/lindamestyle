<?php if ( class_exists( 'WooCommerce' ) ) : ?>
    <?php
        $count = WC()->cart->get_cart_contents_count(); 
    ?>
    <div class="offcanvas offcanvas-end" id="minicartCanvas" tabindex="-1" aria-labelledby="minicartCanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="minicartCanvasLabel">
                <?php echo esc_html( 'Your Cart', TEXT_DOMAIN ); ?>
                <?php if ( $count > 0 ) : ?>
                    <span class="cart_contents_count">
                        (<?php echo esc_html( $count ); ?> <?php echo _n( 'item', 'items', $count, TEXT_DOMAIN ); ?>)
                    </span>
                <?php endif; ?>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="<?php echo esc_attr('Close', TEXT_DOMAIN); ?>"></button>
        </div>
        <div class="offcanvas-body">
            <div class="woocommerce-mini-cart__wrapper"><?php woocommerce_mini_cart(); ?></div>
        </div>
    </div>
<?php endif; ?>