require(['jquery', 'jquery/ui'], function($){ 
    !function(t){"use strict";t.fn.placeholderTypewriter=function(e){var n=t.extend({delay:50,pause:1e3,text:[]},e);function r(t,e){t.attr("placeholder",""),function t(e,r,u,a){var i=n.text[r],o=e.attr("placeholder");if(e.attr("placeholder",o+i[u]),u<i.length-1)return setTimeout(function(){t(e,r,u+1,a)},n.delay),!0;a()}(t,e,0,function(){setTimeout(function(){!function t(e,r){var u=e.attr("placeholder"),a=u.length;if(e.attr("placeholder",u.substr(0,a-1)),a>1)return setTimeout(function(){t(e,r)},n.delay),!0;r()}(t,function(){r(t,(e+1)%n.text.length)})},n.pause)})}return this.each(function(){r(t(this),0)})}}(jQuery);

    jQuery(document).ready(function() {
        var placeholderText = [
            "Can we help you Find what you want ????",
            "Just type the product name ",
            "Sure we have it ..."
        ];
        if(jQuery(window).width() > 400) {
            jQuery('[id^=search_]').placeholderTypewriter({text: placeholderText,pause:5000});
        }
        else {
            jQuery('[id^=search_]').placeholderTypewriter({text: placeholderText,pause:5000});
        }
        var mobilewrapper = $('<div class="mobile-wrapper"></div>'); 
        $('.minicart-wrapper, .block.block-search, .custom-header').wrapAll(mobilewrapper); 
    });
    jQuery(window).scroll(function () {
        if( jQuery(window).scrollTop() > jQuery('.sections.nav-sections').offset().top && !(jQuery('.sections.nav-sections').hasClass('sticky'))){
        jQuery('.sections.nav-sections').addClass('sticky');
        jQuery('.page-header .minicart-wrapper .block-minicart').css('top','50px');
        
    } else if (jQuery(window).scrollTop() == 0){
        jQuery('.sections.nav-sections').removeClass('sticky');
    }
});
    // jQuery(window).scroll(function () {
	// 		if( jQuery(window).scrollTop() > jQuery('.nav-sections-item-content:first-child').offset().top && !(jQuery('.nav-sections-item-content:first-child').hasClass('sticky'))){
	// 		jQuery('.nav-sections-item-content:first-child').addClass('sticky');
	// 		jQuery('.page-header .minicart-wrapper .block-minicart').css('top','50px');
			
	// 	} else if (jQuery(window).scrollTop() == 0){
	// 		jQuery('.nav-sections-item-content:first-child').removeClass('sticky');
	// 	}
	// });
});