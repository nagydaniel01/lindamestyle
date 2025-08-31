<?php if ( is_user_logged_in() ) : 
    $current_user = wp_get_current_user();
?>

    <form id="event_registration_form" class="form">
        <?php wp_nonce_field( 'event_registration_form_action', 'event_registration_form_nonce' ); ?>
        <input type="hidden" name="event_id" value="<?php echo esc_attr( get_the_ID() ); ?>">

        <div class="mb-3">
            <label class="form-label" for="reg_name"><?php echo esc_html( 'Full Name', TEXT_DOMAIN ); ?></label>
            <input type="text" class="form-control" id="reg_name" name="reg_name" value="<?php echo esc_attr( $current_user->display_name ); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="reg_email"><?php echo esc_html( 'Email', TEXT_DOMAIN ); ?></label>
            <input type="email" class="form-control" id="reg_email" name="reg_email" value="<?php echo esc_attr( $current_user->user_email ); ?>" required>
        </div>

        <div class="form__actions">
            <button type="submit" class="btn btn-primary mb-3"><?php echo esc_html( 'Register', TEXT_DOMAIN ); ?></button>
            <div id="event_response" role="status" aria-live="polite"></div>
        </div>
    </form>
    
<?php else : ?>
    <p><?php echo esc_html( 'You must be logged in to register.', TEXT_DOMAIN ); ?></p>
<?php endif; ?>
