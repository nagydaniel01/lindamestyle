<?php
    $current_user = wp_get_current_user();
?>

<form id="event_registration_form" class="form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" novalidate>
    <?php wp_nonce_field( 'event_registration_form_action', 'event_registration_form_nonce' ); ?>
    <input type="hidden" name="event_id" value="<?php echo esc_attr( get_the_ID() ); ?>">

    <div class="mb-3">
        <label class="form-label" for="reg_name">
            <?php echo esc_html( 'Name', TEXT_DOMAIN ); ?> <span class="required">*</span>
        </label>
        <input type="text" class="form-control" id="reg_name" name="reg_name" value="<?php echo esc_attr( $current_user->display_name ); ?>" required aria-required="true">
    </div>

    <div class="mb-3">
        <label class="form-label" for="reg_email">
            <?php echo esc_html( 'E-mail', TEXT_DOMAIN ); ?> <span class="required">*</span>
        </label>
        <input type="email" class="form-control" id="reg_email" name="reg_email" value="<?php echo esc_attr( $current_user->user_email ); ?>" required aria-required="true">
    </div>
    
    <fieldset>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="reg_privacy_policy" name="reg_privacy_policy" required aria-required="true">
            <label class="form-check-label" for="reg_privacy_policy">
                <?php 
                    echo sprintf(
                        esc_html__( 'I agree to the %s', TEXT_DOMAIN ), 
                        '<a href="' . esc_url( get_privacy_policy_url() ) . '" target="_blank">' . esc_html__( 'Privacy Policy', TEXT_DOMAIN ) . '</a>'
                    ); 
                ?>
                <span class="required">*</span>
            </label>
        </div>
    </fieldset>

    <div class="form__actions">
        <button type="submit" class="btn btn-primary mb-3"><?php echo esc_html( 'Register', TEXT_DOMAIN ); ?></button>
        <div id="event_response" role="status" aria-live="polite"></div>
    </div>
</form>
