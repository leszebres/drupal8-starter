<?php

/**
 * @inheritdoc
 */
function drup_theme_theme_suggestions_field_alter(array &$suggestions, array $variables) {
    //dsm($variables);
    /*$element = $variables['element'];
    $suggestions[] = 'field__' . $element['#view_mode'];
    $suggestions[] = 'field__' . $element['#view_mode'] . '__' . $element['#field_name'];*/
}

/**
 * @inheritdoc
 */
function drup_theme_preprocess_field(array &$variables, $hook) {
   $fieldName = $variables['element']['#field_name'];
   
   if (strpos($fieldName, 'field_image') !== false) {
       $variables['attributes']['class'][] = 'paragraph-media';
       $variables['attributes']['class'][] = 'item-media';
       $variables['attributes']['class'][] = 'media';
       $variables['attributes']['class'][] = 'l-image';
   
   } elseif (strpos($fieldName, 'field_body') !== false) {
       $variables['attributes']['class'][] = 'paragraph-body';
       $variables['attributes']['class'][] = 'item-body';
   }
}
