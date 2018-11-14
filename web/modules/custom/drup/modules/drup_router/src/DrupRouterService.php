<?php

namespace Drupal\drup_router;

/**
 * Class DrupRouterService.
 */
class DrupRouterService {
    
    protected $routes;
    protected $languageCurrent;
    protected $languageDefault;
    protected $entity;
    
    /**
     * DrupRouterService constructor.
     */
    public function __construct() {
        $this->routes = \Drupal::config('drup.routes')->get('routes');
        $this->languageCurrent = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $this->languageDefault = \Drupal::languageManager()->getDefaultLanguage()->getId();
        $this->entity = \Drupal\drup\DrupCommon::getPageEntity();
    }
    
    /**
     * @return array|mixed|null
     */
    public function getRoutes() {
        return $this->routes;
    }
    
    /**
     * Return specific route datas by name
     * @param $routeName
     *
     * @return null
     */
    public function getRoute($routeName = null) {
        if (!empty($this->routes)) {
            if ($routeName === null) {
                $routeName = $this->getName();
            }
            foreach ($this->routes as $route) {
                if ($route['routeName'] === $routeName) {
                    return $route;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Get entity id attached to route name
     * @param $routeName
     * @param null $language
     *
     * @return null
     */
    public function getId($routeName, $language = null) {
        $language = $this->getLanguage($language);
        
        if ($route = $this->getRoute($routeName)) {
            return $route[$language];
        }
        
        return null;
    }
    
    /**
     * Get url alias of given route name
     * @param $routeName
     * @param null $language
     *
     * @return null
     */
    public function getPath($routeName, $language = null) {
        $language = $this->getLanguage($language);
        
        if (($route = $this->getRoute($routeName)) && isset($route[$language])) {
            $entityPath = ($route['targetType'] === 'taxonomy_term') ? 'taxonomy/term' : $route['targetType'];
            return \Drupal::service('path.alias_manager')->getAliasByPath('/' . $entityPath . '/' . $route[$language]);
        }
        
        return null;
    }
    
    /**
     * Get drupal entity uri of a given route name
     * @param $routeName
     * @param null $language
     *
     * @return null|string
     */
    public function getUri($routeName, $language = null) {
        $language = $this->getLanguage($language);
        
        if (($route = $this->getRoute($routeName)) && isset($route[$language])) {
            return 'internal:/' . $route['targetType'] . '/' . $route[$language];
        }
        
        return null;
    }
    
    /**
     * Return route name attached to a given drupal entity
     * @param null $entityId
     *
     * @return null
     */
    public function getName($entityId = null, $entityType = null, $language = null) {
        $language = $this->getLanguage($language);
        
        if ($entityId === null) {
            $entityId = $this->getEntity()->id;
        }
        if ($entityType === null) {
            $entityType = $this->getEntity()->type;
        }
        
        if (!empty($entityId) && !empty($this->routes)) {
            foreach ($this->routes as $route) {
                if (($route['targetType'] === $entityType) && ($entityId === $route[$language])) {
                    return $route['routeName'];
                }
            }
        }
        
        return null;
    }
    
    /**
     * @param $routeName
     *
     * @return bool
     */
    public function isRoute($routeName, $language = null) {
        $language = $this->getLanguage($language);
        
        if ($route = $this->getRoute($routeName)) {
            return (($this->getEntity()->type === $route['targetType']) && ($this->getEntity()->id === $route[$language]));
        }
        
        return false;
    }
    
    
    /**
     * @param null $language
     *
     * @return null|string
     */
    protected function getLanguage($language = null) {
        if ($language === null) {
            //$language = $this->languageCurrent;
            $language = $this->languageDefault; // force main entity
        }
        
        return $language;
    }
    
    /**
     * @return array|null
     */
    protected function getEntity() {
        return $this->entity;
    }
}
