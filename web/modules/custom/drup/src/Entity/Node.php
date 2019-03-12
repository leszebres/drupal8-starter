<?php

namespace Drupal\drup\Entity;

use Drupal\drup\DrupPageEntity;

/**
 * Class Node
 *
 * @package Drupal\drup\Entity
 */
class Node extends ContentEntityBase {

    /**
     * @return array
     */
    public static function getSiblingsNodes()
    {
        $entities = [
            'previous' => (object)[
                'operator' => '>',
                'sort' => 'ASC'
            ],
            'next' => (object)[
                'operator' => '<',
                'sort' => 'DESC'
            ]
        ];
        $currentEntity = DrupPageEntity::getPageEntity(true);

        if (!empty($currentEntity->entity)) {
            foreach ($entities as $type => $filters) {
                $query = \Drupal::entityQuery($currentEntity->type);
                $query->condition('status', 1);
                $query->condition('type', $currentEntity->bundle);
                $query->condition('langcode', \Drupal::languageManager()->getCurrentLanguage()->getId());
                $query->condition('created', $currentEntity->entity->getCreatedTime(), $filters->operator);
                $query->sort('created', $filters->sort);
                $query->range(0, 1);

                $result = $query->execute();
                $entities[$type] = !empty($result) ? \Drupal\node\Entity\Node::load(current($result)) : null;
            }
        }

        return $entities;
    }

    /**
     * Check if node is available on current language
     *
     * @param $node
     *
     * @return bool
     */
    public static function isNodeTranslated($node)
    {
        $isAllowed = true;

        $languageId = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $nodeTranslations = $node->getTranslationLanguages();

        // Node untranslated
        if (!isset($nodeTranslations['und']) && !$node->hasTranslation($languageId)) {
            $isAllowed = false;
        }

        return $isAllowed;
    }
}
