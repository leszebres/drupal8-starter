<?php

namespace Drupal\drup\CookieNotice;

/**
 * Class Config
 *
 * @package CookieNotice
 */
abstract class Config
{
    /**
     * Configuration de CookieNotice
     *
     * Pour ne pas activer la personnalisation des serices, supprimez la configuration 'modal'
     * @return array
     */
    public static function set()
    {
        return [
            // Configuration du bandeau notice
            'notice' => [
                // Contenu de la notice
                'description' => 'En poursuivant votre navigation sur ce site, vous acceptez l\'utilisation de cookies ou technologies similaires de traçage permettant de vous offrir la meilleure expérience de navigation : conserver vos préférences, établir des statistiques de fréquentation, vous proposer des offres et des contenus adaptés à vos centres d\'intérêt y compris de partenaires tiers. Pour en savoir plus et paramétrer les cookies, <a href="/politique-de-confidentialite">consultez notre politique de confidentialité</a>.',
                // Résumé de la notice affichée en version mobile
                'summary' => 'En poursuivant votre navigation sur ce site, vous acceptez l\'utilisation de cookies... (voir plus)',
                // Label du bouton pour personnaliser les services
                'customize' => 'Personnaliser',
                // Label du bouton pour accepter tous les services
                'agree' => 'Ok, j\'accepte'
            ],
            // Configuration de la popup pour personnaliser les services. Commentez cette partie pour ne pas autoriser la personnalisation des services
            'modal' => [
                // Nom de la popup
                'label' => 'Gestion de vos préférences sur les cookies',
                // Description de la popup (optionnel)
                'description' => 'En autorisant ces services tiers, vous acceptez le dépôt et la lecture de cookies et l\'utilisation de technologies de suivi nécessaires à leur bon fonctionnement.',
                // Nom du bouton de fermeture
                'close' => 'Fermer'
            ],
            // Liste des groupes de services
            'groups' => [
                'advertising' => [
                    'label' => 'Régie publicitaire'
                ],
                'analytics' => [
                    'label' => 'Mesure d\'audience',
                    'description' => 'Les services de mesure d\'audience permettent de générer des statistiques de fréquentation utiles à l\'amélioration du site.'
                ],
                'api' => [
                    'label' => 'APIs'
                ],
                'comment' => [
                    'label' => 'Commentaire'
                ],
                'social' => [
                    'label' => 'Réseaux sociaux',
                    'description' => 'Les réseaux sociaux permettent d\'améliorer la convivialité du site et aident à sa promotion via les partages.'
                ],
                'support' => [
                    'label' => 'Support'
                ],
                'video' => [
                    'label' => 'Vidéo',
                    'description' => 'Les services de partage de vidéo permettent d\'enrichir le site de contenu multimédia et augmentent sa visibilité.'
                ]
            ],
            // Liste des services associés aux groupes
            'services' => [
                // TOUS
                'all' => [
                    // Nom du service
                    'label' => 'Préférences pour tous les services',
                    // Label du bouton "autoriser"
                    'agree' => 'Autoriser',
                    // Label du bouton "interdire"
                    'disagree' => 'Interdire'
                ],
                
                // ANALYTICS
//                'analytics' => [
//                    'label' => 'Google Analytics',
//                    'url' => 'https://support.google.com/analytics/answer/6004245',
//                    'group' => 'analytics'
//                ],
                'googletagmanager' => [
                    'label' => 'Google Tag Manager',
                    'url' => 'https://www.google.com/analytics/tag-manager/use-policy',
                    'group' => 'analytics'
                ],
                
                // APIs
                'googlemaps' => [
                    'label' => 'Google Maps',
                    'url' => 'https://developers.google.com/maps/terms',
                    'group' => 'api'
                ],
                'googleplaceautocomplete' => [
                    'label' => 'Place Autocomplete',
                    'url' => 'https://developers.google.com/maps/terms',
                    'group' => 'api'
                ],
//                'recaptcha' => [
//                    'label' => 'reCAPTCHA',
//                    'url' => 'https://policies.google.com/privacy',
//                    'group' => 'api'
//                ],
                
                // SOCIAL
//                'facebook' => [
//                    'label' => 'Facebook',
//                    'url' => 'https://www.facebook.com/policies/cookies',
//                    'group' => 'social'
//                ],
//                'twitter' => [
//                    'label' => 'Twitter',
//                    'url' => 'https://help.twitter.com/fr/rules-and-policies/twitter-cookies',
//                    'group' => 'social'
//                ],
//                'linkedin' => [
//                    'label' => 'LinkedIn',
//                    'url' => 'https://www.linkedin.com/legal/cookie_policy',
//                    'group' => 'social'
//                ],
                
                // VIDEOS
                'youtube' => [
                    'label' => 'YouTube',
                    'url' => 'https://policies.google.com/privacy',
                    'group' => 'video'
                ],
                'vimeo' => [
                    'label' => 'Vimeo',
                    'url' => 'https://vimeo.com/privacy',
                    'group' => 'video'
                ]
            ]
        ];
    }
    
    /**
     * Récupération de la configuration en string JSON
     *
     * @return string
     */
    public static function get()
    {
        return htmlspecialchars(json_encode(self::set()));
    }
}
