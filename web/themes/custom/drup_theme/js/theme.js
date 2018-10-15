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
            cookieNotice: $('#cookie-notice'),
            page: $('#page')
        };
        this.elements.externalLinks = this.elements.page.find('a[href^="http"]');

        // Variables
        this.deviceDetect;
        this.devices;
        this.spinner;
        this.cookieNotice;
        this.userIsLoggedIn = this.elements.body.hasClass('is-logged-in');
        this.pathToTheme    = drupalSettings.pathToTheme || '';

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
         * Init common
         */
        init: function () {
            this.deviceDetectHandler();
            this.spinnerHandler();
            this.cookieNoticeHandler();
            this.externalLinksHandler();
            this.anchorLinksHandler();
        },

        /**
         * Gestionnaire de la detection des périphériques
         */
        deviceDetectHandler: function () {
            var self = this

            self.deviceDetect = new $.DeviceDetect();
            self.devices = self.deviceDetect.getDevices();

            self.deviceDetect.onOldBrowser(function () {
                $('<div>', {
                    'class': 'notice notice--browser',
                    html: Drupal.t('This website is not optimized for your browser version.')
                }).appendTo(self.common.elements.body);
            });
        },

        /**
         * Gestionnaire du loader sur les requêtes ajax
         */
        spinnerHandler: function () {
            this.spinner = this.elements.body.spinner({
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
        },

        /**
         * Gestionnaire des liens externes
         */
        externalLinksHandler: function () {
            if (this.elements.externalLinks.length) {
                this.elements.externalLinks.each(function (i, link) {
                    link = $(link);

                    if (link.attr('href').indexOf(window.location.host) === -1) {
                        link.attr('target', '_blank');
                    }
                });
            }
        },

        /**
         * Gestionnaire des ancres
         */
        anchorLinksHandler: function () {
            var self = this;
            var offset = 100;
            var speed = 500;

            self.elements.page.on('click', 'a[href*="#"]:not([href="#"])', function (event) {
                if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
                    var target = $(this.hash);
                    target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');

                    if (target.length) {
                        event.preventDefault();

                        self.elements.htmlBody.animate({
                            scrollTop: target.offset().top - offset
                        }, speed);
                    }
                }
            });
        }
    };

    /**
     * Load Themes
     * @type {{attach: attach, detach: detach}}
     */
    Drupal.behaviors.drupTheme = {
        attach: function (context) {
            if (context.body !== undefined) {
                Drupal.Theme = new Theme.Common();
                Drupal.Theme.init();
                Drupal.Theme.load('all').init();

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
        }
    };

}(jQuery, Drupal, drupalSettings));
