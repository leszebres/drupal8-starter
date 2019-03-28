<?php

namespace Drupal\drup_social_links;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ImmutableConfig;

class DrupSocialLinks {

    /**
     * @var string
     */
    protected static $configName = 'drup.social_links';

    /**
     * @return array|mixed|null
     */
    public static function getItems() {
        if (($config = self::getConfig()) && ($items = $config->get('items'))) {
            self::formatItems($items);

            return $items;
        }

        return [];
    }

    /**
     * @param bool $editable
     *
     * @return bool|ImmutableConfig
     */
    public static function getConfig($editable = false) {
        $config = $editable ? \Drupal::service('config.factory')->getEditable(self::getConfigName()) : \Drupal::config(self::getConfigName());

        if (($editable && $config instanceof Config )|| (!$editable && $config instanceof ImmutableConfig)) {
            return $config;
        }

        return false;
    }

    /**
     * @return string
     */
    public static function getConfigName() {
        return self::$configName;
    }

    /**
     * Formattage des éléments à retourner
     *
     * @param $items
     */
    protected static function formatItems(&$items) {
        foreach ($items as $i => $item) {
            $items[$i]['link'] = (bool) $item['link'];
            $items[$i]['share'] = (bool) $item['share'];

            $options = explode(',', $item['options']);
            $items[$i]['options'] = [];
            foreach ($options as $option) {
                list($key, $value) = explode('=', $option);
                $items[$i]['options'][trim($key)] = trim($value);
            }
        }
    }

    /**
     * Retourne les liens vers les réseaux sociaux
     */
    public static function getLinkItems() {
        $items = self::getItems();

        if (!empty($items)) {
            $items = array_filter($items, function ($item) {
                return $item['link'];
            });
        }

        return $items;
    }

    /**
     * Retourne les liens pour le partage sur les réseaux sociaux
     */
    public static function getShareItems() {
        $items = self::getItems();
        $config = \Drupal::config('system.site');
        $request = \Drupal::request();
        $routeMatch = \Drupal::routeMatch();
        $pathAlias = \Drupal::service('path.alias_manager')->getAliasByPath($request->getPathInfo());

        $title = urlencode($config->get('name') . ' : ' . \Drupal::service('title_resolver')->getTitle($request, $routeMatch->getRouteObject()));
        $url = urlencode($request->getSchemeAndHttpHost() . $request->getBaseUrl() . $pathAlias);

        if (!empty($items)) {
            $items = array_filter($items, function ($item) {
                return $item['share'];
            });

            foreach ($items as $item) {
                switch ($item['id']) {
                    case 'facebook':
                        $item['url'] = 'https://www.facebook.com/sharer/sharer.php?u=' . $url . '&t=' . $title;
                        break;

                    case 'twitter':
                        $item['url'] = 'https://twitter.com/share?url=' . $url . '&text=' . $title;
                        break;

                    case 'linkedin':
                        $item['url'] ='https://www.linkedin.com/shareArticle?url=' . $url . '&title=' . $title;
                        break;
                }
            }
        }

        return $items;
    }
}