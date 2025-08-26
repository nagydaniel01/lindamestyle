<?php get_header(); ?>

<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>

    <?php 
        $posts_per_page  = get_option('posts_per_page');

        $post_type       = get_post_type();
        $post_format     = get_post_format();
        $categories_list = get_the_category_list(', ');
        $post_lead       = get_field('post_lead');
        $post_oembed     = get_field('post_oembed');

        // Extract iframe src
        preg_match('/src="([^"]+)"/', $post_oembed, $matches);
        $src = $matches[1] ?? '';

        // Update iframe with parameters and attributes
        if (!empty($src)) {
            $params      = ['controls' => 0, 'hd' => 1, 'autohide' => 1];
            $new_src     = add_query_arg($params, $src);
            $post_oembed = str_replace($src, $new_src, $post_oembed);

            $post_oembed = str_replace(
                '></iframe>',
                ' frameborder="0"></iframe>',
                $post_oembed
            );
        }
        
        $estimated_reading_time = get_estimated_reading_time( get_the_content() );

        // Related posts
        $related_posts = new WP_Query([
            'post_status'    => 'publish',
            'posts_per_page' => $posts_per_page,
            'category__in'   => wp_get_post_categories(get_the_ID()),
            'post__not_in'   => [get_the_ID()],
        ]);

        // Recently viewed posts
        $recently_viewed_posts_ids = get_recently_viewed();

        if ( empty($recently_viewed_posts_ids) ) {
            $recently_viewed_posts_ids = array(0);
        }

        $recently_viewed_posts = new WP_Query([
            'post_type'      => $post_type,
            'post_status'    => 'publish',
            'posts_per_page' => $posts_per_page,
            'post__in'       => $recently_viewed_posts_ids,
            'post__not_in'   => [get_the_ID()],
            'orderby'        => 'post__in',
        ]);

        // Most popular posts
        $most_popular_posts = new WP_Query([
            'post_type'         => $post_type,
            'post_status'       => 'publish',
            'posts_per_page'    => $posts_per_page,
            'date_query' => [
                [
                    'after'     => date('Y-m-d', strtotime('-14 days')),
                    'before'    => date('Y-m-d'),
                    'inclusive' => true,
                ],
            ],
            'meta_query' => [
                [
                    'key'       => '_post_views_count',
                    'value'     => 1,
                    'compare'   => '>=',
                    'type'      => 'NUMERIC'
                ],
            ],
            'meta_key'          => '_post_views_count',
            'orderby'           => 'meta_value_num',
            'order'             => 'DESC'
        ]);

        // Define taxonomy dynamically based on post type
        switch ( $post_type ) {
            case 'product':
                $taxonomy = 'product_cat';
                break;

            case 'service':
                $taxonomy = 'service_cat';
                break;

            case 'knowledge_base':
                $taxonomy = 'knowledge_base_cat';
                break;

            case 'event':
                $taxonomy = 'event_cat';
                break;

            default:
                $taxonomy = 'category';
                break;
        }
    ?>

    <main class="page page--single page--single-<?php echo esc_attr( $post_type ); ?> page--single-<?php echo esc_attr( $post_format ); ?>">
        <section class="section section--single section--single-<?php echo esc_attr( $post_type ); ?> section--single-<?php echo esc_attr( $post_format ); ?>">
            <div class="container container--narrow">
                <header class="section__header">
                    <?php if ( function_exists('rank_math_the_breadcrumbs') ) rank_math_the_breadcrumbs(); ?>

                    <h1 class="section__title"><?php the_title(); ?></h1>

                    <?php if ( $post_lead ) : ?>
                        <div class="section__lead"><?php echo wp_kses_post( $post_lead ); ?></div>
                    <?php endif; ?>

                    <div class="section__meta">
                        <span class="section__date">
                            <?php
                                $published = get_the_date();
                                $modified  = get_the_modified_date();

                                if ( $published !== $modified ) {
                                    // Show last modified date if different
                                    printf(
                                        /* translators: %s: Post modified date */
                                        __('Updated on %s', TEXT_DOMAIN),
                                        esc_html( $modified )
                                    );
                                } else {
                                    // Otherwise show published date
                                    printf(
                                        /* translators: %s: Post date */
                                        __('Published on %s', TEXT_DOMAIN),
                                        esc_html( $published )
                                    );
                                }
                            ?>
                        </span>
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

                    <?php if ( $post_oembed ) : ?>
                        <div class="section__video-wrapper">
                            <?php $video_id = get_youtube_video_id($src) ?? ''; ?>
                            <div class="section__video-wrapper"> 
                                <div class="youtube-player" data-id="<?php echo esc_attr($video_id); ?>"></div>
                            </div>
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

                    <?php 
                        if ( get_the_author_meta('ID') ) {
                            $template_args = array('author_id' => esc_attr(get_the_author_meta('ID')));
                            get_template_part('template-parts/cards/card', 'author', $template_args);
                        }
                    ?>

                    <?php if ( $related_posts->have_posts() ) : ?>
                        <?php
                            $template_args = array('post_type' => esc_attr($post_type));
                            $template      = locate_template("template-parts/cards/card-related.php");
                        ?>
                        <div class="section__related-posts">
                            <h2 class="section__title"><?php _e('You may also be interested in', TEXT_DOMAIN); ?></h2>
                            <div class="slider slider--related" id="related-posts-slider">
                                <div class="slider__list">
                                    <?php while ( $related_posts->have_posts() ) : $related_posts->the_post(); ?>
                                        <div class="slider__item">
                                            <?php
                                                if ( ! empty( $template ) ) {
                                                    get_template_part('template-parts/cards/card', 'related', $template_args);
                                                } else {
                                                    get_template_part('template-parts/cards/card', 'default', $template_args);
                                                }
                                            ?>
                                        </div>
                                    <?php endwhile; wp_reset_postdata(); ?>
                                </div>
                                <div class="slider__controls"></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ( $recently_viewed_posts->have_posts() ) : ?>
                        <?php
                            $template_args = array('post_type' => esc_attr($post_type));
                            $template      = locate_template("template-parts/cards/card-related.php");
                        ?>
                        <div class="section__recently-viewed-posts">
                            <h2 class="section__title"><?php _e('Recently viewed', TEXT_DOMAIN); ?></h2>
                            <div class="slider slider--related" id="recently-viewed-posts-slider">
                                <div class="slider__list">
                                    <?php while ( $recently_viewed_posts->have_posts() ) : $recently_viewed_posts->the_post(); ?>
                                        <div class="slider__item">
                                            <?php
                                                if ( ! empty( $template ) ) {
                                                    get_template_part('template-parts/cards/card', 'related', $template_args);
                                                } else {
                                                    get_template_part('template-parts/cards/card', 'default', $template_args);
                                                }
                                            ?>
                                        </div>
                                    <?php endwhile; wp_reset_postdata(); ?>
                                </div>
                                <div class="slider__controls"></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ( $most_popular_posts->have_posts() ) : ?>
                        <?php
                            $template_args = array('post_type' => esc_attr($post_type));
                            $template      = locate_template("template-parts/cards/card-related.php");
                        ?>
                        <div class="section__popular-posts">
                            <h2 class="section__title"><?php _e('Most popular on Blog', TEXT_DOMAIN); ?></h2>
                            <div class="slider slider--related" id="popular-post-slider">
                                <div class="slider__list">
                                    <?php while ( $most_popular_posts->have_posts() ) : $most_popular_posts->the_post(); ?>
                                        <div class="slider__item">
                                            <?php
                                                if ( ! empty( $template ) ) {
                                                    get_template_part('template-parts/cards/card', 'related', $template_args);
                                                } else {
                                                    get_template_part('template-parts/cards/card', 'default', $template_args);
                                                }
                                            ?>
                                        </div>
                                    <?php endwhile; wp_reset_postdata(); ?>
                                </div>
                                <div class="slider__controls"></div>
                            </div>
                        </div>
                    <?php endif; ?>
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
