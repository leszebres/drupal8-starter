<?php

namespace Drupal\drup_settings;

/**
 * Class DrupSettings
 * Retourne des variables prefixées par la langue courante
 *
 * @package Drupal\drup_settings
 */
class DrupSettings {

    /**
     * @var string
     */
    public $langcode;

    /**
     * @var \Drupal\Core\Config\Config
     */
    public $config;

    /**
     * DrupSettings constructor.
     *
     * @param null $langcode
     */
    public function __construct($langcode = null) {
        $this->setLang($langcode);
        $this->config = \Drupal::service('config.factory')->getEditable('system.site');
    }

    /**
     * @param null $langcode
     */
    public function setLang($langcode = null) {
        if ($langcode === null) {
            $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
        }

        $this->langcode = $langcode;
    }

    /**
     *
     */
    public function setNeutralLang() {
        $this->langcode = 'und';
    }

    /**
     * Return prefixed variable name
     * @param $variable
     *
     * @return string
     */
    public function getName($variable) {
        return $this->langcode . '_' . $variable;
    }

    /**
     * Return prefixed variable value
     * @param $variable
     *
     * @return mixed
     */
    public function getValue($variable) {
        return $this->config->get($this->getName($variable));
    }

    /**
     * Recherche dans la config toutes les variables commençant par un pattern
     * @param $search
     * @param bool $trimKeys
     *
     * @return array
     */
    public function searchValues($search, $trimKeys = true) {
        $values = [];
        $searchValue = $this->getName($search);

        foreach ($this->config->get() as $key => $value) {
            if (strpos($key, $searchValue) !== false) {
                if ($trimKeys === true) {
                    $key = str_replace($searchValue, '', $key);
                }
                $values[$key] = $value;
            }
        }

        return $values;
    }

    /**
     * Register prefixed variable value by name
     * @param $variable
     * @param $value
     */
    public function set($variable, $value) {
        $this->config->set($this->getName($variable), $value);
        $this->save();
    }

    /**
     * Save system.config
     */
    public function save() {
        $this->config->save();
    }
}
