<?php

$f3 = require('lib/base.php');
$f3->mset(array(
    "UI" => "app/view/;app/plugin/",
    "ESCAPE" => false,
    "LOGS" => "log/",
    "TEMP" => "tmp/",
    "PREFIX" => "dict.",
    "LOCALES" => "app/dict/",
    "FALLBACK" => "en",
    "CACHE" => true,
    "AUTOLOAD" => "app/",
    "PACKAGE" => "Phproject",
    "microtime" => microtime(true),
    "site.url" => $f3->get("SCHEME") . "://" . $f3->get("HOST") . $f3->get("BASE") . "/"
));
$f3->config("config.ini");

// db connection
$f3->set('DB', new DB\SQL(
    'mysql:host=localhost;port=3306;dbname=allen_clients',
     $f3->get("db.user"),
     $f3->get("db.pass")
));

require('app/controller/sources.php');
require('app/controller/cms.php');
require('app/controller/update.php');

/* routing functions */

$f3->route('GET /','cms->home');
$f3->route('GET /about','cms->about');

$f3->route('GET /update/crawl','update->crawl');
$f3->route('GET /update/transcode','update->transcode');
$f3->route('GET /update/populate','update->populate');

$f3->route('GET /@client','cms->client');
$f3->route('GET /@client/@project', 'cms->project');

$f3->route(
    array(
        'GET /@client/@project/@dl',
        'GET /@client/@project/@dl/@version',
        'GET /@client/@project/@dl/@version/@quality'
    ),
    'cms->dl'
);





$f3->run();
