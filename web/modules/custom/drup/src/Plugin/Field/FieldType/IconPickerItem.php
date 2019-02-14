<?php

namespace Drupal\drup\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'drup_iconpicker' field type.
 *
 * @FieldType(
 *   id = "drup_iconpicker",
 *   label = @Translation("IconPicker"),
 *   category = @Translation("General"),
 *   default_widget = "drup_iconpicker",
 *   default_formatter = "drup_iconpicker_default"
 * )
 */
class IconPickerItem extends FieldItemBase {

    /**
     * {@inheritdoc}
     */
    public function isEmpty() {
        if ($this->icon !== null) {
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {

        $properties['icon'] = DataDefinition::create('string')
            ->setLabel(t('Icon'));

        return $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraints() {
        $constraints = parent::getConstraints();

        // @todo Add more constrains here.
        return $constraints;
    }

    /**
     * {@inheritdoc}
     */
    public static function schema(FieldStorageDefinitionInterface $field_definition) {

        $columns = [
            'icon' => [
                'type'   => 'varchar',
                'length' => 255,
            ],
        ];

        $schema = [
            'columns' => $columns,
            // @DCG Add indexes here if necessary.
        ];

        return $schema;
    }
}
