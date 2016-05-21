<?php
/**
 * Created by PhpStorm.
 * User: Allen
 * Date: 5/14/2016
 * Time: 8:43 PM
 */


class push {

    function thumb($f3, $args) {
        $out = array();

        if($args['type'] == 'p') {
            $project_obj = new Project;
            $path = $project_obj->find_thumb($args['id']);
            $out[] = 'type is project';

        } elseif($args['type'] == 'v') {
            $version_obj = new Version;
            $version = $version_obj->get($args['id']);
            $path = $version['thumb'];

        } else { // assume file
            $file_obj = new File;
            $file = $file_obj->get($args['id']);
            $version_obj = new Version;
            $version = $version_obj->get($file['version_id']);
            $path = $version['thumb'];
        }

        $out[] = array("About to embed this file",$path); // todo check sanity of $path. shouldn't be outside working dir, should exist, should end in jpg

        header("X-Sendfile: $path");
        header('Content-type: image/jpeg');
        #header('Content-disposition: attachment; filename="'.$file['filename'].'"');
        die();
    }


    function embed($f3, $args) {
        $out = array();
        $file_obj = new File;
        $file = $file_obj->get($args['file_id']);

        $out[] = array("About to embed this file",$file);

        $path = $file['path'];
 
        header("X-Sendfile: $path");
        header('Content-type: video/mp4');
        #header('Content-disposition: attachment; filename="'.$file['filename'].'"');
        die();

    }

    function download($f3=NULL, $args=NULL, $file_id=NULL) {

        $out = array();
        $file_obj = new File;
        $file = $file_obj->get($args['file_id']?$args['file_id']:$file_id);

        $path = $file['path'];

        header("X-Sendfile: $path");

        if(is_mobile()) {
            header("X-Sendfile: $path");
            header('Content-type: video/mp4');
        } else {
            header('Content-type: application/octet-stream');
            header('Content-disposition: attachment; filename="' . $file['filename'] . '"');
        }
        die();

    }


}