<?php

namespace Drupal\drup;

use Drupal\Core\Menu\MenuLinkInterface;
use Drupal\file\Entity\File;
use Drupal\menu_link_content\Plugin\Menu\MenuLinkContent;
use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;
use Drupal\views\Views;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Unicode;

/**
 * Class DrupCommon
 *
 * @package Drupal\drup
 */
abstract class DrupCommon {

    /**
     * @param bool $loadEntity
     *
     * @return object
     */
    public static function getPageEntity($loadEntity = false) {
        $args = in_array(\Drupal::routeMatch()->getRouteName(), [
            'views.ajax',
            'entity.node.preview',
        ])
            ? self::getPreviousRouteParameters()
            : \Drupal::routeMatch()->getParameters()->all();

        $data = (object) [
            'type' => null,
            'bundle' => null,
            'id' => null,
            'entity' => null,
        ];

        if (!empty($args)) {
            $entityType = current(array_keys($args));

            if (!empty($entityType) && (!in_array($entityType, [
                    'entity',
                    'uid',
                ]))) {
                $entity = $args[$entityType];
                $data->type = $entityType;

                if (is_object($entity)) {
                    $data->bundle = method_exists($entity, 'bundle') ? $entity->bundle() : null;
                    $data->id = method_exists($entity, 'id') ? $entity->id() : null;
                }
                else {
                    $data->id = (int) $entity;
                }

                if ($loadEntity === true && !empty($data->id)) {
                    $data->entity = \Drupal::entityTypeManager()
                        ->getStorage($entityType)
                        ->load($data->id);
                }
            }
        }

        return $data;
    }

    /**
     * Récupère les paramètres de l'url précédente
     * @return array
     */
    public static function getPreviousRouteParameters() {
        $previousUrl = \Drupal::request()->server->get('HTTP_REFERER');
        $fakeRequest = Request::create($previousUrl);
        $url = \Drupal::service('path.validator')
            ->getUrlIfValid($fakeRequest->getRequestUri());

        if ($url) {
            return $url->getRouteParameters();
        }

        return [];
    }

