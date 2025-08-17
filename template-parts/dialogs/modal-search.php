<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchModalLabel"><?php echo esc_html('Search', TEXT_DOMAIN); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo esc_attr('Close', TEXT_DOMAIN); ?>"></button>
            </div>
            <div class="modal-body">
                <?php get_search_form(); ?>
            </div>
        </div>
    </div>
</div>