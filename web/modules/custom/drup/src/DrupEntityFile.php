<?php

namespace Drupal\drup;

/**
 * Class DrupEntityFile
 *
 * @package Drupal\drup
 */
class DrupEntityFile extends DrupEntityMedia {

    /**
     * DrupEntityFile constructor.
     *
     * @param $medias
     * @param null $fileField
     */
    public function __construct($medias, $fileField = null) {
        $this->type = 'file';
        parent::__construct($medias, $fileField);
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    public function getMediasData($attributes = []) {
        $medias = [];

        foreach ($this->mediasData as $index => $media) {
            $medias[] = $this->getMediaData($index, $attributes);
        }

        return $medias;
    }

    /**
     * @return array
     */
    public function getMediasUrl() {
        $urls = [];

        foreach ($this->mediasData as $index => $media) {
            $urls[] = $this->getMediaUrl($index);
        }

        return $urls;
    }


    /**
     * @param int $index
     * @param array $attributes
     *
     * @return array
     */
    protected function getMediaData($index = 0, $attributes = []) {
        $data = [];

        if ($url = $this->getMediaUrl($index)) {
            $data = [
                'url' => $url,
                'size' => format_size($this->mediasData[$index]->fileEntity->getSize())->__toString(),
                'mime' => explode('/', $this->mediasData[$index]->fileEntity->getMimeType())[1],
                'name' => $this->mediasData[$index]->fileEntity->getFilename(),
                'title' => $this->mediasData[$index]->mediaEntity->getName(),
            ];
        }

        return $data;
    }

    /**
     * @param int $index
     *
     * @return bool|string
     */
    protected function getMediaUrl($index = 0) {
        if (!empty($this->mediasData[$index]) && ($fileUri = $this->mediasData[$index]->fileEntity->getFileUri())) {
            return file_create_url($fileUri);
        }

        return false;
    }
}
