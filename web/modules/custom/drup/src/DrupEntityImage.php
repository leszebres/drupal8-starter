<?php

namespace Drupal\drup;

use Drupal\image\Entity\ImageStyle;
use Drupal\responsive_image\Entity\ResponsiveImageStyle;

/**
 * Class DrupEntityImage
 *
 * @package Drupal\drup
 */
class DrupEntityImage extends DrupEntityMedia {

    /**
     * DrupEntityImage constructor.
     *
     * @param $medias
     * @param null $fileField
     */
    public function __construct($medias, $fileField = null) {
        $this->type = 'image';
        parent::__construct($medias, $fileField);
    }

    /**
     * @param $style
     * @param array $attributes
     *
     * @return array
     */
    public function renderMedias($style, $attributes = []) {
        $medias = [];

        if (!empty($this->mediasData)) {
            foreach ($this->mediasData as $index => $media) {
                $medias[] = $this->renderMedia($style, $index, $attributes);
            }
        }

        return $medias;
    }

    /**
     * @param $style
     *
     * @return array
     */
    public function getMediasUrl($style) {
        $urls = [];

        foreach ($this->mediasData as $index => $media) {
            $urls[] = $this->getMediaUrl($style, $index);
        }

        return $urls;
    }

    /**
     * @param $style
     * @param array $attributes
     *
     * @return array
     */
    public function getMediasData($style, $attributes = []) {
        $data = [];

        foreach ($this->mediasData as $index => $media) {
            $data[] = $this->getMediaData($style, $index, $attributes);
        }

        return $data;
    }


    /**
     * Render an image with simple image style, responsive image style or as
     * original
     *
     * @param $style
     * @param int $index
     * @param array $attributes
     *
     * @return bool
     */
    protected function renderMedia($style, $index = 0, $attributes = []) {
        if (isset($this->mediasData[$index]) && ($fileUri = $this->mediasData[$index]->fileEntity->getFileUri())) {

            // Check if image still exists
            $image = \Drupal::service('image.factory')->get($fileUri);
            if (!$image->isValid()) {
                return false;
            }

            $rendererOptions = [
                '#uri' => $fileUri,
                '#attributes' => [
                    'alt' => $this->mediasData[$index]->fileReferenced->get('alt')
                        ->getString(),
                ],
                '#width' => $image->getWidth(),
                '#height' => $image->getHeight(),
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
                    \Drupal::messenger()
                        ->addMessage('Le style d\'image ' . $style . ' n\'existe pas', 'error');
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
            $renderer->addCacheableDependency($rendererOptions, $this->mediasData[$index]->fileEntity);

            return $renderer->render($rendererOptions);
        }

        return false;
    }

    /**
     * @param $style
     * @param int $index
     *
     * @return bool|string
     */
    protected function getMediaUrl($style, $index = 0) {
        $croppedUrl = false;

        if (!empty($this->mediasData)) {
            $fileUri = $this->mediasData[$index]->fileEntity->getFileUri();

            if ($fileUri) {
                if ($style !== null) {
                    if ($imageStyle = $this->getImageStyleEntity($style)) {
                        $croppedUrl = $imageStyle->buildUrl($fileUri);
                    }
                    else {
                        \Drupal::messenger()
                            ->addMessage('Le style d\'image ' . $style . ' n\'existe pas', 'error');
                    }
                }
                else {
                    $croppedUrl = file_create_url($fileUri);
                }
            }
        }

        return $croppedUrl;
    }

    /**
     * @param $style
     * @param int $index
     * @param array $attributes
     *
     * @return array
     */
    protected function getMediaData($style, $index = 0, $attributes = []) {
        $data = [];

        if (isset($this->mediasData[$index]) && ($fileUri = $this->mediasData[$index]->fileEntity->getFileUri())) {
            $image = \Drupal::service('image.factory')->get($fileUri);
            if (!$image->isValid()) {
                return $data;
            }

            $data = [
                'url' => $this->getMediaUrl($style, $index),
                'alt' => $this->mediasData[$index]->fileReferenced->get('alt')
                    ->getString(),
                'title' => $this->mediasData[$index]->fileReferenced->get('title')
                    ->getString(),
                'name' => $this->mediasData[$index]->mediaEntity->getName(),
            ];
        }

        return $data;
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

        if (($allowResponsiveImageStyle === true) && ($responsiveImageStyle = ResponsiveImageStyle::load($style))) {
            if ($responsiveImageStyle instanceof ResponsiveImageStyle) {
                return $responsiveImageStyle;
            }
        }
        return null;
    }
}
