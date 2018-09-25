<?php

namespace Drupal\drup\Controller;

use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\drup\DrupEntityAutocompleteMatcher;

class DrupEntityAutocompleteController extends \Drupal\system\Controller\EntityAutocompleteController {
    
    /**
     * The autocomplete matcher for entity references.
     */
    protected $matcher;
    
    /**
     * {@inheritdoc}
     */
    public function __construct(DrupEntityAutocompleteMatcher $matcher, KeyValueStoreInterface $key_value) {
        $this->matcher = $matcher;
        $this->keyValue = $key_value;
    }
    
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
