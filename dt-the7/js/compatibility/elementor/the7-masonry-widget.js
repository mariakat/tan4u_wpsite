(function ($) {
    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction("frontend/element_ready/the7_elements.default", function ($scope) {
            the7ApplyColumns($scope.attr("data-id"), $scope.find(".iso-container"), the7GetElementorMasonryColumnsConfig);
            the7ApplyMasonryWidgetCSSGridFiltering($scope.find(".jquery-filter .dt-css-grid"));
        });
        elementorFrontend.hooks.addAction("frontend/element_ready/the7-wc-products.default", function ($scope) {
            the7ApplyColumns($scope.attr("data-id"), $scope.find(".iso-container"), the7GetElementorMasonryColumnsConfig);
            the7ApplyMasonryWidgetCSSGridFiltering($scope.find(".jquery-filter .dt-css-grid"));
            if (!$scope.hasClass("preserve-img-ratio-y")) {
                window.the7ApplyWidgetImageRatio($scope);
            }

            the7ProductsFixAddToCartStyle($scope);
        });
        elementorFrontend.hooks.addAction("frontend/element_ready/the7-wc-products-carousel.default", the7ProductsFixAddToCartStyle);
    });

    function the7ProductsFixAddToCartStyle($scope) {
        $("body").on("wc_cart_button_updated", function (event, $button) {
            if ($button.attr("data-widget-id") !== $scope.attr("data-id")) {
                return;
            }

            const $addedToCartIcon = $scope.find(".elementor-widget-container > .added-to-cart-icon-template").children().clone();
            const $addedToCartButton = $button.next();

            if ( $button.hasClass("woo-popup-button") ) {
                $addedToCartIcon.addClass("popup-icon");
                $addedToCartButton.wrapInner('<span class="filter-popup"></span>');
            }

            $addedToCartButton.append($addedToCartIcon);
        });
    }
})(jQuery);
