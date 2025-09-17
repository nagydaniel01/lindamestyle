<?php get_header(); ?>

<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>

    <?php 
        $post_id          = get_the_ID();
        $post_type        = get_post_type();
        $event_start_date = get_field('event_start_date', $post_id);
        $event_start_time = get_field('event_start_time', $post_id);
        $event_end_date   = get_field('event_end_date', $post_id);
        $event_end_time   = get_field('event_end_time', $post_id);
        $taxonomy         = 'event_cat';
    ?>

    <main class="page page--single page--single-<?php echo esc_attr( $post_type ); ?>">
        <section class="section section--single section--single-<?php echo esc_attr( $post_type ); ?>">
            <div class="container container--narrow">
                <header class="section__header">
                    <?php if ( function_exists('rank_math_the_breadcrumbs') ) rank_math_the_breadcrumbs(); ?>

                    <h1 class="section__title"><?php the_title(); ?></h1>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="section__image-wrapper">
                            <?php
                                $thumbnail_id = get_post_thumbnail_id( get_the_ID() );
                                $alt_text = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );

                                the_post_thumbnail('full', [
                                    'class'         => 'section__image',
                                    'alt'           => $alt_text ?: get_the_title(),
                                    'loading'       => 'eager',
                                    'fetchpriority' => 'high',
                                    'decoding'      => 'async'
                                ]);
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="section__meta">
                        <?php if ( $event_start_date || $event_end_date ) : ?>
                            <div class="block block--event-data">
                                <?php if ( $event_start_date ) : ?>
                                    <div class="block__inner">
                                        <h2 class="block__title"><?php echo esc_html__( 'Start:', TEXT_DOMAIN ); ?></h2>
                                        <div class="block__content">
                                            <time datetime=""><?php echo wp_safe_format_date( $event_start_date, 'd/m/Y', 'Y. F d.' ); ?></time>
                                            <?php if ( $event_start_time ) : ?>
                                                <time datetime=""><?php echo ' ' . wp_safe_format_time( $event_start_time, 'g:i a' ); ?></time>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $event_end_date ) : ?>
                                    <div class="block__inner">
                                        <h2 class="block__title"><?php echo esc_html__( 'End:', TEXT_DOMAIN ); ?></h2>
                                        <div class="block__content">
                                            <time datetime=""><?php echo wp_safe_format_date( $event_end_date, 'd/m/Y', 'Y. F d.' ); ?></time>
                                            <?php if ( $event_end_time ) : ?>
                                                <time datetime=""><?php echo ' ' . wp_safe_format_time( $event_end_time, 'g:i a' ); ?></time>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
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

                    <div class="block block--event-registration">
                        <h2 class="block__title"><?php echo esc_html__( 'Registration', TEXT_DOMAIN ); ?></h2>
                        <?php 
                            get_template_part( 'template-parts/forms/form', 'event_registration' );
                        ?>
                    </div>
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
