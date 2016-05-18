<?php
/**
 * Created by PhpStorm.
 * User: Allen
 * Date: 5/14/2016
 * Time: 8:43 PM
 */


class Whatever {

    var $client_short;
    var $client_full;
    var $client_id;

    function get($client_id=NULL) {

    }



}

class update {

    function crawl_client_folders() {
        $f3 = \Base::instance();
        $path = $f3->get('project_root');
        $db = $f3->get('DB');
        $client_obj = new Client;
        $clients = $client_obj->get_all();
        $client = new DB\SQL\Mapper($db,'clients');
       # print_r($clients);

        $files = scandir($path);
        $out = array();
        $out[]="Scanning for changes for new client folders";
        for($i=0;$i<count($files);$i++) {
            $match = 0;
            if($files[$i]== "." || $files[$i] == ".." || $files[$i] == "(template)" || $files[$i] == ".sync" || $files[$i] == "CacheClip" || $files[$i] == "(others)" || $files[$i] == "Allen Ellis" || $files[$i] == "Archive" || $files[$i] == "Transcodes" ) continue;
            for($j=0;$j<count($clients);$j++) {
                if($clients[$j]['client_full']  ==  $files[$i]) {
                    $out[]="Detected: " . $files[$i] . " [already exists]";
                    $match=1;
                    continue;
                }
            }
            if($match == 0) {
                $client->reset();
                $client->client_full = $new_client['client_full'] = $files[$i];
                $client->client_short = $new_client['client_short'] = shorten($files[$i]);
                $out[]=array("New: " . $files[$i] . " [adding to db]",$new_client);
                $client->save();
            }

        }
    return($out);
    }

    function crawl_project_folders() {
        $f3 = \Base::instance();
        $path = $f3->get('project_root');
        $out = array();

        $db = $f3->get('DB');

        $client_obj = new Client;
        $clients = $client_obj->get_all();

        $out[]="Scanning for changes for new projects within all client folders";
        foreach($clients as $client) {

            $out[]="Scanning client: " . $client['client_full'];
            $project_obj = new Project;
            $projects = $project_obj->get_all($client['client_id']);
            $project = new DB\SQL\Mapper($db, 'projects');

            $client_dir = $path . "/" . $client['client_full'];
            if (!$files = @scandir($client_dir)) {
                $out[] .= "Notice: can't find any files inside this client folder, skipping [$client_dir]";
                continue;
            }

            for ($i = 0; $i < count($files); $i++) {
                $match = 0;
                if ($files[$i] == "." || $files[$i] == ".." || $files[$i] == "(template)" || $files[$i] == ".sync" || $files[$i] == "CacheClip" || $files[$i] == "Archive" || $files[$i] == "Transcodes" ) {
                    continue;
                }
                for ($j = 0; $j < count($projects); $j++) {
                    if ($projects[$j]['project_full'] == $files[$i]) {
                        $match = 1;
                        $out[]="Detected: " . $files[$i] . " [already exists]";
                        continue;
                    }
                }
                if ($match == 0) {
                    $project->reset();
                    $new_project=array();

                    $description = @file_get_contents($path.'/'.$client['client_full'].'/'.$files[$i].'/description.txt');

                    $project->client_id = $new_project['client_id'] = $client['client_id'];
                    $project->project_full = $new_project['project_full'] =  $files[$i];
                    $project->project_short = $new_project['project_short'] = shorten($files[$i]);
                    if($description) $project->description = $new_project['description'] = $description;

                    $out[]=array("New: " . $files[$i] . " [adding to db]",$new_project);
                    $project->save();
                }
            }
        }
        return($out);
    }

