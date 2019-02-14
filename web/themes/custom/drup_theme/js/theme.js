let Theme = {};

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
        this.elements.openInNewWindowLinks = this.elements.page.find('a[href$=".pdf"]');

        // Variables
        this.deviceDetect = undefined;
        this.devices = undefined;
        this.spinner = undefined;
        this.cookieNotice = undefined;

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
            let self = this;

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
            this.deviceDetectHandler();
            this.spinnerHandler();
            this.cookieNoticeHandler();
            this.externalLinksHandler();
            this.openInNewWindowHandler();
            this.anchorLinksHandler();
            this.drupBlockAdminFallback();
        },

        /**
         * Gestionnaire de la détection des périphériques
         */
        deviceDetectHandler: function () {
            let self = this;

            self.deviceDetect = new $.DeviceDetect();
            self.devices = self.deviceDetect.getDevices();

            self.deviceDetect.onOldBrowser(function () {
                $('<div>', {
                    'class': 'notice notice--browser',
                    html: Drupal.t('This website is not optimized for your browser version.')
                }).appendTo(self.elements.body);
            });
        },

        /**
         * Gestionnaire du loader sur les requêtes ajax
         */
        spinnerHandler: function () {
            this.spinner = this.elements.body.spinner({
                auto: true,
                autoPathsExceptions: []
            });
        },

        /**
         * Gestionnaire de CookieNotice
         */
        cookieNoticeHandler: function () {
            if (this.elements.cookieNotice.length) {
                this.cookieNotice = this.elements.cookieNotice.cookieNotice();
            }
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
         * Gestionnaire pour l'ouverture de lien dans un nouvel onglet
         */
        openInNewWindowHandler: function () {
            if (this.elements.openInNewWindowLinks.length) {
                this.elements.openInNewWindowLinks.on('click', function (event) {
                    event.preventDefault();
                    window.open($(event.currentTarget).attr('href'));
                });
            }
        },

        /**
         * Gestionnaire des ancres
         */
        anchorLinksHandler: function () {
            let self = this;
            let offset = 100;

            self.elements.page.on('click', 'a[href*="#"]:not([href="#"])', function (event) {
                if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
                    let target = $(this.hash);
                    target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');

                    if (target.length) {
                        event.preventDefault();

                        self.scrollTop((target.offset().top - offset));
                    }
                }
            });
        },

        /**
         * Scroll top
         *
         * @param offset
         * @param speed
         */
        scrollTop: function (offset, speed) {
            speed = speed || 500;

            this.elements.htmlBody.animate({
                scrollTop: offset
            }, speed);

            return this;
        },

        /**
         * If drupblockadmin is saved from iframe, fancybox is closed and page reloaded
         */
        drupBlockAdminFallback: function () {
            if (this.userIsLoggedIn) {
                let fancyboxInstance = parent.jQuery.fancybox.getInstance();

                if ((fancyboxInstance !== false) && (fancyboxInstance.current.type === 'iframe') && (fancyboxInstance.current.src.search('drup-blocks-context') >= 0)) {
                    this.elements.body.spinner({
                        auto: false,
                        maxTimeout: -1,
                        onShow: function () {
                            parent.location.reload();
                        }
                    }).show();
                }
            }
        }
    };

    /**
     * Load Themes
     * @type {{attach: attach, detach: detach}}
     */
    Drupal.behaviors.drupTheme = {
        attach: function (context) {
            // XHR
            if (context.body === undefined) {
                context = $(context);

                if (context.length) {
                    // if (context.is('.block--articles-term')) {
                    //     let thematic = Drupal.Theme.load('thematic');
                    //     thematic.customFormHandler();
                    // }
                }
            }
            // Ready
            else {
                Drupal.Theme = new Theme.Common();
                Drupal.Theme.init();
                Drupal.Theme.load('all').init();

                Drupal.Theme.autoload({
                    //contact: '.route--contact'
                });
            }
        }
    };

}(jQuery, Drupal, drupalSettings));
