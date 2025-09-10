import $ from 'jquery';

$(document).ready(function () {
    const $header = $('.header');
    let lastScroll = $(window).scrollTop();

    $(window).on('scroll', function () {
        const currentScroll = $(this).scrollTop();

        if (currentScroll > lastScroll && currentScroll > 100) {
            // scrolling down → hide header
            $header.addClass('is-hidden').removeClass('is-sticky');
        } else if (currentScroll < lastScroll) {
            // scrolling up → show header
            $header.removeClass('is-hidden').addClass('is-sticky');
        }

        // remove sticky if at very top
        if (currentScroll === 0) {
            $header.removeClass('is-hidden is-sticky');
        }

        lastScroll = currentScroll;
    });
});
