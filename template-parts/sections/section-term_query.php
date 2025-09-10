<?php
$section_title      = $section['term_query_section_title'] ?? '';
$section_hide_title = $section['term_query_section_hide_title'] ?? false;
$section_slug       = sanitize_title($section_title);
$section_lead       = $section['term_query_section_lead'] ?? '';

$link               = $section['term_query_link'] ?? '';
$slider             = $section['term_query_slider'] ?? '';
$box                = $section['term_query_box'] ?? '';

$url         = $link['url'] ?? '';
$title       = $link['title'] ?? esc_url($url);
$target      = isset($link['target']) && $link['target'] !== '' ? $link['target'] : '_self';
$is_external = is_external_url($url, get_home_url());

$query_args = [
    'taxonomy'   => $section['taxonomy_type'] ?? 'category',
    'orderby'    => $section['orderby'] ?? 'name',
    'order'      => strtoupper($section['order'] ?? 'ASC'),
    'hide_empty' => isset($section['hide_empty']) ? (bool) $section['hide_empty'] : true,
    'number'     => (int) ($section['terms_per_page'] ?? get_option('posts_per_page')),
];

// Manual selection
if (!empty($section['selection_type']) && $section['selection_type'] === 'manual') {
    if (!empty($section['category']) && is_array($section['category'])) {
        $query_args['include'] = array_map(fn($t) => $t->term_id, $section['category']);
    }

    if (!empty($section['event_cat']) && is_array($section['event_cat'])) {
        $query_args['include'] = array_map(fn($t) => $t->term_id, $section['event_cat']);
    }
}

// Auto selection – parent/child terms
if (!empty($section['selection_type']) && $section['selection_type'] === 'auto') {
    if ($taxonomy_type === 'category' && !empty($section['parent_category']) && is_object($section['parent_category'])) {
        $query_args['parent'] = (int) $section['parent_category']->term_id;
    }

    if ($taxonomy_type === 'event_cat' && !empty($section['parent_event_cat']) && is_object($section['parent_event_cat'])) {
        $query_args['parent'] = (int) $section['parent_event_cat']->term_id;
    }
}

/*
echo '<pre>';
var_dump($query_args);
echo '</pre>';
*/

$term_query = new WP_Term_Query($query_args);
?>

<?php if (!empty($term_query->terms)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--term_query<?php echo ($slider != false) ? ' section--slider' : ''; ?><?php echo ($box != false) ? ' section--box' : ''; ?>">
        <div class="container">
            
            <?php if (($section_title && $section_hide_title !== true) || $section_lead) : ?>
                <div class="section__header">
                    <?php if ($section_hide_title !== true) : ?>
                        <h1 class="section__title"><?php echo esc_html($section_title); ?></h1>
                    <?php endif; ?>
                    <?php if (!empty($section_lead)) : ?>
                        <div class="section__lead"><?php echo wp_kses_post($section_lead); ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="section__content">
                <?php if (!empty($url)) : ?>
                    <a href="<?php echo esc_url($url); ?>" target="<?php echo esc_attr($target); ?>" <?php echo $is_external ? 'rel="noopener noreferrer"' : ''; ?> class="btn btn-link section__link">
                        <span><?php esc_html_e($title, TEXT_DOMAIN); ?></span>
                        <svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg>
                    </a>
                <?php endif; ?>

                <?php if ($slider != false) : ?>
                    <div class="slider slider--term-query">
                        <div class="slider__list">
                            <?php foreach ($term_query->terms as $key => $term) : ?>
                                <div class="slider__item">
                                    <?php 
                                        $taxonomy = esc_attr($term->taxonomy);
                                        $template_args = ['term' => $term];
                                        $template = locate_template("template-parts/cards/card-term-{$taxonomy}.php");

                                        if (!empty($template)) {
                                            get_template_part('template-parts/cards/card-term', $taxonomy, $template_args);
                                        } else {
                                            get_template_part('template-parts/cards/card-term', 'default', $template_args);
                                        }
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="slider__controls"></div>
                    </div>
                <?php else : ?>
                    <div class="row gy-4">
                        <?php foreach ($term_query->terms as $key => $term) : ?>
                            <?php 
                                $taxonomy = esc_attr($term->taxonomy);
                                $template_args = ['term' => $term];
                                $template = locate_template("template-parts/cards/card-term-{$taxonomy}.php");
                            ?>
                            <div class="col-12 col-lg-6 col-xl-4">
                                <?php
                                    if (!empty($template)) {
                                        get_template_part('template-parts/cards/card-term', $taxonomy, $template_args);
                                    } else {
                                        get_template_part('template-parts/cards/card-term', 'default', $template_args);
                                    }
                                ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
