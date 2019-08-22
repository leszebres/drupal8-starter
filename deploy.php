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
    'web/.htaccess',
    'web/sites/{{drupal_site}}/settings.php'
]);
add('shared_dirs', [
    'web/sites/{{drupal_site}}/files',
    'web/themes/custom/drup_theme/node_modules',
]);

add('writable_dirs', [
    //'web/sites/{{drupal_site}}/files'
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

desc('Load gulp dependencies');
task('deploy:node_modules', function () {
    run('cd {{release_path}}/web/themes/custom/drup_theme && yarn --production=true');
});

desc('Update database');
task('drush:updb', function () {
    run('php {{release_path}}/vendor/bin/drush updb -y');
});

desc('Revert feature_content');
task('drush:fr', function () {
    run('php {{release_path}}/vendor/bin/drush fr feature_content -y');
});


after('deploy:symlink', 'drush:fr');
after('drush:fr', 'drush:updb');
after('drush:updb', 'drush:langimp');
after('drush:langimp', 'drush:cr');


// Composer dependencies
after('deploy:shared', 'deploy:vendors');
after('deploy:vendors', 'deploy:node_modules');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
