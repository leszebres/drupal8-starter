<?php

/**
 * @param array $suggestions
 * @param array $variables
 */
function drup_theme_theme_suggestions_node_alter(array &$suggestions, array $variables) {
  /*$node = $variables['elements']['#node'];

  if ($variables['elements']['#view_mode'] == "full") {

  }*/
}


/**
 * @param array $variables
 */
function drup_theme_preprocess_node(array &$variables) {
  // Default to turning off byline/submitted.
  //$variables['display_submitted'] = FALSE;
}
