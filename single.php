<?php get_header(); ?>

<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>

    <?php 
        $extra_classes   = '';
        $categories_list = get_the_category_list(', ');
        $tags_list       = get_the_tag_list('', ', ');
        $author_id       = get_the_author_meta('ID');
        $author_bio      = get_the_author_meta('description', $author_id);

        if ( is_singular() ) {
            $post_type = get_post_type();
            $extra_classes .= esc_attr( $post_type );
        }
    ?>

    <main class="page page--single page--single-<?php echo esc_attr($extra_classes); ?>">
        <section class="section section--single section--single-<?php echo esc_attr($extra_classes); ?>">
            <div class="container container--narrow">
                <header class="section__header">
                    <h1 class="section__title"><?php the_title(); ?></h1>
                    
                    <div class="post-meta">
                        <span class="post-date">
                            <?php
                            printf(
                                /* translators: %s: Post date */
                                __('Published on %s', TEXT_DOMAIN),
                                get_the_date()
                            );
                            ?>
                        </span>
                    </div>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('full'); ?>
                        </div>
                    <?php endif; ?>
                </header>
                
                
                <div class="section__content">
                    <?php
                    // The main content
                    the_content();

                    // Optional: Pagination for multi-page posts
                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . __('Pages:', TEXT_DOMAIN),
                        'after'  => '</div>',
                    ));
                    ?>
                </div>

                <footer class="section__footer">
                    <?php if ( get_the_author_meta('ID') ) : 
                        $author_id = get_the_author_meta('ID');
                    ?>
                        <div class="card card--author">
                            <?php echo get_avatar( $author_id, 512, '', get_the_author_meta('display_name', $author_id), ['class' => 'card-img-top'] ); ?>
                            <div class="card-body">
                                <div>
                                    <h5 class="card-title mb-2"><?php echo esc_html( get_the_author_meta('display_name', $author_id) ); ?></h5>

                                    <?php if ( $author_bio ) : ?>
                                        <?php echo wpautop( wp_kses_post( $author_bio ) ); ?>
                                    <?php endif; ?>

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
                    <?php endif; ?>

                    <?php if ( $categories_list ) : ?>
                        <span class="post-categories">
                            <?php
                                /* translators: %s: List of categories */
                                printf(__('Categories: %s', TEXT_DOMAIN), $categories_list);
                            ?>
                        </span>
                    <?php endif; ?>

                    <?php if ( $tags_list ) : ?>
                        <span class="post-tags">
                            <?php
                                /* translators: %s: List of tags */
                                printf(__('Tags: %s', TEXT_DOMAIN), $tags_list);
                            ?>
                        </span>
                    <?php endif; ?>

                    - Recent posts
                    - Most read posts
                    - Related posts
                    - Upsell posts (a cikk közepére)
                </footer>

                <?php
                // Load comments template
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;
                ?>
            </div>
        </section>
    </main>

    <?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>
