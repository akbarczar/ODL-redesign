define([
    'jquery',
    'underscore',
    'meigeeSwiper'
], function ($, _, meigeeSwiper) {
    'use strict';

    $.widget('meigee.themeSlider',{

        /**
         * @private
         */
        _init: function () {
            var widget = this,
                options = this.getOptions(this.options, this.element),
                themeSwiperSlider = {};

            if ( this.options.sliderType === 'small-only' ) {
                if (window.matchMedia("(max-width: 767px)").matches) {
                    themeSwiperSlider = new meigeeSwiper(this.element, options);
                } else {
                    this.element.removeClass('swiper-container').find('.product-items').removeClass('swiper-wrapper');
                }
            } else {
                themeSwiperSlider = new meigeeSwiper(this.element, options);
            }

            if (themeSwiperSlider.params) {
                themeSwiperSlider.on('init', function() {
                    // after init Swiper
                    widget.afterSliderInit();
                });

                // init Swiper
                themeSwiperSlider.init();
            }
        },

        /**
         * Set swiper navigation buttons position
         */
        navigationButtonsPosition: function () {
            if ($(this.element).find('.swiper-navigation').length){
                var navigationButtons = $(this.element).find('.swiper-navigation > div'),
                    $images = $(this.element).find('img.product-image-photo'),
                    maxImageHeight = 0;

                $images.each(function (imageIndex, imageEl) {
                    var $imageEl = $(imageEl),
                        $imageElHeight = $imageEl.height();
                    if (maxImageHeight < $imageElHeight) {
                        maxImageHeight = $imageElHeight;
                    }
                });
                if (maxImageHeight > 0) {
                    navigationButtons.css({'top' : maxImageHeight/2});
                }
            }
        },

        /**
         * Hide swiper navigation buttons if navigation disabled
         */
        hideNavigationButtons: function () {
            if (!this.options.navigation) {
                $(this.element).find('div[class*="swiper-button-"]').addClass('hidden');
            }
        },

        /**
         * Hide swiper slider-loader
         */
        hideSliderLoader: function () {
            if ($(this.element).parent().find('.slider-loader').length) {
                $(this.element).parent().css('min-height','auto').find('.slider-loader').addClass('hidden');
            }
        },

        /**
         * void
         */
        afterSliderInit: function () {
            this.hideSliderLoader();
            this.hideNavigationButtons();
            this.navigationButtonsPosition();
        },

        /**
         *
         * @returns {Object}
         */
        getOptions: function (config, element) {
            var options = {
                init: false,
                loop: config.loop
            };
            var classes = '';
            if ($(element).attr('id')) {
                classes = '#' + $(element).attr('id');
            } else {
                classes = $(element).attr('class').split(" ").join(".");
                classes = '.' + classes;
            }
            if (config.direction == "vertical") {
                options['direction'] = "vertical";
            }
            if (config.lazyload) {
                options['preloadImages'] = false;
                options['lazy'] = true;
            }
            if (config.navigation) {
                options['navigation'] = {
                    nextEl: classes + ' .swiper-button-next',
                    prevEl: classes + ' .swiper-button-prev'
                };
            }
            if (config.pagination) {
                options['pagination'] = {
                    el: '.swiper-pagination',
                    clickable: true
                };
            }
            if (config.autoplay) {
                options['autoplay'] = {
                    delay: 5000
                };
            }
            //if (config.sliderItems) {
                options['slidesPerView'] = 5;
            //}
            //if (config.sliderItemsTablet) {
                options['breakpoints'] = {

                    3000: {
                        spaceBetween: 30
                    },
                    1024: {
                        slidesPerView: 3,
                    },
                    801: {
                        slidesPerView: 2,
                    },
                    640: {
                        slidesPerView: 1,
                    },
                    300: {
                        slidesPerView: 1,
                    }
                };
            //}
            return options;
        }
    });

    return $.meigee.themeSlider;

});
