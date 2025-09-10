<?php
    $section_title      = $section['slider_section_title'] ?? '';
    $section_hide_title = $section['slider_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['slider_section_lead'] ?? '';
    $slider_items       = $section['slider_items'] ?? [];
    $slider_ratio       = $section['slider_ratio'] ?? '16x9'; // e.g. "16x9", "4x3"
    $slider_text_align  = $section['slider_text_align'] ?? 'center'; // left, center, right

    $cta_url    = $slide_cta['url'] ?? '';
    $cta_title  = $slide_cta['title'] ?? esc_url($cta_url);
    $cta_target = isset($slide_cta['target']) && $slide_cta['target'] !== '' ? $slide_cta['target'] : '_self';

    // Filter out empty items (image empty)
    $slider_items = array_filter($slider_items, function ($item) {
        $image = $item['slide_image'] ?? '';
        if (is_array($image)) $image = $image['url'] ?? '';
        return $image !== '';
    });
?>

<?php if (!empty($slider_items)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--slider pt-0 pb-0">
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
            <div class="slider slider--main" id="<?php echo esc_attr($section_slug); ?>-slider">
                <div class="slider__list">
                    <?php foreach ($slider_items as $key => $item) : 
                        $slide_title       = $item['slide_title'] ?? '';
                        $slide_description = $item['slide_description'] ?? '';
                        $slide_image       = $item['slide_image'] ?? '';
                        $slide_cta         = $item['slide_call_to_action'] ?? '';
                        
                        if (is_array($slide_image)) $slide_image_id = $slide_image['id'] ?? '';

                        $slide_image_alt = get_post_meta($slide_image_id, '_wp_attachment_image_alt', true);
                        if (!$slide_image_alt) $slide_image_alt = $slide_title;
                    ?>
                        <div class="slider__item">
                            <?php if ($slide_image_id) : ?>
                                <figure class="slider__image-wrapper ratio ratio-<?php echo esc_attr($slider_ratio); ?>">
                                    <?php echo wp_get_attachment_image($slide_image_id, 'full', false, ['class' => 'slider__image', 'alt' => esc_attr($slide_image_alt)]); ?>
                                </figure>
                            <?php endif; ?>

                            <div class="slider__caption text-<?php echo esc_attr($slider_text_align); ?>">
                                <div class="container">
                                    <div class="slider__caption-inner">
                                        <?php if ($slide_title) : ?>
                                            <h2 class="slider__caption-title"><?php echo esc_html($slide_title); ?></h2>
                                        <?php endif; ?>
    
                                        <?php if ($slide_description) : ?>
                                            <div class="slider__caption-description"><?php echo wp_kses_post($slide_description); ?></div>
                                        <?php endif; ?>
    
                                        <?php if ($cta_url) : ?>
                                            <a href="<?php echo esc_url($cta_url); ?>" target="<?php echo esc_attr($cta_target); ?>" class="slider-item__cta btn btn-primary"><?php echo esc_html($cta_title); ?></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="slider__controls"></div>
            </div>
        </div>
    </section>
<?php endif; ?>