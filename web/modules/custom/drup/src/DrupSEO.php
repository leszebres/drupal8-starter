<?php

namespace Drupal\drup;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\drup\Media\DrupMediaImage;
use Drupal\node\Entity\Node;
use Drupal\drup\Helpers\DrupUrl;

/**
 * Class DrupSEO
 *
 * @package Drupal\drup
 */
abstract class DrupSEO {

    /**
     * Nom du groupe contenant les tokens SEO
     * 
     * @var string
     */
    public static $tokenType = 'seo';

    /**
     * Nom de l'image style utilisé pour les images en SEO
     *
     * @var string
     */
    public static $imageStyle = 'seo';

    /**
     * Déclaration des tokens pour le SEO
     *
     * @param $info
     */
    public static function tokensInfo(&$info) {
        // Déclaration du groupe SEO
        $info['types'][self::$tokenType] = [
            'name' => 'SEO',
            'description' => 'Données utilisées pour les métas SEO',
            'needs-data' => 'node' // Ajout l'objet Node si on est sur un node
        ];

        // Tokens associés au groupe SEO

        // Meta title/desc
        $info['types'][self::$tokenType]['meta:front:title'] = [
            'name' => 'Méta "title" pour la page d\'accueil'
        ];
        $info['types'][self::$tokenType]['meta:front:desc'] = [
            'name' => 'Méta "description" pour la page d\'accueil'
        ];
        $info['types'][self::$tokenType]['meta:title'] = [
            'name' => 'Meta "title" automatique'
        ];
        $info['types'][self::$tokenType]['meta:desc'] = [
            'name' => 'Meta "description" automatique'
        ];

        // Logo site
        $info['types'][self::$tokenType]['logo:url'] = [
            'name' => 'Url du logo'
        ];
        $info['types'][self::$tokenType]['logo:width'] = [
            'name' => 'Largeur du logo (px)'
        ];
        $info['types'][self::$tokenType]['logo:height'] = [
            'name' => 'Hauteur du logo (px)'
        ];
        $info['types'][self::$tokenType]['logo:type'] = [
            'name' => 'Type d\'image logo (image/png)'
        ];

        // Miniature node
        $info['types'][self::$tokenType]['thumbnail:url'] = [
            'name' => 'URL de la vignette',
            'description' => 'Avec le style d\'image "' . strtoupper(self::$imageStyle) . '"'
        ];
        $info['types'][self::$tokenType]['thumbnail:type'] = [
            'name' => 'Type d\'image de la vignette (image/jpg)'
        ];
        $info['types'][self::$tokenType]['thumbnail:width'] = [
            'name' => 'Largeur de la vignette (px)'
        ];
        $info['types'][self::$tokenType]['thumbnail:height'] = [
            'name' => 'Hauteur de la vignette (px)'
        ];
    }

    /**
     * Contenu des tokens
     *
     * @param $replacements
     * @param $type
     * @param array $tokens
     * @param array $data
     * @param array $options
     * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
     */
    public static function tokens(&$replacements, $type, array $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
        if ($type === self::$tokenType) {
            $drupSettings = \Drupal::service('drup_settings.variables');
            $metatagManager = \Drupal::service('metatag.manager');
            $logo = DrupSite::getLogo();

            // Node
            $node = $drupField = false;
            if (isset($data['node']) && $data['node'] instanceof Node) {
                /** @var \Drupal\node\Entity\Node $node */
                $node = \Drupal::service('entity.repository')->getTranslationFromContext($data['node'], $options['langcode']);
                $drupField = new DrupEntityField($node);
            }

            // Tokens
            foreach ($tokens as $name => $original) {
                // Dans un noeud
                if ($node) {
                    if ($name === 'meta:title') {
                        $tags = $metatagManager->tagsFromEntity($data['node']);

                        if (empty($tags['title'])) {
                            $replacements[$original] = $node->getTitle();

                        } else {
                            $replacements[$original] = $tags['title'];
                        }

                    } elseif ($name === 'meta:desc') {
                        $tags = $metatagManager->tagsFromEntity($data['node']);

                        if (empty($tags['description'])) {
                            if ($fieldSubtitle = $drupField->get('subtitle')) {
                                $description = $fieldSubtitle->value;

                            } elseif (($fieldDescription = $drupField->get('body_layout')) && \is_array($fieldDescription) && !empty($fieldDescription)) {
                                foreach ($fieldDescription as $paragraphItem) {
                                    if ($paragraphItem !== null) {
                                        $paragraphItem->entity = \Drupal::service('entity.repository')->getTranslationFromContext($paragraphItem->entity, $options['langcode']);

                                        if (!empty($paragraphItem->entity) && isset($paragraphItem->entity->field_body)) {
                                            $description = $paragraphItem->entity->field_body->value;
                                            break;
                                        }
                                    }
                                }

                            } elseif ($fieldBody = $drupField->get('body')) {
                                $description = $fieldBody->value;
                            }

                            if (!empty($description)) {
                                $replacements[$original] = DrupCommon::trimString($description);
                            }

                        } else {
                            $replacements[$original] = $tags['description'];
                        }
                    }

                } elseif ($name === 'meta:front:title' && ($title = $drupSettings->getValue('home_meta_title'))) {
                    $replacements[$original] = $title;

                } elseif ($name === 'meta:front:desc' && ($desc = $drupSettings->getValue('home_meta_desc'))) {
                    $replacements[$original] = $desc;

                } elseif ($name === 'logo:url') {
                    $replacements[$original] = $logo->url;

                } elseif ($name === 'logo:width') {
                    $replacements[$original] = $logo->width;

                } elseif ($name === 'logo:height') {
                    $replacements[$original] = $logo->height;

                } elseif ($name === 'logo:type') {
                    $replacements[$original] = $logo->mimetype;

                } elseif ($name === 'thumbnail:url') {
                    $mediaField = false;
                    if ($thumbnail = $drupField->get('thumbnail')) {
                        $mediaField = $thumbnail;
                    } elseif ($banner = $drupField->get('banner')) {
                        $mediaField = $banner;
                    }

                    if ($mediaField && ($media = new DrupMediaImage($mediaField))) {
                        $replacements[$original] = current($media->getMediasUrl(self::$imageStyle));
                    }

                } elseif ($name === 'thumbnail:type') {
                    $replacements[$original] = 'image/jpg';

                } elseif ($name === 'thumbnail:width' || $name === 'thumbnail:height') {
                    $imageStyle = ImageStyle::load(self::$imageStyle);

                    if ($imageStyle instanceof ImageStyle) {
                        $imageStyleEffect = current($imageStyle->getEffects()->getConfiguration());

                        $replacements[$original] = $imageStyleEffect['data'][str_replace('thumbnail:', '', $name)];
                    }
                }
            }
        }
    }

