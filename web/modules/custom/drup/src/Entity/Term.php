<?php

namespace Drupal\drup\Entity;

use Drupal\drup\DrupCommon;

/**
 * Class Term
 *
 * @package Drupal\drup\Entity
 */
class Term extends ContentEntityBase {

    /**
     * @param $vid
     * @param int $parent
     *
     * @return array
     */
    public static function getTermsAsTree($vid, $parent = 0)
    {
        $tree = [];
        $languageId = \Drupal::languageManager()->getCurrentLanguage()->getId();

        $terms = \Drupal::service('entity_type.manager')
            ->getStorage("taxonomy_term")
            ->loadTree($vid, $parent, 1, true);

        if (!empty($terms)) {
            foreach ($terms as $tid => &$term) {
                /** @var \Drupal\taxonomy\Entity\Term $term */
                $term = \Drupal::service('entity.repository')
                    ->getTranslationFromContext($term, $languageId);
                $tree[$tid] = (object)[
                    'term' => $term,
                    'children' => \Drupal\taxonomy\Entity\Term::getTermsAsTree($vid, $term->id()),
                ];
            }
        }

        return $tree;
    }
}
