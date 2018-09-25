<?php

/**
 * @file
 * Hook implementations for hooks provided by Views API.
 */

use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\Core\Database\Query\Condition;
use Drupal\drup\DrupEntityField;

/**
 * @see hooks :
    'views_pre_view',
    'views_pre_build',
    'views_post_build',
    'views_pre_execute',
    'views_post_execute',
    'views_pre_render',
    'views_post_render',

 */

/**
 * Implements hook_views_query_alter().
 */
function drup_views_query_alter(ViewExecutable $view, QueryPluginBase $query) {

}

/**
 * Implements hook_views_pre_view().
 */
function drup_views_pre_view(ViewExecutable $view, $display_id, array &$args) {

}

/**
 * Implements hook_views_pre_render().
 */
function drup_views_pre_render(ViewExecutable $view) {

}
