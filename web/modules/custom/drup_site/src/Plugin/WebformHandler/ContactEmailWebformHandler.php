<?php

namespace Drupal\drup\Plugin\WebformHandler;

use Drupal\webform\Plugin\WebformHandler\EmailWebformHandler;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Emails a webform submission.
 *
 * @WebformHandler(
 *   id = "email_form_select",
 *   label = @Translation("CUSTOM : Dynamic recipient"),
 *   category = @Translation("Notification"),
 *   description = @Translation("Email d'envoi différente selon la clé d'un select du form"),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 * )
 */
class ContactEmailWebformHandler extends EmailWebformHandler {
    
    /**
     * @param \Drupal\webform\WebformSubmissionInterface $webform_submission
     * @param array $message
     */
    public function sendMessage(WebformSubmissionInterface $webform_submission, array $message) {
    
        /**
         * @see /admin/structure/webform/manage/nous_contacter/handlers
         */
        
        
//        $message['to_mail'] = $recipient;
        
//        echo '<pre>';
//        var_dump($message);
//        echo '</pre>';
//        die;
        
        parent::sendMessage($webform_submission, $message);
    }
}
