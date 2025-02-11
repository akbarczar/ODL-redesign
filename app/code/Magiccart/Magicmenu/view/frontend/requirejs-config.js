var config = {
    map: {
        '*': {
            'magicmenu': 'Magiccart_Magicmenu/js/magicmenu',
            'easing': 'Magiccart_Magicmenu/js/jquery.easing' // Ensure easing is mapped
        }
    },

    paths: {
        'easing': 'Magiccart_Magicmenu/js/jquery.easing' // Define easing path
    },

    shim: {
        'easing': {
            deps: ['jquery']
        },
        'magicmenu': {
            deps: ['jquery', 'easing']
        }
    }
};
