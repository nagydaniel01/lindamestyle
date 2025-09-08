<?php
    $current_user = wp_get_current_user();
?>

<form id="contact_form" class="form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" novalidate>
    <?php wp_nonce_field('contact_form_action', 'contact_form_nonce'); ?>

    <div class="mb-3">
        <label class="form-label" for="name">
            <?php echo esc_html( 'Name', TEXT_DOMAIN ); ?> <span class="required">*</span>
        </label>
        <input type="text" class="form-control" id="name" name="name" value="<?php echo esc_attr($current_user->display_name); ?>" placeholder="<?php echo esc_attr('Enter your full name', TEXT_DOMAIN ); ?>" required aria-required="true">
    </div>

    <div class="mb-3">
        <label class="form-label" for="email">
            <?php echo esc_html( 'E-mail', TEXT_DOMAIN ); ?> <span class="required">*</span>
        </label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo esc_attr($current_user->user_email); ?>" placeholder="<?php echo esc_attr('Enter your email address', TEXT_DOMAIN ); ?>" required aria-required="true">
    </div>

    <div class="mb-3">
        <label class="form-label" for="phone">
            <?php echo esc_html( 'Phone', TEXT_DOMAIN ); ?>
        </label>
        <input type="tel" class="form-control" id="phone" name="phone" placeholder="<?php echo esc_attr('Enter your phone number', TEXT_DOMAIN ); ?>">
    </div>

    <div class="mb-3">
        <label class="form-label" for="subject">
            <?php echo esc_html( 'Subject', TEXT_DOMAIN ); ?> <span class="required">*</span>
        </label>
        <input type="text" class="form-control" id="subject" name="subject" placeholder="<?php echo esc_attr('Enter subject', TEXT_DOMAIN ); ?>" required aria-required="true">
    </div>

    <div class="mb-3">
        <label class="form-label" for="message">
            <?php echo esc_html( 'Message', TEXT_DOMAIN ); ?> <span class="required">*</span>
        </label>
        <textarea class="form-control" id="message" name="message" rows="4" placeholder="<?php echo esc_attr('Write your message here...', TEXT_DOMAIN ); ?>" required aria-required="true"></textarea>
    </div>

    <fieldset class="mb-3">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="privacy_policy" name="privacy_policy" required aria-required="true">
            <label class="form-check-label" for="privacy_policy">
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
        <button type="submit" class="btn btn-primary mb-3"><?php echo esc_html( 'Send Message', TEXT_DOMAIN ); ?></button>
        <div id="response" role="status" aria-live="polite"></div>
    </div>
</form>
