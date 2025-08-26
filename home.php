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
    $authors = get_users(['who' => 'authors', 'has_published_posts' => ['post'], 'orderby' => 'display_name', 'order' => 'ASC']);
?>

<main class="page page--archive page--archive-<?php echo esc_attr(get_post_type()); ?>">
    <section class="section section--archive section--archive-<?php echo esc_attr(get_post_type()); ?>" data-post-type="<?php echo esc_attr($post_type); ?>" data-posts-per-page="<?php echo esc_attr($posts_per_page); ?>">
        <div class="container">
            <header class="section__header">
                <h1 class="section__title"><?php esc_html_e($page_title); ?></h1>
                <input type="text" name="filter-search" id="filter-search" class="form-control filter filter--search js-filter-search" placeholder="<?php echo esc_attr(sprintf(__('Search for %s', TEXT_DOMAIN), strtolower($post_type_obj->labels->name))); ?>" >
            </header>
            
            <div class="section__toolbar">
                <div class="row"> <!-- Bootstrap row wrapper -->

                    <?php if (!empty($categories) && is_array($categories)) : ?>
                        <div class="col-md-4 mb-3">
                            <fieldset id="filter-categories">
                                <legend>
                                    <?php 
                                        $filter_label = __('Categories', TEXT_DOMAIN);
                                        echo esc_html(sprintf(__('Filter by %s', TEXT_DOMAIN), strtolower($filter_label)));
                                    ?>
                                </legend>

                                <?php 
                                    $selected_categories = (array) get_query_var('category_filter');
                                ?>

                                <?php foreach ($categories as $category) : ?>
                                    <div class="form-check">
                                        <input type="checkbox" name="category[]" value="<?php echo esc_attr($category->slug); ?>" id="category-<?php echo esc_attr($category->slug); ?>" class="form-check-input filter js-filter js-filter-default" data-filter="category" <?php checked(in_array($category->slug, $selected_categories, true)); ?>>
                                        <label class="form-check-label" for="category-<?php echo esc_attr($category->slug); ?>">
                                            <?php echo esc_html($category->name); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </fieldset>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($authors)) : ?>
                        <div class="col-md-4 mb-3">
                            <?php $filter_label = __('Authors', TEXT_DOMAIN); ?>
                            <select name="author[]" multiple="multiple" id="filter-author" class="form-select filter js-filter js-filter-default" data-filter="author" data-placeholder="<?php echo esc_attr(sprintf(__('Filter by %s', TEXT_DOMAIN), strtolower($filter_label))); ?>">
                                <?php foreach ($authors as $author) : ?>
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
                            foreach ($fields as $field) :
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
                                        echo '<legend class="fw-bold mb-2">' . esc_html($filter_label) . '</legend>';

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
                </div> <!-- end row -->
            </div>

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