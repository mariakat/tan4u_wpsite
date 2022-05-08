function elementorEditorAddOnChangeHandler(widgetType, handler) {
    widgetType = widgetType ? ":" + widgetType : "";
    elementor.channels.editor.on("change" + widgetType, handler);
}

function elementorEditorOnChangeWidgetHandlers(widgetType, widgetControls, handler) {
    widgetControls.forEach(function (control) {
            elementorEditorAddOnChangeHandler(widgetType + ":" + control, handler);
        }
    );
}