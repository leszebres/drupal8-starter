<?php

/**
 * @file
 * Hook implementations for hooks provided by Views API.
 */

use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\query\QueryPluginBase;

/**
 * @see hooks :
 * 'views_pre_view',
 * 'views_pre_build',
 * 'views_post_build',
 * 'views_pre_execute',
 * 'views_post_execute',
 * 'views_pre_render',
 * 'views_post_render',
 */

/**
 * @inheritdoc
 */
function drup_views_query_alter(ViewExecutable $view, QueryPluginBase $query) {

}

/**
 * @inheritdoc
 */
function drup_views_pre_view(ViewExecutable $view, $display_id, array &$args) {

}

/**
 * @inheritdoc
 */
function drup_views_pre_render(ViewExecutable $view) {

}
