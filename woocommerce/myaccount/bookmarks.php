<?php
    if (isset($GLOBALS['wp_query']->query_vars['bookmarks'])) {
        $current_user_id = get_current_user_id();
        $post_type       = ['post', 'knowledge_base'];
        $posts_per_page  = get_option('posts_per_page');
        $bookmark_ids    = get_field('user_bookmarks', 'user_'.$current_user_id) ?: [0];
        ?>

            <section class="section section__bookmarked-posts" data-post-type='<?php echo json_encode($post_type); ?>' data-posts-per-page="<?php echo esc_attr($posts_per_page); ?>" data-post-ids='<?php echo json_encode($bookmark_ids); ?>'>
                <div id="bookmark-list" class="section__content">
                    <?php 
                        $template_args = array('post_type' => $post_type, 'post_ids' => $bookmark_ids);
                        get_template_part( 'template-parts/queries/query', 'post-type', $template_args );
                    ?>
                </div>
            </section>
        
        <?php
    }
