# Mises à jour du CMS et dépendances

## Installer un module

composer require drupal/monmodule:version
drush updatedb
drush cr

## Mettre à jour

Note : Ne pas oublier de mettre à jour les traductions

### Mettre à jour un module
 
composer update
drush updatedb
drush cr

### Mettre à jour Drupal

Changer la version dans le fichier composer.json

composer update
drush updatedb
drush cr
