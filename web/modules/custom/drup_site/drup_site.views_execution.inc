<?php

use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\views\ViewExecutable;

/**
 * Implements hook_views_query_alter().
 */
function drup_site_views_query_alter(ViewExecutable $view, QueryPluginBase $query) {
    $exposedValues = $view->getExposedInput();

//    if (in_array($view->id(), ['publications'])) {
//        // Applique un range sur une année
//        if (isset($exposedValues['annees']) && !empty($exposedValues['annees'])) {
//            $year = $exposedValues['annees'];
//            $min = new \Drupal\Core\Datetime\DrupalDateTime('first day of January ' . $year);
//            $max = new \Drupal\Core\Datetime\DrupalDateTime('last day of December ' . $year);
//
//            $query->addWhere(1, sprintf('media_field_data.created BETWEEN %s AND %s', $min->getTimestamp(), $max->getTimestamp()), [], 'formula');
//        }
//    }
}

/**
 * Implements hook_views_pre_render().
 */
function drup_site_views_pre_render(ViewExecutable $view) {
}

/**
 * @param \Drupal\Core\Database\Query\AlterableInterface $query
 */
function drup_site_query_drup_alter(\Drupal\Core\Database\Query\AlterableInterface $query){

}
