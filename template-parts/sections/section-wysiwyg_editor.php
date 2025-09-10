<?php
    $section_title      = $section['wysiwyg_editor_section_title'] ?? '';
    $section_hide_title = $section['slider_section_hide_title'] ?? false;
    $section_slug       = sanitize_title($section_title);
    $section_lead       = $section['wysiwyg_editor_section_lead'] ?? '';
    $wysiwyg_editor     = $section['wysiwyg_editor'] ?? '';
?>

<?php if (!empty($wysiwyg_editor)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--wysiwyg_editor">
        <div class="container">
            <div class="section__inner">
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
                    <?php echo wp_kses_post($wysiwyg_editor); ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>