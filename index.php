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



//echo $_SERVER['REMOTE_ADDR'];
//echo "<hr />";
//phpinfo();
//die;
//HTTP_X_REAL_IP

$f3->run();
if ($f3->get('push_hits') == TRUE) {
    logit($_SERVER['REMOTE_ADDR'],$_SERVER['REQUEST_URI'],$_SERVER['HTTP_REFERER']);
}

function logit($ip,$request,$referer) {
    //$ip = urlencode($ip);
    //$request = urlencode($request);
    //$referer = urlencode($referer);

    $post_array = array(
        'ip'=>$ip,
        'request'=>$request,
        'referer'=>$referer
    );

    wget_request('http://127.0.0.1/clients/logit.php',$post_array,false);

}

