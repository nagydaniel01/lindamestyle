<?php
    if (isset($GLOBALS['wp_query']->query_vars['bookmarks'])) {
        $current_user_id    = get_current_user_id();
        $bookmark_ids       = get_field('user_bookmarks', 'user_'.$current_user_id) ?: [];
    
        if (!empty($bookmark_ids)) {
            $post_type = ['post', 'knowledge_base'];
            $posts_per_page = get_option('posts_per_page');
            ?>
                <section class="section section__bookmarked-posts" data-post-type="<?php echo esc_attr('post'); ?>" data-posts-per-page="<?php echo esc_attr($posts_per_page); ?>">
                    <h2 class="section__title"><?php _e('Saved aricles', TEXT_DOMAIN); ?></h2>
                    <div id="post-list" class="section__content">
                        <?php 
                            $template_args = array('post_type' => esc_attr($post_type), 'bookmark_ids' => $bookmark_ids);
                            dump($template_args);
                            get_template_part( 'template-parts/queries/query', 'post-type', $template_args );
                        ?>
                    </div>
                </section>
            <?php
        }
    }