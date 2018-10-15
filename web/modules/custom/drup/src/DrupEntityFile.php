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
     * @param $medias
     * @param null $fileField
     * @throws \Drupal\Core\TypedData\Exception\MissingDataException
     */
    public function __construct($medias, $fileField = null) {
        $this->type = 'file';
        parent::__construct($medias, $fileField);
    }
    
    /**
     * @param array $attributes
     * @return array
     */
    public function getMediasDatas($attributes = []) {
        $medias = [];
        
        foreach ($this->mediasDatas as $index => $media) {
            $medias[] = $this->getMediaDatas($index, $attributes);
        }
        
        return $medias;
    }
    
    /**
     * @return array
     */
    public function getMediasUrl() {
        $urls = [];
        
        foreach ($this->mediasDatas as $index => $media) {
            $urls[] = $this->getMediaUrl($index);
        }
        
        return $urls;
    }
    
    
    /**
     * @param int $index
     * @param array $attributes
     * @return array
     */
    protected function getMediaDatas($index = 0, $attributes = []) {
        $datas = [];
        
        if ($url = $this->getMediaUrl($index)) {
            $datas = [
                'url' => $url,
                'size' => format_size($this->mediasDatas[$index]->fileEntity->getSize())->__toString(),
                'mime' => explode('/', $this->mediasDatas[$index]->fileEntity->getMimeType())[1],
                'name' => $this->mediasDatas[$index]->fileEntity->getFilename(),
                'title' => $this->mediasDatas[$index]->mediaEntity->getName(),
            ];
        }
        
        return $datas;
    }
    
    /**
     * @param int $index
     * @return bool|string
     */
    protected function getMediaUrl($index = 0) {
        if (!empty($this->mediasDatas[$index]) && ($fileUri = $this->mediasDatas[$index]->fileEntity->getFileUri())) {
            return file_create_url($fileUri);
        }
        
        return false;
    }
}
