<?php

namespace Drupal\drup_settings;

/**
 * Class DrupSettingsVariables
 * Retourne des variables prefixées par la langue courante
 *
 * @package Drupal\drup_settings
 */
class DrupSettingsVariables {
    
    public $currentLanguage;
    public $config;
    
    /**
     * DrupSettingsVariables constructor.
     */
    public function __construct() {
        $this->currentLanguage = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $this->config = \Drupal::service('config.factory')->getEditable('system.site');
    }

    public function setLanguageId($id) {
        $this->currentLanguage = $id;
    }
    
    /**
     * Return prefixed variable name
     * @param $variable
     *
     * @return string
     */
    public function getName($variable) {
        return $this->currentLanguage . '_' . $variable;
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
     *
     * @return mixed
     */
    public function set($variable, $value) {
        $this->config->set($this->getName($variable), $value);
        $this->save();
        //\Drupal::messenger()->addMessage('La variable '.$this->getName($variable).' a bien été mise à jour avec la valeur ' . json_encode($value), 'status');
    }
    
    /**
     * Save system.config
     */
    public function save() {
        $this->config->save();
    }
}
