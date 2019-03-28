(function ($, Drupal) {
    'use strict';

    Drupal.behaviors.drup_social_links_form = {
        attach: function () {
            // Elements
            var elements = {
                form: $('#edit-drup-social-links')
            };
            elements.newRow = elements.form.find('tbody > tr:last-child');
            elements.itemId = elements.newRow.find('input.form-item-id');
            elements.itemOptions = elements.newRow.find('input.form-item-options');

            // States
            var states = {
                addId: false,
                addOptions: false
            };

            if (elements.itemId.val() === '') {
                states.addId = true;
            }
            if (elements.itemOptions.val() === '') {
                states.addOptions = true;
            }

            // Events
            elements.newRow.on('focusout', 'input.form-item-title', function (event) {
                var fieldTitle = $(event.currentTarget);
                var title = fieldTitle.val().toLowerCase();

                if (title !== '') {
                    if (states.addId) {
                        elements.itemId.val(title);
                    }
                    if (states.addOptions) {
                        elements.itemOptions.val('icon=' + title);
                    }

                }
            });
        }
    };
}(jQuery, Drupal));