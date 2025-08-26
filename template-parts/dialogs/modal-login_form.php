<?php if ( class_exists( 'WooCommerce' ) ) : ?>
    <div class="modal modal--alt fade" id="login_formModal" tabindex="-1" aria-labelledby="login_formModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="login_formModalLabel"><?php echo esc_html__( 'Login', 'woocommerce' ); ?> / <?php echo esc_html__( 'Register', 'woocommerce' ); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo esc_attr('Close', TEXT_DOMAIN); ?>"></button>
                </div>
                <div class="modal-body">
                    <div id="wc-login-form">
                    <?php
                        if ( ! is_user_logged_in() ) {
                            echo do_shortcode('[woocommerce_my_account]'); 
                        }
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>