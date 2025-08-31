<?php get_header(); ?>

<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>

    <?php 
        $post_type       = get_post_type();
        $taxonomy        = 'event_cat';
        
        $estimated_reading_time = get_estimated_reading_time( get_the_content() );
    ?>

    <main class="page page--single page--single-<?php echo esc_attr( $post_type ); ?>">
        <section class="section section--single section--single-<?php echo esc_attr( $post_type ); ?>">
            <div class="container container--narrow">
                <header class="section__header">
                    <?php if ( function_exists('rank_math_the_breadcrumbs') ) rank_math_the_breadcrumbs(); ?>

                    <h1 class="section__title"><?php the_title(); ?></h1>

                    <div class="section__meta">
                        <?php if ( $estimated_reading_time ) : ?>
                            <span class="section__reading-time">
                                <?php
                                    /* translators: %s: Estimated reading time in minutes */
                                    printf(
                                        _n(
                                            '%s minute reading',   // singular
                                            '%s minutes reading',  // plural
                                            $estimated_reading_time,
                                            TEXT_DOMAIN
                                        ),
                                        esc_html( $estimated_reading_time )
                                    );
                                ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="section__image-wrapper">
                            <?php
                                $thumbnail_id = get_post_thumbnail_id( get_the_ID() );
                                $alt_text = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );

                                the_post_thumbnail('full', [
                                    'class' => 'section__image',
                                    'alt'   => $alt_text ?: get_the_title(),
                                    'loading' => 'lazy'
                                ]);
                            ?>
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

                    <?php 
                        get_template_part( 'template-parts/forms/form', 'event_registration' );
                    ?>
                </div>

                <footer class="section__footer">
                    <?php if ( $taxonomy ) : ?>
                        <?php 
                            $taxonomy_obj   = get_taxonomy( $taxonomy ); 
                            $taxonomy_label = $taxonomy_obj ? $taxonomy_obj->labels->name : __('Categories', TEXT_DOMAIN);
                        ?>
                        <span class="section__categories category">
                            <div class="category__container">
                                <strong class="visually-hidden"><?php echo esc_html( $taxonomy_label ) . ':'; ?></strong>
                                <div class="category__wrapper">
                                    <?php 
                                        // Categories
                                        wp_list_categories( array(
                                            'current_category'     => 0,
                                            'depth'                => 0,
                                            'echo'                 => true,
                                            'exclude'              => '',
                                            'exclude_tree'         => '',
                                            'feed'                 => '',
                                            'feed_image'           => '',
                                            'feed_type'            => '',
                                            'hide_title_if_empty'  => false,
                                            'separator'            => '',
                                            'show_count'           => 0,
                                            'show_option_all'      => '',
                                            'show_option_none'     => '',
                                            'style'                => '',
                                            'taxonomy'             => $taxonomy,
                                            'title_li'             => '',
                                            'use_desc_for_title'   => 0,
                                            'walker'               => '',
                                        ) ); 
                                    ?>
                                </div>
                            </div>
                        </span>
                    <?php endif; ?>
                </footer>
            </div>
        </section>
    </main>

    <?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>
