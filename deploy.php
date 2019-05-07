<?php

namespace Deployer;

require 'recipe/drupal8.php';


/**
 * SSH
 */
set('ssh_type', 'native');
set('ssh_multiplexing', true);


/**
 * GIT & DEPLOY
 */
set('repository', 'XXXXgit@gitlab.comXXXX');
set('default_stage', 'draft');


/**
 * REPOS
 */
set('drupal_site', 'default');

add('shared_files', [
    'web/sites/{{drupal_site}}/settings.php',
    'web/sites/{{drupal_site}}/services.yml'
]);
add('shared_dirs', [
    'web/sites/{{drupal_site}}/files'
]);

add('writable_dirs', [
    'web/sites/{{drupal_site}}/files'
]);


/**
 * TASKS
 */
host('draft')
    ->stage('draft')
    ->set('branch', 'develop')
    ->hostname('hostXXXX')
    ->user('www-sync')
    ->set('deploy_path', '/home/www/XXXX')
    ->set('writable_mode', 'chmod')
    ->forwardAgent();


/**
 * TASKS ADDITIONAL
 * @param type = Drupal
 */

desc('Clear cache with Drush');
task('drush:cr', function () {
    run('php {{release_path}}/vendor/bin/drush cr -y');
});

desc('Run translations');
task('drush:langimp', function () {
    run('php {{release_path}}/vendor/bin/drush langimp {{release_path}}/web/themes/custom/drup_theme/translations/drup_theme-8.x-1.0.fr.po');
});

desc('Update database');
task('drush:updb', function () {
    run('php {{release_path}}/vendor/bin/drush updb -y');
});

desc('Reload features');
task('drush:fra', function () {
    run('php {{release_path}}/vendor/bin/drush fra -y');
});


after('deploy:symlink', 'drush:updb');
after('drush:updb', 'drush:langimp');
//after('drush:updb', 'drush:fra');
after('drush:langimp', 'drush:cr');


// Composer dependencies
after('deploy:shared', 'deploy:vendors');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
