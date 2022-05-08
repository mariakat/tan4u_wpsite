jQuery(function ($) {
    $.productCat = function (el) {
        var $widget = $(el);
        var $ulMenu = $widget.find('.dt-product-categories');
        var methods;
        var menuTimeoutHide;

        // Store a reference to the object
        $.data(el, "productCat", $widget);
        $widget.vars = {
            toogleSpeed: 250,
            animationSpeed: 150,
            fadeIn: {opacity: 1},
            fadeOut: {opacity: 0}
        };
        // Private methods
        methods = {
            init: function () {
                let origY;
                let origX;
                var $elementsThatTriggerDropdown = $widget.find("li.has-children > a .next-level-button ");
                $ulMenu.find(" li.current-cat-parent:last > a").addClass("active-item");
                if($ulMenu.find(".children").length <= 0){
                    $ulMenu.parent().addClass('indicator-off');
                }

                $widget.find('.the7-product-categories.collapsible').on("click", ".filter-header", function (e) {
                    if ($widget.hasClass('closed')) {
                        $ulMenu.css($widget.vars.fadeOut).slideDown($widget.vars.toogleSpeed).animate(
                            $widget.vars.fadeIn,
                            {
                                duration: $widget.vars.animationSpeed,
                                queue: false,
                            }
                        );
                    } else {
                        $ulMenu.css($widget.vars.fadeOut).slideUp($widget.vars.toogleSpeed);
                    }
                    $widget.toggleClass('closed');
                });
                if($widget.find('.dt-sub-menu-display-on_click').length > 0){

                    $ulMenu.find('li.has-children').each(function() {
                        var $this = $(this);

                        if (!$this.length) {
                            return;
                        }
                        var itemLink = $this.find("> a");

                        var subMenu = $this.find(" > .children");

                        if ($this.hasClass("current-cat-parent")) {
                            $this.addClass("open-sub");
                            $this.find("> a").addClass("active");
                        }

                        if ($this.find(".children li").hasClass("act")) {
                            $this.addClass("open-sub");
                            itemLink.addClass("active");
                            subMenu.css("opacity", "0").stop(true).slideDown({
                                start: function () {
                                }
                            }, 250).animate(
                                {opacity: 1},
                                {queue: false, duration: 150}
                            );
                        }
                    });

                    if($('.touchevents').length > 0){
                        $elementsThatTriggerDropdown.on("touchstart", function(e) {
                            origY = e.originalEvent.touches[0].pageY;
                            origX = e.originalEvent.touches[0].pageX;
                        });
                        $elementsThatTriggerDropdown.on("touchend", function(e) {
                            let touchEX = e.originalEvent.changedTouches[0].pageX;
                            let touchEY = e.originalEvent.changedTouches[0].pageY;

                            if( origY === touchEY || origX === touchEX ){
                                let $this = $(this).parent('a');

                                e.stopImmediatePropagation();
                                e.preventDefault();

                                methods.displaySubCategory($this);
                            }
                        });
                    }else{
                        $elementsThatTriggerDropdown.on("click", function(e) {
                            var $this = $(this).parent('a');
                            e = window.event || e;

                            e.stopPropagation();
                            e.preventDefault();
                            methods.displaySubCategory($this);
                            
                        })
                    }
                    $widget.find(".dt-sub-menu-display-on_click").css('visibility', 'visible');
                }

            },
            showSubMenu: function ($el) {
                var subMenu = $el.siblings(" .children"),
                    $elParent = $el.parent();
                $elParent.siblings().find(" .children").css("opacity", "0").stop(true, true).slideUp(250);
                subMenu.css("opacity", "0").stop(true, true).slideDown({
                    start: function () {
                    }
                }, 250).animate(
                    {opacity: 1},
                    {queue: false, duration: 150}
                );
                $el.siblings().removeClass("active");
                $el.addClass("active");
                $elParent.siblings().removeClass("open-sub");
                $elParent.siblings().find("a").removeClass("active");
                $elParent.addClass("open-sub");
            },
            hideSubMenu: function ($el) {
                var subMenu = $el.siblings(" .children"),
                    $elParent = $el.parent();
                subMenu.css("opacity", "0").stop(true, true).slideUp(250, function () {});
                $el.removeClass("active");
                $elParent.removeClass("open-sub");
            },
            displaySubCategory: function ($a) {
                clearTimeout(menuTimeoutHide);
                menuTimeoutHide = setTimeout(function () {
                    if ($a.hasClass("active")) {
                        methods.hideSubMenu($a);
                    } else {
                        methods.showSubMenu($a);
                    }
                    return false;
                }, 100);
            },
        };

        $widget.delete = function () {
            $widget.removeData("productCat");
        };
        methods.init();
    };
    $.fn.productCat = function () {
        return this.each(function () {
            var widgetData = $(this).data('productCat');
            if (widgetData !== undefined) {
                widgetData.delete();
            }
            new $.productCat(this);
        });
    };

});
(function ($) {
    // Make sure you run this code under Elementor.
    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction("frontend/element_ready/the7_product-categories.default", function ($widget, $) {
            $(document).ready(function () {
                $widget.productCat();
            })
        });
    });
})(jQuery);
