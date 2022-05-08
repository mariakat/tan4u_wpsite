jQuery(function ($) {
    $.productFilterPrice = function (el) {
        var $widget = $(el),
            methods = {};
        $widget.vars = {
            toogleSpeed: 250,
            animationSpeed: 150,
            fadeIn: {opacity: 1},
            fadeOut: {opacity: 0}
        };
        // Store a reference to the object
        $.data(el, "productFilterPrice", $widget);
        // Private methods
        methods = {
            init: function () {
                $( document.body ).trigger( 'init_price_filter');
                $widget.find('.the7-product-price-filter.collapsible').on("click", ".filter-header", function (e) {
                    var $this = $(this),
                        $filter = $this.parents('.the7-product-price-filter'),

                        $filterCont = $filter.find('.filter-container');
                    if ($filter.hasClass('closed')) {
                        $filterCont.css($widget.vars.fadeOut).slideDown($widget.vars.toogleSpeed).animate(
                            $widget.vars.fadeIn,
                            {
                                duration: $widget.vars.animationSpeed,
                                queue: false,
                            }
                        );
                    } else {
                        $filterCont.css($widget.vars.fadeOut).slideUp($widget.vars.toogleSpeed);
                    }
                    $filter.toggleClass('closed');
                });

                if (typeof dtGlobals != 'undefined') {
                    dtGlobals.addOnloadEvent(function () {
                        $widget.find('.the7-product-price-filter').addClass("animate");
                    });
                }
            },

        };
        //global functions

        methods.init();
    };

    $.fn.productFilterPrice = function () {
        return this.each(function () {
            if ($(this).data('productFilterPrice') !== undefined) {
                $(this).removeData("productFilterPrice")
            }
            new $.productFilterPrice(this);
        });
    };
});
(function ($) {
    // Make sure you run this code under Elementor.
    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction("frontend/element_ready/the7-woocommerce-filter-price.default", function ($widget, $) {
            $(document).ready(function () {
                $widget.productFilterPrice();
            })
        });
    });
})(jQuery);
