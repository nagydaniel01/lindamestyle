<?php
    $post_type    = $args['post_type'] ?? '';

    $current_user_id = get_current_user_id();

    $post_id      = get_the_ID();
    $title        = get_the_title();
    $thumbnail_id = get_post_thumbnail_id();
    $fallback_id  = PLACEHOLDER_IMAGE_ID;
    $image_id     = $thumbnail_id ?: $fallback_id;

    $testimonial_name = get_post_meta($post_id, 'testimonial_name', true);
    if ($testimonial_name) {
        $title = $testimonial_name;
    }

    if ($image_id === $fallback_id) {
        $alt_text = __('', TEXT_DOMAIN);
    } else {
        $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: $title;
    }

    $extra_classes = '';
    if ($post_type) {
        $extra_classes = 'card--'.$post_type;
    }
?>

<article class="card <?php echo esc_attr($extra_classes); ?>">
    <?php if ($image_id) : ?>
        <div class="card__header">
            <?php echo wp_get_attachment_image($image_id, 'medium', false, ['class' => 'card__image', 'alt' => esc_attr($alt_text), 'loading' => 'lazy']); ?>
        </div>
    <?php endif; ?>

    <div class="card__content">
        <h3 class="card__title">
            <?php echo esc_html($title); ?>
        </h3>
        
        <div class="card__lead"><?php the_content(); ?></div>
    </div>
</article>
