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
        this.toggleMenu = undefined;
    };

    Theme.all.prototype = {
        init: function () {
            this.navMainHandler();
        },

        /**
         * Gestionnaire de la navigation principale
         */
        navMainHandler: function () {
            // Nav Main
            if (this.elements.navMain.length) {
                this.togglemenu = new $.ToggleMenu();

                this.togglemenu.setOptions('hover', {
                    elements: {
                        menu: this.elements.navMain
                    },
                    interval: 300
                });
                this.togglemenu.toggleMenu('hover');
            }
        }
    };

}(jQuery, Drupal, drupalSettings));
