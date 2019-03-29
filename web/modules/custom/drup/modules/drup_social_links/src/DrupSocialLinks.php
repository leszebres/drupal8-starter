<?php

namespace Drupal\drup_social_links;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\drup\DrupPageEntity;

class DrupSocialLinks {

    /**
     * Nom de la configuration
     *
     * @var string
     */
    protected static $configName = 'drup.social_links';

    /**
     * Retourne les items enregistrés pour la lecture seulement (formatés)
     *
     * @return array|mixed|null
     */
    public static function getItems() {
        if (($config = self::getConfig()) && ($items = $config->get('items')) && !empty($items)) {
            self::formatItems($items);

            return $items;
        }

        return [];
    }

    /**
     * Retourne la configuration des items (lecture ou écriture)
     *
     * @param bool $editable
     *
     * @return bool|Config|ImmutableConfig
     */
    public static function getConfig($editable = false) {
        $config = $editable ? \Drupal::service('config.factory')->getEditable(self::getConfigName()) : \Drupal::config(self::getConfigName());

        if (($editable && $config instanceof Config )|| (!$editable && $config instanceof ImmutableConfig)) {
            return $config;
        }

        return false;
    }

    /**
     * Retourne le nom de la configuration
     *
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
        if (!empty($items)) {
            foreach ($items as $i => $item) {
                $items[$i]['link'] = (bool) $item['link'];
                $items[$i]['share'] = (bool) $item['share'];

                $options = explode(',', $item['options']);
                $items[$i]['options'] = [];
                if (!empty($options)) {
                    foreach ($options as $option) {
                        if (!empty($option)) {
                            list($key, $value) = explode('=', $option);
                            $items[$i]['options'][trim($key)] = trim($value);
                        }
                    }
                }
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

        if (!empty($items)) {
            $token = \Drupal::token();
            $drupPageEntity = DrupPageEntity::loadEntity();

            foreach ($items as $id => $item) {
                if (!$item['share']) {
                    unset($items[$id]);
                    continue;
                }

                $shareUrlTokens = $token->scan($item['share_url']);
                $replaceOptions = [];

                if ($drupPageEntity->getEntityType() === 'node') {
                    $replaceOptions['node'] = $drupPageEntity->getEntity();
                }

                foreach ($shareUrlTokens as $shareUrlTokenGroup => $shareUrlToken) {
                    $items[$id]['share_url'] = $token->replace($item['share_url'], $replaceOptions);
                }
            }
        }

        return $items;
    }
}