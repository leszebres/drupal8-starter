<?php

namespace Drupal\drup\Media;

use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\responsive_image\Entity\ResponsiveImageStyle;

/**
 * Class DrupFile
 *
 * @package Drupal\drup\Media
 */
class DrupFile {

    /**
     * @var File
     */
    public $fileEntity;

    /**
     * @var string
     */
    public $fileUri;

    /** @var \Drupal\Core\Image\Image $image */
    public $image;

    /**
     * DrupFileImage constructor.
     *
     * @param \Drupal\file\Entity\File $fileEntity
     */
    public function __construct(File $fileEntity) {
        $this->fileEntity = $fileEntity;
        $this->fileUri = $fileEntity->getFileUri();
        $this->image = \Drupal::service('image.factory')->get($this->fileUri);
    }

    /**
     * @param $style
     * @param array $attributes
     *
     * @return bool
     */
    public function renderMedia($style, $attributes = []) {
        if (!$this->isValid()) {
            return false;
        }

        $rendererOptions = [
            '#uri' => $this->fileUri,
            '#attributes' => [],
            '#width' => $this->image->getWidth(),
            '#height' => $this->image->getHeight(),
        ];

        // Render as image style
        if (!empty($style)) {
            if ($imageStyle = $this->getImageStyleEntity($style, true)) {
                if ($imageStyle instanceof ResponsiveImageStyle) {
                    $rendererOptions += [
                        '#theme' => 'responsive_image',
                        '#responsive_image_style_id' => $style,
                    ];
                }
                else {
                    $rendererOptions += [
                        '#theme' => 'image_style',
                        '#style_name' => $style,
                    ];
                }
            }
            else {
                \Drupal::messenger()->addMessage('Le style d\'image ' . $style . ' n\'existe pas', 'error');
                return false;
            }
        }
        // Render original image
        else {
            $rendererOptions += [
                '#theme' => 'image',
            ];
        }

        if (!empty($attributes)) {
            $rendererOptions['#attributes'] = array_merge_recursive($rendererOptions['#attributes'], $attributes);
        }

        $renderer = \Drupal::service('renderer');
        $renderer->addCacheableDependency($rendererOptions, $this->fileEntity);

        return $renderer->render($rendererOptions);
    }

    /**
     * @param $style
     *
     * @return \Drupal\Core\GeneratedUrl|string|null
     */
    public function getMediaUrl($style) {
        $url = null;

        if ($style !== null && $this->isValid()) {
            if ($imageStyle = $this->getImageStyleEntity($style)) {
                $url = $imageStyle->buildUrl($this->fileUri);
            }
            else {
                \Drupal::messenger()->addMessage('Le style d\'image ' . $style . ' et de type responsive ou n\'existe pas', 'error');
            }
        }
        else {
            $url = file_create_url($this->fileUri);
        }

        return $url;
    }

    /**
     * @return bool
     */
    public function isValid() {
        return $this->image->isValid();
    }

    /**
     * @param $style
     * @param bool $allowResponsiveImageStyle
     *
     * @return \Drupal\Core\Entity\EntityInterface|\Drupal\image\Entity\ImageStyle|\Drupal\responsive_image\Entity\ResponsiveImageStyle|null
     */
    protected function getImageStyleEntity($style, $allowResponsiveImageStyle = false) {
        $imageStyle = ImageStyle::load($style);

        if ($imageStyle instanceof ImageStyle) {
            return $imageStyle;
        }

        if (($allowResponsiveImageStyle === true) && ($responsiveImageStyle = ResponsiveImageStyle::load($style)) && $responsiveImageStyle instanceof ResponsiveImageStyle) {
            return $responsiveImageStyle;
        }

        return null;
    }
}
