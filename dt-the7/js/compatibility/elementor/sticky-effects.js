(function ($) {
    "use strict";
    $.the7StickyRow = function (el) {
        let $widget = $(el),
            elementorSettings,
            settings,
            modelCID,
            // breakpoints,
            effectsActive = false;

        let methods = {};
        $widget.vars = {};
        // Store a reference to the object
        $.data(el, "the7StickyRow", $widget);
        // Private methods
        methods = {
            init: function () {
                elementorSettings = new The7ElementorSettings($widget);
                modelCID = elementorSettings.getModelCID();
                if (elementorFrontend.isEditMode()) {
                    elementor.channels.data.on('element:destroy', methods.onDestroy);
                }
                $widget.refresh();
                methods.bindEvents();
                methods.toggle = elementorFrontend.debounce(methods.toggle, 300);
            },
            bindEvents: function () {
                elementorFrontend.elements.$window.on('resize', methods.toggle);
                elementorFrontend.elements.$window.on("scroll", methods.toggle);
                $widget.on('the7-sticky:effect-active', methods.onEffectActive);
                $widget.on('the7-sticky:effect-not-active', methods.onEffectNotActive);
            },
            unBindEvents: function () {
                elementorFrontend.elements.$window.off('resize', methods.toggle);
                elementorFrontend.elements.$window.off('scroll', methods.toggle);
                $widget.off('the7-sticky:effect-active', methods.onEffectActive);
                $widget.off('the7-sticky:effect-not-active', methods.onEffectNotActive);
            },
            toggle: function () {
                if (typeof settings !== 'undefined') {
                    var currentDeviceMode = elementorFrontend.getCurrentDeviceMode();
                    if (settings['the7_sticky_effects'] === 'yes') {
                        var devices = settings['the7_sticky_effects_devices'],
                            isCurrentModeActive = !devices || -1 !== devices.indexOf(currentDeviceMode);
                        if (isCurrentModeActive) {
                            methods.activateEffects();
                        } else {
                            methods.deactivateEffects();
                        }
                    } else {
                        methods.deactivateEffects();
                    }

                    if (settings['the7_sticky_row'] === 'yes' && !settings['sticky']) {
                        var devices = settings['the7_sticky_row_devices'],
                            isCurrentModeActive = !devices || -1 !== devices.indexOf(currentDeviceMode);
                        if (isCurrentModeActive) {
                            methods.activateSticky();
                        } else {
                            methods.deactivateSticky();
                        }
                    } else {
                        methods.deactivateSticky();
                    }
                }
            },
            onEffectActive: function () {
                let $elements = $widget.find('.the7-e-on-sticky-effect-visibility, .elementor-widget-the7_horizontal-menu');
                $elements.each(function () {
                        $(this).trigger('effect-active');
                    }
                );
            },
            onEffectNotActive: function () {
                let $elements = $widget.find('.the7-e-on-sticky-effect-visibility');
                $elements.each(function () {
                    $(this).trigger('effect-not-active');
                });
            },
            refresh: function () {
            },
            activateEffects: function () {
                if (effectsActive) {
                    return;
                }
                effectsActive = true;
                $widget.reactivateSticky();
            },
            deactivateEffects: function () {
                if (!effectsActive) {
                    return;
                }
                effectsActive = false;
                $widget.reactivateSticky();
            },
            activateSticky: function () {
                if (methods.isStickyInstanceActive()) {
                    return;
                }
                //do not initialize if
                if ($widget.hasClass('elementor-sticky')) {
                    return;
                }
                var stickyTo = "top"; // elementorSettings.sticky;
                var stickyOptions = {
                        to: stickyTo,
                        offset: elementorSettings.getCurrentDeviceSetting('the7_sticky_row_offset'),
                        effectsOffset: elementorSettings.getCurrentDeviceSetting('the7_sticky_effects_offset'),
                        classes: {
                            sticky: "the7-e-sticky",
                            stickyActive: "the7-e-sticky-active",
                            stickyEffects: "the7-e-sticky-effects",
                            spacer: "the7-e-sticky-spacer",
                        }
                    },
                    $wpAdminBar = elementorFrontend.elements.$wpAdminBar;

                if (!effectsActive) {
                    stickyOptions.classes.stickyEffects = '';
                }

                /*if (elementorSettings.sticky_parent) {
                    stickyOptions.parent = '.elementor-widget-wrap';
                }*/

                if ($wpAdminBar.length && 'top' === stickyTo && 'fixed' === $wpAdminBar.css('position')) {
                    var barH = $wpAdminBar.height();
                    stickyOptions.offset += barH;
                    stickyOptions.extraOffset = barH;
                }

                $widget.The7Sticky(stickyOptions);
            },
            deactivateSticky: function () {
                if (!methods.isStickyInstanceActive()) {
                    return;
                }
                $widget.The7Sticky('destroy');
                $widget.removeClass('the7-e-sticky-effects');
            },
            isStickyInstanceActive: function () {
                return undefined !== $widget.data('the7-sticky');
            },
            onDestroy: function (removedModel) {
                if (removedModel.cid !== modelCID) {
                    return;
                }
                $widget.delete();
            },
        };
        //global functions
        $widget.refresh = function () {
            settings = elementorSettings.getSettings();
            methods.toggle();
            methods.refresh();
        };
        $widget.delete = function () {
            methods.unBindEvents();
            methods.deactivateEffects();
            methods.deactivateSticky();
            $widget.removeData("the7StickyRow");
        };
        $widget.reactivateSticky = function () {
            if (!methods.isStickyInstanceActive()) {
                return;
            }
            settings = elementorSettings.getSettings();
            methods.deactivateSticky();
            methods.activateSticky();
        };
        methods.init();
    };

    var the7StickyRow = function ($elements) {
        $elements.each(function () {
            var $this = $(this);
            if ($this.hasClass('the7-e-sticky-yes') || $this.hasClass('the7-e-sticky-row-yes')) {
                if ($this.hasClass("the7-e-sticky-spacer") || $this.hasClass("elementor-inner-section")) {
                    return;
                }
                var widgetData = $(this).data('the7StickyRow');
                if (widgetData !== undefined) {
                    widgetData.delete();
                }
                new $.the7StickyRow(this);
            }
        });
    };

    $.the7StickyEffectElementHide = function (el) {
        var effectOff = '';
        var $widget = $(el),
            elementorSettings,
            modelCID,
            currentEffect = effectOff,
            effectTimeout,
            classes = {
                effect: "the7-e-on-sticky-effect-visibility",
                hide: "the7-e-on-sticky-effect-visibility-hide",
                show: "the7-e-on-sticky-effect-visibility-show",
            };

        var methods = {};
        $widget.vars = {
            animDelay: 500,
        };
        // Store a reference to the object
        $.data(el, "the7StickyEffectElementHide", $widget);
        // Private methods
        methods = {
            init: function () {
                elementorSettings = new The7ElementorSettings($widget);
                modelCID = elementorSettings.getModelCID();
                if (elementorFrontend.isEditMode()) {
                    elementor.channels.data.on('element:destroy', methods.onDestroy);
                }
                $widget.refresh();
                methods.bindEvents();
                methods.toggle = elementorFrontend.debounce(methods.toggle, 300);
            },
            bindEvents: function () {
                elementorFrontend.elements.$window.on('resize', methods.toggle);
                $widget.on('effect-active', methods.onEffectActive);
                $widget.on('effect-not-active', methods.onEffectNotActive);
            },
            unBindEvents: function () {
                elementorFrontend.elements.$window.off('resize', methods.toggle);
                $widget.off('effect-active', methods.onEffectActive);
                $widget.off('effect-not-active', methods.onEffectNotActive);
            },
            toggle: function () {
                var currMode = elementorSettings.getCurrentDeviceSetting('the7_hide_on_sticky_effect');
                if (currMode === undefined) return;
                if (currMode !== effectOff) {
                    methods.activateEffects(currMode);
                } else {
                    methods.deactivateEffects();
                }
            },
            activateEffects: function (currMode) {
                if (currentEffect === currMode || currMode === effectOff) {
                    return;
                }
                $widget.removeClass(classes.hide);
                $widget.removeClass(classes.show);
                $widget.addClass(classes.effect);
                $widget.addClass(classes[currMode]);
                currentEffect = currMode;
            },
            deactivateEffects: function () {
                if (currentEffect === effectOff) {
                    return;
                }
                $widget.removeClass(classes.hide);
                $widget.removeClass(classes.show);
                $widget.removeClass(classes.effect);
                methods.unsetHeight();
                currentEffect = effectOff;
            },
            onDestroy: function (removedModel) {
                if (removedModel.cid !== modelCID) {
                    return;
                }
                $widget.delete();
            },
            getHeight: function () {
                return $widget.outerHeight();
            },
            setHeight: function (height) {
                $widget.css({height: height});
            },
            unsetHeight: function () {
                $widget.css({height: ""});
            },
            updateHeight: function () {
                methods.unsetHeight();
                $widget.removeClass(classes[currentEffect]);
                methods.setHeight(methods.getHeight());
                window.setTimeout(methods.addEffectClass, 1);
            },
            addEffectClass: function () {
                $widget.addClass(classes[currentEffect]);
            },
            onEffectActive: function () {
                switch (currentEffect) {
                    case 'hide':
                        clearTimeout(effectTimeout);
                        methods.updateHeight();
                        break;
                    case 'show':
                        effectTimeout = window.setTimeout(methods.unsetHeight, $widget.vars.animDelay);
                        break;
                }
            },
            onEffectNotActive: function () {
                switch (currentEffect) {
                    case 'hide':
                        effectTimeout = window.setTimeout(methods.unsetHeight, $widget.vars.animDelay);
                        break;
                    case 'show':
                        clearTimeout(effectTimeout);
                        methods.updateHeight();
                        break;
                }
            }
        };
        //global functions
        $widget.refresh = function () {
            methods.toggle();
        };
        $widget.delete = function () {
            methods.unBindEvents();
            methods.deactivateEffects();
            $widget.removeData("the7StickyEffectElementHide");
        };
        methods.init();
    };

    var the7StickyEffectElementHide = function ($elements) {
        $elements.each(function () {
            var $this = $(this);
            if ($this.hasClass("the7-e-sticky-spacer")) {
                return;
            }
            var widgetData = $(this).data('the7StickyEffectElementHide');
            if (widgetData !== undefined) {
                widgetData.delete();
            }
            new $.the7StickyEffectElementHide(this);
        });
    };

    // Make sure you run this code under Elementor.
    $(window).on("elementor/frontend/init", function () {

        function handleSection($widget) {
            the7StickyRow($widget);
            let elementorSettings = new The7ElementorSettings($widget);
            let settings = elementorSettings.getSettings();
            if (typeof settings !== 'undefined') {
                let list = The7ElementorSettings.getResponsiveSettingList('the7_hide_on_sticky_effect');
                let isActive = list.some(function (item) {
                    return item in settings && settings[item] !== '';
                });
                if (isActive) {
                    the7StickyEffectElementHide($widget);
                }
            }
        }

        function initSections($widgets) {
            $widgets.each(function () {
                var $widget = $(this);
                handleSection($widget);
            });
        }

        elementorFrontend.elements.$document.on('elementor/popup/show', function (event, id, popupElementorObject) {
            initSections(popupElementorObject.$element.find('.elementor-section'));
        });

        if (elementorFrontend.isEditMode()) {
            elementorEditorAddOnChangeHandler("section", refresh);
            elementorFrontend.hooks.addAction("frontend/element_ready/section", function ($widget, $) {
                $(document).ready(function () {
                    handleSection($widget);
                })
            });
        } else {
            elementorFrontend.on("components:init", function () {
                $(document).ready(function () {
                    initSections($('.elementor-section'));
                })
            });
        }

        function refresh(controlView, widgetView) {
            var reactivateControls = [
                ...The7ElementorSettings.getResponsiveSettingList('the7_sticky_row_offset'),
                ...The7ElementorSettings.getResponsiveSettingList('the7_sticky_effects_offset'),
                'the7_sticky_row_overlap',
                "the7_sticky_effects_devices",
                "the7_sticky_effects",
            ];
            var controls = [
                "the7_sticky_row",
                "the7_sticky_row_devices",
                "sticky",
                ...reactivateControls
            ];
            var controlName = controlView.model.get('name');
            if (-1 !== controls.indexOf(controlName)) {
                var $widget = window.jQuery(widgetView.$el);
                var widgetData = $widget.data('the7StickyRow');
                if (typeof widgetData !== 'undefined') {
                    widgetData.refresh();
                    if (-1 !== reactivateControls.indexOf(controlName)) {
                        widgetData.reactivateSticky()
                    }
                } else {
                    the7StickyRow($widget);
                }
            }

            var hide_effect_controls = [
                ...The7ElementorSettings.getResponsiveSettingList('the7_hide_on_sticky_effect'),
            ];
            if (-1 !== hide_effect_controls.indexOf(controlName)) {
                var $widget = window.jQuery(widgetView.$el);
                var widgetData = $widget.data('the7StickyEffectElementHide');
                if (typeof widgetData !== 'undefined') {
                    widgetData.refresh();
                } else {
                    the7StickyEffectElementHide($widget);
                }
            }

        }
    });
})(jQuery);
