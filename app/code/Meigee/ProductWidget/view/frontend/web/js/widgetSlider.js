define([
    'jquery',
    'underscore',
    'meigeeSwiper'
], function ($, _, meigeeSwiper) {
    'use strict';

    $.widget('meigee.widgetSilder',{

        options: {
            lazyClass: 'swiper-lazy',
            loadingClass: 'swiper-lazy-loading',
            loadedClass: 'swiper-lazy-loaded',
            preloaderClass: 'swiper-lazy-preloader'
        },

        /**
         * @private
         */
        _init: function () {
            var widget = this,
                options = this.getOptions(),
                widgetSwiperSlider = new meigeeSwiper(this.element, options);

            widgetSwiperSlider.on('init', function() {
                // after init Swiper
                widget.afterSliderInit();
            });

            // init Swiper
            widgetSwiperSlider.init();
        },

        /**
         * Set swiper navigation buttons position
         */
        navigationButtonsPosition: function () {
            if ($(this.element).find('.swiper-navigation').length){
                var navigationButtons = $(this.element).find('.swiper-navigation > div'),
                    $images = $(this.element).find('img.product-image-photo'),
                    maxImageHeight = 0,
                    parent_top_padding = parseInt($(this.element).css("padding-top").replace("px", ""));

                $images.each(function (imageIndex, imageEl) {
                    var $imageEl = $(imageEl),
                        $imageElHeight = $imageEl.height();
                    if (maxImageHeight < $imageElHeight) {
                        maxImageHeight = $imageElHeight;
                    }
                })
                if (maxImageHeight > 0) {
                    navigationButtons.css({'top' : parent_top_padding + maxImageHeight/2});
                }
            }
        },

        /**
         * void
         */
        afterSliderInit: function () {
            this.navigationButtonsPosition();
        },

        /**
         *
         * @returns {Object}
         */
        getOptions: function () {
            return this.options.sliderOptions;
        }
    });

    return $.meigee.widgetSilder;

});
