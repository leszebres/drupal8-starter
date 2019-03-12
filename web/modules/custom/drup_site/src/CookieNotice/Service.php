<?php
    
    namespace Drupal\drup_site\CookieNotice;
    
    /**
     * Class Service
     *
     * @package CookieNotice
     */
    abstract class Service
    {
        
        /**
         * Détermine si un service est autorisé par l'utilisateur
         *
         * @param $service
         */
        public static function isAllowed($service)
        {
            return (self::getState($service) === true);
        }
        
        /**
         * Retourne l'état du service. Si le choix n'a pas été fait, l'état retourné est "undefined"
         *
         * @param $service
         */
        public static function getState($service)
        {
            if (self::hasConsent()) {
                $services = json_decode($_COOKIE['cookienotice']);
                
                if (isset($services->{$service})) {
                    return $services->{$service};
                }
            }
            
            return 'undefined';
        }
        
        /**
         * Détermine si il y a eu un consentement (accepté ou non)
         *
         * @return boolean
         */
        public static function hasConsent()
        {
            return (isset($_COOKIE['cookienotice']));
        }
    }
