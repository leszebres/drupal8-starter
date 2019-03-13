<?php

namespace Drupal\drup\Entity;

use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * Class ContentEntityBase
 *
 * @package Drupal\drup\Entity
 */
class ContentEntityBase extends \Drupal\Core\Entity\ContentEntityBase {

    /**
     * @param \Drupal\Core\Entity\ContentEntityBase $entity
     * @param null $languageId
     *
     * @return bool
     */
    public static function isTranslated(\Drupal\Core\Entity\ContentEntityBase $entity, $languageId = null) {
        $isAllowed = true;

        if ($languageId === null) {
            $languageId = \Drupal::languageManager()->getCurrentLanguage()->getId();
        }

        $translations = $entity->getTranslationLanguages();

        if (!isset($translations['und']) && !$entity->hasTranslation($languageId)) {
            $isAllowed = false;
        }

        return $isAllowed;
    }

    /**
     * @param \Drupal\Core\Entity\ContentEntityBase $entity
     * @param null $languageId
     *
     * @return \Drupal\Core\Entity\ContentEntityBase
     */
    public static function translate(\Drupal\Core\Entity\ContentEntityBase $entity, $languageId = null) {
        if ($languageId === null) {
            $languageId = \Drupal::languageManager()->getCurrentLanguage()->getId();
        }

        if ($entity->hasTranslation($languageId)) {
            return $entity->getTranslation($languageId);
        }

        return $entity;
    }






    /**
     * todo a revoir
     * @param $terms
     *
     * @return array
     */
    public static function getReferencedTerms($terms) {
        $items = [];
        $languageId = \Drupal::languageManager()->getCurrentLanguage()->getId();

        if (!empty($terms)) {
            foreach ($terms as $termReferenced) {
                if (empty($termReferenced['target_id'])) {
                    continue;
                }

                $termEntity = Term::load($termReferenced['target_id']);
                if ($termEntity instanceof Term) {
                    if ($termEntity->hasTranslation($languageId)) {
                        //$termEntity->getTranslation()
                        $termEntity = \Drupal::service('entity.repository')
                            ->getTranslationFromContext($termEntity, $languageId);
                    }
                    $items[] = (object) [
                        'id' => $termEntity->id(),
                        'name' => $termEntity->getName(),
                        'uri' => 'internal:/taxonomy/term/' . $termEntity->id(),
                    ];
                }
            }
        }

        return $items;
    }

    /**
     * todo a revoir
     * @param $nodes
     *
     * @return array
     */
    public static function getReferencedNodes($nodes)
    {
        $items = [];
        $languageId = \Drupal::languageManager()->getCurrentLanguage()->getId();

        if (!empty($nodes)) {
            foreach ($nodes as $nodeReferenced) {
                if (empty($nodeReferenced['target_id'])) {
                    continue;
                }

                $nodeEntity = Node::load($nodeReferenced['target_id']);
                if (($nodeEntity instanceof Node) && $nodeEntity->hasTranslation($languageId)) {
                    $nodeEntity = \Drupal::service('entity.repository')
                        ->getTranslationFromContext($nodeEntity, $languageId);

                    $items[] = (object)[
                        'id' => $nodeEntity->id(),
                        'name' => $nodeEntity->getTitle(),
                        'uri' => 'internal:/node/' . $nodeEntity->id(),
                    ];
                }
            }
        }

        return $items;
    }
}
