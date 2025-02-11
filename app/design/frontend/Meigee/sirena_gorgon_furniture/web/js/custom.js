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
});