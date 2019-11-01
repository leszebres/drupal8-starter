<?php

use Drupal\drup\Helper\DrupTheme;

/**
 * Implements hook_theme().
 */
function drup_site_theme() {
    $themes = [
//        'drup_blocks_admin_expertise_pushes' => [
//            'variables' => [
//                'items' => []
//            ]
//        ],
    ];

//    DrupTheme::format($themes);

    return $themes;
}