<div class="modal fade" id="testimonial_formModal" tabindex="-1" aria-labelledby="testimonial_formModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="testimonial_formModalLabel"><?php echo esc_html__('Rate us', TEXT_DOMAIN); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo esc_attr('Close', TEXT_DOMAIN); ?>"></button>
            </div>
            <div class="modal-body">
                <?php
                    $form_args = array();
                    get_template_part('template-parts/forms/form', 'testimonial_form', $form_args);
                ?>
            </div>
        </div>
    </div>
</div>