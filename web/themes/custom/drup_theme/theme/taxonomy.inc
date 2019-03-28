<?php

use Drupal\drup\Entity\Term;

/**
 * @param $variables
 */
function drup_theme_preprocess_taxonomy_term(&$variables) {
    $term = Term::load($variables['term']->id());
    if (!$term instanceof Term) {
        return;
    }

    $drupField = $term->DrupField();

    $variables['fields'] = [];
}
