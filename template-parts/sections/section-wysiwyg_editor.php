<?php
    $section_title  = $section['wysiwyg_editor_section_title'] ?? '';
    $section_slug   = sanitize_title($section_title);
    $section_lead   = $section['wysiwyg_editor_section_lead'] ?? '';
    $wysiwyg_editor = $section['wysiwyg_editor'] ?? '';
?>

<?php if (!empty($wysiwyg_editor)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--wysiwyg_editor">
        <div class="container">
            <div class="section__header">
                <h1 class="section__title"><?php echo esc_html($section_title); ?></h1>
                <?php if (!empty($section_lead)) : ?>
                    <div class="section__lead"><?php echo wp_kses_post($section_lead); ?></div>
                <?php endif; ?>
            </div>
            <div class="section__content">
                <?php echo wp_kses_post($wysiwyg_editor); ?>
            </div>    
        </div>
    </section>
<?php endif; ?>