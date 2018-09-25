<?php

namespace Drupal\drup\Controller;

use Symfony\Component\HttpFoundation\Request;
use Drupal\search\SearchPageInterface;
use Drupal\search\Controller\SearchController;

/**
 * Override the Route controller for search.
 */
class DrupSearchController extends SearchController {

    /**
     * {@inheritdoc}
     */
    public function view(Request $request, SearchPageInterface $entity) {
        $build = parent::view($request, $entity);

        if (isset($build['search_results_title'])) {
            unset($build['search_results_title']);
        }

        return $build;
    }
}