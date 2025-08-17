(function ($) {
    'use strict';

    $('.edit-request-link').on('click', function (e) {
        e.preventDefault();

        const $link = $(this);
        const href = $link.attr('href');
        const title = $link.data('title') || '';

        Swal.fire({
            title: translations.confirmTitle,
            text: `${translations.confirmMessage} "${title}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: translations.confirmButton,
            cancelButtonText: translations.cancelButton
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });
    
})(jQuery);