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
    $post_tag       = get_terms(['taxonomy' => 'post_tag', 'hide_empty' => true]);
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
                            <select id="filter-category" name="category[]" multiple="multiple" class="filter form-select js-filter js-filter-default" data-filter="category" data-placeholder="<?php esc_attr_e('Szűrés kategóriák szerint', TEXT_DOMAIN); ?>">
                                <?php foreach ($categories as $category) : ?>
                                    <option value="<?php esc_attr_e($category->slug); ?>" <?php selected(get_query_var('category_filter'), $category->slug); ?>>
                                        <?php esc_html_e($category->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>