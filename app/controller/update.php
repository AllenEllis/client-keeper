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
            if(
                $files[$i]== "." ||
                $files[$i] == ".." ||
                $files[$i] == "(template)"
                || $files[$i] == ".sync"
                || $files[$i] == "CacheClip"
                || $files[$i] == "(others)"
                || $files[$i] == "(other)"
                || $files[$i] == "New folder"
                || $files[$i] == "Allen Ellis"
                || $files[$i] == "Archive"
                || $files[$i] == "Transcodes"
                || $files[$i] == "@eaDir"
                || $files[$i] == ".DS_STORE") continue;
            if(!is_dir($files[$i])) continue;
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
                if ($files[$i] == "." ||
                    $files[$i] == ".." ||
                    $files[$i] == "(template)" ||
                    $files[$i] == "(other)" ||
                    $files[$i] == "New folder" ||
                    $files[$i] == ".sync" ||
                    $files[$i] == "CacheClip" ||
                    $files[$i] == "Archive" ||
                    $files[$i] == "Transcodes" ||
                    $files[$i] == "Stock Footage" ||
                    $files[$i] == "@eaDir" ||
                    $files[$i] == ".DS_Store") {
                    continue;
                }
                if(!is_dir($files[$i])) continue;
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

                    $extension = strtolower(substr($files[$i],-3));

                    if ($files[$i] == "." || $files[$i] == ".." || $files[$i] == "(template)" || $files[$i] == ".sync" || $files[$i] == "CacheClip" || $files[$i] == "Archive" || $files[$i] == "Transcodes" ) {
                        continue;
                    }
                    if ($extension != "mp4" && $extension != "mov") continue;

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

    function update_all() {
        set_time_limit(1200);
	$starttime = microtime(TRUE);
        echo "Running update: <pre>";
        $out = array();
	$out[] = "Starting time: $starttime";
        $out[] = $this->crawl();
	$crawltime = microtime(TRUE);
	$out[] = array("Time spent crawling: ",$crawltime - $starttime);
        $out[] = $this->transcode();
	$tctime = microtime(TRUE);
	$out[] = array("Time spent transcoding: ",$tctime - $crawltime);
        $out[] = $this->populate();
	$poptime = microtime(TRUE);
	$endtime = microtime(TRUE);
	$runtime = $endtime - $starttime;
	$out[] = array("Time spent populating: ",$poptime-$tctime);
	$out[] = "Ending time: $endtime";
	$out[] = "Total run-time: $runtime";
        print_r($out);
    }

    function crawl() {

        $out = array();
        $out[] = "Crawling project folders";
        $obj = new update;

        $out[] = $obj->crawl_client_folders();
        $out[] = $obj->crawl_project_folders();
        $out[] = $obj->crawl_drafts_folders();

        return($out);

    }

    function transcode() {
        $out = array();
        $out[] = "Transcoding project folders";

/*
        // todo loop through all clients, all projects  

        $version_obj = new Version;
        $version = $version_obj->get_all(85); // 86 for 4-year grad, 108 for sample proj, 109 for sample laser

        #$out[] = array("I would have transcoded",$version);

*/
        $f3 = \Base::instance();
        $client_obj = new Client;
        $project_obj = new Project;
        $version_obj = new Version;

        $clients = $client_obj->get_all();

        foreach($clients as $client) {
            $projects = $project_obj->get_all($client['client_id']);
            foreach($projects as $project) {
                $versions = $version_obj->get_all($project['project_id']);
                foreach($versions as $version) {
                    if($f3->get('transcoder_full') == 1) {
                        $out [] = array("Transcoder is full, aborting all future transcodes. Adavancing to population step.");
                        break;
                    }
		    if($version['transcoded']) continue;
                    $out[] = $version_obj->transcode($version);
                }
            }
        }


        return($out);

    }

    function populate() {

        $out = array();

        $out[] = "Populating finished file columns with processed videos";


        $f3 = \Base::instance();
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

        return($out);
    }




    function status($f3,$args) {
        
        $jobs = new Job;
        
        $status = sanitize_status($args['status']);

        $active_jobs = $jobs->get_all($status);

        $jobs_array = array();

        foreach($active_jobs as $key=>$active_job) {
            $job = $active_job->cast();
            $job['progress'] = float_to_percent($job['progress']);
            $job['opts'] = json_decode($job['opts'],true);

            $encoder_url = $f3->get('encoder_url') . "/jobs";
            $job_id = $job['internalId'];

            $job['controls'] = "<a href='".$f3->get('site.url')."status/delete/".$job['id']."'>X</a>";

            // TODO This code could work one day in browser if only my transcoder were served over HTTPS
/*

            $job['controls'] = "<div data-id='del_".$job_id."'><button class='delete-button'>X</button></div>" . <<<END
            
                <script>
                $('.delete-button').click(function(){               
                    $.ajax({
                        type: "DELETE",
                        url: "{$encoder_url}/{$job_id}",

                        
                });
             });
               
                </script>
                            
END;*/
            $jobs_array[$key] = $job;

    }



        $vendor = new Vendor;
        $vendor->get($f3->get('default_vendor'));
        $f3->set('jobs',$jobs_array);
        $f3->set('vendor',$vendor->dump_vars());
        $f3->set('status',$status);
        $job_rows = \Template::instance()->render('job_rows.html');
        $f3->set('job_rows',$job_rows);
        echo \Template::instance()->render('status.html');

    }

    function transcode_delete($f3,$args) {
        $jobs = new Job;


        $job = $jobs->get($args['jobid']);
        $encoder_url = $f3->get('encoder_url') . "/jobs";
        $base_url = $f3->get('base_url');

        if(!$job['internalId']) {
            echo "Can't delete this job. It looks like it's already been deleted";
            return;
        }

        $url = $encoder_url . "/" . $job['internalId'];
        #echo $url;
        #die;
        $result = curl_del($url);
        header("Location:".$base_url."/status");



        #echo $result;

        return;

    }



}