    /**
     * Tokens alter
     *
     * @param $replacements
     * @param $context
     */
    public static function tokensAlter(&$replacements, &$context) {
        if ($context['type'] === self::$tokenType) {
            $metas = [
                'meta:title',
                'meta:front:title'
            ];

            foreach ($metas as $meta) {
                $metaKey = '[' . self::$tokenType . ':' . $meta . ']';

                if (isset($context['tokens'][$meta], $replacements[$metaKey])) {
                    self::addSiteTitle($replacements[$metaKey]);
                }
            }
        }
    }

    /**
     * Page attachments Alter
     *
     * @param $attachments
     */
    public static function attachmentsAlter(&$attachments) {
        if (!empty($attachments['#attached']['html_head'])) {
            foreach ($attachments['#attached']['html_head'] as $index => $attachment) {
                if (isset($attachment[1]) && $attachment[1] === 'title') {
                    self::addSiteTitle($attachments['#attached']['html_head'][$index][0]['#attributes']['content']);
                }
            }
        }
    }

    /**
     * Ajoute le nom du site à la fin de la chaine fournie
     *
     * @param $string
     * @param string $separator
     */
    public static function addSiteTitle(&$string, $separator = '|') {
        if (strpos($string, $separator) === false) {
            $string .= ' ' . $separator . ' ' . \Drupal::service('drup_settings.variables')->getValue('site_name');
        }
    }

    /**
     * Gestionnaire de pagination
     *
     * @param $variables
     * @param int $itemsPerPage
     */
    public static function pagerHandler(&$variables, $itemsPerPage = 10) {
        if (isset($variables['page']['#attached']['html_head'])) {
            $links       = [];
            $currentPath = \Drupal::service('path.alias_manager')->getAliasByPath(\Drupal::service('path.current')->getPath());
            $queryString = \Drupal::request()->getQueryString();
            $currentPage = (int) Xss::filter(\Drupal::request()->get('page'));
            $nextItems   = ($currentPage * $itemsPerPage) + $itemsPerPage;
            $totalItems  = (!empty($GLOBALS['pager_total_items']) && isset($GLOBALS['pager_total_items'][0])) ? (int) $GLOBALS['pager_total_items'][0] : 0;

            // Prev
            if ($currentPage > 0) {
                $links['prev'] = $currentPage - 1;
            }

            // Next
            if ($totalItems > $nextItems) {
                $links['next'] = $currentPage + 1;
            }

            // Add
            foreach ($links as $link => $page) {
                $variables['page']['#attached']['html_head'][] = [
                    [
                        '#tag' => 'link',
                        '#attributes' => [
                            'rel' => $link,
                            'href' => $currentPath . DrupUrl::replaceArgument('page', $page, $queryString)
                        ]
                    ],
                    'pager-' . $link
                ];
            }

            // Update canonical
            foreach ((array) $variables['page']['#attached']['html_head'] as &$meta) {
                if ($queryString !== null && $meta[1] === 'canonical_url') {
                    $meta[0]['#attributes']['href'] = $meta[0]['#attributes']['href'] . '?' . $queryString;
                    break;
                }
            }
        }
    }
}
