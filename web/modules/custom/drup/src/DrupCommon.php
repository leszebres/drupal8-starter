<?php

namespace Drupal\drup;

use Drupal\Core\Menu\MenuLinkInterface;
use Drupal\menu_link_content\Plugin\Menu\MenuLinkContent;
use Drupal\Core\Language\LanguageInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Unicode;

/**
 * Class DrupCommon
 * @package Drupal\drup
 */
abstract class DrupCommon {
    
    /**
     * Check from url if current page is a node, taxonomy term, etc ... and retrieve its id
     *
     * @return array|null
     */
    public static function getPageEntity($loadEntity = false) {
        $args = (in_array(\Drupal::routeMatch()->getRouteName(), ['views.ajax', 'search.view_node_search', 'entity.node.preview']))
          ? self::getPreviousRouteParameters()
          : \Drupal::routeMatch()->getParameters()->all();
        
        $data = (object) [
            'type' => null,
            'bundle' => null,
            'id' => null,
            'entity' => null
        ];

        if (!empty($args)) {
            $entityType = current(array_keys($args));

            if (!empty($entityType) && ($entityType !== 'entity')) {
                $entity = $args[$entityType];
                $data->type = $entityType;

                if (is_object($entity)) {
                    $data->bundle = (method_exists($entity, 'bundle')) ? $entity->bundle() : null;
                    $data->id = (method_exists($entity, 'id')) ? $entity->id() : null;
                } else {
                    $data->id = (int) $entity;
                }

                if ($loadEntity === true && !empty($data->id) && (!empty($entityType))) {
                    $data->entity = \Drupal::entityTypeManager()->getStorage($entityType)->load($data->id);
                }
            }
        }

        return $data;
    }

    /**
     * Récupère les paramètres de l'url précédente
     *
     * @return mixed
     */
    public static function getPreviousRouteParameters() {
        $previousUrl = \Drupal::request()->server->get('HTTP_REFERER');
        $fakeRequest = \Symfony\Component\HttpFoundation\Request::create($previousUrl);
        $url = \Drupal::service('path.validator')->getUrlIfValid($fakeRequest->getRequestUri());

        if ($url) {
            return $url->getRouteParameters();
        }

        return [];
    }

    /**
     * @param $item
     * @param $language
     *
     * @return bool|void
     */
    public static function checkMenuItemTranslation($item, $language) {
        $menuLinkEntity = self::loadLinkEntityByLink($item['original_link']);

        if ($menuLinkEntity !== null) {
            $isTranslated = self::checkEntityTranslation($menuLinkEntity, $language);

            if ($isTranslated === false) {
                return false;

            } elseif ($isTranslated === true) {
                if (count($item['below']) > 0) {
                    foreach ($item['below'] as $subkey => $subitem) {
                        if (!$item['below'][$subkey] = self::checkMenuItemTranslation($subitem, $language)) {
                            unset($item['below'][$subkey]);
                        }
                    }
                }
                return $item;
            }
        }
        return;
    }

    /**
     * @param $entity
     * @param $language
     *
     * @return bool|null
     */
    public static function checkEntityTranslation($entity, $language) {
        if ($entity !== null) {
            $languages = $entity->getTranslationLanguages();
        
            if ($language === null) {
                $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
            }
        
            // Remove links which are not translated to the current language.
            if (!array_key_exists($language, $languages)) {
                return false;
            }
            else {
                return true;
            }
        }
        return null;
    }
    
    /**
     * Check if node is available on current domain and current language
     * @param $node
     *
     * @return bool
     */
    public static function isNodeTranslated($node) {
        $isAllowed = true;
        
        $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
        
        // Node untranslated
        if (!$node->hasTranslation($langcode)) {
            $isAllowed = false;
        }
        
        return $isAllowed;
    }
    
    /**
     * @param \Drupal\drup\MenuLinkInterface $menuLinkContentPlugin
     *
     * @return null
     */
    public static function loadLinkEntityByLink(MenuLinkInterface $menuLinkContentPlugin) {
        $entity = NULL;
        if ($menuLinkContentPlugin instanceof MenuLinkContent) {
            $menu_link = explode(':', $menuLinkContentPlugin->getPluginId(), 2);
            $uuid = $menu_link[1];
            $entity = \Drupal::service('entity.repository')
                ->loadEntityByUuid('menu_link_content', $uuid);
        }
        return $entity;
    }
    
