(function ($, Drupal, drupalSettings) {
    'use strict';

    Theme.contact = function (common, options) {
        // Héritage
        this.common = common;

        // Éléments
        this.elements = {
            webform: $('form.form--webform')
        };
    };

    Theme.contact.prototype = {
        load: function () {
            var self = this;

            if (self.elements.webform.length) {
                // LAYOUT
                self.customFormHandler();
            }
        },

        /**
         *
         */
        customFormHandler: function () {
            var self = this;

            if (typeof $.fn.customForm !== 'undefined') {

                // Init
                self.customForm = self.elements.webform.customForm();

                self.customFormCheckboxes = self.customForm.setSupport('checkbox');
                self.customFormRadios = self.customForm.setSupport('radio');
                self.customFormSelects = self.customForm.setSupport('select');
            }
        },
    };

}(jQuery, Drupal, drupalSettings));
