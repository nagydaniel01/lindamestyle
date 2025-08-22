<?php get_header(); ?>

<?php
    global $wp_query;
    
    $show_on_front  = get_option('show_on_front');
    $page_title     = get_the_title();

    if ($show_on_front === 'page') {
        $page_for_posts = get_option('page_for_posts');
        if ($page_for_posts) {
            $page_title = get_the_title($page_for_posts);
            $post_type  = 'post';
        }
    }
    
    $post_type_obj  = get_post_type_object($post_type);
    $posts_per_page = get_option('posts_per_page');

    $categories     = get_categories(['taxonomy' => 'category', 'hide_empty' => true]);
    //$post_tag       = get_terms(['taxonomy' => 'post_tag', 'hide_empty' => true]);

    // Get authors (only those who have published posts)
    $authors = get_users(['who' => 'authors', 'has_published_posts' => ['post'], 'orderby'  => 'display_name', 'order'    => 'ASC']);
?>

<main class="page page--archive page--archive-<?php esc_attr_e(get_post_type()); ?>">
    <section class="section section--archive section--archive-<?php esc_attr_e(get_post_type()); ?>" data-post-type="<?php esc_attr_e($post_type); ?>" data-posts-per-page="<?php esc_attr_e($posts_per_page); ?>">
        <div class="container">
            <header class="section__header">
                <h1 class="section__title"><?php esc_html_e($page_title); ?></h1>
                
                <div class="section__toolbar">
                    <input type="text" name="filter-search" id="filter-search" placeholder="<?php esc_attr_e( sprintf( __( '%s keresése', TEXT_DOMAIN ), $post_type_obj->labels->name ) ); ?>" class="filter filter--search form-control js-filter-search">
                </div>
            </header>

            <div class="row flex-row-reverse">
                <div class="col-12 col-lg-8 col-xl-9">
                    <div class="section__body">
                        <div id="post-list" class="section__content">
                            <?php 
                                $template_args = array('post_type' => esc_attr($post_type));
                                get_template_part( 'template-parts/queries/query', 'post-type', $template_args );
                            ?>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4 col-xl-3">
                    <div class="section__sidebar">
                        <?php if (!empty($categories) && is_array($categories)) : ?>
                            <div class="mb-3">
                                <select id="filter-category" name="category[]" multiple="multiple" class="filter form-select js-filter js-filter-default" data-filter="category" data-placeholder="<?php esc_attr_e('Szűrés kategóriák szerint', TEXT_DOMAIN); ?>">
                                    <?php foreach ($categories as $category) : ?>
                                        <option value="<?php esc_attr_e($category->slug); ?>" <?php selected(get_query_var('category_filter'), $category->slug); ?>>
                                            <?php esc_html_e($category->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($authors)) : ?>
                            <div class="mb-3">
                                <select id="filter-author" name="author[]" multiple="multiple" class="filter form-select js-filter js-filter-default" data-filter="author" data-placeholder="<?php esc_attr_e('Szűrés szerzők szerint', TEXT_DOMAIN); ?>">
                                    <?php foreach ($authors as $author) : ?>
                                        <option value="<?php esc_attr_e($author->ID); ?>" <?php selected(get_query_var('author_filter'), $author->ID); ?>>
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

                            foreach ($fields as $field) :
                                $filter_label   = $field['label'];
                                $filter_name    = $field['name'];
                                $filter_type    = $field['type'];
                                $filter_options = $field['choices'] ?? [];

                                // Get current selected values (from GET query vars)
                                $selected_values = !empty($_GET[$filter_name]) ? (array) $_GET[$filter_name] : [];

                                echo '<div class="mb-3 filter-group">';
                                echo '<label class="form-label" for="filter-' . esc_attr($filter_name) . '">' . esc_html($filter_label) . '</label>';

                                switch ($filter_type) {
                                    case 'checkbox':
                                    case 'radio':
                                        // Show each choice as individual checkbox/radio
                                        foreach ($filter_options as $value => $label) :
                                            $input_type = ($filter_type === 'checkbox') ? 'checkbox' : 'radio';
                                            $checked = in_array($value, $selected_values) ? 'checked' : '';
                                            echo '<div class="form-check">';
                                            echo '<input type="' . $input_type . '" name="' . esc_attr($filter_name) . ($input_type === 'checkbox' ? '[]' : '') . '" value="' . esc_attr($value) . '" id="' . esc_attr($filter_name . '-' . $value) . '" class="form-check-input filter js-filter js-filter-default" ' . $checked . '>';
                                            echo '<label class="form-check-label" for="' . esc_attr($filter_name . '-' . $value) . '">' . esc_html($label) . '</label>';
                                            echo '</div>';
                                        endforeach;
                                        break;

                                    case 'select':
                                        echo '<select name="' . esc_attr($filter_name) . ($field['multiple'] ? '[]' : '') . '" ' . ($field['multiple'] ? 'multiple' : '') . ' id="filter-' . esc_attr($filter_name) . '" class="form-select filter js-filter js-filter-default" data-filter="' . esc_attr($filter_name) . '" data-placeholder="' . esc_attr($filter_label) . '">';

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
                                        echo '<input type="text" class="form-control filter js-filter js-filter-default" 
                                                    name="' . esc_attr($filter_name) . '" 
                                                    value="' . esc_attr($value) . '" 
                                                    placeholder="' . esc_attr($filter_label) . '">';
                                        break;

                                    // Add more ACF types here if needed (number, date picker, etc.)
                                }

                                echo '</div>';
                            endforeach;

                        endif;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>