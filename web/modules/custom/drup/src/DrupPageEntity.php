<?php

namespace Drupal\drup;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class DrupPageEntity
 *
 * @package Drupal\drup
 */
class DrupPageEntity {
    /**
     * @param bool $loadEntity
     *
     * @return mixed
     */
    public static function getPageEntity($loadEntity = false)
    {
        $args = in_array(\Drupal::routeMatch()->getRouteName(), [
            'views.ajax',
            'entity.node.preview',
        ])
            ? self::getPreviousRouteParameters()
            : \Drupal::routeMatch()->getParameters()->all();

        $data = (object)[
            'type' => null,
            'bundle' => null,
            'id' => null,
            'entity' => null,
        ];

        if (!empty($args)) {
            $entityType = current(array_keys($args));

            if (!empty($entityType) && !in_array($entityType, ['entity', 'uid'])) {
                $entity = $args[$entityType];

                if (is_object($entity)) {
                    $data->entity = $entity;
                    $data->id = method_exists($data->entity, 'id') ? (int)$data->entity->id() : null;
                } else {
                    if (\Drupal::entityTypeManager()->getDefinition($entityType, false) === null) {
                        return $data;
                    }
                    $data->entity = \Drupal::entityTypeManager()->getStorage($entityType)->load($entity);
                    $data->id = (int)$entity;
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
    public static function getPreviousRouteParameters()
    {
        $previousUrl = \Drupal::request()->server->get('HTTP_REFERER');
        $fakeRequest = Request::create($previousUrl);
        $url = \Drupal::service('path.validator')
            ->getUrlIfValid($fakeRequest->getRequestUri());

        if ($url) {
            return $url->getRouteParameters();
        }

        return [];
    }

    /**
     * todo
     */
    public function id() {

    }
    public function getEntity() {

    }
    public function getEntityType() {

    }
    public function getBundle() {

    }
}