<?php
    $section_title  = $section['form_section_title'] ?? '';
    $section_slug   = sanitize_title($section_title);
    $section_lead   = $section['form_section_lead'] ?? '';
    $form_id        = $section['form'] ?? '';
?>

<?php if (!empty($form_id)) : ?>
    <section id="<?php echo esc_attr($section_slug); ?>" class="section section--form">
        <div class="container">
            <?php if ($section_title || $section_lead) : ?>
                <div class="section__header">
                    <h1 class="section__title"><?php echo esc_html($section_title); ?></h1>
                    <?php if (!empty($section_lead)) : ?>
                        <div class="section__lead"><?php echo wp_kses_post($section_lead); ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="section__content">
                <?php
                    $form_args = array();
                    get_template_part('template-parts/forms/form', $form_id, $form_args);
                ?>
            </div>    
        </div>
    </section>
<?php endif; ?>