    function crawl_drafts_folders() {
        $f3 = \Base::instance();
        $path = $f3->get('project_root');
        $out = array();

        $db = $f3->get('DB');
        $client_obj = new Client;
        $clients = $client_obj->get_all();

        $out[]="Scanning for changes for new versions within all client folders";
        $version = new DB\SQL\Mapper($db, 'versions');

        foreach($clients as $client) {

            $out[].="Searching through " . $client['client_full'];
            $project_obj = new Project;
            $projects = $project_obj->get_all($client['client_id']);

            foreach($projects as $project) {


                $drafts_dir = $path . "/" . $client['client_full'] . "/" . $project['project_full'] . "/Drafts";
                $out[].="----Scanning $drafts_dir";
                if (!$files = @scandir($drafts_dir)) {
                    $out[] .= "----Notice: can't find a drafts folder here, skipping [$drafts_dir]";
                    continue;
                }

                for ($i = 0; $i < count($files); $i++) {
                    $match = 0;

                    $version_obj = new Version;
                    $versions = $version_obj->get_all($project['project_id']);

                    if ($files[$i] == "." || $files[$i] == ".." || $files[$i] == "(template)" || $files[$i] == ".sync" || $files[$i] == "CacheClip" || $files[$i] == "Archive" || $files[$i] == "Transcodes" ) {
                        continue;
                    }
                    for ($j = 0; $j < count($versions); $j++) {
                        if ($versions[$j]['version_master_filename'] == $files[$i]) {
                            $match = 1;
                            $out[] = "Detected: " . $files[$i] . " [already exists]";
                            continue;
                        }
                    }
                    if ($match == 0) {
                        $version->reset();
                        $version_name = parse_version_name($files[$i]);
                        $full_path = $drafts_dir . '/' . $files[$i];

                        $thumb = ""; // todo auto generate?

                        $version->project_id = $file['project_id'] = $project['project_id'];
                        $version->version_name = $file['version_name'] = $version_name['name'];
                        $version->version_master_filename = $file['version_master_filename'] = $files[$i];
                        $version->version_master_full_path = $file['version_master_full_path'] = $full_path;
                        $version->timestamp = $file['timestamp'] = filemtime($full_path);
                        $version->thumb = $file['thumbnail'] = $thumb;
                        $out[] = array("Adding this file to the DB: ", $file);

                        $version->save();
                    }

                }
            }
        }
        return($out);
    }


    function crawl($f3,$args) {
        //$view=new View;
        //echo $view->render('home.html');

        echo "\r\nCrawling project folders\r\n<pre>";

        $out = array();

        $obj = new update;

        $out[] = $obj->crawl_client_folders();
        $out[] = $obj->crawl_project_folders();
        $out[] = $obj->crawl_drafts_folders();

        print_r($out);

    }

    function transcode($f3,$args) {
        echo "\r\nTranscoding project folders\r\n<pre>";


        $out = array();

        // todo loop through all clients, all projects
        $version_obj = new Version;
        $version = $version_obj->get_all(86);

        $out[] = $version_obj->transcode($version[0]);

        print_r($out);

    }

    function populate($f3,$args) {
        echo "\r\nPopulating finished file columns with processed videos\r\n<pre>";

        $out = array();

        $files_db=new DB\SQL\Mapper($f3->get('DB'),'files');
        $files=$files_db->find(array(),array('order' => 'path ASC'));

        foreach($files as $file) {

            $out[] = array("Analyzing file_id: ".$file->file_id.", path: ".$file->path);
            if($file->complete == 1) {
                if(@file_exists($file->path)) {
                    continue;
                } else {
                    $file->complete=0;
                    $file->save();
                    $out[] = "Disabled file that has gone missing: ". $file->path;

                }
            } else {
                if(@file_exists($file->path)) {
                    $filesize = filesize($file->path);
                    if($filesize == $file->filesize || "-1" == $file->filesize) {
                        // proceed either if the file is the same size we expected it to be, or is -1 (undetected)
                        $file->complete=1;
                        $file->filesize=$filesize;
                        $file->save();
                        $out[] = "New file marked as complete: " . $file->path;
                    } else {
                        $out[] ="File has mysteriously come back as a different size, skipping...";
                        continue;
                    }


                } else {
                    continue;
                }
            }
        }

        print_r($out);
    }


}
