define(['jquery'], function( $ ) {
    return function( config, element ) {
        'use strict';

        /* Header Mobile Search */
        $('.block-search .block-title').on('click', function() {
            if (!$(this).hasClass('open')) {
                $(this).addClass('open');
            }
        });
        $('.minisearch .field.search .label').on('click', function() {
            $(this).parents('.block-search').find('.block-title').removeClass('open');
        });
        if ( window.matchMedia("(max-width: 801px)").matches ) {
            var header_height = $('.page-header').outerHeight();
            $('.page-header .block-search .block-content .minisearch').css('min-height', header_height + 'px');
        }

        /* Top links Auth Form */
        if ( $(".page-header .auth-form-wrapper").length ) {
            if ( $(".auth-form-wrapper .form-create-account").length && $(".auth-form-wrapper .block-customer-login").length ) {
                $('.auth-form-wrapper .form-create-account').addClass('hidden');
            }
            $(".auth-form-wrapper").find('.title .form-switcher').on('click', function() {
                if ( $(this).hasClass('active') ) {
                    $(this).removeClass('active').addClass('hidden');
                    if ( $(this).hasClass('login') ) {
                        $(this).parent().find('.form-switcher.register').removeClass('hidden').addClass('active');
                        $(this).parent().find('strong.login').removeClass('hidden');
                        $(this).parent().find('strong.register').addClass('hidden');
                        $('.auth-form-wrapper .form-create-account').addClass('hidden');
                        $('.auth-form-wrapper .block-customer-login').removeClass('hidden');
                    } else if ($(this).hasClass('register')) {
                        $(this).parent().find('.form-switcher.login').removeClass('hidden').addClass('active');
                        $(this).parent().find('strong.login').addClass('hidden');
                        $(this).parent().find('strong.register').removeClass('hidden');
                        $('.auth-form-wrapper .form-create-account').removeClass('hidden');
                        $('.auth-form-wrapper .block-customer-login').addClass('hidden');
                    }
                }
            });
        }

        /* Sticky Header */
        if ( $('body').hasClass('sticky-header') && !$('body').hasClass('cms-no-route') ) {
            var header_height = $('.page-header').outerHeight(),
                scroll_height = header_height,
                screen_check = window.matchMedia("(min-width: 801px)").matches;
            if ( $('body').hasClass('sticky-tablet') ) {
                screen_check = window.matchMedia("(min-width: 768px)").matches;
            }
            if (screen_check) {
                $(window).on("scroll", function () {
                    if ($('.hero-img-wrapper').length) {
                        scroll_height = header_height + $('.hero-img-wrapper').outerHeight();
                    }
                    if ($(this).scrollTop() > scroll_height) {
                        if (!$('.page-header').hasClass('sticky-header')) {
                            if ( !$('.page-header').hasClass('transparent-header') ) {
                                $('.page-wrapper').css('padding-top', header_height+'px');
                            }
                            if ($('#custom-sidebar').length && $('#custom-sidebar').hasClass('active')) {
                                return;
                            } else {
                                $('.page-header').addClass('sticky-header');
                            }
                        }
                    } else {
                        if ( !$('.page-header').hasClass('transparent-header') ) {
                            $('.page-wrapper').css('padding-top', '0');
                        }
                        $('.page-header').removeClass('sticky-header');
                    }
                });
            }
        }

        /* Header Dropdowns */
        $('.header-wrapper .options-wrapper:not(.side-menu)').each(function(){
            var this_block = $(this);
            this_block.off().on('click', function() {
                if (!this_block.hasClass('open')) {
                    this_block.addClass('open').find('.options-dropdown').css('visibility', 'visible');
                }
            });
            $(document).on('click.dropdowns', function(event) {
                if (!$(event.target).closest(this_block).length) {
                    this_block.removeClass('open').find('.options-dropdown').delay(300).queue(function() {
                        $(this).css('visibility', 'hidden').dequeue();
                    });
                }
            });
        });

        /* Add transition-disabled class on resize */
        var menu = $('.sections.nav-sections');
        var search = $('.block-search .block-content');
        $(window).bind("resize", function () {
            if (($(this).width() > 800)) {
                menu.addClass('transition-disabled');
                search.addClass('transition-disabled');
                $('.page-header .block-search .block-content .minisearch').css('min-height', 'auto');
            } else {
                menu.removeClass('transition-disabled');
                search.removeClass('transition-disabled');
            }
        }).trigger('resize');

        /* Fix position for mobile navigation on small devices */
        if ( !$('body').hasClass('cms-no-route') ) {
            var header_height = $('.page-header').outerHeight();

            /* Tablet */
            if (window.matchMedia("(min-width: 768px)").matches && window.matchMedia("(max-width: 800px)").matches) {
                document.documentElement.style.setProperty('--mobile-top-offset', header_height+'px');
                $(window).on("scroll", function () {
                    var scroll_top = $(window).scrollTop();
                    if (scroll_top > header_height) {
                        if ($('.page-header').hasClass('sticky-header') && $('body').hasClass('sticky-tablet')) {
                            document.documentElement.style.setProperty('--mobile-top-offset', header_height+'px');
                        } else {
                            document.documentElement.style.setProperty('--mobile-top-offset', 0);
                        }
                    } else {
                        document.documentElement.style.setProperty('--mobile-top-offset', header_height+'px');
                    }
                });
            }

            /* Small mobile devices */
            if (window.matchMedia("(max-width: 767px)").matches) {
                $(window).on("scroll", function () {
                    var scroll_top = $(window).scrollTop();
                    if (scroll_top > header_height) {
                        document.documentElement.style.setProperty('--mobile-top-offset', 0);
                    } else {
                        document.documentElement.style.setProperty('--mobile-top-offset', header_height+'px');
                    }
                });
            }
        }

    }
})
