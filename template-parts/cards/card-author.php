<?php
    $author_id  = $args['author_id'] ?? '';
    $author_bio = get_the_author_meta('description', $author_id);
?>

<div class="card card--author">
    <div class="card__inner">
        <div class="card__header">
            <?php echo get_avatar( $author_id, 100, '', get_the_author_meta('display_name', $author_id), ['class' => 'card__image'] ); ?>
        </div>
        <div class="card__content">
            <h5 class="card__title"><?php echo esc_html( get_the_author_meta('display_name', $author_id) ); ?></h5>
    
            <?php if ( $author_bio ) : ?>
                <div class="card__lead">
                    <?php echo wpautop( wp_kses_post( $author_bio ) ); ?>
                </div>
            <?php endif; ?>
    
            <div class="card__meta">
                <address class="mb-0">
                    <p class="mb-1">
                        <strong><?php _e('Email:', TEXT_DOMAIN); ?></strong> 
                        <?php echo esc_html( get_the_author_meta('user_email', $author_id) ); ?>
                    </p>
    
                    <p class="mb-0">
                        <strong><?php _e('Website:', TEXT_DOMAIN); ?></strong> 
                        <a href="<?php echo esc_url( get_the_author_meta('user_url', $author_id) ); ?>" target="_blank">
                            <?php echo esc_html( get_the_author_meta('user_url', $author_id) ); ?>
                        </a>
                    </p>
                </address>
            </div>
        </div>
    </div>
</div>