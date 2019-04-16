<?php
    
    namespace Drupal\drup_site\CookieNotice;
    
    /**
     * Class Config
     *
     * @package CookieNotice
     */
    abstract class Config {
        
        /**
         * Configuration de CookieNotice
         *
         * Pour ne pas activer la personnalisation des serices, supprimez la
         * configuration 'modal'
         *
         * @return array
         */
        public static function set() {
            $drupRouter = \Drupal::service('drup_router');
            
            return [
                // Configuration du bandeau notice
                'notice' => [
                    // Contenu de la notice
                    'description' => t('By continuing your visit to this site, you accept the use of cookies or similar tracing technologies to offer you the best browsing experience: keep your preferences, establish attendance statistics, offer you offers and content tailored to your needs. interests including third party partners. To learn more and set cookies, read our <a href="@link">privacy policy</a>.', ['@link' => $drupRouter->getPath('legal-terms')]),
                    // Résumé de la notice affichée en version mobile
                    'summary' => t('By continuing your visit to this site, you accept the use of cookies... (see more)'),
                    // Label du bouton pour personnaliser les services
                    'customize' => t('Personnaliser'),
                    // Label du bouton pour accepter tous les services
                    'agree' => t('I understand'),
                ],
            ];
        }
        
        /**
         * Récupération de la configuration en string JSON
         *
         * @return string
         */
        public static function get() {
            return htmlspecialchars(json_encode(self::set()));
        }
    }
