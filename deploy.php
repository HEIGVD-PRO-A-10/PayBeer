<?php
namespace Deployer;

require 'recipe/symfony4.php';

// Project name
set('application', 'paybeer');

set('http_user', 'www-data');

// Project repository
set('repository', 'git@github.com:HEIGVD-PRO-A-10/PayBeer.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);


// Hosts
host('4d30w.ftp.infomaniak.com')
    ->user('4d30w_paybeer')
    ->set('deploy_path', '~/{{application}}');
    
// Tasks

desc('Composer install dependencies');
task('deploy:vendors', function () {
    run('cd {{release_path}} && /opt/php7.4/bin/composer install --optimize-autoloader');
});

desc('Fill database');
task('database:fixtures', function() {
    run('{{bin/php}} {{bin/console}} doctrine:fixtures:load {{console_options}}');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'database:migrate');

after('database:migrate', 'database:fixtures');
