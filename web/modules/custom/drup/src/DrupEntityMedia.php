<?php

namespace Drupal\drup;

Use Drupal\media\Entity\Media;
Use Drupal\file\Entity\File;

/**
 * Class DrupEntityMedia.
 */
class DrupEntityMedia {
    
    public $mediaType;
    
    public $fieldDatas;
    
    public $fieldName;
    
    public $medias;
    
    
    /**
     * DrupEntityMedia constructor.
     *
     * @param $fieldDatas
     *
     * @throws \Drupal\Core\TypedData\Exception\MissingDataException
     */
    public function __construct($fieldDatas, $fieldName = NULL) {
        $this->fieldDatas = $fieldDatas;
        $this->fieldName = $this->formatFieldName($fieldName);
        $this->medias = $this->getFieldMedias();
    }
    
    /**
     * @return mixed
     */
    protected function getFieldDatas() {
        return $this->fieldDatas;
    }
    
    /**
     * @return array
     * @throws \Drupal\Core\TypedData\Exception\MissingDataException
     */
    protected function getFieldMedias() {
        $medias = [];
        $languageId = \Drupal::languageManager()->getCurrentLanguage()->getId();

        if (!empty($this->fieldDatas)) {
            foreach ($this->fieldDatas as $mediaReferenced) {
                if (is_int($mediaReferenced) || is_string($mediaReferenced)) {
                    $mediaEntity = Media::load($mediaReferenced);
                } else {
                    $mediaReferencedId = $mediaReferenced->getValue();
                    $mediaEntity = Media::load($mediaReferencedId['target_id']);
                }
                
                if (empty($mediaEntity)) {
                    return false;
                }
                
                $mediaEntity = \Drupal::service('entity.repository')->getTranslationFromContext($mediaEntity, $languageId);

                if ($mediaEntity instanceof Media) {
                    $fileReferenced = $mediaEntity->get($this->fieldName)->first();
                    $fileReferencedId = $fileReferenced->getValue();
                    $fileEntity = File::load($fileReferencedId['target_id']);
                    //$fileEntity = \Drupal::service('entity.repository')->getTranslationFromContext($fileEntity, $languageId);

                    if ($fileEntity instanceof File) {
                        $medias[] = (object) [
                            'mediaEntity' => $mediaEntity,
                            'fileEntity' => $fileEntity,
                            'fileReferenced' => $fileReferenced,
                        ];
                    }
                }
            }
        }

        return $medias;
    }
    
    /**
     * @return string
     */
    protected function formatFieldName($fieldName) {
        return ($fieldName !== null) ? $fieldName : 'field_media_' . $this->mediaType;
    }
}
