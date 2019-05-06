<?php

namespace Drupal\drup;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\drup\Entity\DrupField;
use Drupal\drup\Entity\Node;
use Drupal\drup\Helper\DrupRequest;
use Drupal\drup\Helper\DrupString;
use Drupal\drup\Media\DrupFile;
use Drupal\drup\Media\DrupMediaImage;
use Drupal\drup_settings\DrupSettings;
use Drupal\image\Entity\ImageStyle;
use Drupal\drup\Helper\DrupUrl;

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
        if (DrupRequest::isAdminRoute()) {
            return false;
        }

        if ($type === self::$tokenType) {
            $drupSettings = new DrupSettings();
            $metatagManager = \Drupal::service('metatag.manager');

            $logo = false;
            if (array_key_exists('logo:url', $tokens) || array_key_exists('logo:width', $tokens) || array_key_exists('logo:height', $tokens) || array_key_exists('logo:type', $tokens)) {
                $logo = DrupFile::getLogo('png');
            }

            // Node
            $node = $drupField = false;
            if (isset($data['node']) && $data['node'] instanceof Node) {
                $node = $data['node'];
                $drupField = $node->drupField();
            }

            // Tokens
            foreach ($tokens as $name => $original) {
                // Dans un noeud
                if ($node) {
                    if ($name === 'meta:title') {
                        $tags = $metatagManager->tagsFromEntity($node);

                        if (empty($tags['title'])) {
                            $replacements[$original] = $node->getName();

                        } else {
                            $replacements[$original] = $tags['title'];
                        }

                    } elseif ($name === 'meta:desc') {
                        $tags = $metatagManager->tagsFromEntity($node);

                        if (empty($tags['description'])) {
                            if ($fieldSubtitle = $drupField->getValue('subtitle', 'value')) {
                                $description = $fieldSubtitle;

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

                            } elseif ($fieldBody = $drupField->getValue('body', 'value')) {
                                $description = $fieldBody;
                            }

                            if (!empty($description)) {
                                $replacements[$original] = DrupString::truncate($description);
                            }

                        } else {
                            $replacements[$original] = $tags['description'];
                        }
                    }

                } elseif ($name === 'meta:front:title' && ($title = $drupSettings->getValue('home_meta_title'))) {
                    $replacements[$original] = $title;

                } elseif ($name === 'meta:front:desc' && ($desc = $drupSettings->getValue('home_meta_desc'))) {
                    $replacements[$original] = $desc;

                } elseif ($name === 'logo:url' && $logo) {
                    $replacements[$original] = $logo->url;

                } elseif ($name === 'logo:width' && $logo) {
                    $replacements[$original] = $logo->width;

                } elseif ($name === 'logo:height' && $logo) {
                    $replacements[$original] = $logo->height;

                } elseif ($name === 'logo:type' && $logo) {
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
                if (isset($attachment[1])) {
                    if ($attachment[1] === 'title') {
                        self::addSiteTitle($attachments['#attached']['html_head'][$index][0]['#attributes']['content']);

                    } elseif ($attachment[1] === 'canonical_url') {
                        $queryString = \Drupal::request()->getQueryString();

                        if ($queryString !== null) {
                            $attachments['#attached']['html_head'][$index][0]['#attributes']['href'] .= '?' . $queryString;
                        }
                    }
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
            $string .= ' ' . $separator . ' ' . (new DrupSettings())->getValue('site_name');
        }
    }


    /**
     * Gestionnaire de pagination
     *
     * @param $variables
     */
    public static function pagerHandler(&$variables) {
        if (isset($variables['current'])) {
            $links = [];
            $queryString = \Drupal::request()->getQueryString();
            $currentPath = Url::fromRoute('<current>')->toString();
            $currentPage = (int) $variables['current'] - 1;
            $totalPages = preg_replace('/^.*page=(\d+).*$/', '$1', $variables['items']['last']['href']);

            // Prev
            if ($currentPage > 0) {
                $links['prev'] = $currentPage - 1;
            }

            // Next
            if ($currentPage < $totalPages) {
                $links['next'] = $currentPage + 1;
            }

            // Add
            if (!empty($links)) {
                foreach ($links as $link => $page) {
                    $variables['#attached']['html_head_link'][] = [
                        [
                            'rel' => $link,
                            'href' => $currentPath . DrupUrl::replaceArgument('page', $page, $queryString)
                        ],
                        true
                    ];
                }
            }
        }
    }
}
