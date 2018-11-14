(function ($, Drupal, drupalSettings) {
    'use strict';

    Theme.contact = function (common, options) {
        // Héritage
        this.common = common;

        // Éléments
        this.elements = {
            form: $('form.form--webform')
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
            if (typeof $.fn.customForm !== 'undefined' && this.elements.form.length) {
                // Init
                this.customForm = this.elements.form.customForm();
                this.customFormCheckboxes = this.customForm.setSupport('checkbox');
                this.customFormRadios = this.customForm.setSupport('radio');
                this.customFormSelects = this.customForm.setSupport('select');
            }
        },
    };

}(jQuery, Drupal, drupalSettings));
