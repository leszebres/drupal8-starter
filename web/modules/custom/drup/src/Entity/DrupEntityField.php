<?php

namespace Drupal\drup\Entity;

/**
 * Class DrupEntityField
 *
 * @package Drupal\drup\Entity
 */
class DrupEntityField {

    /**
     * @var \Drupal\Core\Entity\ContentEntityBase
     */
    protected $entity;

    /**
     * DrupField constructor.
     *
     * @param $entity
     */
    public function __construct($entity) {
        $this->entity = $entity;
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
            }

            $data = (object) current($data);
            if (isset($data->{$key})) {
                return $data->{$key};
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
     *
     * @return \Drupal\field\Entity\FieldConfig
     */
    public function getConfig($field) {
        return \Drupal\field\Entity\FieldConfig::loadByName($this->entity->getEntityTypeId(), $this->entity->bundle(), self::format($field));
    }

    /**
     * @param $field
     *
     * @return mixed
     * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
     * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
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
