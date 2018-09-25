<?php

namespace Drupal\drup\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

class DrupRouteSubscriber extends RouteSubscriberBase {
    
    public function alterRoutes(RouteCollection $collection) {
        // Autocomplete
        if ($route = $collection->get('system.entity_autocomplete')) {
            $route->setDefault('_controller', '\Drupal\drup\Controller\DrupEntityAutocompleteController::handleAutocomplete');
        }

        // Search
        if ($route = $collection->get('search.view_node_search')) {
            $route->setDefault('_controller', '\Drupal\drup\Controller\DrupSearchController::view');
        }
    }
}
