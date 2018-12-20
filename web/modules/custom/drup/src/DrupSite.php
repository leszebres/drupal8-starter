<?php

namespace Drupal\drup;

use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\drup_settings\DrupSettings;

/**
 * Class DrupSite
 *
 * @package Drupal\drup
 */
class DrupSite {

    public static $publicPathLogos = 'public://logos/';
    public static $publicPathIcons = 'public://icons/';
    public static $publicPathOthers = 'public://others/';

    /**
     * @return \Drupal\Component\Render\FormattableMarkup
     */
    public static function get404Content() {
        $drupRouter = \Drupal::service('drup_router.router');

        $content404 = '<h2 class="title--h3">' . t('You may have followed a broken link, or tried to view a page that no longer exists.') . '</h2>';
        if ($contact = $drupRouter->getPath('contact')) {
            $content404 .= '<p>' . t('If the problem persists, <a href="%link">contact us</a>.', ['%link' => $contact]) . '</p>';
        }
        $content404 .= '<p><a href="' . Url::fromRoute('<front>')
                ->toString() . '" class="btn btn--primary">' . t('Back to the front page') . '</a></p>';

        return new \Drupal\Component\Render\FormattableMarkup($content404, []);
    }

    /**
     * @param bool $forceLoad
     *
     * @return array
     */
    public static function getSocialLinks($forceLoad = true) {
        $socialNetworks = ['facebook', 'twitter', 'linkedin', 'youtube'];
        $drupSettings = new DrupSettings();

        $links = [];
        foreach ($socialNetworks as $socialNetwork) {
            $url = $drupSettings->getValue('site_' . $socialNetwork);

            if ($forceLoad === false && empty($url)) {
                continue;
            }

            $links[$socialNetwork] = [
                'url' => $url,
                'title' => ucfirst($socialNetwork),
            ];
        }

        return $links;
    }

    /**
     * @return array
     */
    public static function getShareItems() {
        $config = \Drupal::config('system.site');
        $request = \Drupal::request();
        $route_match = \Drupal::routeMatch();

        $title = $config->get('name') . ' : ' . \Drupal::service('title_resolver')
                ->getTitle($request, $route_match->getRouteObject());
        $currentTitle = urlencode($title);
        $pathAlias = \Drupal::service('path.alias_manager')
            ->getAliasByPath($request->getPathInfo());
        $currentUrl = urlencode($request->getSchemeAndHttpHost() . $request->getBaseUrl() . $pathAlias);

        return [
            'linkedin' => [
                'url' => 'https://www.linkedin.com/shareArticle?url=' . $currentUrl . '&title=' . $currentTitle,
                'icon' => 'linkedin',
            ],
            'twitter' => [
                'url' => 'https://twitter.com/share?url=' . $currentUrl . '&text=' . $currentTitle,
                'icon' => 'twitter',
            ],
            'facebook' => [
                'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . $currentUrl . '&t=' . $currentTitle,
                'icon' => 'facebook',
            ],
        ];
    }

    /**
     * @param string $type
     *
     * @return \Drupal\Component\Render\MarkupInterface|string
     */
    public static function getGDPRMention($type = 'contact') {
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
    public static function setGDPRMention(&$form, $type = 'contact') {
        $form['gdpr_infos'] = [
            '#type' => 'container',
            '#weight' => 49
        ];
        $form['gdpr_infos']['#suffix'] = '<div class="form-legal-info">';
        $form['gdpr_infos']['#suffix'] .= self::getGDPRMention($type);
        $form['gdpr_infos']['#suffix'] .= '</div>';
    }
}