    /**
     * @param $item
     * @param $language
     *
     * @return bool
     */
    public static function checkMenuItemTranslation($item, $language) {
        $menuLinkEntity = self::loadLinkEntityByLink($item['original_link']);

        if ($menuLinkEntity !== null) {
            $isTranslated = self::checkEntityTranslation($menuLinkEntity, $language);

            if ($isTranslated === true) {
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
        return false;
    }

    /**
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
     * Check if node is available on current language
     *
     * @param $node
     *
     * @return bool
     */
    public static function isNodeTranslated($node) {
        $isAllowed = true;

        $languageId = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $nodeTranslations = $node->getTranslationLanguages();

        // Node untranslated
        if (!isset($nodeTranslations['und']) && !$node->hasTranslation($languageId)) {
            $isAllowed = false;
        }

        return $isAllowed;
    }

    /**
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
     * @param $variables
     * @param array $data
     * @param int $version
     * @param null $path
     */
    public static function addFavicons(&$variables, $data = [], $version = 1, $path = null) {
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

        if (isset($data['color_mask_icon'])) {
            $metas['mask-icon'] = [
                '#tag' => 'link',
                '#attributes' => [
                    'rel' => 'mask-icon',
                    'href' => $path . '/safari-pinned-tab.svg?v=' . $version,
                    'color' => $data['color_mask_icon']
                ]
            ];
        }
        if (isset($data['color_msapplication'])) {
            $metas['msapplication-TileColor'] = [
                '#tag' => 'meta',
                '#attributes' => [
                    'name' => 'msapplication-TileColor',
                    'content' => $data['color_msapplication']
                ]
            ];
        }
        if (isset($data['color_theme'])) {
            $metas['theme-color'] = [
                '#tag' => 'meta',
                '#attributes' => [
                    'name' => 'theme-color',
                    'content' => $data['color_theme']
                ]
            ];
        }

        foreach ($metas as $metaName => $meta) {
            $variables['page']['#attached']['html_head'][] = [$meta, 'favicons-'.$metaName];
        }
    }

    /**
     * @param null $relativePath
     * @param null $baseUrl
     *
     * @return string
     */
    public static function getAbsolutePath($relativePath = null, $baseUrl = null) {
        if (empty($baseUrl)) {
            $baseUrl = Request::createFromGlobals()->getSchemeAndHttpHost();
        }
        if (empty($relativePath)) {
            $relativePath = \Drupal::service('path.current')->getPath();
        }

        return $baseUrl . $relativePath;
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
     * @return mixed
     */
    public static function getCurrentTitle() {
        $request = \Drupal::request();
        $route = \Drupal::routeMatch()->getRouteObject();

        return \Drupal::service('title_resolver')->getTitle($request, $route);
    }

    /**
     * @param $mediaUrl
     *
     * @return bool|null|string|string[]
     */
    public static function getSVGContent($mediaUrl) {
        $output = null;

        if ($mediaContent = @file_get_contents($mediaUrl)) {
            $mediaContent = preg_replace('/<!--.*?-->/ms','', $mediaContent);
            return $mediaContent;
        }

        return $output;
    }

    /**
     * @param $nid
     * @param string $menuName
     *
     * @return array|bool
     */
    public static function getNodeChildren($nid, $menuName = 'main') {
        $navItems = [];

        $menu_link_manager = \Drupal::service('plugin.manager.menu.link');
        $links = $menu_link_manager->loadLinksByRoute('entity.node.canonical', ['node' => $nid], $menuName);
        $root_menu_item = array_pop($links);

        $menuParameters = new \Drupal\Core\Menu\MenuTreeParameters();
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
            $languageId = \Drupal::languageManager()
                ->getCurrentLanguage()
                ->getId();

            foreach ($menuItems['#items'] as $index => $menuItem) {
                $navItems[$index] = (object) [
                    'menuItem' => $menuItem,
                ];
                if ($nid = self::getNidFromMenuItem($menuItem)) {
                    $node = Node::load($nid);
                    $node = \Drupal::service('entity.repository')
                        ->getTranslationFromContext($node, $languageId);
                    $navItems[$index]->node = $node;
                }
            }
            return $navItems;
        }

        return false;
    }

    /**
     * @param $terms
     *
     * @return array
     */
    public static function getReferencedTerms($terms) {
        $items = [];
        $languageId = \Drupal::languageManager()->getCurrentLanguage()->getId();

        if (!empty($terms)) {
            foreach ($terms as $termReferenced) {
                if (empty($termReferenced['target_id'])) {
                    continue;
                }

                $termEntity = Term::load($termReferenced['target_id']);
                if ($termEntity instanceof Term) {
                    if ($termEntity->hasTranslation($languageId)) {
                        $termEntity = \Drupal::service('entity.repository')
                            ->getTranslationFromContext($termEntity, $languageId);
                    }
                    $items[] = (object) [
                        'id' => $termEntity->id(),
                        'name' => $termEntity->getName(),
                        'uri' => 'internal:/taxonomy/term/' . $termEntity->id(),
                    ];
                }
            }
        }

        return $items;
    }

    /**
     * @param $nodes
     *
     * @return array
     */
    public static function getReferencedNodes($nodes) {
        $items = [];
        $languageId = \Drupal::languageManager()->getCurrentLanguage()->getId();

        if (!empty($nodes)) {
            foreach ($nodes as $nodeReferenced) {
                if (empty($nodeReferenced['target_id'])) {
                    continue;
                }

                $nodeEntity = Node::load($nodeReferenced['target_id']);
                if (($nodeEntity instanceof Node) && $nodeEntity->hasTranslation($languageId)) {
                    $nodeEntity = \Drupal::service('entity.repository')
                        ->getTranslationFromContext($nodeEntity, $languageId);

                    $items[] = (object) [
                        'id' => $nodeEntity->id(),
                        'name' => $nodeEntity->getTitle(),
                        'uri' => 'internal:/node/' . $nodeEntity->id(),
                    ];
                }
            }
        }

        return $items;
    }

    /**
     * @param $vid
     * @param int $parent
     *
     * @return array
     */
    public static function getTermsAsTree($vid, $parent = 0) {
        $tree = [];
        $languageId = \Drupal::languageManager()->getCurrentLanguage()->getId();

        $terms = \Drupal::service('entity_type.manager')
            ->getStorage("taxonomy_term")
            ->loadTree($vid, $parent, 1, true);

        if (!empty($terms)) {
            foreach ($terms as $tid => &$term) {
                /** @var Term $term */
                $term = \Drupal::service('entity.repository')
                    ->getTranslationFromContext($term, $languageId);
                $tree[$tid] = (object) [
                    'term' => $term,
                    'children' => self::getTermsAsTree($vid, $term->id()),
                ];
            }
        }

        return $tree;
    }

    /**
     * @return mixed
     */
    public static function isAdminRoute() {
        $adminContext = \Drupal::service('router.admin_context');

        return $adminContext->isAdminRoute();
    }


    /**
     * Remove some head metas that invalidate W3C
     *
     * @param $attachments
     */
    public static function removeHeaderLinks(&$attachments) {
        if (!isset($attachments['#attached']['html_head_link'])) {
            return;
        }
        // Array to unset.
        $unset_html_head_link = [
            'delete-form',
            'edit-form',
            'version-history',
            'revision',
            'display',
            'drupal:content-translation-overview',
            'drupal:content-translation-add',
            'drupal:content-translation-edit',
            'drupal:content-translation-delete',
            'devel-load',
            'devel-render',
            'devel-definition',
            'clone-form',
            'token-devel',
            'delete-multiple-form'
            //'shortlink',
            //'canonical'
        ];
        // Unset loop.
        foreach ($attachments['#attached']['html_head_link'] as $key => $value) {
            if (isset($value[0]['rel']) && in_array($value[0]['rel'], $unset_html_head_link)) {
                unset($attachments['#attached']['html_head_link'][$key]);
            }
        }
    }

    /**
     * @return array
     */
    public static function getSiblingsNodes() {
        $entities = [
            'previous' => (object) [
                'operator' => '>',
                'sort' => 'ASC'
            ],
            'next' => (object) [
                'operator' => '<',
                'sort' => 'DESC'
            ]
        ];
        $currentEntity = self::getPageEntity(true);

        if (!empty($currentEntity->entity)) {
            foreach ($entities as $type => $filters) {
                $query = \Drupal::entityQuery($currentEntity->type);
                $query->condition('status', 1);
                $query->condition('type', $currentEntity->bundle);
                $query->condition('langcode', \Drupal::languageManager()->getCurrentLanguage()->getId());
                $query->condition('created', $currentEntity->entity->getCreatedTime(), $filters->operator);
                $query->sort('created', $filters->sort);
                $query->range(0, 1);

                $result = $query->execute();
                $entities[$type] = !empty($result) ? Node::load(current($result)) : null;
            }
        }

        return $entities;
    }

    /**
     * @param $elements
     */
    public static function setRequiredAndPlaceholderAttributes(&$elements) {
        if (!empty($elements)) {
            foreach ($elements as $index => &$element) {
                if (is_array($element) && isset($element['#type'])) {
                    $isRequired = (isset($element['#required']) && (bool) $element['#required']);

                    switch ($element['#type']) {
                        // Apply
                        case 'textfield':
                        case 'textarea':
                        case 'email':
                        case 'tel':
                            $element['#attributes']['placeholder'] = $element['#title'];
                            if ($isRequired) {
                                $element['#attributes']['placeholder'] .= ' *';
                            }
                            break;
                        case 'select':
                        case 'webform_select_other':
                            if ($isRequired && isset($element['#empty_option'])) {
                                $element['#empty_option'] .= ' *';
                            }
                            break;
                            break;
                        case 'checkboxes':
                            if ($isRequired ) {
                                if (count($element['#options']) === 1) {
                                    foreach ($element['#options'] as &$option) {
                                        $option .= ' *';
                                    }
                                }
                            }
                            break;
                        // Loop again
                        case 'fieldset':
                        case 'container':
                            self::setRequiredAndPlaceholderAttributes($element);
                            break;
                    }
                }
            }
        }
    }

    /**
     * @param $viewId
     * @param $viewDisplayId
     * @param array $arguments
     *
     * @return bool
     */
    public static function buildView($viewId, $viewDisplayId, $arguments = []) {
        $output = false;

        $view = Views::getView($viewId);
        if ($view instanceof ViewExecutable) {
            $view->setDisplay($viewDisplayId);

            $arguments = [implode(',', $arguments)];
            $view->setArguments($arguments);
            $view->execute();

            $render = $view->render();
            $output = \Drupal::service('renderer')->render($render);
        }

        return $output;
    }

    /**
     * @param $fid
     *
     * @return mixed
     */
    public static function setFilePermanent($fid) {
        if (is_array($fid)) {
            $fid = current($fid);
        }
        $file = File::load($fid);

        if ($file instanceof File) {
            $file->setPermanent();
            $file->save();

            return $file;
        }

        return false;
    }

    /**
     * @param $fid
     *
     * @return string|null
     */
    public static function getFileUrl($fid, $absolute = true) {
        $url = null;

        if (is_array($fid)) {
            $fid = current($fid);
        }

        $file = File::load($fid);
        if ($file instanceof File) {
            $url = $file->getFileUri();

            if ($absolute) {
                $url = file_create_url($url);
            }
        }

        return $url;
    }
}
