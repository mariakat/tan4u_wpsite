jQuery(function ($) {
    "use strict";
    $.horizontalMenu = function (el) {
        let $widget = $(el), elementorSettings, settings, methods;
        const $mobMenu = $widget.find('.dt-nav-menu-horizontal--main').first();
        const $widgetContainer = $widget.find(".elementor-widget-container").first();
        const $menuWrap = $widget.find(".horizontal-menu-wrap").first();
        const $ulMenu = $widget.find('.dt-nav-menu-horizontal').first();
        const $menuToggle = $widget.find('.horizontal-menu-toggle').first();
        const $page = $("#page");
        var menuMobileTimeoutHide;
        // Store a reference to the object
        $.data(el, "horizontalMenu", $widget);
        const visibilityTimeout = "the7HorizontalSubMenuVisibilityTimeout";
        const classes = {
            subnav: "the7-e-sub-nav",
            subnavHorizontal: "horizontal-sub-nav",
            menuDropdown: "horizontal-menu-dropdown",
            submenuIndicator: "submenu-indicator",
            isClicked: "is-clicked",
            noTransition: "notranstion",
        };
        const state = {
            type: "horizontal",
            isDropdownOpen: false,
            isSubmenuOpen: false,
            isDropdown: false,
        };

        // Private methods
        methods = {
            init: function () {
                elementorSettings = new The7ElementorSettings($widget);
                methods.handleItemsClick();
                methods.handleDropdownEvents();
                $widget.refresh();
                methods.handleResize = elementorFrontend.debounce(methods.handleResize, 300);
                methods.bindEvents();
                methods.openActiveItems();
            },
            openActiveItems: function () {
                if (!methods.isDropdown()) {
                    return;
                }
                $ulMenu.find(".menu-item.act.has-children").each(function () {
                    let $elItem = $(this).find(" > a ");
                    methods.openMobSubMenu($elItem);
                });
            },
            handleToggleButton: function () {
                if (state.isDropdownOpen) {
                    methods.closeDropdownMenu();
                } else {
                    methods.openDropdownMenu();
                }
            },
            handleDropdownEvents: function () {
                var $elementsThatTriggerDropdown = $widget.find("li.has-children > a");
                let $nonClickableItems = $widget.find(".not-clickable-item");
                if ($(".touchevents").length) {
                    let origY;
                    let origX;
                    $elementsThatTriggerDropdown.on("touchstart", function (e) {
                        let touch = e.originalEvent.touches[0];
                        origY = touch.pageY;
                        origX = touch.pageX;
                    });
                    $elementsThatTriggerDropdown.on("touchend", function (e) {
                        let touch = e.originalEvent.changedTouches[0];
                        if (origY === touch.pageY || origX === touch.pageX) {
                            var $this = $(this);
                            e = window.event || e;
                            methods.handleMobileMenu($this, e);
                        }
                    });

                    $nonClickableItems.on("touchstart", function (e) {
                        let touch = e.originalEvent.touches[0];
                        origY = touch.pageY;
                        origX = touch.pageX;
                    });
                    $nonClickableItems.on("touchend", function (e) {
                        let touch = e.originalEvent.changedTouches[0];
                        if (origY === touch.pageY || origX === touch.pageX) {
                            e.preventDefault();
                            e.stopPropagation();
                        }
                    });
                } else {
                    $elementsThatTriggerDropdown.on("click", function (e) {
                        var $this = $(this);
                        e = window.event || e;
                        methods.handleMobileMenu($this, e);
                    });

                    $nonClickableItems.on("click", function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                    });
                }
            },
            handleItemsClick: function () {
                $ulMenu.each(function () {
                    let $mainNav = $(this);
                    $('body').on("click", function (e) {
                        if (!$(e.target).hasClass(classes.subnav) && !methods.isDropdown()) {
                            $("li.dt-hovered > ." + classes.subnav, $mainNav).animate({
                                "opacity": 0
                            }, 100, function () {
                                $(this).css("visibility", "hidden");
                            });

                            $("li.has-children", $mainNav).removeClass("dt-hovered parent-clicked");
                            $("li.has-children > a", $mainNav).removeClass(classes.isClicked);
                        }
                    });

                    $(".act", $mainNav).parents("li").addClass("act");
                    $(" li.has-children ", $mainNav).each(function () {
                        var $this = $(this);
                        var $thisHover = $this.find("> a");

                        $this.find("> a").on("click", function (e) {
                            if (dtGlobals.isMobile || dtGlobals.isWindowsPhone) {
                                if (!$(this).hasClass("dt-clicked")) {
                                    e.preventDefault();
                                    $mainNav.find(".dt-clicked").removeClass("dt-clicked");
                                    $(this).addClass("dt-clicked");
                                } else {
                                    e.stopPropagation();
                                }
                            }
                        });
                        if (($widget.hasClass('show-sub-menu-on-hover') || $widget.hasClass('parent-item-clickable-yes'))) {
                            $thisHover.on("mouseenter tap", function (e) {
                                if (e.type === "tap") e.stopPropagation();
                                if (!methods.isDropdown()) {
                                    methods.showSubMenu($(this));
                                }
                            });

                            $this.on("mouseleave", function (e) {
                                if (!methods.isDropdown()) {
                                    methods.hideSubMenu($(this));
                                }
                            });
                        } else {
                            $thisHover.on("click", function (e) {
                                var $this = $(this),
                                    $thisLink = $this.parent("li");

                                if ($this.hasClass(classes.isClicked)) {
                                    methods.hideSubMenu($thisLink);
                                    $this.removeClass(classes.isClicked);
                                    $this.parent().removeClass("parent-clicked");
                                } else {
                                    methods.showSubMenu($this);
                                    $("li.has-children > a").removeClass(classes.isClicked);
                                    $("li.has-children").removeClass("parent-clicked");
                                    $this.parent().addClass("parent-clicked");
                                    if (!$(e.target).parents().hasClass(classes.subnav)) {
                                        $(" li.has-children").removeClass("dt-hovered");
                                        $this.parent().addClass("dt-hovered");
                                    }
                                    if (!methods.isDropdown()) {
                                        $(".dt-nav-menu-horizontal > li:not(.dt-hovered) > ." + classes.subnav).stop().animate({
                                            "opacity": 0
                                        }, 150, function () {
                                            $(this).css("visibility", "hidden");
                                        });
                                    }
                                    $this.parent().siblings().find("." + classes.subnav).stop().animate({
                                        "opacity": 0
                                    }, 150, function () {
                                        $(this).css("visibility", "hidden");
                                    });
                                    $this.addClass(classes.isClicked);
                                    return false;
                                }
                            })
                        }
                    });
                });
            },
            handleMegaMenuSize: function () {
                let megaMenuItems = $ulMenu.find('.the7-e-mega-menu');
                megaMenuItems.each(function () {
                    var $this = $(this);
                    let $megaMenuMainWrapper = $this.find('.the7-e-mega-menu-content');
                    const classAuto = 'the7-e-mega-menu-width-auto',
                        classFull = 'the7-e-mega-menu-width-full';
                    if ($megaMenuMainWrapper.hasClass(classFull)) {
                        $this.addClass(classFull);
                    } else {
                        let $eBoxedContainer = $widget.parents(".elementor-section.elementor-section-boxed > .elementor-container");
                        if ($eBoxedContainer.length) {
                            $this.addClass(classAuto);
                            $this.removeClass(classFull);
                        } else {
                            $this.addClass(classFull);
                            $this.removeClass(classAuto);
                        }
                    }
                    if ($megaMenuMainWrapper.length){
                        $megaMenuMainWrapper.css('--mega-vh', '');
                        let menuHeight = $megaMenuMainWrapper.height();
                        let availHeight = (window.innerHeight - ($megaMenuMainWrapper.offset().top - dtGlobals.winScrollTop));
                        if (menuHeight > availHeight ) {
                            $megaMenuMainWrapper.css('--mega-vh', availHeight + 'px');
                        }
                    }
                });
            },
            handleMobileMenu: function ($this, e) {
                if (!methods.isDropdown()) {
                    return false;
                }
                let $el = $(e.target);
                let isParentMobileEmpty = $el.parents('.menu-item').hasClass('the7-e-mega-menu-mobile-empty');
                if (!(!$el.hasClass(classes.submenuIndicator) && ((settings["parent_is_clickable"] === "yes") || isParentMobileEmpty))) {
                    e.stopPropagation();
                    e.preventDefault();
                    clearTimeout(menuMobileTimeoutHide);
                    //timeout to prevent fast open/close
                    menuMobileTimeoutHide = setTimeout(function () {
                        if ($this.hasClass("item-active")) {
                            methods.closeMobSubMenu($this);
                        } else {
                            methods.openMobSubMenu($this);
                        }
                    }, 100);
                }
            },
            showSubMenu: function ($el) {
                let $thisPar, parentLi = $el.parent("li");
                if (parentLi.length > 0) {
                    $thisPar = parentLi;
                } else {
                    $thisPar = $el;
                }
                let timeoutID = $thisPar.data(visibilityTimeout) || 0;
                clearTimeout(timeoutID);
                timeoutID = setTimeout(function () {
                    if (methods.isDropdown()) {
                        return;
                    }
                    let $subMenu;
                    if (parentLi.length > 0) {
                        $subMenu = $el.siblings("ul");
                    } else {
                        $subMenu = $el.find("> a").siblings("ul");
                    }
                    $thisPar.addClass("dt-hovered");
                    /*Right overflow menu*/
                    if ($subMenu.length > 0) {
                        if ($page.width() - ($subMenu.offset().left - $page.offset().left) - $subMenu.innerWidth() < 0) {
                            $subMenu.addClass("right-overflow");
                        } else if (($subMenu.offset().left < $page.offset().left)) {
                            $subMenu.addClass("left-overflow");
                        }
                        /*Bottom overflow menu*/

                        if (elementorFrontend.elements.$window.height() - ($subMenu.offset().top - dtGlobals.winScrollTop) - $subMenu.innerHeight() < 0) {
                            $subMenu.addClass("bottom-overflow");
                        }
                    }
                    methods.handleMegaMenuSize();
                    state.isSubmenuOpen = true;
                    $subMenu.stop().css({"visibility": "inherit", 'display': ''})
                        .animate({"opacity": 1}, 200, function () {
                        });
                }, 100);

                $thisPar.data(visibilityTimeout, timeoutID);
            },
            hideSubMenu: function ($el) {
                let timeoutID = $el.data(visibilityTimeout) || 0;
                clearTimeout(timeoutID);

                timeoutID = setTimeout(function () {
                    if (methods.isDropdown() || !$el.hasClass("dt-hovered")) {
                        return;
                    }
                    $el.removeClass("dt-hovered");
                    let $thisLink = $el.find("> a"),
                        $subMenu = $thisLink.siblings("ul");

                    $thisLink.removeClass("dt-clicked");

                    $subMenu.stop().animate({"opacity": 0}, 150, function () {
                        $subMenu.css("visibility", "hidden");
                        if (!$el.hasClass("dt-hovered")) {
                            $subMenu.removeClass(["right-overflow", "left-overflow", "bottom-overflow"]);
                        }
                    });
                    state.isSubmenuOpen = false;
                }, 150);
                $el.data(visibilityTimeout, timeoutID);
            },
            bindEvents: function () {
                elementorFrontend.elements.$window.on('resize', methods.handleResize);
                // Close dropdown menu upon scrolling to the element.
                elementorFrontend.elements.$window.on("the7.anchorScrolling", methods.closeDropdownMenu);
                // Close dropdown menu on any popup close.
                elementorFrontend.elements.$document.on('elementor/popup/hide', methods.closeDropdownMenu);
                // Toggle dropdown menu open/close on button click.
                $menuToggle.on("click", methods.handleToggleButton);
                $widget.on('effect-active', methods.onEffectActive);
            },
            unBindEvents: function () {
                elementorFrontend.elements.$window.off('resize', methods.handleResize);
                elementorFrontend.elements.$window.off("the7.anchorScrolling", methods.closeDropdownMenu);
                elementorFrontend.elements.$document.off('elementor/popup/hide', methods.closeDropdownMenu);
                $menuToggle.off("click", methods.handleToggleButton);
                $widget.off('effect-active', methods.onEffectActive);
            },
            isDropDownActive: function () {
                let currentDeviceMode = elementorFrontend.getCurrentDeviceMode();
                let dropdownDeviceMode = settings['dropdown'];

                if (!dropdownDeviceMode) {
                    return false;
                }

                if (dropdownDeviceMode === 'desktop') {
                    return true;
                }

                if (dropdownDeviceMode === 'desktop') {
                    dropdownDeviceMode = 'laptop';
                }

                if (currentDeviceMode === 'desktop') {
                    currentDeviceMode = 'laptop';
                }

                let breakpoints = elementorFrontend.config.responsive.breakpoints;

                let dropDownBreakPoint = 0;
                let currentBreakPoint = 0;
                if (breakpoints.hasOwnProperty(dropdownDeviceMode)) {
                    dropDownBreakPoint = breakpoints[dropdownDeviceMode].value;
                }
                if (breakpoints.hasOwnProperty(currentDeviceMode)) {
                    currentBreakPoint = breakpoints[currentDeviceMode].value;
                }
                return dropDownBreakPoint >= currentBreakPoint;
            },
            handleResize: function () {
                if (methods.isDropDownActive()) {
                    if (!methods.isDropdown()) {
                        $ulMenu.removeClass("dt-nav-menu-horizontal").addClass(classes.subnavHorizontal);
                        $menuWrap.addClass(classes.noTransition);
                        $menuWrap.addClass(classes.menuDropdown);
                        $menuWrap.removeClass(classes.noTransition);
                        state.isDropdown = true;
                    }
                } else if (methods.isDropdown()) {
                    $widget.find(".dt-nav-menu-horizontal--main > ul").addClass("dt-nav-menu-horizontal").removeClass(classes.subnavHorizontal);
                    $menuWrap.removeClass(classes.menuDropdown);
                    methods.closeDropdownMenu();
                    state.isDropdown = false;
                }
                /**
                 * Setup dynamic helper css var to determine  section container
                 */
                let $widgetLeft = $widgetContainer.offset().left;
                let $eContainer = $widget.parents(".elementor-section.elementor-section-boxed > .elementor-container");
                let $eContainerLeft = $widgetLeft;
                if ($eContainer.length) {
                    $eContainerLeft = $eContainer.offset().left;
                    $widget.css("--dynamic-submenu-content-width", $eContainer.outerWidth() + "px");
                }

                $widget.css("--dynamic-submenu-content-left-offset", -1 * ($widgetLeft - $eContainerLeft) + "px");
                /**
                 * Setup dynamic css var with the menu wrapper left offset.
                 *
                 * It supports "justify" submenu alighnment.
                 */
                $widget.css("--dynamic-justified-submenu-left-offset", "-" + $widgetLeft + "px");

                /**
                 * Scrollbar width css var. It supports "justify" submenu max-width.
                 */
                $widget.css("--scrollbar-width", (window.innerWidth - document.scrollingElement.clientWidth) + "px");

                methods.handleMegaMenuSize();
            },
            isDropdown: function () {
                return state.isDropdown;
            },
            closeDropdownMenu: function () {
                if (!state.isDropdownOpen) {
                    return;
                }

                $menuToggle.attr("aria-expanded", "false").removeClass("elementor-active");
                $mobMenu.attr("aria-hidden", "true");

                state.isDropdownOpen = false;
                if (dtGlobals.isMobile) {
                    elementorFrontend.elements.$body.css({'overflow-y': '', 'position': '', 'height': ''})
                }
                // Remove "closeOnOuterClickHandler" when menu is closed.
                elementorFrontend.elements.$body.off("click touchstart", methods.closeOnOuterClickHandler);

                // TODO: Do we really need this when body has overflow hidden?
                elementorFrontend.elements.$window.off("scroll", methods.setDropdownHeight);
            },
            openDropdownMenu: function () {
                if (state.isDropdownOpen) {
                    return;
                }

                if (settings["dropdown_type"] === "popup") {
                    return;
                }
                $menuToggle.attr("aria-expanded", "true").addClass("elementor-active");
                $mobMenu.attr("aria-hidden", "false");

                state.isDropdownOpen = true;
                methods.setDropdownHeight();

                if (dtGlobals.isMobile) {
                    elementorFrontend.elements.$body.css({
                        'overflow-y': 'hidden',
                        'position': 'relative',
                        'height': window.innerHeight
                    });
                }

                // Add "closeOnOuterClickHandler" while opening dropdown menu.
                elementorFrontend.elements.$body
                    .off("click touchstart", methods.closeOnOuterClickHandler)
                    .on("click touchstart", methods.closeOnOuterClickHandler);

                // TODO: Do we really need this when body has overflow hidden?
                elementorFrontend.elements.$window
                    .off("scroll", methods.setDropdownHeight)
                    .on("scroll", methods.setDropdownHeight);
            },
            closeMobSubMenu: function ($el) {
                $el.siblings("." + classes.subnav).css("opacity", "0").stop(true, true).slideUp(250);
                $el.removeClass("item-active");
            },
            openMobSubMenu: function ($el) {
                let menuSelector = ".menu-item.depth-0";
                let $curMenuItem = $el.parents(menuSelector);
                let $otherMenuItems = $ulMenu.find(menuSelector).not($curMenuItem);
                $otherMenuItems.each(function () {
                    let $this = $(this);
                    let $elItem = $this.find(" > a ");
                    if ($elItem.hasClass("item-active")) {
                        methods.closeMobSubMenu($elItem);
                    }
                });
                $el.siblings("." + classes.subnav).css("opacity", "0").stop(true, true).slideDown(250)
                    .animate(
                        {opacity: 1},
                        {queue: false, duration: 150}
                    );
                $el.addClass("item-active");
            },
            setDropdownHeight: function () {
                if (state.isDropdownOpen) {
                    let vh = (window.innerHeight - ($mobMenu.offset().top - dtGlobals.winScrollTop));
                    $widget.css('--vh', vh + 'px');
                }
            },
            closeOnOuterClickHandler: function (event) {
                /**
                 * Close dropdown if event path not contains menu wrap object.
                 *
                 * @see https://developer.mozilla.org/en-US/docs/Web/API/Event/composedPath
                 */
                if (!event.originalEvent.composedPath().includes($menuWrap.get(0))) {
                    methods.closeDropdownMenu();
                }
            },
            onEffectActive: function () {
                if (methods.isDropdown()) {
                    return;
                }
                $ulMenu.find("li.dt-hovered").each(function () {
                    methods.hideSubMenu($(this));
                });
            }
        };
        //global functions
        $widget.refresh = function () {
            settings = elementorSettings.getSettings();
            methods.handleResize();
        };
        $widget.delete = function () {
            methods.unBindEvents();
            $widget.removeData("horizontalMenu");
        };
        methods.init();
    };
    $.fn.horizontalMenu = function () {
        return this.each(function () {
            var widgetData = $(this).data('horizontalMenu');
            if (widgetData !== undefined) {
                widgetData.delete();
            }
            new $.horizontalMenu(this);
        });
    };

});
(function ($) {
    // Make sure you run this code under Elementor.
    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction("frontend/element_ready/the7_horizontal-menu.default", function ($widget, $) {
              $(document).ready(function () {
            $widget.horizontalMenu();
               })
        });

        if (elementorFrontend.isEditMode()) {
            elementorEditorAddOnChangeHandler("the7_horizontal-menu", refresh);
            elementorEditorAddOnChangeHandler("section", refreshAllMenus);
        }

        function refresh(controlView, widgetView) {
            let refresh_controls = [
                "parent_is_clickable",
                "dropdown",
            ];
            var controlName = controlView.model.get('name');
            if (-1 !== refresh_controls.indexOf(controlName)) {
                var $widget = window.jQuery(widgetView.$el);
                var widgetData = $widget.data('horizontalMenu');
                if (typeof widgetData !== 'undefined') {
                    widgetData.refresh();
                } else {
                    $widget.horizontalMenu();
                }
            }
        }

        //update megamenu width on section layout change
        function refreshAllMenus(controlView, widgetView) {
            let refresh_controls = [
                "layout",
                "content_width",
            ];
            var controlName = controlView.model.get('name');
            if (-1 !== refresh_controls.indexOf(controlName)) {
                $('.elementor-widget-the7_horizontal-menu').each(function () {
                    var $widget = $(this);
                    var widgetData = $widget.data('horizontalMenu');
                    if (typeof widgetData !== 'undefined') {
                        widgetData.refresh();
                    }
                });
            }
        }
    });
})(jQuery);
