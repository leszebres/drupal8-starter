<?php

namespace Drupal\drup\Entity;

use Drupal\Core\Entity\Entity;
use Drupal\field\Entity\FieldConfig;

/**
 *
 * Class DrupField
 *
 * @package Drupal\drup\Entity
 */
class DrupField {

    /**
     * @var \Drupal\drup\Entity\ContentEntityBase
     */
    protected $entity;

    /**
     * DrupField constructor.
     *
     * @param $entity
     */
    public function __construct(Entity $entity) {
        $this->entity = $entity;
    }

    /**
     * @param string $field
     *
     * @return \Drupal\Core\Field\FieldItemList|bool
     */
    public function get($field) {
        if ($this->entity->hasField(self::format($field)) && ($data = $this->entity->get(self::format($field))) && !$data->isEmpty()) {
            return $data;
        }

        return false;
    }

    /**
     * @param string $field
     * @param null|string $key
     *
     * @return mixed|null
     * @throws \Drupal\Core\TypedData\Exception\MissingDataException
     */
    public function getValue($field, $key = null) {
        if (($fieldEntity = $this->get($field)) && ($fistField = $fieldEntity->first()) && ($data = $fistField->getValue())) {
            if (is_string($key)) {
                if (isset($data[$key])) {
                    return $data[$key];
                }
            } else {
                return $data;
            }
        }

        return null;
    }

    /**
     * @param string $field
     * @param null|string $key
     *
     * @return array
     */
    public function getValues($field, $key = null) {
        $values = [];

        if ($fields = $this->get($field)) {
            foreach ($fields->getIterator() as $index => $fieldEntity) {
                if ($data = $fieldEntity->getValue()) {
                    if (is_string($key)) {
                        if (isset($data[$key])) {
                            $values[] = $data[$key];
                        }
                    } else {
                        $values[] = $data;
                    }
                }
            }
        }

        return $values;
    }

    /**
     * @param string $field
     *
     * @return \Drupal\Core\Entity\Entity[]
     */
    public function getReferencedEntities($field) {
        /** @var \Drupal\Core\Field\EntityReferenceFieldItemList $fields */
        if ($fields = $this->get($field)) {
            return $fields->referencedEntities();
        }

        return [];
    }

    /**
     * @param $field
     * @param string $type
     *
     * @return null|\Drupal\drup\Media\DrupMediaImage|\Drupal\drup\Media\DrupMediaDocument
     */
    public function getDrupMedia($field, $type = 'image') {
        if ($entities = $this->getReferencedEntities($field)) {
            $className = '\\Drupal\\drup\\Media\\DrupMedia' . ucfirst(strtolower($type));

            if (class_exists($className)) {
                return new $className($entities);
            }
        }

        return null;
    }

    /**
     * @param $input
     * @param $field
     * @param bool $multiple
     * @param null $outputKey
     * @param null $fieldKey
     */
    public function add(&$input, $field, $outputKey = null, $fieldKey = null, $multiple = false) {
        if (empty($outputKey)) {
            $outputKey = $field;
        }

        $value = $this->{$multiple ? 'getValues' : 'getValue'}($field, $fieldKey);

        if (is_array($input)) {
            $input[$outputKey] = $value;

        } else {
            $input->{$outputKey} = $value;
        }
    }

    /**
     * @param $field
     *
     * @return \Drupal\field\Entity\FieldConfig
     */
    public function getConfig($field) {
        return FieldConfig::loadByName($this->entity->getEntityTypeId(), $this->entity->bundle(), self::format($field));
    }

    /**
     * @param $field
     *
     * @return mixed
     */
    public function getDisplayConfig($field) {
        $formDisplay = \Drupal::entityTypeManager()->getStorage('entity_form_display')->load($this->entity->getEntityTypeId() . '.' . $this->entity->bundle() . '.default');

        /** @var \Drupal\Core\Entity\EntityDisplayBase $formDisplay **/
        if ($formDisplay !== null) {
            return $formDisplay->getComponent(self::format($field));
        }

        return null;
    }

    /**
     * @param $field
     *
     * @return string
     */
    public static function format($field) {
        return in_array($field, ['body', 'title']) ? $field : 'field_' . $field;
    }
}
