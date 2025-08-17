import { Fancybox } from '@fancyapps/ui';

document.querySelectorAll('.section--single-post .section__content a:has(img)').forEach(link => {
    const img = link.querySelector('img');
    if (img) {
        // Many WP thumbnails have -300x200 or similar before the extension
        link.href = img.src.replace(/-\d+x\d+(?=\.[a-z]{3,4}$)/i, '');
    }
});

Fancybox.bind("[data-fancybox], .section--single-post .section__content a:has(img)");
Fancybox.bind("[data-fancybox], .section--single-event .block--sidebar-featured a:has(img)");
Fancybox.bind("[data-fancybox], .section--single-announcement .block--sidebar-featured a:has(img)");