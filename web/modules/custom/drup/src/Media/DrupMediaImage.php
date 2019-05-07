<?php

namespace Drupal\drup\Media;

use Drupal\drup_settings\DrupSettings;
use Drupal\media\Entity\Media;

/**
 * Class DrupMediaImage
 *
 * @package Drupal\drup\Media
 */
class DrupMediaImage extends DrupMedia {

    /**
     * DrupMediaImage constructor.
     *
     * @param int|array|Media|Media[] $medias
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
     *
     * @return array
     */
    public function getMediasData($style) {
        $data = [];

        foreach ($this->mediasData as $index => $media) {
            $data[] = $this->getMediaData($style, $index);
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
        if (isset($this->mediasData[$index])) {
            $drupFileImage = new DrupFile($this->mediasData[$index]->fileEntity);

            $attributes = array_merge([
                'alt' => $this->mediasData[$index]->fileReferenced->get('alt')->getString(),
            ], $attributes);

            return $drupFileImage->renderMedia($style, $attributes);
        }

        return false;
    }

    /**
     * @param $style
     * @param int $index
     *
     * @return bool|\Drupal\Core\GeneratedUrl|string|null
     */
    protected function getMediaUrl($style, $index = 0) {
        if (isset($this->mediasData[$index])) {
            $drupFileImage = new DrupFile($this->mediasData[$index]->fileEntity);
            return $drupFileImage->getMediaUrl($style);
        }

        return false;
    }

    /**
     * @param $style
     * @param int $index
     *
     * @return array
     */
    protected function getMediaData($style, $index = 0) {
        $data = [];

        if (isset($this->mediasData[$index])) {
            $drupFileImage = new DrupFile($this->mediasData[$index]->fileEntity);

            if ($drupFileImage->isValid()) {
                $data = [
                    'url' => $drupFileImage->getMediaUrl($style),
                    'alt' => $this->mediasData[$index]->fileReferenced->get('alt')
                        ->getString(),
                    'title' => $this->mediasData[$index]->fileReferenced->get('title')
                        ->getString(),
                    'name' => $this->mediasData[$index]->mediaEntity->getName(),
                ];
            }
        }

        return $data;
    }

    /**
     * Récupère le mid de l'image par défaut dans les listes
     *
     * @return int|null
     */
    public static function getFallbackId() {
        $drupSettings = new DrupSettings();
        $drupSettings->setNeutralLang();

        if ($mediaId = $drupSettings->getValue('default_list_image')) {
            return (int) $mediaId;
        }

        return null;
    }
}
