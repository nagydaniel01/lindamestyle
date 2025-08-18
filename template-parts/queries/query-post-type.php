<?php
$post_type          = $args['post_type'] ?? '';
$current_date_time  = current_time('Y-m-d H:i:s');
$paged              = max(1, get_query_var('paged', 1));
$post_type_args     = array();


if (!empty($post_type)) {
    $post_type_args['post_type'] = $post_type;
    $post_type_args['post_status'] = 'publish';

    if ($post_type == 'service' && !empty($event_type)) {
        $post_type_args['orderby'] = 'name';
        $post_type_args['order'] = 'ASC';
    } else {
        $post_type_args['orderby'] = 'date';
        $post_type_args['order'] = 'DESC';
    }
}

if (!empty($filter_object)) {
    $tax_counter = 0;
    $meta_counter = 0;

    foreach ($filter_object as $filter => $values) {
        if ($filter === 'per_page') {
            $post_type_args['posts_per_page'] = $values;
        } elseif ( $filter === 'offset') {
            $post_type_args['offset'] = $values;
        } elseif ( $filter === 'current_page') {
            $post_type_args['paged'] = $values;
        } elseif ($filter === 'keyword') {
            $post_type_args['s'] = $values;
        } elseif ($filter === 'alphabet') {
            $post_type_args['alphabet'] = $filter_object['alphabet'];
        } elseif ($filter === 'post_date') {
            $post_type_args['year'] = $values;
        } elseif ($filter === 'event_date') {
            $year = intval($values);

            $year_start = $year . '0101';
            $year_end   = $year . '1231';

            $post_type_args['meta_query'][] = array(
                array(
                    'key'     => 'event_start_date',
                    'value'   => array($year_start, $year_end),
                    'compare' => 'BETWEEN',
                    'type'    => 'DATE'
                )
            );
        } else {
            $options = array(
                'taxonomy' => $filter,
                'field' => 'slug',
                'terms' => $values,
            );
            
            if ($tax_counter === 0) {
                $post_type_args['tax_query'] = array('relation' => 'AND');
            }
            
            $post_type_args['tax_query'][] = $options;

            $tax_counter++;
        }
    }
}

/*
echo '<pre>';
var_dump($post_type_args);
echo '</pre>';
*/

$post_type_query = new WP_Query($post_type_args);
?>

<?php if (is_object($post_type_query) && $post_type_query->have_posts()) : ?>
    <div class="row gy-4">
        <?php while ($post_type_query->have_posts()) : $post_type_query->the_post(); ?>
            <?php 
                $post_type = get_post_type(); // Get the current post type once
                $template_args = array('post_type' => esc_attr($post_type));
                $template = locate_template("template-parts/cards/card-{$post_type}.php");

                switch ($post_type) {
                    case 'event':
                        ?>

                        <div class="col-12 col-lg-6 col-xl-4">
                            <?php
                                if (!empty($template)) {
                                    // File exists, include it
                                    get_template_part('template-parts/cards/card', $post_type, $template_args);
                                } else {
                                    // File does not exist, handle accordingly
                                    get_template_part('template-parts/cards/card', 'default', $template_args);
                                }
                            ?>
                        </div>

                        <?php
                        break;
                    
                    default:
                        ?>

                        <div class="col-12 col-lg-6 col-xl-4">
                            <?php
                                if (!empty($template)) {
                                    // File exists, include it
                                    get_template_part('template-parts/cards/card', $post_type, $template_args);
                                } else {
                                    // File does not exist, handle accordingly
                                    get_template_part('template-parts/cards/card', 'default', $template_args);
                                }
                            ?>
                        </div>

                        <?php
                        break;
                }
            ?>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
    </div>

    <?php
        // posts to display per page
        $posts_per_page = (int) $post_type_query->query_vars['posts_per_page'];
        // current page
        $current_page = (int) $post_type_query->query_vars['paged'];
        // the overall amount of pages
        $max_page = $post_type_query->max_num_pages;

        // we don't have to display pagination or load more button in this case
        if( $max_page <= 1 ) return;

        // set the current page to 1 if not exists
        if( empty( $current_page ) || $current_page == 0) $current_page = 1;

        // how much links to display in pagination
        $links_in_the_middle = 3;
        $links_in_the_middle_minus_1 = $links_in_the_middle-1;

        // required to display the pagination properly for large amount of pages
        $first_link_in_the_middle = $current_page - floor( $links_in_the_middle_minus_1/2 );
        $last_link_in_the_middle = $current_page + ceil( $links_in_the_middle_minus_1/2 );

        // some calculations with $first_link_in_the_middle and $last_link_in_the_middle
        if( $first_link_in_the_middle <= 0 ) $first_link_in_the_middle = 1;
        if( ( $last_link_in_the_middle - $first_link_in_the_middle ) != $links_in_the_middle_minus_1 ) { $last_link_in_the_middle = $first_link_in_the_middle + $links_in_the_middle_minus_1; }
        if( $last_link_in_the_middle > $max_page ) { $first_link_in_the_middle = $max_page - $links_in_the_middle_minus_1; $last_link_in_the_middle = (int) $max_page; }
        if( $first_link_in_the_middle <= 0 ) $first_link_in_the_middle = 1;
    ?>
    <div class="load-more js-load-more">
        <nav class="section__pagination pagination js-pagination" role="navigation" data-max-pages="<?php esc_attr_e($post_type_query->max_num_pages); ?>">
            <ul class="page-numbers">
                <?php if ($current_page != 1) : ?>
                    <li>
                        <a href="" class="page-numbers prev js-pagination-link">
                            <svg class="icon icon-chevron-left"><use xlink:href="#icon-chevron-left"></use></svg>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($first_link_in_the_middle >= 3 && $links_in_the_middle < $max_page) : ?>
                    <li>
                        <a href="" data-number="1" class="page-numbers number js-pagination-link">1</a>
                    </li>
    
                    <?php if( $first_link_in_the_middle != 2 ) : ?>
                        <li>
                            <span class="page-numbers dots">...</span>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $first_link_in_the_middle; $i <= $last_link_in_the_middle; $i++) : ?>
                    <?php if ($i == $current_page) : ?>
                        <li>
                            <span aria-current="page" class="page-numbers current"><?php esc_html_e($i); ?></span>
                        </li>
                    <?php else : ?>
                        <li>
                            <a href="" data-number="<?php esc_attr_e($i); ?>" class="page-numbers number js-pagination-link"><?php esc_html_e($i); ?></a>
                        </li>
                    <?php endif; ?>
                <?php endfor; ?>
    
                <?php if ( $last_link_in_the_middle < $max_page ) : ?>
                    <?php if ( $last_link_in_the_middle != ($max_page-1) ) : ?>
                        <li>
                            <span class="page-numbers dots">...</span>
                        </li>
                    <?php endif; ?>
                    
                    <li>
                        <a href="" data-number="<?php esc_attr_e($max_page); ?>" class="page-numbers number js-pagination-link"><?php esc_html_e($max_page); ?></a>
                    </li>
                <?php endif; ?>
                
                <?php if ($current_page != $last_link_in_the_middle ) : ?>
                    <li>
                        <a href="" class="page-numbers next js-pagination-link">
                            <svg class="icon icon-chevron-right"><use xlink:href="#icon-chevron-right"></use></svg>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
<?php else : ?>
    <p class="no-result"><?php esc_html_e('Sajnáljuk, de a megadott feltételek alapján nem találtunk eredményeket.', TEXT_DOMAIN); ?></p>
<?php endif; ?>
