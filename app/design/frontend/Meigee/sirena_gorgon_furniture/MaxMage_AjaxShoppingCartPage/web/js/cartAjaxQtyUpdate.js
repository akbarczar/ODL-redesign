define([
    'jquery',
    'Magento_Checkout/js/action/get-totals',
    'Magento_Customer/js/customer-data'
], function ($, getTotalsAction, customerData) {

    $(document).ready(function(){
        /*  $(document).on('change', 'input[name$="[qty]"]', function(){  */
	  /*  jQuery(document).on('click', '.quantity-increase.qty-button,.quantity-decrease.qty-button', function(){ */
	  jQuery(document).on('click', '.update-qty', function(){
            var form = $('form#form-validate');
            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                showLoader: true,
                success: function (res) {
                    var parsedResponse = $.parseHTML(res);
                    var result = $(parsedResponse).find("#form-validate");
                    var sections = ['cart'];
                    //$("#form-validate").replaceWith(result);
					var selval = $('.cartqty option:selected').text();
					console.log(selval);
					$('.qty-cart').text(selval);
                    // The mini cart reloading
                    customerData.reload(sections, true);

                    // The totals summary block reloading
                    var deferred = $.Deferred();
                    getTotalsAction([], deferred);
                },
                error: function (xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        });
    });
});