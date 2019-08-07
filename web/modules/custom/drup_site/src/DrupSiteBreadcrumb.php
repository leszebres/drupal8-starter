<?php

namespace Drupal\drup_site;

use Drupal\drup\DrupBreadcrumb;

/**
 * Class DrupSiteBreadcrumb
 *
 * @package Drupal\drup_site
 */
class DrupSiteBreadcrumb extends DrupBreadcrumb {

    /**
     * Custom breadcrumb items for nodes or terms
     *
     * @return array
     */
    public function buildList() {
        $breadcrumbs = [
//            'drup_route' => [
//                 'contact' => []
//            ],
//             ENTITY_TYPE (ex : taxonomy_term, node) | DRUP ROUTE | SYSTEM ROUTE => [
//                 BUNDLE (ex : node_type or vocabulary) | DRUP ROUTE NAME | SYSTEM ROUTE NAME => [
//                        ID (ex : DrupRouteName, Field id without field_ prefix) => TYPE (drup_route/referenced_entity/referenced_taxonomy_term_parents),
//                        ...
//                 ], ...
//            ]
//
//            'node' => [
//                'page' => [
//                    'contact' => 'drup_route',
//                    'nodes' => 'referenced_entity',
//                    'terms' => 'referenced_taxonomy_term_parents',
//                ]
//            ],
//            'taxonomy_term' => [
//            ],
//            'system' => [
//                 'drup_site.home' => []
//            ]
        ];

        return $breadcrumbs;
    }
}
