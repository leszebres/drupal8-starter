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
     *
     * @var array
     */
    public $mediasList;

    /**
     * Data for each medias
     *
     * @var
     */
    public $mediasData;

    /**
     * Media type (ex : Image or File)
     *
     * @var
     */
    public $type;

    /**
     * Media entity field representing File entity
     *
     * @var
     */
    public $filesField;

    /**
     * Current language id
     *
     * @var string
     */
    public $langcode;

    /**
     * DrupEntityMedia constructor.
     *
     * @param $medias
     * @param null $fileField
     */
    public function __construct($medias, $fileField = null) {
        $this->langcode = \Drupal::languageManager()
            ->getCurrentLanguage()
            ->getId();

        $this->mediasList = $this->formatMedias($medias);
        $this->filesField = $this->formatFieldName($fileField);

        $this->mediasData = $this->getData();
    }

    /**
     * Standardize media sources into array of media entities
     * @param $medias
     *
     * @return array
     */
    protected function formatMedias($medias) {
        $entities = [];

        if (!is_array($medias)) {
            $medias = [$medias];
        }

        foreach ($medias as $media) {
            $entity = ($media instanceof Media) ? $media : $this->loadMedia($media);
            if ($entity !== null) {
                $entities[] = \Drupal::service('entity.repository')
                    ->getTranslationFromContext($entity, $this->langcode);
            }
        }

        return $entities;
    }

    /**
     * Get Media's file entities
     * @return array
     */
    protected function getData() {
        $data = [];

        if (!empty($this->mediasList)) {
            foreach ($this->mediasList as $mediaEntity) {

                if ($mediaEntity->hasField($this->filesField)) {
                    $fileReferenced = $mediaEntity->get($this->filesField)
                        ->first();

                    if (!$fileReferenced->isEmpty() && ($fileData = $fileReferenced->getValue())) {
                        if (isset($fileData['target_id']) && ($fileEntity = File::load($fileData['target_id']))) {
                            $data[] = (object) [
                                'mediaEntity' => $mediaEntity,
                                'fileEntity' => $fileEntity,
                                'fileReferenced' => $fileReferenced,
                            ];
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @param $mid
     *
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
     *
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
