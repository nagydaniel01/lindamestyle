import $ from 'jquery';
import 'slick-slider';

var productGallerySlider = $('.woocommerce-product-gallery__wrapper');

if (productGallerySlider) {
    productGallerySlider.slick({
        infinite: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        prevArrow: '<button type="button" class="slick-arrow slick-prev" aria-label="Előző"><svg class="icon icon-arrow-left"><use xlink:href="#icon-arrow-left"></use></svg></button>',
        nextArrow: '<button type="button" class="slick-arrow slick-next" aria-label="Következő"><svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg></button>',
        mobileFirst: true,
        responsive: [
            {
                breakpoint: 991,
                settings: {
                  arrows: true,
                }
            }
        ]
    });
}