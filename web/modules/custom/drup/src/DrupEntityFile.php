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
         * @param $fieldDatas
         */
        public function __construct($fieldDatas) {
            $this->mediaType = 'file';
            parent::__construct($fieldDatas);
        }
        
        /**
         * @param $style
         * @param int $index
         */
        public function renderMedia($index = 0, $attributes = []) {
            $datas = [];
            
            if ($url = $this->getMediaUri($index)) {
                $datas = [
                    'url' => $url,
                    'size' => format_size($this->medias[$index]->fileEntity->getSize())->__toString(),
                    'mime' => explode('/', $this->medias[$index]->fileEntity->getMimeType())[1],
                    'name' => $this->medias[$index]->fileEntity->getFilename(),
                    'title' => $this->medias[$index]->mediaEntity->getName(),
                ];
            }
            
            return $datas;
        }
        
        /**
         * @param $style
         */
        public function renderMedias() {
            $medias = [];
            
            foreach ($this->medias as $index => $media) {
                $medias[] = $this->renderMedia($index);
            }
            
            return $medias;
        }
        
        /**
         * @param $style
         * @param int $index
         *
         * @return bool
         */
        public function getMediaUri($index = 0) {
            if (!empty($this->medias[$index]) && ($fileUri = $this->medias[$index]->fileEntity->getFileUri())) {
                return file_create_url($fileUri);
            }
            
            return FALSE;
        }
        
        /**
         * @param $style
         *
         * @return array
         */
        public function getMediasUri() {
            $uris = [];
            
            foreach ($this->medias as $index => $media) {
                $uris[] = $this->getMediaUri($index);
            }
            
            return $uris;
        }
    }
