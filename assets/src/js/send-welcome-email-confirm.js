(function ($) {
    'use strict';

    $('.send-welcome-email-link').on('click', function (e) {
        e.preventDefault();

        const $link = $(this);
        const href = $link.attr('href');
        const name = $link.data('name') || '';

        Swal.fire({
            title: translations.confirmTitle,
            text: `${translations.confirmMessage} "${name}"?`,
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