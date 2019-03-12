<?php

namespace Drupal\drup;

use Drupal\drup\Helper\DrupRequest;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DrupPageEntity
 *
 * @package Drupal\drup
 */
class DrupPageEntity {

    /**
     * @var array
     */
    protected $args;

    /**
     * @var object
     */
    protected $entity;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $bundle;

    /**
     * DrupPageEntity constructor.
     */
    public function __construct() {
        $this->args = in_array(\Drupal::routeMatch()->getRouteName(), [
            'views.ajax',
            'entity.node.preview',
        ]) ? $this->getPreviousRouteParameters() : DrupRequest::getArgs();

        $this->init();
    }

    /**
     * @param bool $loadEntity
     *
     * @return mixed
     */
    public function init() {
        $data = (object) [
            'entity' => null,
            'type' => null,
            'bundle' => null,
            'id' => null
        ];

        if (!empty($this->args)) {
            $entityType = current(array_keys($this->args));

            if (!empty($entityType) && !in_array($entityType, ['entity', 'uid'])) {
                $entity = $this->args[$entityType];

                if (is_object($entity)) {
                    $data->entity = $entity;
                    $data->id = method_exists($data->entity, 'id') ? (int)$data->entity->id() : null;

                } else {
                    if (\Drupal::entityTypeManager()->getDefinition($entityType, false) === null) {
                        return $data;
                    }
                    $data->entity = \Drupal::entityTypeManager()->getStorage($entityType)->load($entity);
                    $data->id = (int) $entity;
                }

                $data->type = $entityType;
                $data->bundle = method_exists($data->entity, 'bundle') ? $data->entity->bundle() : null;
            }
        }

        return $data;
    }

    /**
     * Récupère les paramètres de l'url précédente
     *
     * @return array
     */
    public function getPreviousRouteParameters() {
        $previousUrl = \Drupal::request()->server->get('HTTP_REFERER');
        $fakeRequest = Request::create($previousUrl);

        /** @var \Drupal\Core\Url $url **/
        $url = \Drupal::service('path.validator')->getUrlIfValid($fakeRequest->getRequestUri());

        if ($url) {
            return $url->getRouteParameters();
        }

        return [];
    }

    /**
     * @param \StdClass $data
     */
    protected function setData(\StdClass $data) {
        $this->entity = $data['entity'];
        $this->type = $data['type'];
        $this->id = $data['id'];
        $this->bundle = $data['bundle'];
    }

    /**
     * @return string
     */
    public function id() {
        return $this->id;
    }

    /**
     * @return object
     */
    public function getEntity() {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getEntityType() {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getBundle() {
        return $this->bundle;
    }

    /**
     * @return DrupPageEntity
     */
    public static function loadEntity() {
        return new static;
    }
}