class Job {
    var $job_id;
    var $internalId;
    var $status;
    var $progress;
    var $duration;
    var $filesize;
    var $opts;
    var $message;
    var $createdAt;
    var $updatedAt;
    var $thumbnails;
    var $playlist;
    var $segments;

    var $jobs;

    function get($id=NULL, $job=NULL) {
        $f3 = \Base::instance();

        if($job == NULL) {
            $f3 = \Base::instance();
            $this->job_db=new DB\SQL\Mapper($f3->get('DBT'),'Jobs');
            $job=$this->job_db->load(array('id=?',$id));
        }

        $out = array(
            "id" => $job->id,
            "internalId" => $job->internalId,
            "status" => $job->status,
            "progress" => $job->progress
        );
        return $out;
/*
        $this-> $id = $job->id;
        echo $id;
        $this-> $internalId = $job->internalId;
        $this-> $status = $job->status;
        $this-> $progress = $job->progress;
        $this-> $duration = $job->duration;
        $this-> $filesize = $job->filesize;
        $this-> $opts = $job->opts;
        $this-> $message = $job->message;
        $this-> $createdAt = $job->createdAt;
        $this-> $updatedAt = $job->updatedAt;
        $this-> $thumbnails = $job->thumbnails;
        $this-> $playlist = $job->playlist;
        $this-> $segments = $job->segments;

        return $this;                 */
    }

    function get_all($status) {

        $f3 = \Base::instance();

        $status = sanitize_status($status);

        $job = new DB\SQL\Mapper($f3->get('DBT'),'Jobs');
        $list = $job->find('status="'.$status.'"',array('order'=>'id desc'));

        return $list;
        /*




        $this->job_db=new DB\SQL\Mapper($f3->get('DBT'),'Jobs');
        $jobs=$this->job_db->find(array('status','failed'));
        $jobs=array();
        foreach ($jobs as $key => $job) {
            $this->jobs[$key] = $job;
        }*/
    }

    function dump_vars() {
        $out = object_to_array($this);
        unset($out['job_db']);
        return $out;
    }

}
