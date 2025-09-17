<div aria-live="polite" aria-atomic="true" class="position-relative">
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div class="toast" id="newPostToast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto"><?php echo esc_html__('🎉 New Post', TEXT_DOMAIN); ?></strong>
                <small><?php echo esc_html__('Just now', TEXT_DOMAIN); ?></small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="<?php echo esc_attr__('Close', TEXT_DOMAIN); ?>"></button>
            </div>
            <div class="toast-body">
                <a href="#" id="newPostLink"><?php echo esc_html__('A new post has been published!', TEXT_DOMAIN); ?></a>
            </div>
        </div>
    </div>
</div>