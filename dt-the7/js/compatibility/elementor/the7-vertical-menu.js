jQuery(function ($) {
    $.vericalMenu = function (el) {
        var $widget = $(el);
        var $ulMenu = $widget.find('.dt-nav-menu');
        var methods;

        // Store a reference to the object
        $.data(el, "vericalMenu", $widget);
        // Private methods
        methods = {
            init: function () {
                let origY;
                let origX;
                var menuTimeoutHide;
                var $elementsThatTriggerDropdown = $widget.find("li.has-children > a ");
                var parentsAreNotClickable = $widget.find(".not-clickable-item").length > 0;
                if (parentsAreNotClickable) {
                    const $nonClickableLinks = $widget.find("li.has-children > a.not-clickable-item");
                    $elementsThatTriggerDropdown = $elementsThatTriggerDropdown.add($nonClickableLinks);
                }
                $ulMenu.find(" li.act:last > a").addClass("active-item");
                if($ulMenu.find(".vertical-sub-nav").length <= 0){
                    $ulMenu.parent().addClass('indicator-off');
                }
                if($widget.find('.dt-sub-menu-display-on_click').length > 0 || $widget.find('.dt-sub-menu-display-on_item_click').length > 0){

                    $ulMenu.find('li.has-children').each(function() {
                        var $this = $(this);

                        if (!$this.length) {
                            return;
                        }
                        var itemLink = $this.find("> a");

                        var subMenu = $this.find(" > .vertical-sub-nav");

                        if ($this.hasClass("act")) {
                            $this.addClass("open-sub");
                            $this.find("> a").addClass("active");
                        }

                        if ($this.find(".vertical-sub-nav li").hasClass("act")) {
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
                                let $this = $(this);

                                if (!parentsAreNotClickable && !e.originalEvent.composedPath().includes($this.children(".next-level-button").get(0))) {
                                    return;
                                }

                                e.stopImmediatePropagation();
                                e.preventDefault();

                                clearTimeout(menuTimeoutHide);
                                menuTimeoutHide = setTimeout(function () {
                                    if ($this.hasClass("active")) {
                                        methods.hideSubMenu($this);
                                    } else {
                                        methods.showSubMenu($this);
                                    }
                                }, 100);
                            }
                        });
                    }else{
                        $elementsThatTriggerDropdown.on("click", function(e) {
                            var $this = $(this);


                            var $thisTarget = $this.attr("target") ? $this.attr("target") : "_self";
                            e = window.event || e;

                            e.stopPropagation();
                            e.preventDefault();

                            clearTimeout(menuTimeoutHide);
                            menuTimeoutHide = setTimeout(function () {
                                if (!$(e.target).parents().hasClass("next-level-button") && !$(e.target).hasClass("next-level-button") && !parentsAreNotClickable) {
                                        window.open($this.attr("href"), $thisTarget);
                                       // return true;
                                    } else {
                                        if ($this.hasClass("active")) {
                                            methods.hideSubMenu($this);
                                        } else {
                                            methods.showSubMenu($this);
                                        }
                                        return false;
                                    }
                            }, 100);
                        })
                    }
                    $widget.find(".dt-sub-menu-display-on_click, .dt-sub-menu-display-on_item_click").css('visibility', 'visible');
                }

            },
            showSubMenu: function ($el) {
                var subMenu = $el.siblings(" .vertical-sub-nav");
                $el.parent().siblings().find(" .vertical-sub-nav").css("opacity", "0").stop(true, true).slideUp(250);
                subMenu.css("opacity", "0").stop(true, true).slideDown({
                    start: function () {
                    }
                }, 250).animate(
                    {opacity: 1},
                    {queue: false, duration: 150}
                );
                $el.siblings().removeClass("active");
                $el.addClass("active");
                $el.parent().siblings().removeClass("open-sub");
                $el.parent().siblings().find("a").removeClass("active");
                $el.parent().addClass("open-sub");

                $(" .dt-nav-menu").layzrInitialisation();
            },
            hideSubMenu: function ($el) {
                var subMenu = $el.siblings(" .vertical-sub-nav");
                subMenu.css("opacity", "0").stop(true, true).slideUp(250, function () {
                    subMenu.find("li").removeClass("open-sub");
                    subMenu.find("a").removeClass("active");
                });
                $el.removeClass("active");
                $el.parent().removeClass("open-sub");
            },
        };

        $widget.delete = function () {
            $widget.removeData("vericalMenu");
        };
        methods.init();
    };
    $.fn.vericalMenu = function () {
        return this.each(function () {
            var widgetData = $(this).data('vericalMenu');
            if (widgetData !== undefined) {
                widgetData.delete();
            }
            new $.vericalMenu(this);
        });
    };

});
(function ($) {
    // Make sure you run this code under Elementor.
    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction("frontend/element_ready/the7_nav-menu.default", function ($widget, $) {
            $(document).ready(function () {
                $widget.vericalMenu();
            })
        });
    });
})(jQuery);
