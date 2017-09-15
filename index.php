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
	//    "site.url" => $f3->get("SCHEME") . "://" . $f3->get("HOST") . $f3->get("BASE") . "/"
	"site.url" => "//" . $f3->get("HOST") . $f3->get("BASE") . "/"
));
$f3->config("config.ini");
$f3->config("passwords.ini");

// db connection
$f3->set('DB', new DB\SQL(
    'mysql:host=localhost;port=3306;dbname=allen_clients',
     $f3->get("db.user"),
     $f3->get("db.pass")
));

$f3->set('DBT', new DB\SQL(
    'mysql:host=localhost;port=3306;dbname=allen_transcoder_2',
    $f3->get("db.user"),
    $f3->get("db.pass")
));

require('app/controller/sources.php');
require('app/controller/cms.php');
require('app/controller/update.php');
require('app/controller/push.php');

/* routing functions */

$f3->route('GET /','cms->bounce');
$f3->route('GET /vendorlist','cms->vendorlist');
$f3->route('GET /allenhome','cms->home');
$f3->route('GET /about','cms->about');

$f3->route('GET /status','update->status');
$f3->route('GET /status/delete/@jobid','update->transcode_delete');
$f3->route('GET /status/@status','update->status');

$f3->route('GET /update','update->update_all');
$f3->route('GET /update/crawl','update->crawl');
$f3->route('GET /update/transcode','update->transcode');
$f3->route('GET /update/populate','update->populate');

$f3->route('GET /@client','cms->client');
$f3->route('GET /@client/@project', 'cms->project');
$f3->route('GET /@client/@project/@version_name', 'cms->project');
$f3->route('GET /@client/@project/@version_name/@edit', 'cms->project');

$f3->route('GET /@client/@project/@version_id/@edit/@type/@val', 'cms->set');

$f3->route('GET /thumb/@type/@id', 'push->thumb');
$f3->route('GET /embed/@file_id', 'push->embed'); 
$f3->route('GET /dl/@file_id', 'push->download');

$f3->route(
    array(
        'GET /@client/@project/@dl',
        'GET /@client/@project/@dl/@version',
        'GET /@client/@project/@dl/@version/@quality'
    ),
    'cms->dl'
);




$f3->run();


// log visitor
$line = date('Y-m-d H:i:s') . ", $_SERVER[REMOTE_ADDR], ".$_SERVER['REQUEST_URI'].", ".$_SERVER['HTTP_REFERER'];
@file_put_contents('hits.log', $line . PHP_EOL, FILE_APPEND);





// push hits
if($f3->get('push_hits') == TRUE){

    include('php-pushover/Pushover.php');

    //$ip = $_SERVER['REMOTE_ADDR'];
    $ip = $_SERVER['HTTP_X_REAL_IP'];

    if ($ip != "10.10.10.1" && $ip != "10.10.10.80") {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) ) // is a local ip address
        {

            $push = new Pushover();
            $push->setToken('ax583zqspqdn6ssupdsowq5uo3kwos');
            $push->setUser('uuet8bfx4sdt7y57x8sjkhgcbrt85b');
            $push->setTitle('Video hit: ' . substr(urldecode($_SERVER['REQUEST_URI']), 9));
            $push->setMessage('By ' . $ip);
            #   $push->setUrl('https://allenell.is/clients/hits.php');
            $push->setUrlTitle('Client hit log');
            $push->setDevice('ap');
            $push->setPriority(0);
            $push->setRetry(0); //Used with Priority = 2; Pushover will resend the notification every 60 seconds until the user accepts.
            $push->setExpire(0); //Used with Priority = 2; Pushover will resend the notification every 60 seconds for 3600 seconds. After that point, it stops sending notifications.
            $push->setCallback('');
            $push->setTimestamp(time());
            $push->setDebug(false);
            $push->setSound('');
            $go = @$push->send();
        }


    }

}
