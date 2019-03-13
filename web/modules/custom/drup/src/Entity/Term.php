<?php

namespace Drupal\drup\Entity;

/**
 * Class Term
 *
 * @package Drupal\drup\Entity
 */
class Term extends \Drupal\taxonomy\Entity\Term {

    /**
     * @inheritdoc
     */
    public static function load($id) {
        if ($entity = parent::load($id)) {
            return ContentEntityBase::translate($entity);
        }

        return null;
    }

    /**
     * @param null $languageId
     *
     * @return bool
     */
    public function isTranslated($languageId = null) {
        return ContentEntityBase::isTranslated($this, $languageId);
    }

    /**
     * @param null $languageId
     *
     * @return \Drupal\Core\Entity\ContentEntityBase
     */
    public function translate($languageId = null) {
        return ContentEntityBase::translate($this, $languageId);
    }

    /**
     * @return \Drupal\drup\Entity\DrupField
     */
    public function drupField() {
        return new DrupField($this);
    }


    /**
     * todo
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
