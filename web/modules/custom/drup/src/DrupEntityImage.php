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
     * @param $medias
     * @param null $fileField
     * @throws \Drupal\Core\TypedData\Exception\MissingDataException
     */
    public function __construct($medias, $fileField = null) {
        $this->type = 'image';
        parent::__construct($medias, $fileField);
    }
    
    /**
     * @param $style
     * @param array $attributes
     * @return array
     */
    public function renderMedias($style, $attributes = []) {
        $medias = [];
        
        if (!empty($this->mediasDatas)) {
            foreach ($this->mediasDatas as $index => $media) {
                $medias[] = $this->renderMedia($style, $index, $attributes);
            }
        }
        
        return $medias;
    }
    
    /**
     * @param $style
     * @return array
     */
    public function getMediasUrl($style) {
        $urls = [];
        
        foreach ($this->mediasDatas as $index => $media) {
            $urls[] = $this->getMediaUrl($style, $index);
        }
        
        return $urls;
    }
    
    /**
     * @param $style
     * @param array $attributes
     * @return array
     */
    public function getMediasDatas($style, $attributes = []) {
        $datas = [];
        
        foreach ($this->mediasDatas as $index => $media) {
            $datas[] = $this->getMediaDatas($style, $index, $attributes);
        }
        
        return $datas;
    }
    
    
    /**
     * @param $style
     * @param int $index
     * @param array $attributes
     * @return bool
     */
    protected function renderMedia($style, $index = 0, $attributes = []) {
        if (isset($this->mediasDatas[$index]) && ($fileUri = $this->mediasDatas[$index]->fileEntity->getFileUri())) {
            
            // Check if image still exists
            $image = \Drupal::service('image.factory')->get($fileUri);
            if (!$image->isValid()) {
                return false;
            }
            
            $rendererOptions = [
                '#uri' => $fileUri,
                '#attributes' => [
                    'alt' => $this->mediasDatas[$index]->fileReferenced->get('alt')->getString(),
                    'title' => $this->mediasDatas[$index]->mediaEntity->getName()
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
        
        return false;
    }
    
    /**
     * @param $style
     * @param int $index
     * @return bool|string
     */
    protected function getMediaUrl($style, $index = 0) {
        $croppedUrl = false;
        
        if (!empty($this->mediasDatas)) {
            $fileUri = $this->mediasDatas[$index]->fileEntity->getFileUri();
            
            if ($fileUri) {
                if ($style !== null) {
                    if ($imageStyle = $this->checkImageStyle($style)) {
                        $croppedUrl = $imageStyle->buildUrl($fileUri);
                    } else {
                        \Drupal::messenger()->addMessage('Le style d\'image ' . $style . ' n\'existe pas', 'error');
                    }
                } else {
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
     * @return array
     */
    protected function getMediaDatas($style, $index = 0, $attributes = []) {
        $datas = [];
        
        if (isset($this->mediasDatas[$index]) && ($fileUri = $this->mediasDatas[$index]->fileEntity->getFileUri())) {
            $image = \Drupal::service('image.factory')->get($fileUri);
            if (!$image->isValid()) {
                return false;
            }
            
            $datas = [
                'url' => $this->getMediaUrl($style, $index),
                'alt' => $this->mediasDatas[$index]->fileReferenced->get('alt')
                    ->getString(),
                'title' => $this->mediasDatas[$index]->fileReferenced->get('title')
                    ->getString(),
                'name' => $this->mediasDatas[$index]->mediaEntity->getName()
            ];
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
