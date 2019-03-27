<?php

use Drupal\drup\DrupCommon;
use Drupal\drup\DrupSite;
use Drupal\drup_site\DrupSiteForm;

/**
 * Implements hook_theme_suggestions_field_alter().
 */
function drup_theme_theme_suggestions_fieldset_alter(array &$suggestions, array $variables) {
    $element = $variables['element'];
    
    // Fielset + legend bugs with flexbox => div/div
    if (isset($element['#attributes']['class']) && in_array('fieldset-custom', $element['#attributes']['class'])) {
        $suggestions[] = 'fieldset__custom';
    }
}

/**
 * @param $suggestions
 * @param array $variables
 */
function drup_theme_theme_suggestions_input_alter(array &$suggestions, array $variables) {
    $element = $variables['element'];
    if (isset($element['#type']) && $element['#type'] === 'submit' && (!isset($element['#button_type']) || (isset($element['#button_type']) && $element['#button_type'] !== 'input'))) {
        $suggestions[] = 'input__submit_custom';
    }
    
    //if (isset($element['#attributes']['data-twig-suggestion'])) {
        //$suggestions[] = 'input__' . $element['#type'] . '__' . $element['#attributes']['data-twig-suggestion'];
    //}
}

/**
 * @param $variables
 */
function drup_theme_preprocess_form_element_label(&$variables) {
    if ($variables['element']['#title_display'] !== 'after') {
        $variables['attributes']['class'][] = 'form-group-title';
    }
}


/**
 * @param $variables
 */
function drup_theme_preprocess_container(&$variables) {
    if (isset($variables['attributes']['id']) && $variables['attributes']['id'] === 'edit-actions') {
        if (isset($variables['element']['submit']['#id'])) {
            $variables['attributes']['id'] = $variables['attributes']['data-drupal-selector'] = $variables['element']['submit']['#id'] . '-actions';
        }
    }
}

/**
 * @param array $variables
 */
function drup_theme_preprocess_form(array &$variables) {
    //$variables['attributes']['novalidate'] = 'novalidate';
}

/**
 * @param array $variables
 */
function drup_theme_preprocess_select(array &$variables) {
    //$variables['attributes']['class'][] = 'select-chosen';
}

/**
 * @param array $variables
 */
function drup_theme_preprocess_input(array &$variables) {
    if (!empty($variables['attributes'])) {
        if (isset($variables['attributes']['size'])) {
            unset($variables['attributes']['size']);
        }
    }
}

/**
 * @param array $suggestions
 * @param array $variables
 */
function drup_theme_theme_suggestions_webform_email_message_html_alter(array &$suggestions, array $variables) {
    $suggestions[] = 'webform_email_message_html__custom';
}

/**
 * @param array $variables
 */
function drup_theme_preprocess_webform_message(array &$variables) {
//    echo '<pre>';
//    var_dump($variables);
//    echo '</pre>';
//    die;
}
