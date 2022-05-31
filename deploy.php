<?php

namespace Deployer;

require_once(__DIR__ . '/vendor/blueways/deployer-recipes/autoload.php');

set('repository', 'git@github.com:maikschneider/bw_todo.git');

host('staging')
    ->hostname('maik-tailor.de')
    ->stage('staging')
    ->user('web0')
    ->port('24327')
    ->set('branch', 'master-deployer')
    ->set('public_urls', ['https://deployer.maik-tailor.de'])
    ->set('http_user', 'www-data')
    ->set('writable_mode', 'chmod')
    ->set('bin/composer', '/usr/local/bin/composer')
    ->set('bin/php', '/usr/bin/php')
    ->set('deploy_path', '/home/web0/vhosts/deployer.maik-tailor.de/htdocs/branches/master');

host('local')
    ->hostname('local')
    ->set('deploy_path', getcwd());
