(function ($, Drupal) {
    'use strict';

    /**
     * Vue d'exemple
     */
    Drupal.behaviors.formExample = {
        context: undefined,
        settings: undefined,
        common: undefined,

        elements: {
            form: undefined
        },

        config: {},

        /**
         * Paramètres nécessaires
         */
        prepare: function (context, settings) {
            this.common = Drupal.Theme;
            this.context = context;
            this.settings = settings;

            this.elements.form = $(settings.form_selector);

            return this.elements.form.length > 0;
        },

        /**
         * A l'init
         *
         * @param context
         * @param settings
         */
        attach: function(context, settings) {
            if (this.prepare(context, settings)) {
                $.extend(this.elements, {

                });

                //todo init methods
            }
        }
    };

}(jQuery, Drupal));
