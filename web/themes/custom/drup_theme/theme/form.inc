<?php

/**
 * @inheritdoc
 */
function drup_theme_theme_suggestions_fieldset_alter(array &$suggestions, array $variables) {
    $element = $variables['element'];

    // Fielset + legend bugs with flexbox => div/div + error message
    if ((isset($element['#attributes']['class']) && in_array('fieldset-custom', $element['#attributes']['class'], true)) || in_array($element['#type'], ['radios', 'checkboxes'])) {
        $suggestions[] = 'fieldset__custom';
    }
}

/**
 * @inheritdoc
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
 * @inheritdoc
 */
function drup_theme_preprocess_form_element_label(&$variables) {
    if ($variables['element']['#title_display'] !== 'after') {
        $variables['attributes']['class'][] = 'form-group-title';
    }
}


/**
 * @inheritdoc
 */
function drup_theme_preprocess_container(&$variables) {
    if (isset($variables['attributes']['id']) && $variables['attributes']['id'] === 'edit-actions') {
        if (isset($variables['element']['submit']['#id'])) {
            $variables['attributes']['id'] = $variables['attributes']['data-drupal-selector'] = $variables['element']['submit']['#id'] . '-actions';
        }
    }
}

/**
 * @inheritdoc
 */
function drup_theme_preprocess_form(array &$variables) {
    //$variables['attributes']['novalidate'] = 'novalidate';
}

/**
 * @inheritdoc
 */
function drup_theme_preprocess_select(array &$variables) {
    //$variables['attributes']['class'][] = 'select-chosen';
}

/**
 * @inheritdoc
 */
function drup_theme_preprocess_input(array &$variables) {
    if (!empty($variables['attributes'])) {
        if (isset($variables['attributes']['size'])) {
            unset($variables['attributes']['size']);
        }
    }
}

/**
 * @inheritdoc
 */
function drup_theme_theme_suggestions_webform_email_message_html_alter(array &$suggestions, array $variables) {
    $suggestions[] = 'webform_email_message_html__custom';
}

/**
 * @inheritdoc
 */
function drup_theme_preprocess_webform_message(array &$variables) {
//    echo '<pre>';
//    var_dump($variables);
//    echo '</pre>';
//    die;
}
