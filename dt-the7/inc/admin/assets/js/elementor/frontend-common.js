(function ($) {
    // Make sure you run this code under Elementor.
    $(window).on("elementor/frontend/init", function () {
        The7ElementorSettings = function ($el) {
            this.$widget = $el;
            // Private methods
            var methods = {
                getModelCID: function ($widget) {
                    return  $widget.data('model-cid');
                },
                getItems: function (items, itemKey) {
                    if (itemKey) {
                        const keyStack = itemKey.split('.'),
                            currentKey = keyStack.splice(0, 1);

                        if (!keyStack.length) {
                            return items[currentKey];
                        }

                        if (!items[currentKey]) {
                            return;
                        }

                        return methods.getItems(items[currentKey], keyStack.join('.'));
                    }

                    return items;
                }
            };
            The7ElementorSettings.prototype.getModelCID = function () {
                return methods.getModelCID(this.$widget);
            };

            The7ElementorSettings.prototype.getCurrentDeviceSetting = function (settingKey) {
                return elementorFrontend.getCurrentDeviceSetting(this.getSettings(), settingKey);
            };

            The7ElementorSettings.prototype.getSettings = function (setting) {
                var elementSettings = {};
                const modelCID = methods.getModelCID(this.$widget);
                if (modelCID) {
                    const settings = elementorFrontend.config.elements.data[modelCID],
                        attributes = settings.attributes;

                    var type = attributes.widgetType || attributes.elType;

                    if (attributes.isInner) {
                        type = 'inner-' + type;
                    }

                    var settingsKeys = elementorFrontend.config.elements.keys[type];

                    if (!settingsKeys) {
                        settingsKeys = elementorFrontend.config.elements.keys[type] = [];

                        $.each(settings.controls, function (name) {
                            if (this.frontend_available) {
                                settingsKeys.push(name);
                            }
                        });
                    }

                    $.each(settings.getActiveControls(), function (controlKey) {
                        if (-1 !== settingsKeys.indexOf(controlKey)) {
                            var value = attributes[controlKey];

                            if (value.toJSON) {
                                value = value.toJSON();
                            }

                            elementSettings[controlKey] = value;
                        }
                    });
                } else {
                    elementSettings = this.$widget.data('settings') || {};
                }
                return methods.getItems(elementSettings, setting);
            };
        };

        The7ElementorSettings.getResponsiveSettingList = function (setting) {
            const breakpoints = Object.keys(elementorFrontend.config.responsive.activeBreakpoints);
            return ['', ...breakpoints].map(suffix => {
                return suffix ? `${setting}_${suffix}` : setting;
            });
        };
    });
})(jQuery);
