<?php
    $term        = $args['term'];
    $term_id     = $term->term_id;
    $taxonomy    = $term->taxonomy;
    $term_link   = get_term_link($term);
    $title       = $term->name;
    $description = term_description($term_id, $taxonomy);
?>

<article class="card <?php echo esc_attr($extra_classes); ?>">
    <a href="<?php echo esc_url($term_link); ?>" class="card__link">
        <div class="card__content">
            <h3 class="card__title">
                <?php echo esc_html($title); ?>
            </h3>
            
            <?php if (!empty($description)) : ?>
                <div class="card__lead">
                    <?php echo wp_trim_words(wp_strip_all_tags($description), 20, '…'); ?>
                </div>
            <?php endif; ?>

            <div class="card__meta">
                <span class="card__taxonomy">
                    <?php echo esc_html(get_taxonomy($taxonomy)->labels->singular_name); ?>
                </span>
            </div>

            <span class="card__button">
                <svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg>
            </span>
        </div>
    </a>
</article>