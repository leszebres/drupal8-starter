<?php

/**
 * @inheritdoc
 */
function drup_query_alter(Drupal\Core\Database\Query\AlterableInterface $query) {
    if (isset($query->alterMetaData['view'])) {
//        if ($query->alterMetaData['view']->storage->id() === 'healthreview_article') {
//            if ($query->alterMetaData['view']->current_display === 'similar_content') {
//                $query->groupBy('node_field_data.nid');
//                $query->groupBy('node_field_data.langcode');
//            }
//        }
    }
}
