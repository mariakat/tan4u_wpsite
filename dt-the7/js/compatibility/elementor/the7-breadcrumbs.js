jQuery(function ($) {
    $.breadcrumbs = function (el) {
        var $widget = $(el);
        var $ulMenu = $widget.find('.breadcrumbs');
        var methods;
        // Store a reference to the object
        $.data(el, "breadcrumbs", $widget);
        // Private methods
        methods = {
            init: function () {
                if(!$widget.hasClass('split-breadcrumbs-y')){
                    $ulMenu.find('li').first().addClass('first');
                    $ulMenu.rcrumbs({
                        animation: {
                            speed: 400
                        },
                        callback: {

                            postCrumbsListDisplay: function (plugin) {
                                const crumbSpeed = plugin.options.animation.speed + 15;
                                setTimeout(function(){
                                    if(!$ulMenu.find('li:first-child').hasClass('show')){
                                        $ulMenu.addClass('hidden-crumbs');
                                    }else{
                                        $ulMenu.removeClass('hidden-crumbs')
                                    }
                                }, crumbSpeed)
                            }
                        }
                    });
                }
            },
          
        };
        methods.init();
    };
    $.fn.breadcrumbs = function () {
        return this.each(function () {
            if ($(this).data('breadcrumbs') !== undefined) {
                $(this).removeData("breadcrumbs")
            }
            new $.breadcrumbs(this);
        });
    };

});
(function ($) {
    // Make sure you run this code under Elementor.
    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction("frontend/element_ready/the7-breadcrumb.default", function ($widget, $) {
            $(document).ready(function () {
                $widget.breadcrumbs();
            })
        });
    });
})(jQuery);
