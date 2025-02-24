define(['jquery'], function ($) {
    'use strict';

    return function (addToCart) {
        $('.quantity-decrease.qty-button').click(function () {
            const currentQty = parseInt($('#qty').val()) || 0;
            if (currentQty > 0) {
                $('#qty').val(currentQty - 1);
            }
        });

        $('.quantity-increase.qty-button').click(function () {
            const currentQty = parseInt($('#qty').val()) || 0;
            $('#qty').val(currentQty + 1);
        });

        return addToCart;
    }
});
