<?php

namespace Drupal\drup_blocks;

use Drupal\Core\Url;

use Drupal\drup\DrupCommon;

/**
 * Class DrupBlockAdmin
 *
 * @package Drupal\drup_blocks
 */
abstract class DrupBlockAdmin {
    
    public static $contextKey = 'drup-blocks-context';
    
    /**
     * @param string $view
     *
     * @return mixed|string
     */
    public static function getContext($view = 'front') {
        if ($view === 'admin') {
            return \Drupal::request()->query->get(self::$contextKey);
        }
        else {
            if (\Drupal::service('path.matcher')->isFrontPage()) {
                return 'front';
            }
            else {
                if ($entity = DrupCommon::getPageEntity()) {
                    return $entity->type . '/' . $entity->id;
                }
            }
        }
        
        return null;
    }
    
    /**
     * @param $key
     *
     * @return mixed
     */
    public static function getValues($key) {
        return \Drupal::service('config.factory')->getEditable('drup_blocks.admin_values')->get($key);
    }
    
    /**
     * @param $key
     * @param $values
     */
    public static function setValues($key, $values) {
        $service = \Drupal::service('config.factory')->getEditable('drup_blocks.admin_values');
        $service->set($key, $values);
        $service->save();
    }
    
    /**
     * @param $blockID
     * @param string $view
     *
     * @return null
     */
    public static function getContextedValues($blockId, $view = 'admin') {
        if ($context = self::getContext($view)) {
            $key = self::formatContextedKey($blockId, $context);
            
            return self::getValues($key);
        }
        
        return null;
    }
    
    /**
     * @param $blockID
     *
     * @return mixed
     */
    public static function getAdminConfigUrl($blockID) {
        if (\Drupal::currentUser()->hasPermission('administer blocks')) {
            $context = self::getContext('front');
            
            return Url::fromRoute('entity.block.edit_form', ['block' => $blockID], [
                'query' => [
                    'destination' => \Drupal::destination()->get(),
                    self::$contextKey => $context
                ]
            ]);
        }
        
        return null;
    }
    
    /**
     * @param $blockID
     * @param $view
     *
     * @return string
     */
    public static function formatContextedKey($blockID, $context) {
        $currentDomain = \Drupal::service('domain.negotiator')->getActiveDomain()->id();
        $currentLanguage = \Drupal::languageManager()->getCurrentLanguage()->getId();
        
        return $blockID . '.' . $currentDomain . '.' . $currentLanguage . '.' . $context;
    }
}
