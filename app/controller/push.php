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
        $project_obj = new Project;
        $path = $project_obj->find_thumb($args['project_id']);

        $out[] = array("About to embed this file",$path);

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
        header('Content-type: application/octet-stream');
        header('Content-disposition: attachment; filename="'.$file['filename'].'"');
        die();

    }


}