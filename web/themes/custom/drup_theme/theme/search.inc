<?php

function drup_theme_preprocess_item_list__search_results(&$variables) {
    $searchKeys = \Drupal::request()->get('keys');
    $countItems = count($variables['items']);
    $countTitle = \Drupal::translation()->formatPlural(
        $countItems,
        '@count result founded',
        '@count results founded'
    );

    $variables['title'] = preg_replace('/([0-9]+)/', '<span class="theme-color-primary">$1</span> ', $countTitle);
    $variables['title'] .= ' ' . t('for the search') . ' « <span class="theme-color-primary">' . $searchKeys . '</span> »';
}
