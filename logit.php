<?php
/**
 * Created by PhpStorm.
 * User: Allen
 * Date: 9/25/2017
 * Time: 10:31 PM
 */
echo "
SCRIPT START
";
$_POST = json_decode(file_get_contents('php://input'), true);

logit($_POST['ip'],$_POST['request'],$_POST['referer']);

function logit($ip,$request,$referer)
{

    $referer=$str = preg_replace('#^https?://#', '', $referer);

    $message= "";
// log visitor



// push hits


        include('php-pushover/Pushover.php');

        //$ip = $_SERVER['REMOTE_ADDR'];
        //$ip = $_SERVER['HTTP_X_REAL_IP'];

        if ($ip != "10.10.10.1" && $ip != "10.10.10.80") {
        //if(1==1) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) // is a local ip address
            //if(1==1)
            {

                $details = ip_details($ip);
                print_r(array("details",$details));

                $_details = array();
                //$_details['ip'] = $details['ip']?$details['ip']:$ip;
                $_details['org'] = $details['org']?$details['org']:"";
                $_details['hostname'] = $details['hostname']?$details['hostname']:"";
                $_details['network'] = $details['network']?$details['network']:"";
                $_details['referer'] = $referer?"From ".$referer:"";


print_r(array("_details",$_details));

                $message=implode(" | ",array_filter($_details));

                //$message = $details['ip'] . " | " . $details['org'];
               /* $message = 'https://ipinfo.io/' . $ip ."
".$referer*/

               print_r(array("message",$message));

                $line = date('Y-m-d H:i:s') . ", " .
                    $ip .",".
                    $request .",".
                    $referer .",".
                    $details['hostname'].",".
                    $details['org'].",".
                    str_replace(',',' ',$details['network']).",".
                    $details['city'].",".
                    $details['region'].",".
                    $details['country'].",".
                    str_replace(',',' | ',$details['loc']).",".
                    $details['postal'];
                @file_put_contents('hits.csv', $line . PHP_EOL, FILE_APPEND);

                //print_r($_GET);
                //print_r($_POST);
                $push = new Pushover();
                $push->setToken('ax583zqspqdn6ssupdsowq5uo3kwos');
                $push->setUser('uuet8bfx4sdt7y57x8sjkhgcbrt85b');
                $push->setTitle('Video hit: ' . substr(urldecode($request), 9));
                $push->setMessage($message);
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

function ip_details($ip) {
    $json = file_get_contents("http://ipinfo.io/{$ip}/json");
    $details = json_decode($json, true);
    return $details;
}
