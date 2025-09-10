<?php
    $object = get_queried_object();
    if ( $object instanceof WP_Post_Type ) {
    
        // It's a post type object
        $post_type     = $object->name;
        $post_type_obj = get_post_type_object($post_type);
        $taxonomies    = get_object_taxonomies($post_type);
        $description   = get_field($post_type.'_description', 'option');
        $label         = $object->labels->name;
    
    } elseif ( $object instanceof WP_Term ) {
    
        // It's a taxonomy term object
        $taxonomy     = $object->taxonomy;
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
                    <div class="row mt-3"> <!-- Bootstrap row wrapper -->
                        <div class="col-md-4 mb-3">
                            <?php foreach ($taxonomies as $key => $tax_obj) :
                                $terms = get_terms([
                                    'taxonomy'   => $tax_obj,
                                    'hide_empty' => false,
                                ]);

                                if (!empty($terms) && !is_wp_error($terms)) :
                                    $taxonomy = get_taxonomy($tax_obj);
                                    $label    = !empty($taxonomy->label) ? $taxonomy->label : '';

                                    // Current selected filters
                                    $selected_filters = get_query_var($tax_obj . '_filter');
                                    if (!is_array($selected_filters)) {
                                        $selected_filters = $selected_filters ? [$selected_filters] : [];
                                    }
                                    ?>

                                    <div class="filter-checkbox-group mb-3" id="filter-<?php echo esc_attr($tax_obj); ?>">
                                        <p class="fw-bold mb-2">
                                            <?php echo esc_html(sprintf(__('Filter by %s', TEXT_DOMAIN), strtolower($label))); ?>
                                        </p>

                                        <?php foreach ($terms as $key => $term) : ?>
                                            <div class="form-check">
                                                <input type="checkbox"
                                                    name="<?php echo esc_attr($tax_obj); ?>[]"
                                                    value="<?php echo esc_attr($term->slug); ?>"
                                                    id="<?php echo esc_attr($tax_obj . '-' . $term->slug); ?>"
                                                    class="form-check-input filter js-filter js-filter-default"
                                                    data-filter="<?php echo esc_attr($tax_obj); ?>"
                                                    <?php checked(in_array($term->slug, $selected_filters, true)); ?>>
                                                
                                                <label class="form-check-label" for="<?php echo esc_attr($tax_obj . '-' . $term->slug); ?>">
                                                    <?php echo esc_html($term->name); ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <?php
                                endif;
                            endforeach; ?>
                        </div>

                        <?php if (!empty($authors) && count($authors) > 1) : ?>
                            <div class="col-md-4 mb-3">
                                <?php $filter_label = __('Authors', TEXT_DOMAIN); ?>
                                <select name="author[]" multiple="multiple" id="filter-author" class="form-select filter js-filter js-filter-default" data-filter="author" data-placeholder="<?php echo esc_attr(sprintf(__('Filter by %s', TEXT_DOMAIN), strtolower($filter_label))); ?>">
                                    <?php foreach ($authors as $key => $author) : ?>
                                        <option value="<?php echo esc_attr($author->ID); ?>" <?php selected(get_query_var('author_filter'), $author->ID); ?>>
                                            <?php echo esc_html($author->display_name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
    
                        <?php
                            $post_filter_group_id = 'group_68a866a6aa801';
                            $fields               = acf_get_fields($post_filter_group_id);
    
                            if (!empty($fields) && is_array($fields)) :
                                foreach ($fields as $key => $field) :
                                    $filter_label   = $field['label'];
                                    $filter_name    = $field['name'];
                                    $filter_type    = $field['type'];
                                    $filter_options = $field['choices'] ?? [];
                                    $selected_values = !empty($_GET[$filter_name]) ? (array) $_GET[$filter_name] : [];
    
                                    echo '<div class="col-md-4 mb-3">';
    
                                    if (!in_array($filter_type, ['checkbox', 'radio'])) {
                                        // Optional: label for non-checkbox/radio types
                                    }
    
                                    switch ($filter_type) {
                                        case 'checkbox':
                                        case 'radio':
                                            echo '<fieldset class="filter-' . esc_attr($filter_name) . ' mb-3">';
                                            echo '<legend class="fw-bold mb-2">' . esc_html(sprintf(__('Filter by %s', TEXT_DOMAIN), strtolower($filter_label))) . '</legend>';
    
                                            foreach ($filter_options as $value => $label) :
                                                $input_type = ($filter_type === 'checkbox') ? 'checkbox' : 'radio';
                                                $checked = in_array($value, $selected_values) ? 'checked' : '';
                                                echo '<div class="form-check">';
                                                echo '<input type="' . $input_type . '" name="' . esc_attr($filter_name) . ($input_type === 'checkbox' ? '[]' : '') . '" value="' . esc_attr($value) . '" id="' . esc_attr($filter_name . '-' . $value) . '" class="form-check-input filter js-filter js-filter-default" data-filter="' . esc_attr($filter_name) . '" ' . $checked . '>';
                                                echo '<label class="form-check-label" for="' . esc_attr($filter_name . '-' . $value) . '">' . esc_html($label) . '</label>';
                                                echo '</div>';
                                            endforeach;
    
                                            echo '</fieldset>';
                                            break;
    
                                        case 'select':
                                            echo '<select name="' . esc_attr($filter_name) . ($field['multiple'] ? '[]' : '') . '" ' . ($field['multiple'] ? 'multiple' : '') . ' id="filter-' . esc_attr($filter_name) . '" class="form-select filter js-filter js-filter-default" data-filter="' . esc_attr($filter_name) . '" data-placeholder="' . esc_attr(sprintf('Filter by %s', strtolower($filter_label))) . '">';
    
                                            if (!$field['multiple']) {
                                                echo '<option value="" disabled selected hidden>' . esc_html($filter_label) . '</option>';
                                            }
    
                                            foreach ($filter_options as $value => $label) :
                                                $selected = in_array($value, $selected_values) ? 'selected' : '';
                                                echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
                                            endforeach;
    
                                            echo '</select>';
                                            break;
    
                                        case 'text':
                                            $value = $selected_values[0] ?? '';
                                            echo '<input type="text" name="' . esc_attr($filter_name) . '" value="' . esc_attr($value) . '" class="form-control filter js-filter js-filter-default" data-filter="' . esc_attr($filter_name) . '" placeholder="' . esc_attr($filter_label) . '">';
                                            break;
                                    }
    
                                    echo '</div>'; // col-md-4
                                endforeach;
                            endif;
                        ?>

                        <?php if ( is_user_logged_in() ) : ?>
                            <div class="col-md-4 mb-3">
                                <fieldset id="filter-profile">
                                    <legend class="fw-bold mb-2">
                                        <?php _e('My Profile Filters', TEXT_DOMAIN); ?>
                                    </legend>
                                    <?php 
                                        $apply_profile_checked = !empty($_GET['apply_profile_filters']) && $_GET['apply_profile_filters'] == '1';
                                    ?>
                                    <div class="form-check">
                                        <input type="checkbox" name="apply_profile_filters" value="1" id="apply-profile-filters" class="form-check-input filter js-filter js-filter-default" data-filter="apply_profile_filters" <?php checked($apply_profile_checked); ?>>
                                        <label class="form-check-label" for="apply-profile-filters">
                                            <?php _e('Apply my profile settings', TEXT_DOMAIN); ?>
                                        </label>
                                    </div>
                                </fieldset>
                            </div>
                        <?php endif; ?>
                    </div> <!-- end row -->
                </div>
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