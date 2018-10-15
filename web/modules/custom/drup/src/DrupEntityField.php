<?php

namespace Drupal\drup;

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Field\FieldDefinitionInterface;

use Drupal\drup\DrupCommon;

/**
 * Class DrupEntityField
 *
 * @package Drupal\drup
 */
class DrupEntityField {

    /**
     * DrupField constructor.
     *
     * @param $entity
     */
    public function __construct($entity) {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @param $field
     *
     * @return bool
     */
    public function get($field) {
        if ($this->entity->hasField(self::format($field)) && ($data = $this->entity->get(self::format($field))) && !$data->isEmpty()) {
            return $data;
        }

        return false;
    }

    /**
     * @param $field
     * @param string $key
     *
     * @return null|object|array
     */
    public function getValue($field, $key = 'value') {
        if (($fieldEntity = $this->get($field)) && $fieldEntity && ($data = $fieldEntity->getValue())) {
            if (count($data) > 1) {
                return $data;

            } else {
                $data = (object) current($data);

                if (isset($data->{$key})) {
                    return $data->{$key};
                }
            }
        }

        return null;
    }
    
    /**
     * @param $field
     *
     * @return null
     */
    public function getValues($field) {
        if (($fieldEntity = $this->get($field)) && $fieldEntity && ($data = $fieldEntity->getValue())) {
            return $data;
        }

        return [];
    }
    
    /**
     * @param $field
     * @return array
     */
    public function getReferencedNodes($field) {
        if ($values = self::getValues($field)) {
            return DrupCommon::getReferencedNodes($values);
        }
        
        return [];
    }
    
    /**
     * @param $field
     * @return array
     */
    public function getReferencedTerms($field) {
        if ($values = self::getValues($field)) {
            return DrupCommon::getReferencedTerms($values);
        }
        
        return [];
    }

    /**
     * @param $input
     * @param $outputKey
     * @param null $field
     * @param string $fieldKey
     */
    public function add(&$input, $outputKey, $field = null, $fieldKey = 'value') {
        if (empty($field)) {
            $field = $outputKey;
        }

        $value = $this->getValue($field, $fieldKey);

        if (is_array($input)) {
            $input[$outputKey] = $value;

        } else {
            $input->{$outputKey} = $value;
        }
    }

    /**
     * Récupère les paramètres du champ
     *
     * @param $field
     * @param null $setting
     *
     * @return bool
     */
    public function getConfig($field) {
        return \Drupal\field\Entity\FieldConfig::loadByName($this->entity->getEntityTypeId(), $this->entity->bundle(), self::format($field));
    }

    /**
     * Récupère les paramètres d'affichage du champ
     *
     * @param $field
     *
     * @return mixed
     */
    public function getDisplayConfig($field) {
        $formDisplay = \Drupal::entityTypeManager()
            ->getStorage('entity_form_display')
            ->load($this->entity->getEntityTypeId() . '.' . $this->entity->bundle() . '.default');

        return $formDisplay->getComponent(self::format($field));
    }

    /**
     * @param $field
     *
     * @return string
     */
    public static function format($field) {
        return ($field === 'body') ? $field : 'field_' . $field;
    }
}