    /**
     * @param $menuItem
     *
     * @return int|null
     */
    public static function getNidFromMenuItem($menuItem) {
        if (isset($menuItem['url']) && !$menuItem['url']->isExternal()) {
            $urlParameters = $menuItem['url']->getRouteParameters();
            if (isset($urlParameters['node'])) {
                return (int) $urlParameters['node'];
            }
        }
        
        return null;
    }
    
    /**
     * @param $variables
     * @param $path
     * @param $datas
     */
    public static function addFavicons(&$variables, $datas = [], $version = 1, $path = null)
    {
        if (empty($path)) {
            $path = '/' . \Drupal::theme()->getActiveTheme()->getPath() . '/images/favicons';
        }
        $metas = [
            'apple-touch-icon' => [
                '#tag' => 'link',
                '#attributes' => [
                    'rel' => 'apple-touch-icon',
                    'sizes' => '180x180',
                    'href' => $path . '/apple-touch-icon.png?v=' . $version
                ]
            ],
            'icon32x32' => [
                '#tag' => 'link',
                '#attributes' => [
                    'rel' => 'icon',
                    'sizes' => '32x32',
                    'href' => $path . '/favicon-32x32.png?v=' . $version
                ]
            ],
            'icon16x16' => [
                '#tag' => 'link',
                '#attributes' => [
                    'rel' => 'icon',
                    'sizes' => '16x16',
                    'href' => $path . '/favicon-16x16.png?v=' . $version
                ]
            ],
            'manifest' => [
                '#tag' => 'link',
                '#attributes' => [
                    'rel' => 'manifest',
                    'href' => $path . '/site.webmanifest?v=' . $version
                ]
            ]
        ];
        
        if (isset($datas['color_mask_icon'])) {
            $metas['mask-icon'] = [
                '#tag' => 'link',
                '#attributes' => [
                    'rel' => 'mask-icon',
                    'href' => $path . '/safari-pinned-tab.svg?v=' . $version,
                    'color' => $datas['color_mask_icon']
                ]
            ];
        }
        if (isset($datas['color_msapplication'])) {
            $metas['msapplication-TileColor'] = [
                '#tag' => 'meta',
                '#attributes' => [
                    'name' => 'msapplication-TileColor',
                    'content' => $datas['color_msapplication']
                ]
            ];
        }
        if (isset($datas['color_theme'])) {
            $metas['theme-color'] = [
                '#tag' => 'meta',
                '#attributes' => [
                    'name' => 'theme-color',
                    'content' => $datas['color_theme']
                ]
            ];
        }
    
        foreach ($metas as $metaName => $meta) {
            $variables['page']['#attached']['html_head'][] = [$meta, 'favicons-'.$metaName];
        }
    }
    
    /**
     * @param $relativePath
     * @param null $baseUrl
     *
     * @return string
     */
    public static function getAbsolutePath($relativePath = null, $baseUrl = null) {
        if (empty($baseUrl)) {
            $base_url = Request::createFromGlobals()->getSchemeAndHttpHost();
        }
        if (empty($relativePath)) {
            $relativePath = $current_path = \Drupal::service('path.current')->getPath();
        }
        
        return $base_url . $relativePath;
    }
    
    /**
     * @param $string
     * @param int $truncateLimit
     * @param bool $tagsToStrip
     *
     * @return string
     */
    public static function trimString($string, $truncateLimit = 250, $tagsToStrip = true) {
        if ($tagsToStrip !== false) {
            $string = strip_tags($string, $tagsToStrip);
        }
        $string = str_replace(PHP_EOL, '', $string);
        if ($truncateLimit > 0) {
            $string = Unicode::truncate($string, $truncateLimit, true, true);
        }
        
        return $string;
    }
    
    /**
     * @return null
     */
    public static function getCurrentTitle() {
        $request = \Drupal::request();
        $route = \Drupal::routeMatch()->getRouteObject();
        //if ($route = $request->attributes->get(\Symfony\Cmf\Component\Routing\RouteObjectInterface::ROUTE_OBJECT)) {
            return \Drupal::service('title_resolver')->getTitle($request, $route);
        //}
        
        //return null;
    }
    
