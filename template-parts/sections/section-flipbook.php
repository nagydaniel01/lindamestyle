<?php
    $section_title  = $section['flipbook_section_title'] ?? '';
    $section_slug   = sanitize_title($section_title);
    $section_lead   = $section['flipbook_section_lead'] ?? '';
    $flipbook       = $section['flipbook'] ?? [];
    $file_url       = $flipbook['url'] ?? '';
    $file_title     = $flipbook['title'] ?? '';

    $aria_label = sprintf(
        /* translators: %1$s is the file title, %2$s is the singular post type name */
        __('A(z) "%1$s" című %2$s letöltése', TEXT_DOMAIN),
        $file_title,
        __('ebook', TEXT_DOMAIN)
    );
?>

<?php if (shortcode_exists('real3dflipbook') && !empty($file_url)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--flipbook">
        <div class="container">
            <div class="section__header">
                <h1 class="section__title"><?php echo esc_html($section_title); ?></h1>
                <?php if (!empty($section_lead)) : ?>
                    <div class="section__lead"><?php echo wp_kses_post($section_lead); ?></div>
                <?php endif; ?>
            </div>
            <div class="section__content">
                <?php echo do_shortcode('[real3dflipbook id="1" pdf="' . esc_url($file_url) . '"]'); ?>
                <a href="<?php echo esc_url($file_url); ?>" target="_self" aria-label="<?php esc_attr_e($aria_label); ?>" download class="section__button btn btn-primary">
                    <span><?php esc_html_e('PDF letöltése', TEXT_DOMAIN); ?></span>
                    <svg class="icon icon-download"><use xlink:href="#icon-download"></use></svg>
                </a>
            </div>    
        </div>
    </section>
<?php endif; ?>