<?php if ( class_exists( 'WooCommerce' ) ) : ?>
    <div class="offcanvas offcanvas-end" id="minicartCanvas" tabindex="-1" aria-labelledby="minicartCanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="minicartCanvasLabel"><?php echo esc_html( 'Your Cart', TEXT_DOMAIN ); ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="<?php esc_attr_e('Close', TEXT_DOMAIN); ?>"></button>
        </div>
        <div class="offcanvas-body">
            <div class="woocommerce-mini-cart__wrapper"><?php woocommerce_mini_cart(); ?></div>
        </div>
    </div>
<?php endif; ?>