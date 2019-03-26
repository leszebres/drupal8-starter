<?php

namespace Drupal\drup;

use Drupal\Core\Menu\MenuLinkInterface;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\menu_link_content\Plugin\Menu\MenuLinkContent;

/**
 * Class DrupMenu
 *
 * @package Drupal\drup
 */
class DrupMenu {

    /**
     * todo revoir cf pile
     *
     * @param array $item
     * @param string $languageId
     *
     * @return array|bool
     */
    public static function checkMenuItemTranslation(array $item, $languageId) {
        $menuLinkEntity = self::loadLinkEntityByLink($item['original_link']);

        if ($menuLinkEntity !== null) {
            $isTranslated = self::checkEntityTranslation($menuLinkEntity, $languageId);

            if ($isTranslated === true) {
                if (count($item['below']) > 0) {
                    foreach ($item['below'] as $subkey => $subitem) {
                        if (!$item['below'][$subkey] = self::checkMenuItemTranslation($subitem, $languageId)) {
                            unset($item['below'][$subkey]);
                        }
                    }
                }
                return $item;
            }
        }
        return false;
    }

    /**
     * todo revoir cf pileje
     *
     * @param $entity
     * @param $language
     *
     * @return bool
     */
    public static function checkEntityTranslation($entity, $language) {
        if (!empty($entity)) {
            return array_key_exists($language, $entity->getTranslationLanguages()) ? true : false;
        }

        return false;
    }

    /**
     * todo revoir cf pileje
     *
     * @param \Drupal\Core\Menu\MenuLinkInterface $menuLinkContentPlugin
     *
     * @return null
     */
    public static function loadLinkEntityByLink(MenuLinkInterface $menuLinkContentPlugin) {
        $entity = null;
        if ($menuLinkContentPlugin instanceof MenuLinkContent) {
            $menu_link = explode(':', $menuLinkContentPlugin->getPluginId(), 2);
            $uuid = $menu_link[1];
            $entity = \Drupal::service('entity.repository')
                ->loadEntityByUuid('menu_link_content', $uuid);
        }
        return $entity;
    }

    /**
     * todo revoir cf pileje
     *
     * @param $menuItem
     *
     * @return int|null
     */
    public static function getNidFromMenuItem($menuItem) {
        if (isset($menuItem['url']) && !$menuItem['url']->isExternal()) {
            $urlParameters = $menuItem['url']->getRouteParameters();

            if (isset($urlParameters['node'])) {
                return $urlParameters['node'];
            }
        }

        return null;
    }

    /**
     * todo nouvelles classes
     *
     * @param $nid
     * @param string $menuName
     *
     * @return array
     */
    public static function getChildren($nid, $menuName = 'main') {
        $navItems = [];

        $menu_link_manager = \Drupal::service('plugin.manager.menu.link');
        $links = $menu_link_manager->loadLinksByRoute('entity.node.canonical', ['node' => $nid], $menuName);
        $root_menu_item = array_pop($links);

        $menuParameters = new MenuTreeParameters();
        $menuParameters
            ->setMaxDepth(1)
            ->setRoot($root_menu_item->getPluginId())
            ->excludeRoot()
            ->onlyEnabledLinks();
        $menuTreeService = \Drupal::service('menu.link_tree');

        $menuTree = $menuTreeService->load($menuName, $menuParameters);
        $manipulators = [
            ['callable' => 'menu.default_tree_manipulators:checkAccess'],
            ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
        ];
        $tree = $menuTreeService->transform($menuTree, $manipulators);
        $menuItems = $menuTreeService->build($tree);
        $menuItems['#cache']['max-age'] = 0;

        if (!empty($menuItems['#items'])) {
            foreach ($menuItems['#items'] as $index => $item) {
                $navItems[$index] = (object) [
                    'item' => $item
                ];
            }
        }

        return $navItems;
    }
}