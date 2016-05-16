<?php
/**
 * Created by PhpStorm.
 * User: Allen
 * Date: 5/15/2016
 * Time: 6:41 PM
 */
function shorten($name_full) {
    $out = str_replace(')','',str_replace('(','',str_replace(' ', '', strtolower($name_full))));
    return $out;
}

function parse_version_name($filename) {
    $pattern="/^.+? - v(.+)$/is";
    preg_match($pattern , $filename, $match);
    $version['filename'] = $match[1];
    $version['name'] = substr($match[1],0,-4);
    $version['ext'] = substr($match[1],-3);

    if(!$match[1]) return 0;
    return $version;
}


function json_post($url, $data )
{
    $ch = curl_init( $url );
# Setup request to send json via POST.
    $payload = json_encode($data );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
# Return response instead of printing.
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
# Send request.
    $result = curl_exec($ch);
    curl_close($ch);
# Print response.
    return $result;
}
