(function ($, Drupal, drupalSettings) {
    'use strict';

    Theme.all = function (common, options) {
        // Héritage
        this.common = common;

        // Éléments
        this.elements = {
            navMain: $('#nav-main')
        };

        // Variables
        this.toggleMenu;
    };

    Theme.all.prototype = {
        init: function () {
            this.navMainHandler();
        },

        /**
         * Gestionnaire de la navigation principale
         */
        navMainHandler: function () {
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
        }
    };

}(jQuery, Drupal, drupalSettings));
