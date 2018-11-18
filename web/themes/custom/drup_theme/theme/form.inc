<?php

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
 * Implements hook_theme_suggestions_HOOK_alter() for form templates.
 */
function drup_theme_theme_suggestions_form_alter(array &$suggestions, array $variables) {
    if ($variables['element']['#form_id'] === 'search_block_form') {
        $suggestions[] = 'form__search_block_form';
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
 * @param $form
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 * @param $form_id
 */
function drup_theme_form_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, $form_id) {
    
    // SEARCH FORM PAGE SEARCH
    if ($form_id === 'search_form') {
        $form['#attributes']['class'] = ['form', 'form--search'];
        
        $form['basic']['keys']['#title_display'][] = 'invisible';
        $form['basic']['keys']['#attributes']['placeholder'] = t('Click here to make a new search...');
        
        $form['basic']['keys']['#default_value'] = '';
        $form['basic']['submit']['#value'] = t('Ok');
    
        unset($form['advanced'], $form['help_link']);
    }
    
    // WEBFORM (CONTACT)
    elseif (strpos($form_id, 'webform_submission_') !== false) {
        $form['#attributes']['class'] = ['form', 'form--webform'];
    }
    
    // LOGIN + PASSWORD
    elseif (in_array($form_id, ['user_login_form', 'user_pass'])) {
        $form['#attributes']['class'] = ['form', 'form--login'];
    }
}

/**
 * @param $form
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 * @param $form_id
 */
function drup_theme_form_views_exposed_form_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, $form_id) {
    // Vue Tags : articles dans une thématique/notion clé
    if ($form['#id'] === 'views-exposed-form-healthreview-tags-articles-term') {
    
    }
    
    // Force le bouton submit en balise <input>
    $form['actions']['submit']['#button_type'] = 'input';
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
