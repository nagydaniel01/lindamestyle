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
?>
<?php get_header(); ?>

<main class="page page--archive page--archive-<?php esc_attr_e($post_type); ?>">
    <section class="section section--archive section--archive-<?php esc_attr_e($post_type); ?>" data-post-type="<?php esc_attr_e($post_type); ?>" data-posts-per-page="<?php esc_attr_e($posts_per_page); ?>">
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
                    <input type="text" name="filter-search" id="filter-search" class="form-control filter filter--search js-filter-search" placeholder="<?php echo esc_attr(sprintf(__('Search for %s', TEXT_DOMAIN), strtolower($post_type_obj->labels->name))); ?>" >
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

                <div class="section__toolbar">
                    <div class="row"> <!-- Bootstrap row wrapper -->
                        <div class="col-md-4 mb-3">
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
                    </div> <!-- end row -->
                </div>
            </header>

            <div class="section__body">
                <?php 
                    $user_id = get_current_user_id();

                    // If user is not logged in OR not admin and has no active membership
                    if ( ! is_user_logged_in() || ( ! current_user_can( 'administrator' ) && ! wc_memberships_is_user_active_member( $user_id ) ) ) {
                        wc_add_notice( 
                            sprintf(
                                __( 'This content is for members only. Please <a href="%s">buy a subscription</a> to access it.', TEXT_DOMAIN ),
                                esc_url( '/elofizetesek' )
                            ),
                            'notice'
                        );
                        wc_print_notices();
                    } else {
                ?>
                    <div id="post-list" class="section__content">
                        <?php 
                            $template_args = array('post_type' => esc_attr($post_type));
                            get_template_part( 'template-parts/queries/query', 'post-type', $template_args );
                        ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>