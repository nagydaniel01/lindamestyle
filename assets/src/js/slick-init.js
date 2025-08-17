import $ from 'jquery';
import 'slick-slider';

$(document).ready(function() {
    // Define the selectors for the sliders
    const homePostsSliderSelector = $('.slider--home-posts');
    const gallerySliderSelector = $('.slider--gallery .slider__list');
    const postQuerySliderSelector = $('.slider--post-query .slider__list');
    const relatedSliderSelector = $('.slider--related .slider__list');
    const worksSliderSelector = $('.slider--works');
    const logoSliderSelector = $('.slider--logo .slider__list');
    const pageHeadlineSliderSelector = $('.slider--page-headline');

    // Main Slider
    if (homePostsSliderSelector.length) {
        homePostsSliderSelector.slick({
            mobileFirst: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            dots: true,
            autoplay: true,
            autoplaySpeed: 5000,
            fade: true,
        });
    }

    if (pageHeadlineSliderSelector.length) {
        pageHeadlineSliderSelector.slick({
            mobileFirst: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            dots: true,
            autoplay: true,
            autoplaySpeed: 5000,
            fade: true,
        });
    }

    if (logoSliderSelector.length) {
        logoSliderSelector.slick({
            mobileFirst: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            prevArrow: '<button type="button" class="slick-arrow slick-prev" aria-label="Előző"><svg class="icon icon-arrow-left"><use xlink:href="#icon-arrow-left"></use></svg></button>',
            nextArrow: '<button type="button" class="slick-arrow slick-next" aria-label="Következő"><svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg></button>',
            responsive: [
              {
                breakpoint: 991,
                settings: {
                  slidesToShow: 3,
                  slidesToScroll: 1,
                }
              },
              {
                breakpoint: 1599,
                settings: {
                  slidesToShow: 6,
                  slidesToScroll: 1,
                  arrows: false
                }
              }
            ]
        });
    }

    // Gallery Slider
    if (gallerySliderSelector.length) {
      gallerySliderSelector.slick({
        infinite: false,
        mobileFirst: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        variableWidth: true,
        prevArrow: '<button type="button" class="slick-arrow slick-prev" aria-label="Előző"><svg class="icon icon-arrow-left"><use xlink:href="#icon-arrow-left"></use></svg></button>',
        nextArrow: '<button type="button" class="slick-arrow slick-next" aria-label="Következő"><svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg></button>',
        appendArrows: '.slider--gallery .slider__controls',
      });
    }

    // Gallery Slider
    if (postQuerySliderSelector.length) {
      postQuerySliderSelector.slick({
        mobileFirst: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        prevArrow: '<button type="button" class="slick-arrow slick-prev" aria-label="Előző"><svg class="icon icon-arrow-left"><use xlink:href="#icon-arrow-left"></use></svg></button>',
        nextArrow: '<button type="button" class="slick-arrow slick-next" aria-label="Következő"><svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg></button>',
        appendArrows: '.slider--post-query .slider__controls',
        responsive: [
          {
            breakpoint: 767,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1,
            }
          },
          {
            breakpoint: 991,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 1,
            }
          },
          {
            breakpoint: 1199,
            settings: {
              slidesToShow: 4,
              slidesToScroll: 1
            }
          }
        ]
      });
    }

    // Related Slider
    if (relatedSliderSelector.length) {
        relatedSliderSelector.slick({
          infinite: false,
          mobileFirst: true,
          slidesToShow: 1,
          slidesToScroll: 1,
          prevArrow: '<button type="button" class="slick-arrow slick-prev" aria-label="Előző"><svg class="icon icon-arrow-left"><use xlink:href="#icon-arrow-left"></use></svg></button>',
          nextArrow: '<button type="button" class="slick-arrow slick-next" aria-label="Következő"><svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg></button>',
          appendArrows: '.slider--related .slider__controls',
          responsive: [
          {
            breakpoint: 767,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1,
            }
          },
          {
            breakpoint: 1199,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 1
            }
          }
        ]
      });
    }

    // Works slider
    if (worksSliderSelector.length) {
        worksSliderSelector.slick({
            infinite: false,
            mobileFirst: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            prevArrow: '<button type="button" class="slick-arrow slick-prev" aria-label="Előző"><svg class="icon icon-arrow-left"><use xlink:href="#icon-arrow-left"></use></svg></button>',
            nextArrow: '<button type="button" class="slick-arrow slick-next" aria-label="Következő"><svg class="icon icon-arrow-right"><use xlink:href="#icon-arrow-right"></use></svg></button>',
            responsive: [
                {
                  breakpoint: 991,
                  settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1,
                  }
                },
                {
                  breakpoint: 1599,
                  settings: {
                    slidesToShow: 6,
                    slidesToScroll: 1
                  }
                }
            ]
        });
    }
});
