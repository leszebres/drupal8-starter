<?php

namespace Drupal\drup\Controller;

use Drupal\system\Controller\EntityAutocompleteController;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DrupEntityAutocompleteController
 *
 * @package Drupal\drup\Controller
 */
class DrupEntityAutocompleteController extends EntityAutocompleteController {

    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('drup.autocomplete_matcher'),
            $container->get('keyvalue')->get('entity_autocomplete')
        );
    }
}
