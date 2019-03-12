<?php

namespace Drupal\drup_site;

use Drupal\drup\DrupBreadcrumbBuilder;

class DrupSiteBreadcrumbBuilder extends DrupBreadcrumbBuilder {
    /**
     * Custom breadcrumb items for nodes or terms
     * [EntityType (node, term)] => [Bundle (node type or vocabulary name)] => [ID/FieldID => TYPE]
     * @return array
     */
    public function getCustomBreadcrumbItemsList() {
        $breadcrumbs = [
            'node' => [
                'page' => [
                    'contact' => 'druproute'
                ]
//                'healthreview_article' => [
//                    'health-review' => 'druproute',
//                    'thematics' => 'taxonomy_term',
//                    'node_file' => 'node'
//                ],
            ],
            'taxonomy_term' => [
            ]
        ];

        return $breadcrumbs;
    }
}