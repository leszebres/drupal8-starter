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
set('repository', 'XXGITURIXX');
set('default_stage', 'draft');


/**
 * REPOS
 */
set('drupal_site', 'default');

add('shared_files', [
    'web/sites/{{drupal_site}}/settings.php',
    'web/sites/{{drupal_site}}/services.yml',
]);
add('shared_dirs', [
    'web/sites/{{drupal_site}}/files',
    'web/vendor',
]);

add('writable_dirs', [
    "web/sites/{{drupal_site}}/files"
]);


/**
 * TASKS
 */
server('draft', 'XXDRAFTXX')
    ->user('www-sync')
    ->forwardAgent()
    ->set('branch', 'develop')
    ->stage('draft')
    ->set('deploy_path', 'XXPATHXX');


/**
 * TASKS ADDITIONAL
 * @param type = Drupal
 */

desc('Clear cache with Drush');
task('drush:cr', function () {
    cd(get('release_path'));
    run('drush cr -y');
});

desc('Update database');
task('drush:updb', function () {
    cd(get('release_path'));
    run('drush updb -y');
});

desc('Reload features');
task('drush:fra', function () {
    cd(get('release_path'));
    run('drush fra -y');
});


after('deploy:symlink', 'drush:updb');
//after('drush:updb', 'drush:fra');
after('drush:updb', 'drush:cr');
//after('deploy:symlink', 'drush:cr');


// Composer dependencies
after('deploy:shared', 'deploy:vendors');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
