<?php
    $current_user_id = get_current_user_id();
    $szintipus_value = get_field('szintipus', 'user_' . $current_user_id);
?>

<form id="beauty_profile_form" class="form" method="post" action="<?php echo esc_url( admin_url('admin-ajax.php') ); ?>" novalidate>
    <?php wp_nonce_field('beauty_profile_form_action', 'beauty_profile_form_nonce'); ?>

    <fieldset class="mb-3">
        <legend class="form-label">Színtípus</legend>

        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="szintipus" id="tavasz" value="tavasz" <?php checked($szintipus_value, 'tavasz'); ?>>
            <label class="form-check-label" for="tavasz">Tavasz</label>
        </div>

        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="szintipus" id="nyar" value="nyar" <?php checked($szintipus_value, 'nyar'); ?>>
            <label class="form-check-label" for="nyar">Nyár</label>
        </div>

        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="szintipus" id="osz" value="osz" <?php checked($szintipus_value, 'osz'); ?>>
            <label class="form-check-label" for="osz">Ősz</label>
        </div>

        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="szintipus" id="tel" value="tel" <?php checked($szintipus_value, 'tel'); ?>>
            <label class="form-check-label" for="tel">Tél</label>
        </div>
    </fieldset>
    <div class="form__actions">
        <button type="submit" class="btn btn-primary mb-3"><?php echo esc_html__('Mentés', TEXT_DOMAIN); ?></button>
        <div id="message" role="status" aria-live="polite"></div>
    </div>
</form>
