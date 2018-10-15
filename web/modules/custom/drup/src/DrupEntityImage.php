<?php
    
    namespace Drupal\drup;
    
    use Drupal\image\Entity\ImageStyle;
    
    /**
     * Class DrupEntityImage
     *
     * @package Drupal\drup
     */
    class DrupEntityImage extends DrupEntityMedia {
        
        /**
         * DrupEntityImage constructor.
         *
         * @param $fieldDatas
         */
        public function __construct($fieldDatas) {
            $this->mediaType = 'image';
            parent::__construct($fieldDatas);
        }
        
        /**
         * @param $style
         * @param int $index
         */
        public function renderMedia($style, $index = 0, $attributes = []) {
            if (!empty($this->medias)) {
                if (isset($this->medias[$index]) && ($fileUri = $this->medias[$index]->fileEntity->getFileUri())) {
                    
                    // Check if image still exists
                    $image = \Drupal::service('image.factory')->get($fileUri);
                    if (!$image->isValid()) {
                        return false;
                    }
                    
                    $rendererOptions = [
                        '#uri' => $fileUri,
                        '#attributes' => [
                            'alt' => $this->medias[$index]->fileReferenced->get('alt')->getString(),
                            'title' => $this->medias[$index]->mediaEntity->getName()
                        ],
                        '#width' => $image->getWidth(),
                        '#height' => $image->getHeight()
                    ];
                    
                    // Render as image style
                    if ($style !== null) {
                        if ($imageStyle = $this->checkImageStyle($style)) {
                            $rendererOptions += [
                                '#theme' => 'image_style',
                                '#style_name' => $style
                            ];
                        } else {
                            \Drupal::messenger()
                                ->addMessage('Le style d\'image ' . $style . ' n\'existe pas', 'error');
                            return false;
                        }
                    }
                    // Render original image
                    else {
                        $rendererOptions += [
                            '#theme' => 'image'
                        ];
                    }
                    
                    if (!empty($attributes)) {
                        $rendererOptions['#attributes'] = array_merge_recursive($rendererOptions['#attributes'], $attributes);
                    }
                    
                    $renderer = \Drupal::service('renderer');
                    return $renderer->render($rendererOptions);
                }
            }
            
            return false;
        }
        
        /**
         * @param $style
         */
        public function renderMedias($style) {
            $medias = [];
            
            if (!empty($this->medias)) {
                foreach ($this->medias as $index => $media) {
                    $medias[] = $this->renderMedia($style, $index);
                }
            }
            
            return $medias;
        }
        
        /**
         * @param $style
         * @param int $index
         *
         * @return bool
         */
        public function getMediaUri($style, $index = 0) {
            $croppedUri = false;
            
            if (!empty($this->medias)) {
                $fileUri = $this->medias[$index]->fileEntity->getFileUri();
                
                if ($fileUri) {
                    if ($style !== null) {
                        if ($imageStyle = $this->checkImageStyle($style)) {
                            $croppedUri = $imageStyle->buildUrl($fileUri);
                        } else {
                            \Drupal::messenger()->addMessage('Le style d\'image ' . $style . ' n\'existe pas', 'error');
                        }
                    } else {
                        $croppedUri = file_create_url($fileUri);
                    }
                }
            }
            
            return $croppedUri;
        }
        
        /**
         * @param $style
         *
         * @return array
         */
        public function getMediasUri($style) {
            $uris = [];
            
            foreach ($this->medias as $index => $media) {
                $uris[] = $this->getMediaUri($style, $index);
            }
            
            return $uris;
        }
        
        /**
         * @param $style
         * @param int $index
         */
        public function getMediaDatas($style, $index = 0, $attributes = []) {
            $datas = [];
            if (isset($this->medias[$index]) && ($fileUri = $this->medias[$index]->fileEntity->getFileUri())) {
                $datas = [
                    'url' => $this->getMediaUri($style, $index),
                    'alt' => $this->medias[$index]->fileReferenced->get('alt')
                        ->getString(),
                    'title' => $this->medias[$index]->fileReferenced->get('title')
                        ->getString(),
                    'name' => $this->medias[$index]->mediaEntity->getName(),
                ];
            }
            
            return $datas;
        }
        
        /**
         * @param $style
         *
         * @return array
         */
        public function getMediasDatas($style) {
            $datas = [];
            
            foreach ($this->medias as $index => $media) {
                $datas[] = $this->getMediaDatas($style, $index);
            }
            
            return $datas;
        }
        
        /**
         * @param $imageStyle
         *
         * @return bool
         */
        protected function checkImageStyle($style) {
            $imageStyle = ImageStyle::load($style);
            return ($imageStyle instanceof ImageStyle) ? $imageStyle : NULL;
        }
    }
