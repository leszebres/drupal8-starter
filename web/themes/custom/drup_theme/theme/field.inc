<?php

/**
 * Implements hook_theme_suggestions_field_alter().
 */
function drup_theme_theme_suggestions_field_alter(array &$suggestions, array $variables) {
    //dsm($variables);
    /*$element = $variables['element'];
    $suggestions[] = 'field__' . $element['#view_mode'];
    $suggestions[] = 'field__' . $element['#view_mode'] . '__' . $element['#field_name'];*/
}

/**
 * Implements hook_preprocess_field().
 */
function drup_theme_preprocess_field(array &$variables, $hook) {
    switch ($variables['element']['#field_name']) {
        case 'field_image_file':
            $variables['attributes']['class'][] = 'item-media';
            $variables['attributes']['class'][] = 'l-image';
            break;

        case 'field_body':
            $variables['attributes']['class'][] = 'item-body';
            break;
    }
}
