<?php

/**
 * Hook theme
 *
 * @return array
 */
function drup_theme_theme() {
    $themes = [];

//    $themes['drup_views_list__expertises'] = [
//        'variables' => [
//            'items' => []
//        ]
//    ];

    \Drupal\drup_blocks\DrupBlock::format($themes, [
        'type' => 'views'
    ]);

    return $themes;
}
