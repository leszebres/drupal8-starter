var Theme = {};

(function ($, Drupal, drupalSettings) {
    'use strict';

    Drupal.Theme = {};

    /**
     * Constructeur
     */
    Theme.Common = function() {
        // Éléments
        this.elements = {
            htmlBody: $('html, body'),
            body: $('body'),
            page: $('#page')
        };

        // Variables

        this.userIsLoggedIn = this.elements.body.hasClass('is-logged-in');
        this.pathToTheme = drupalSettings.pathToTheme || '';
        this.currentLanguageId = drupalSettings.path.currentLanguage || 'fr';

        return this;
    };

    /**
     * Méthodes
     */
    Theme.Common.prototype = {
        load: function(className, options) {
            if (Theme[className] !== undefined) {
                return new Theme[className](this, options);
            } else {
                console.error('Class "Theme.' + className + '" not found.');
            }
        },
        autoload: function(options) {
            var self = this;

            if (options !== undefined) {
                $.each(options, function(className, condition) {
                    if (self.elements.body.is(condition)) {
                        return self.load(className).init();
                    }
                });

            } else {
                console.error('Options is undefined.');
            }
        },

        /**
         * Init common
         */
        init: function () {
            //
        }
    };

    /**
     * Load Themes
     * @type {{attach: attach, detach: detach}}
     */
    Drupal.behaviors.drupAdminTheme = {
        attach: function (context) {
            if (context.body !== undefined) {
                Drupal.Theme = new Theme.Common();
                Drupal.Theme.init();

                // Drupal.Theme.autoload({
                //     front: '.is-front'
                // });
            }
        }
    };

}(jQuery, Drupal, drupalSettings));
