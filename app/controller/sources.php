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


function set_client_branding($client) {
    $f3 = \Base::instance();

    if($client['client_home_url']) {
        $f3->set('home_url', $client['client_home_url']);
        $f3->set('home_title', $client['client_home_title']);
    }
    return;
}


function get_video_attributes($video) { // todo remove this function
    $out = array();
    $f3 = \Base::instance();
    $ffprobe_path = $f3->get('ffprobe_path');

    $command = $ffprobe_path . ' "' . $video . '" -v error -show_entries stream=width,height,codec_name,codec_long_name,duration, -of default=noprint_wrappers=1 -print_format json -select_streams v:0 >&1';
    $output = shell_exec($command);

    $attributes = object_to_array(json_decode($output))['streams'][0];
    $out[] = array("Attributes are",$attributes);

    return $attributes;

}

function old_get_video_attributes($video) { // todo remove this function
$out = array();
    $f3 = \Base::instance();
    $ffmpeg_path = $f3->get('ffmpeg_path');

    $command = $ffmpeg_path . ' -i "' . $video . '" -vstats 2>&1';
    $output = shell_exec($command);

    $regex_sizes = "/Video: ([^,]*), ([^,]*), ([0-9]{1,4})x([0-9]{1,4})/";
    if (preg_match($regex_sizes, $output, $regs)) {
        $codec = $regs [1] ? $regs [1] : null;
        $width = $regs [3] ? $regs [3] : null;
        $height = $regs [4] ? $regs [4] : null;
    }
    $out[] = array("Regex_sizes"=>$regs);
    $regex_duration = "/Duration: ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}).([0-9]{1,2})/";
    if (preg_match($regex_duration, $output, $regs)) {
        $hours = $regs [1] ? $regs [1] : null;
        $mins = $regs [2] ? $regs [2] : null;
        $secs = $regs [3] ? $regs [3] : null;
        $ms = $regs [4] ? $regs [4] : null;
    }
    $out[] = array(
    "regex duration"=>$regs);
    $out[] = array(
    "Output"=>$output);
    print_r($out);
    die;


    return array (
        'command' => $command,
        'codec' => $codec,
        'width' => $width,
        'height' => $height,
        'hours' => $hours,
        'mins' => $mins,
        'secs' => $secs,
        'ms' => $ms
    );

}

function object_to_array($data) {

    if (is_object($data)) {
        $data = get_object_vars($data);
    }

    if (is_array($data)) {
        return array_map(__FUNCTION__, $data);
    }
    else {
        return $data;
    }
}

// analyzes ffmpeg command, looks for scale: setting, returns that number
function parse_output_dimensions($encoder_settings,$width,$height) {

    //$encoder_settings ="-c:v libx264 -preset ultrafast -profile:v main -pix_fmt yuv420p -crf 20 -vf scale=640:trunc(ow/a/2)*2 -c:a libfdk_aac -ab 192k -ar 44100 -y -threads 0";

    $aspect_ratio = 16/9;
    if($height != 0) $aspect_ratio = $width / $height;

     preg_match('/ scale=((\w+))\:/', $encoder_settings, $matches);

    $output_width = $matches[1];
    $output_height = floor($output_width / $aspect_ratio);

    return array("width"=>$output_width,"height"=>$output_height);


}

function parse_video_codec($codec) {
    $out = "Unknown codec";

    if(preg_match("/prores/",$codec,$matches)) $out = "Pro Res";
    if(preg_match("/h264/",$codec,$matches)) $out = "H.264";
    if(preg_match("/mjpeg/",$codec,$matches)) $out = "QuickTime Motion JPEG";

    return $out;
}

function float_to_percent($float) {
    $percent = $float * 100;
    $percent = round($percent,0);
    $percent .= "%";
    return $percent;
}

function is_mobile() {
    // Include and instantiate the class.
    require_once 'Mobile_Detect.php';
    $detect = new Mobile_Detect;

// Any mobile device (phones or tablets).
    if ( $detect->isMobile() ) {
        return true;
    }

// Any tablet device.
    if( $detect->isTablet() ){
        return true;
    }

     /// more documentation at http://mobiledetect.net/
}

function sanitize_status($status) {
    if($status == 'tc') $status = "processing";
    if($status != 'failed' && $status != 'success') $status = 'processing';
    return $status;
}
