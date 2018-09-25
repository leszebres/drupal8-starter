(function ($, Drupal, drupalSettings) {
    'use strict';

    Theme.all = function (common, options) {
        // Héritage
        this.common = common;

        // Éléments
        this.elements = {
            body: this.common.elements.body,
            navMain: $('#nav-main'),
            externalLinks: $('.page a[href^="http"]'),
            anchorLinks: $('a[href*="#"]:not([href="#"])')
        };
    };

    Theme.all.prototype = {
        load: function () {
            var self = this;

            // Old IE
            self.oldBrowserHandler();

            // Cookies
            self.common.cookieNoticeHandler();

            // Spinner
            self.common.ajaxSpinner();

            // Main menu
            self.menuHandler();

            // Add target blank on links
            self.manageLinksExternal();

            // SmoothAnchors
            self.elements.anchorLinks.smoothScroll();
        },

        /**
         * Togglemenu
         */
        menuHandler: function () {
            var self = this;

            // Nav Main
            if (self.elements.navMain.length) {
                self.togglemenu = new $.ToggleMenu();

                self.togglemenu.setOptions('hover', {
                    elements: {
                        menu: self.elements.navMain
                    },
                    interval: 300
                });
                self.togglemenu.setDisplay('hover');
            }
        },

        /**
         * Adds target _blank on external links
         */
        manageLinksExternal: function () {
            var self = this;

            if (self.elements.externalLinks.length) {
                var urlHost = window.location.host;

                self.elements.externalLinks.each(function (i, link) {
                    link = $(link);
                    if (link.attr('href').search(urlHost) < 0) {
                        link.attr('target', '_blank');
                    }
                });
            }
        },

        /**
         * Gestionnaire des anciens navigateurs
         */
        oldBrowserHandler: function () {
            var regexOldIE = new RegExp(/MSIE ([0-9]|10)\./);

            if (regexOldIE.test(this.common.deviceDetect.userAgent)) {
                $('<div>', {
                    'class': 'notice notice--browser',
                    html: Drupal.t('This website is not optimized for your browser version.')
                }).appendTo(this.common.elements.body);
            }
        }
    };

}(jQuery, Drupal, drupalSettings));
