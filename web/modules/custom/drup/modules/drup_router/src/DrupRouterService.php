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
     * Constructs a new DrupRouterService object.
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
     * @param $routeName
     *
     * @return null
     */
    public function getRoute($routeName) {
        if (!empty($this->routes)) {
            foreach ($this->routes as $route) {
                if ($route['routeName'] === $routeName) {
                    return $route;
                }
            }
        }

        return null;
    }

    /**
     * @param $routeName
     * @param null $language
     *
     * @return null
     */
    public function getId($routeName, $language = null) {
        $language = $this->getLanguage($language);

        if ($route = $this->getRoute($routeName)) {
            return (int) $route[$language];
        }

        return null;
    }

    /**
     * Récupère l'id de la route depuis un retour de $this->getRoute()
     *
     * @param $route
     * @param null $language
     *
     * @return int|null
     */
    public function getIdFromRoute($route, $language = null) {
        $language = $this->getLanguage($language);

        return (!empty($route[$language])) ? (int) $route[$language] : null;
    }
    
    /**
     * @param $routeName
     * @param null $language
     *
     * @return null
     */
    public function getPath($routeName, $language = null) {
        $route = $this->getRoute($routeName);
        $routeId = $this->getIdFromRoute($route, $language);
        
        if ($routeId !== null) {
            return \Drupal::service('path.alias_manager')->getAliasByPath('/' . $route['targetType'] . '/' . $routeId);
        }
        
        return null;
    }
    
    /**
     * @param $routeName
     * @param null $language
     *
     * @return null
     */
    public function getUri($routeName, $language = null) {
        $route = $this->getRoute($routeName);
        $routeId = $this->getIdFromRoute($route, $language);

        if ($routeId !== null) {
            return 'internal:/' . $route['targetType'] . '/' . $routeId;
        }
        
        return null;
    }

    /**
     * Récupère le nom de la route courante
     *
     * @param $nid
     */
    public function getName($entityId = null) {
        if ($entityId === null) {
            $entityId = $this->getEntity()->id;
        }

        if (!empty($entityId) && !empty($this->routes)) {
            foreach ($this->routes as $route) {
                if (in_array($entityId, $route) && $route['targetType'] === $this->getEntity()->type) {
                    return $route['routeName'];
                }
            }
        }

        return null;
    }

    /**
     * Récupère la language par défaut, si non définie
     *
     * @param null $language
     *
     * @return null|string
     */
    public function getLanguage($language = null) {
        if ($language === null) {
            //$language = $this->languageCurrent;
            $language = $this->languageDefault; // force main entity
        }

        return $language;
    }

    /**
     * Récupère l'entité courante
     *
     * @return array|null
     */
    public function getEntity() {
        return $this->entity;
    }

    /**
     * @param $routeName
     *
     * @return bool
     */
    public function isRoute($routeName) {
        if ($route = $this->getRoute($routeName)) {
            return in_array($this->getEntity()->id, $route);
        }

        return false;
    }
}
