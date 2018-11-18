<?php

namespace Drupal\drup\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DrupEntityAutocompleteController
 *
 * @package Drupal\drup\Controller
 */
class DrupEntityAutocompleteController extends \Drupal\system\Controller\EntityAutocompleteController {

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('drup.autocomplete_matcher'),
            $container->get('keyvalue')->get('entity_autocomplete')
        );
    }
}
