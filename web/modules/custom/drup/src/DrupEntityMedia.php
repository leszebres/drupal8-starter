<?php

namespace Drupal\drup;

use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;

/**
 * Class DrupEntityMedia.
 */
class DrupEntityMedia {
    
    /**
     * Media entities list
     * @var array
     */
    public $mediasList;
    
    /**
     * Datas for each medias
     * @var
     */
    public $mediasDatas;
    
    /**
     * Media type (ex : Image or File)
     * @var
     */
    public $type;
    
    /**
     * Media entity field representing File entity
     * @var
     */
    public $filesField;
    
    /**
     * Current language id
     * @var string
     */
    public $langcode;
    
    /**
     * DrupEntityMedia constructor.
     *
     * @param $medias array/int of Media id(s) or Media entity(es)
     * @param $fileField
     *
     * @throws \Drupal\Core\TypedData\Exception\MissingDataException
     */
    public function __construct($medias, $fileField = null) {
        $this->langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
        
        $this->mediasList = $this->formatMedias($medias);
        $this->filesField = $this->formatFieldName($fileField);
        
        $this->mediasDatas = $this->getDatas();
    }
    
    /**
     * Standardize media sources into array of media entities
     * @param $medias
     */
    protected function formatMedias($medias) {
        $entities = [];
        
        if (!is_array($medias)) {
            $medias = [$medias];
        }
        
        foreach ($medias as $media) {
            $entity = ($media instanceof Media) ? $media : self::loadMedia($media);
            if ($entity !== null) {
                $entities[] = \Drupal::service('entity.repository')->getTranslationFromContext($entity, $this->langcode);
            }
        }
        
        return $entities;
    }
    
    /**
     * Get Media's file entities
     * @return array|object
     */
    protected function getDatas() {
        $datas = [];
        
        if (!empty($this->mediasList)) {
            foreach ($this->mediasList as $mediaEntity) {
                
                if ($mediaEntity->hasField($this->filesField)) {
                    $fileReferenced = $mediaEntity->get($this->filesField)->first();
                    
                    if (!$fileReferenced->isEmpty() && ($fileDatas = $fileReferenced->getValue())) {
                        if (isset($fileDatas['target_id']) && ($fileEntity = File::load($fileDatas['target_id']))) {
                            $datas[] = (object) [
                                'mediaEntity' => $mediaEntity,
                                'fileEntity' => $fileEntity,
                                'fileReferenced' => $fileReferenced
                            ];
                        }
                    }
                }
            }
        }
        
        return $datas;
    }
    
    /**
     * @param $mid
     * @return \Drupal\Core\Entity\EntityInterface|Media|null
     */
    protected function loadMedia($mid) {
        if ($mediaEntity = Media::load($mid)) {
            if ($mediaEntity instanceof Media) {
                return $mediaEntity;
            }
        }
        
        return null;
    }
    
    /**
     * @param $fid
     * @return \Drupal\Core\Entity\EntityInterface|Media|null
     */
    protected function loadFile($fid) {
        if ($fileEntity = Media::load($fid)) {
            if ($fileEntity instanceof File) {
                return $fileEntity;
            }
        }
        
        return null;
    }
    
    /**
     * @return string
     */
    protected function formatFieldName($fieldName) {
        return ($fieldName !== null) ? $fieldName : 'field_media_' . $this->type;
    }
}
