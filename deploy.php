<?php
namespace Deployer;

require 'recipe/symfony4.php';

// Project name
set('application', 'paybeer');
set('http_user', 'www-data');
set('bin/php', '/opt/php7.4/bin/php');
set('bin/composer', '/opt/php7.4/bin/composer');

set('bin/console', function () {
    return parse('{{release_path}}/bin/console');
});

// Project repository
set('repository', 'git@github.com:HEIGVD-PRO-A-10/PayBeer.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Hosts
host('4d30w.ftp.infomaniak.com')
    ->user('4d30w_paybeer')
    ->set('deploy_path', '~/{{application}}');
    
// Tasks

desc('Composer install dependencies');
task('deploy:vendors', function () {
    run('cd {{release_path}} && {{bin/composer}} install');
});

desc('Migrate database');
task('database:migrate', function() {
    run('{{bin/php}} {{bin/console}} doctrine:migrations:migrate --allow-no-migration --no-interaction');
});

desc('Fill database');
task('database:fixtures', function() {
    run('{{bin/php}} {{bin/console}} doctrine:fixtures:load --no-interaction');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'database:migrate');

after('database:migrate', 'database:fixtures');