    /**
     * @param $publicUri
     *
     * @return bool|null|string|string[]
     */
    public static function getSVGContent($mediaUrl)
    {
        $output = null;
        
        //$paths = file_stream_wrapper_get_instance_by_uri($publicUri);
        //if ($mediaUrl = $paths->realpath()) {
            if ($mediaContent = @file_get_contents($mediaUrl)) {
                $mediaContent = preg_replace("/<!--.*?-->/ms","", $mediaContent);
                return $mediaContent;
            }
        //}
        
        return $output;
    }
    
    
    /**
     * @param $nid
     */
    public static function getNodeChildren($nid, $menu_name = 'main') {
        $navItems = [];
    
        $menu_link_manager = \Drupal::service('plugin.manager.menu.link');
        $links = $menu_link_manager->loadLinksByRoute('entity.node.canonical', ['node' => $nid]);
        $root_menu_item = array_pop($links);
    
        $menuParameters = new \Drupal\Core\Menu\MenuTreeParameters();
        $menuParameters
            ->setMaxDepth(1)
            ->setRoot($root_menu_item->getPluginId())
            ->excludeRoot()
            ->onlyEnabledLinks();
        $menuTreeService = \Drupal::service('menu.link_tree');
    
        $menuTree = $menuTreeService->load($menuName, $menuParameters);
        $manipulators = array(
            ['callable' => 'menu.default_tree_manipulators:checkAccess'],
            ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort']
        );
        $tree = $menuTreeService->transform($menuTree, $manipulators);
        $menuItems = $menuTreeService->build($tree);
        $menuItems['#cache']['max-age'] = 0;
    
        if (!empty($menuItems['#items'])) {
            $languageId = \Drupal::languageManager()->getCurrentLanguage()->getId();
    
            foreach ($menuItems['#items'] as $index => $menuItem) {
                $navItems[$index] = (object)[
                    'menuItem' => $menuItem
                ];
                if ($nid = DrupCommon::getNidFromMenuItem($menuItem)) {
                    $node = Node::load($nid);
                    $node = \Drupal::service('entity.repository')->getTranslationFromContext($node, $languageId);
                    $navItems[$index]->node = $node;
                }
            }
            return $navItems;
        }
    
        return false;
    }
    
    /**
     * @param $tags
     *
     * @return array
     */
    public static function getReferencedTerms($terms) {
        $items = [];
        $languageId = \Drupal::languageManager()->getCurrentLanguage()->getId();
        
        if (!empty($terms)) {
            foreach ($terms as $term) {
                if (empty($term['target_id'])) {
                    continue;
                }
                
                $term = Term::load($term['target_id']);
                if (($term instanceof Term) && $term->hasTranslation($languageId)) {
                    $translatedTerm = \Drupal::service('entity.repository')
                        ->getTranslationFromContext($term, $languageId);

                    $items[] = (object) [
                        'name' => $translatedTerm->getName(),
                        'uri' => 'internal:/taxonomy/term/' . $translatedTerm->id()
                    ];
                }
            }
        }
        
        return $items;
    }
    
    /**
     * @param $tags
     *
     * @return array
     */
    public static function getReferencedNodes($nodes) {
        $items = [];
        $languageId = \Drupal::languageManager()->getCurrentLanguage()->getId();
        
        if (!empty($nodes)) {
            foreach ($nodes as $node) {
                if (empty($node['target_id'])) {
                    continue;
                }
                
                $node = Node::load($node['target_id']);
                if (($node instanceof Node) && $node->hasTranslation($languageId)) {
                    $translatedNode = \Drupal::service('entity.repository')
                        ->getTranslationFromContext($node, $languageId);

                    $items[] = (object) [
                        'name' => $translatedNode->getTitle(),
                        'uri' => 'internal:/node/' . $translatedNode->id()
                    ];
                }
            }
        }
        
        return $items;
    }
    
    /**
     * @return mixed
     */
    public static function isAdminRoute() {
        $adminContext = \Drupal::service('router.admin_context');

        return $adminContext->isAdminRoute();
    }
}
