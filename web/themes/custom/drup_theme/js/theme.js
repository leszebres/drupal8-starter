var Theme = {};

(function ($, Drupal, drupalSettings) {
    'use strict';

    Drupal.Theme = {};

    /**
     * Constructeur
     */
    Theme.Common = function() {
        this.elements = {
            htmlBody: $('html, body'),
            body: $('body'),
            cookieNotice: $('#cookie-notice')
        };

        // Dépendences
        this.deviceDetect = new $.DeviceDetect();
        this.devices = this.deviceDetect.getDevices();

        this.spinner;

        this.scroller = null;
        this.fixer = {};

        this.maps = {};

        this.cookiesServices = {};

        // Variables
        this.userIsLoggedIn =  this.elements.body.hasClass('is-logged-in');
        this.themePath = drupalSettings.pathToTheme || '';

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
                        return self.load(className).load();
                    }
                });

            } else {
                console.error('Options is undefined.');
            }
        },

        /**
         * Gestion d'un loader sur toutes les requêtes ajax
         */
        ajaxSpinner: function () {
            var self = this;

            // Init Spinner
            self.spinner = self.elements.body.spinner({
                auto: true,
                minTimeout: 600,
                autoPathsExceptions: ['/features/', '/modules/file/', 'https://photon.komoot.de/api/']
            });
        },

        /**
         * Gestionnaire de CookieNotice
         */
        cookieNoticeHandler: function () {
            this.cookieNotice = this.elements.cookieNotice.cookieNotice();
        }
    };

    /**
     * Smooth scroll
     */
    $.fn.smoothScroll = function() {
        $(this).on('click', function(event) {
            if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) +']');

                if (target.length) {
                    event.preventDefault();

                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 500);
                }
            }
        });
    };

    /**
     * Load Themes
     * @type {{attach: attach, detach: detach}}
     */
    Drupal.behaviors.drupTheme = {
        attach: function (context) {
            if (context.body !== undefined) { // or $el.once().action()
                Drupal.Theme = new Theme.Common();
                Drupal.Theme.load('all').load();

                Drupal.Theme.autoload({
                    contact: '.route--contact'
                });

            } else {
                context = $(context);

                if (context.length) {
                    // if (context.hasClass('block--articles-term')) {
                    //     var thematic = Drupal.Theme.load('thematic');
                    //     thematic.customFormHandler();
                    // }
                }
            }
        },
        detach: function (context, trigger) {
        }
    };
}(jQuery, Drupal, drupalSettings));
