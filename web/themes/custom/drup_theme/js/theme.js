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
            page: $('#page'),
            content: $('#content'),
            cookieNotice: $('#cookie-notice')
        };
        this.elements.openInNewWindowLinks = this.elements.page.find('a[href$=".pdf"]');

        // Variables
        this.deviceDetect = undefined;
        this.devices = undefined;
        this.spinner = undefined;
        this.cookieNotice = undefined;

        this.menuScrollOffset = 110;

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
            this.deviceDetectHandler();
            this.spinnerHandler();
            this.cookieNoticeHandler();
            this.openInNewWindowHandler();
            this.anchorLinksHandler();
            this.drupBlockAdminFallback();
        },

        /**
         * Gestionnaire de la détection des périphériques
         */
        deviceDetectHandler: function () {
            var self = this;

            self.deviceDetect = new $.DeviceDetect();
            self.devices = self.deviceDetect.getDevices();

            self.deviceDetect.onOldBrowser(function () {
                $('<div>', {
                    'class': 'notice notice--browser',
                    html: Drupal.t('This website is not optimised for the version of your browser that you are using.')
                }).appendTo(self.elements.body);
            });
        },

        /**
         * Gestionnaire du loader sur les requêtes ajax
         */
        spinnerHandler: function () {
            var options = {
                auto: true,
                autoPathsExceptions: [
                    'spinner=0'
                ]
            };

            this.spinner = this.elements.body.spinner(options);
        },

        /**
         * Gestionnaire de CookieNotice
         */
        cookieNoticeHandler: function () {
            if (this.elements.cookieNotice.length) {
                this.cookieNotice = this.elements.cookieNotice.cookieNotice({
                    reload: false,
                    classes: {
                        btnAgree: '{prefix}-agree btn btn--secondary',
                        btnCustomize: '{prefix}-customize btn btn--secondary'
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
            var self = this;

            self.elements.page.on('click', 'a[href*="#"]:not([href="#"])', function (event) {
                if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
                    var target = $(this.hash);
                    target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');

                    if (target.length) {
                        event.preventDefault();

                        self.scrollTop((target.offset().top - self.menuScrollOffset));
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
                var fancyboxInstance = parent.jQuery.fancybox.getInstance();

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
            if (context.body !== undefined) {
                Drupal.Theme = new Theme.Common();
                Drupal.Theme.init();
                Drupal.Theme.load('all').init();

                // Drupal.Theme.autoload({
                //     front: '.is-front'
                // });
            }
        }
    };

}(jQuery, Drupal, drupalSettings));
