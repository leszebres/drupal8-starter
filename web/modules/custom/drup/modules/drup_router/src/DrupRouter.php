<?php

namespace Drupal\drup_router;

use Drupal\drup\DrupPageEntity;
use Drupal\drup\Entity\Node;
use Drupal\drup\Entity\Term;

/**
 * Class DrupRouter
 *
 * @package Drupal\drup_router
 */
class DrupRouter {

    /**
     * @var array
     */
    protected $routes;

    /**
     * @var string
     */
    protected $languageCurrentId;

    /**
     * @var string
     */
    protected $languageDefaultId;

    /**
     * @var \Drupal\drup\DrupPageEntity
     */
    protected $entity;

    /**
     * DrupRouterService constructor.
     */
    public function __construct() {
        $this->routes = \Drupal::config('drup.routes')->get('routes');
        $this->languageCurrentId = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $this->languageDefaultId = \Drupal::languageManager()->getDefaultLanguage()->getId();
        $this->entity = DrupPageEntity::loadEntity();
    }
    
    /**
     * @return array
     */
    public function getRoutes() {
        return $this->routes;
    }
    
    /**
     * Return specific route data by name
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
     * @param $routeName
     * @param null $language
     *
     * @return \Drupal\drup\Entity\Node|\Drupal\drup\Entity\Term|null
     */
    public function getEntity($routeName, $language = null) {
        $language = $this->getLanguage($language);

        if (($route = $this->getRoute($routeName)) && isset($route[$language])) {
            if ($route['targetType'] === 'taxonomy_term') {
                return Term::load($route[$language]);
            }
            if ($route['targetType'] === 'node') {
                return Node::load($route[$language]);
            }
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
     * @param null $entityType
     * @param null $language
     *
     * @return null
     */
    public function getName($entityId = null, $entityType = null, $language = null) {
        $language = $this->getLanguage($language);
        
        if ($entityId === null) {
            $entityId = (string) $this->entity->id();
        }
        if ($entityType === null) {
            $entityType = $this->entity->getEntityType();
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
     * @param null $language
     *
     * @return bool
     */
    public function isRoute($routeName, $language = null) {
        $language = $this->getLanguage($language);
        
        if ($route = $this->getRoute($routeName)) {
            return (($this->entity->getEntityType() === $route['targetType']) && ($this->entity->id() === $route[$language]));
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
            $language = $this->languageDefaultId; // force main entity
        }
        
        return $language;
    }
}
