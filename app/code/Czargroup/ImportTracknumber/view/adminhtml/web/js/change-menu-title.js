require([
    'jquery'
], function($) {
    $(document).ready(function() {
        var menuItem = $('#menu-czargroup-importtracknumber-importtracknumber');
        
        if(menuItem.length) {
            var submenuTitle = menuItem.find('.submenu-title');
            submenuTitle.text('Czargroup Extension');  
        }
    });
});
