<?php

namespace Drupal\drup_site;

use Drupal\Core\Render\Markup;
use Drupal\drup\DrupSite;

/**
 * Class DrupSiteForm
 *
 * @package Drupal\drup_site
 */
class DrupSiteForm {

    /**
     * @param string $type
     *
     * @return \Drupal\Component\Render\MarkupInterface|string
     */
    public static function getGDPRMention($type = 'contact')
    {
        $drupRouter = \Drupal::service('drup_router.router');

        $text = t('Les informations suivies d\'un astérisque (*) sont nécessaires au traitement de votre demande et sont destinées uniquement à l\'entreprise.');

        switch ($type) {
            default:
                $text .= t('Vous disposez d\'un droit d\'accès, de rectification, et de suppression de vos données, que vous pouvez exercer en adressant une demande accompagnée d\'un justificatif d\'identité par e-mail à <a href="mailto:@email">@email</a> ou par courrier postal à @address. Pour plus d\'informations concernant vos données personnelles, n\'hésitez pas à consulter notre <a href="@url" target="_blank">Politique de confidentialité</a>.', [
                    '@email' => 'email@email.fr',
                    '@address' => 'Addresse',
                    '@url' => $drupRouter->getPath('legal-terms')
                ]);
                break;
        }

        return Markup::create('<p>' . $text . '</p>');
    }

    /**
     * @param $form
     * @param string $type
     */
    public static function setGDPRMention(&$form, $type = 'contact')
    {
        $form['gdpr_infos'] = [
            '#type' => 'container',
            '#weight' => 49
        ];
        $form['gdpr_infos']['#suffix'] = '<div class="form-legal-info">';
        $form['gdpr_infos']['#suffix'] .= self::getGDPRMention($type);
        $form['gdpr_infos']['#suffix'] .= '</div>';
    }
}