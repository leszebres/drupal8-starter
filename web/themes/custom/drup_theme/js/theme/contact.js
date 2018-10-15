(function ($, Drupal, drupalSettings) {
    'use strict';

    Theme.contact = function (common, options) {
        // Héritage
        this.common = common;

        // Éléments
        this.elements = {
            webform: $('form.form--webform')
        };

        // Variables
        this.customForm;
        this.customFormCheckboxes;
        this.customFormRadios;
        this.customFormSelects;
    };

    Theme.contact.prototype = {
        init: function () {
            this.customFormHandler();
        },

        /**
         * Gestionnaire de CustomForm
         */
        customFormHandler: function () {
            var self = this;

            if (typeof $.fn.customForm !== 'undefined' && self.elements.webform.length) {
                // Init
                self.customForm = self.elements.webform.customForm();

                self.customFormCheckboxes = self.customForm.setSupport('checkbox');
                self.customFormRadios = self.customForm.setSupport('radio');
                self.customFormSelects = self.customForm.setSupport('select');
            }
        },
    };

}(jQuery, Drupal, drupalSettings));
