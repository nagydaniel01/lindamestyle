<form id="ml_form" class="block block--subscribe" novalidate>
    <fieldset class="form-group">
        <label for="ml_email" class="form-label visually-hidden">
            <?php esc_html_e('E-mail cím', TEXT_DOMAIN); ?><span class="required--star">*</span>
        </label>
        <input type="email" class="form-control" id="ml_email" name="email" placeholder="<?php esc_attr_e('E-mail cím', TEXT_DOMAIN); ?>" aria-required="true" data-error="<?php esc_attr_e('Kérjük, adjon meg egy érvényes e-mail címet.', TEXT_DOMAIN); ?>">
    </fieldset>

    <fieldset class="form-group">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="ml_privacy_policy" name="privacy_policy" aria-required="true" data-error="<?php esc_attr_e('El kell fogadnia az adatvédelmi tájékoztatót.', TEXT_DOMAIN); ?>">
            <label class="form-check-label" for="ml_privacy_policy">
                <?php printf( __( 'Elfogadom az MMA-MMKI <a href="%s" target="_blank">%s</a> közölteket.', TEXT_DOMAIN ), esc_attr( esc_url( get_privacy_policy_url() ) ), esc_html('Adatvédelmi Tájékoztatójában', TEXT_DOMAIN) ); ?><span class="required--star">*</span>
            </label>
        </div>
    </fieldset>

    <input type="submit" value="<?php esc_attr_e('Feliratkozom', TEXT_DOMAIN); ?>" class="btn btn-light" data-button-text="<?php esc_attr_e('Küldés...', TEXT_DOMAIN); ?>">
    <div id="response-container"></div>
</form>