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
    
    } else {
        echo 'This is neither a post type nor a term.';
    }
?>
<?php get_header(); ?>

<main class="page page--archive page--archive-<?php esc_attr_e($post_type); ?>">
    <section class="section section--archive section--archive-<?php esc_attr_e($post_type); ?>" data-post-type="<?php esc_attr_e($post_type); ?>" data-posts-per-page="<?php esc_attr_e($posts_per_page); ?>">
        <div class="container">
            <div class="section__header">
                <h1 class="section__title">
                    <?php
                        switch ( true ) {
                            case is_category():
                                single_cat_title();
                                break;
                            case is_tag():
                                single_tag_title();
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
                <?php
                    if ( $description ) {
                        echo '<div class="section__lead">';
                        echo wpautop( wp_kses_post( $description ) );
                        echo '</div>';
                    } else {
                        the_archive_description( '<div class="section__lead">', '</div>' );
                    }
                ?>
                <div class="section__toolbar">
                    <input type="text" name="filter-search" id="filter-search" placeholder="<?php esc_attr_e( sprintf( __( '%s keresése', TEXT_DOMAIN ), $label ) ); ?>" class="filter filter--search form-control js-filter-search">
    
                    <?php foreach ($taxonomies as $key => $tax_obj) :
                        $terms = get_terms([
                            'taxonomy'   => $tax_obj,
                            'hide_empty' => false,
                        ]);
                        
                        if (!empty($terms) && !is_wp_error($terms)) :
                            $taxonomy = get_taxonomy($tax_obj);
                            $label = !empty($taxonomy->label) ? $taxonomy->label : '';
                            ?>
                            <select id="filter-<?php echo esc_attr($tax_obj); ?>" name="<?php echo esc_attr($tax_obj); ?>[]" multiple="multiple" class="filter form-select js-filter js-filter-default" data-filter="<?php echo esc_attr($tax_obj); ?>" data-placeholder="<?php echo esc_attr(sprintf(__('Szűrés %s szerint', 'TEXT_DOMAIN'), strtolower($label))); ?>">
                                <?php foreach ($terms as $term) : ?>
                                    <option value="<?php echo esc_attr($term->slug); ?>" 
                                        <?php selected(get_query_var($tax_obj . '_filter'), $term->slug); ?>>
                                        <?php echo esc_html($term->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php
                        endif;
                    endforeach; ?>
                </div>
            </div>
            <div id="post-list" class="section__content">
                <?php 
                    $template_args = array('post_type' => esc_attr($post_type));
                    get_template_part( 'template-parts/queries/query', 'post-type', $template_args );
                ?>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>