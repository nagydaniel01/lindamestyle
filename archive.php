<?php
    $object = get_queried_object();
    if ( $object instanceof WP_Post_Type ) {
    
        // It's a post type object
        $post_type   = $object->name;
        $taxonomies  = get_object_taxonomies($post_type);
        $description = get_field($post_type.'_description', 'option');
        $label       = $object->labels->name;
    
    } elseif ( $object instanceof WP_Term ) {
        // It's a taxonomy term object
        $taxonomy     = $object->taxonomy; // taxonomy slug (e.g. category, product_cat)
        $taxonomy_obj = get_taxonomy( $taxonomy );
        $post_types   = $taxonomy_obj->object_type;
        $post_type    = reset( $post_types );
    } else {
        echo 'This is neither a post type nor a term.';
    }

    $post_type_obj = get_post_type_object( $post_type );
?>
<?php get_header(); ?>

<main class="page page--archive page--archive-<?php echo esc_attr($post_type); ?>">
    <section class="section section--archive section--archive-<?php echo esc_attr($post_type); ?>" data-post-type="<?php echo esc_attr($post_type); ?>" data-posts-per-page="<?php echo esc_attr($posts_per_page); ?>">
        <div class="container">
            <header class="section__header">
                <?php if ( function_exists('rank_math_the_breadcrumbs') ) rank_math_the_breadcrumbs(); ?>
                
                <div class="section__title-wrapper">
                    <h1 class="section__title">
                        <?php
                            switch ( true ) {
                                case is_category():
                                    single_cat_title();
                                    break;
                                case is_tag():
                                    single_tag_title();
                                    break;
                                case is_tax():
                                    single_term_title();
                                    break;
                                case is_author():
                                    printf( /* translators: %s is the author name */
                                        esc_html__( 'Author: %s', TEXT_DOMAIN ),
                                        get_the_author()
                                    );
                                    break;
                                case is_day():
                                    printf(
                                        esc_html__( 'Day: %s', TEXT_DOMAIN ),
                                        get_the_date()
                                    );
                                    break;
                                case is_month():
                                    printf(
                                        esc_html__( 'Month: %s', TEXT_DOMAIN ),
                                        get_the_date( 'F Y' )
                                    );
                                    break;
                                case is_year():
                                    printf(
                                        esc_html__( 'Year: %s', TEXT_DOMAIN ),
                                        get_the_date( 'Y' )
                                    );
                                    break;
                                default:
                                    post_type_archive_title();
                                    break;
                            }
                        ?>
                    </h1>
                </div>
                
                <?php
                    if ( $description ) {
                        echo '<div class="section__lead">';
                        echo wpautop( wp_kses_post( $description ) );
                        echo '</div>';
                    } else {
                        the_archive_description( '<div class="section__lead">', '</div>' );
                    }
                ?>
            </header>

            <div class="section__body">
                <div id="post-list" class="section__content">
                    <?php 
                        $template_args = array('post_type' => esc_attr($post_type));
                        get_template_part( 'template-parts/queries/query', 'post-type', $template_args );
                    ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>