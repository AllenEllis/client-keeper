<?php
/**
 * Created by PhpStorm.
 * User: Allen
 * Date: 5/14/2016
 * Time: 8:43 PM
 */

class Vendor {
    var $vendor_id;
    var $name;
    var $website;
    var $address;
    var $about;
    var $contact_info;
    var $social_media;

    var $vendor_db;

    function get($vendor_id) {
        $f3 = \Base::instance();
        $this->vendor_db=new DB\SQL\Mapper($f3->get('DB'),'vendors');
        $db=$this->vendor_db->load(array('vendor_id=?',$vendor_id));

        $this->vendor_id = $db->vendor_id;
        $this->name = $db->name;
        $this->website = $db->website;
        $this->address = $db->address;
        $this->about = $db->about;
        $this->contact_info = $db->contact_info;
        $this->social_media = json_decode($db->social_media);

    }

    function dump_vars() {
        $out = object_to_array($this);
        unset($out['vendor_db']);
        return $out;
    }

   /* function encode_social_media($array=NULL) {
        $allen = array(
            "facebook"=>"allenellis",
            "twitter"=>"allenellis",
            "instagram"=>"allenellis"
        );
        $refractive = array(
            "facebook"=>"RefractiveFilms",
            "twitter"=>"",
            "instagram"=>"refractivefilms",
            "vimeo"=>"refractivefilms"
        );
        $allegheny = array(
            "facebook"=>"alleghenyimagefactory",
            "vimeo"=>"alleghenyimagefactory"
        );

        return json_encode($allegheny);
    }*/

}

class Client {

    var $client_short;
    var $client_full;
    var $client_id;

    function get($client_id=NULL) {
        $f3 = \Base::instance();

        $client_short = $f3->get('PARAMS.client');
        $clients=new DB\SQL\Mapper($f3->get('DB'),'clients');
        $clients->load(array('client_short=?',$client_short));

        $out = array(
            "client_id"=>$clients->client_id,
            "client_short"=>$client_short,
            "client_full"=> $clients->client_full,
            "client_url"=>$f3->get('site.url').$client_short,
            "client_home_url"=>$clients->client_home_url,
            "client_home_title"=>$clients->client_home_title,
            "vendor_id"=>$clients->vendor_id

        );

        return $out;
    }

    function get_all() {
        $f3 = \Base::instance();
        $clients_obj=new DB\SQL\Mapper($f3->get('DB'),'clients');
        $clients = $clients_obj->find("",array('order'=>'client_full'));
        $out = array(); // todo clean this up so it calls get() instead
        foreach($clients as $client)
        {
            //if($client->active == 0) continue;
            $out[] = array(
                "client_id"=>$client->client_id,
                "client_short"=>$client->client_short,
                "client_full"=> $client->client_full,
                "client_url"=>$f3->get('site.url').$client->client_short,
                "active"=>$client->active,
                "client_home_url"=>$clients->client_home_url,
                "client_home_title"=>$clients->client_home_title
            );
        }
        return $out;
    }

}

function format_bytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    // Uncomment one of the following alternatives
    $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . ' ' . $units[$pow];
}

class Project {
    var $project_id;
    var $client_id;
    var $project_short;
    var $project_full;
    var $description;

    function get($projectID=NULL) {
        $f3 = \Base::instance();

        $project_short = $f3->get('PARAMS.project');
        $projects=new DB\SQL\Mapper($f3->get('DB'),'projects');
        $projects->load(array('project_short=?',$project_short));

        $out = array(
            "project_id"=>$projects->project_id,
            "client_id"=>$projects->client_id,
            "project_short"=>$project_short,
            "project_full"=> $projects->project_full,
            "description"=> $projects->description,
            "drafts" => $projects->drafts
        );

        return $out;
    }


    function find_thumb($project_id) {
        $f3 = \Base::instance();

        $version_obj = new Version;
        $versions = $version_obj->get_all($project_id);

        $thumb = "";
        foreach($versions as $version) {
            if(@file_exists($version['thumb'])){
                $thumb = $version['thumb'];
                break;
            }
        }

        return $thumb;

    }


    function get_all($client_id) {
        // todo error checking for bad client_id
        $f3 = \Base::instance();

        $project_short = $f3->get('PARAMS.project');
        $client_short = $f3->get('PARAMS.client');
        $projects_db=new DB\SQL\Mapper($f3->get('DB'),'projects');
        $projects=$projects_db->find(array('client_id=?',$client_id),array('order'=>'project_full'));

        $out = array();
        foreach($projects as $project)
        {
            //if($project->active == 0) continue;
            $out[] = array(
                "project_id"=>$project->project_id,
                "client_id"=>$project->client_id,
                "project_short"=>$project->project_short,
                "project_full"=>$project->project_full,
                "description"=> $projects->description,
                "active"=>$projects->active,
                "drafts"=>$projects->drafts,
                "project_url"=> $f3->get('site.url').$client_short."/".$project->project_short
            );
        }

        return $out;
    }

    function render_project_summary($project) {
        $f3 = \Base::instance();

        $client_obj = new Client;
        $client = $client_obj->get();

        $f3->set('client',$client);
        $f3->set('project',$project);
        return \Template::instance()->render('client_projects.html');
    }

    function is_active($project) {
        if($project['active'] == 1) return true;
        else return false;
    }

    function is_populated($project) {
    }

    /*
        function get_id($project_short=NULL,$id=NULL) {
            return "id here"; // todo
        }

        function get_full($project_short=NULL,$id=NULL) {
            return "id here"; // todo
        }

        function get_client_id($project_short=NULL,$id=NULL) {
            return "id here"; // todo
        }*/
}

class Version {

    var $version_id;
    var $project_id;
    var $version;
    var $timestamp;
    var $thumb;

    function populate($version_id) {
        $f3 = \Base::instance();
        $versions_db=new DB\SQL\Mapper($f3->get('DB'),'versions');
        $versions_db->load(array('version_id=?',$version_id),array());

        return $versions_db;
    }

    function get($version_id=NULL,$version=NULL) {

        if($version_id) {
            $f3 = \Base::instance();

            $versions_db = new DB\SQL\Mapper($f3->get('DB'), 'versions');
            $version = $versions_db->load(array('version_id=?', $version_id), array(
                'order' => 'timestamp DESC'
            ));
        }

        if(!$version) return;

        $out = array(
            "version_id"=>$version->version_id,
            "project_id"=>$version->project_id,
            "version_name"=>$version->version_name,
            "version_master_filename"=>$version->version_master_filename,
            "version_master_full_path"=>$version->version_master_full_path,
            "timestamp"=>$version->timestamp,
            "datetime"=>date("F j, Y, g:i a",$version->timestamp), // todo, add timestamp calculation here
            "thumb"=>$version->thumb,
            "width"=>$version->width,
            "height"=>$version->height,
	    "transcoded"=>$version->transcoded
        );
        return $out;
    }

    function get_all($project_id,$order_by="date") {
        // todo error checking for bad project_id
        $f3 = \Base::instance();

        if($order_by=="date") $_order_by = array('order'=>'timestamp DESC');
        if($order_by=="alpha") $_order_by = array('order'=>'version_name ASC');

        $versions_db=new DB\SQL\Mapper($f3->get('DB'),'versions');
        $versions=$versions_db->find(array('project_id=?',$project_id),$_order_by);

        $out = array();
        foreach($versions as $version)
        {
           $out[] = $this->get(NULL,$version);
        }

        return $out;

    }

    function render_version_summary($version,$current_version=NULL) {
        $f3 = \Base::instance();
        $f3->set('version',$version);

        $file_obj = new File;
        $files = $file_obj->get_all($version['version_id']);

        $out = "";
        $f3->set('files',$files);
        if($current_version) $f3->set('current_version',$current_version);

        return \Template::instance()->render('version_summary.html');
    }

    function embed_video($version) {
        $f3 = \Base::instance();
        $f3->set('version',$version);

        $file_obj = new File;
        $files = $file_obj->get_all($version['version_id']);

        // loop through all possible files and strip out any that can't be embedded
        $_files = array();
        foreach ($files as $file) {
            if(substr($file['path'],-3) != 'mp4') continue;
            $_files[] = $file;
        }

        $data_ratio = 0;
        $width = $version['width'];
        $height = $version['height'];
        if($width && $height) $data_ratio = $height / $width;

        $f3->set('data_ratio',$data_ratio);

        $f3->set('_files',$_files);
        $f3->set('thumb',$f3->get('base_url').$f3->get('thumb_url').'/v/'.$version['version_id']);

        $out = \Template::instance()->render('video_embed.html');

        return $out;
    }

    function transcode($version) {
        $f3 = \Base::instance();
        $out = array();
        $file_obj = new File;

        $out=array("I've been asked to transcode this file. Running preliminary checks first...",$version);

        $extension = substr($version['version_master_filename'],-3);
        if($extension != "mp4" && $extension != "mov") {
            $out[] = array("Notice: can't transcode this file, it's not an mp4 or mov",$version);
            return $out;
        }

        //if it was modified within the last 3 seconds, assume it's still being rendered or copied


        $moddate = filemtime($version['version_master_full_path']);
        $time = time() - 2;
        $out[]= array("Doing time check.",$time,$moddate);
        if($time < $moddate) {
            $out[] = array("This file was modified within the last 2 seconds. Skipping.");
            return $out;
        }


        $out[] = array("Preparing to transcode version #". $version['version_id'],$version);

        $src_path = $version['version_master_full_path'];

        if(!@file_exists($src_path)) {
            $out[] = array("Error: file doesn't exist, can't transcode",$src_path);
            return $out;
	    }

        $src_filename = $version['version_master_filename'];
        $dst_folder = substr($src_path,0,-strlen($src_filename)) . $f3->get('transcodes_subfolder').'/';

        $out[]= "New dst_folder will be $dst_folder";

        // determine aspect ratio so we can create appropriately sized thumbnails
        
        $src_attributes = get_video_attributes($src_path);

        $out[] = array("Video attributes are:",$src_attributes);

        $width = 1280;
        $height = 720;

        if($src_attributes['width']) $thumb_width = $width = $src_attributes['width'];
        if($src_attributes['height']) $thumb_height = $height = $src_attributes['height'];

        $ar = $width / $height;
        if($width > 1920) {
            $thumb_width = 1920;
            $thumb_height = floor($thumb_width / $ar);
        }
        $out[] = "Thumbnails will be generated at $thumb_width x $thumb_height"; // todo would be nice if thumbnails are never made larger than 1920x1080

        $encoder_options = $f3->get('encoder_options');
        $thumbnail_seconds = $f3->get('thumbnail_seconds');
        $encoder_url = $f3->get('encoder_url') . "/jobs";

        $out[] = array("Encoder options will be",$encoder_options);
        #$out[] = array("Thumbnail percentages will be",$thumbnail_seconds);


        foreach ($encoder_options as $quality=>$encoder_option) {
            // todo security on codem-transcode with tokens to prevent abuse

            // don't upscale sources
            $encoder_output_dimensions = parse_output_dimensions($encoder_option,$src_attributes['width'],$src_attributes['height']);
            if($encoder_output_dimensions['height'] > $height) {
                $out[] = "Notice: skipping quality ".$quality." because our source is $height. This option would have encoded at {$encoder_output_dimensions['height']}";
                continue;
            }

            $dst_filename = substr($src_filename,0,-4) . " [" . $quality . "]." . $f3->get('encoder_extension');

            $out[] = array("Encoder output dimesnions expected to be: ",$encoder_output_dimensions);

            $dst_path = $dst_folder . $dst_filename;

            $data = array(
            "source_file" => $src_path,
            "destination_file" => $dst_path,
            "encoder_options" => $encoder_option,
            "thumbnail_options" => array(
                "seconds" => $thumbnail_seconds,
                "size" => $thumb_width."x".$thumb_height,
                "format" => "jpg"
            ),
            #"segments_options" => array(
            #    "segment_time" => 3
            #),
            "callback_urls" => ""
            );


            if($file_obj->is_in_db($dst_path)) {
                $out[]=array("Notice: destination file is already listed in our DB, skipping",$dst_path);
                continue;
            }

            if(file_exists($dst_path)) {
                $out[]=array("Notice: destination file already exists [but isn't in the database], skipping",$dst_path);
                continue;
            }


            // convert paths to ones that my windows transcoder can understand
            $data['source_file'] = str_replace("/var/www/html/clients/projects/","N:\projects\\",$data['source_file']);
            $data['destination_file'] = str_replace("/var/www/html/clients/projects/","N:\projects\\",$data['destination_file']);

            // convert slashes to windows
            $data['source_file'] = str_replace("/","\\",$data['source_file']);
            $data['destination_file'] = str_replace("/","\\",$data['destination_file']);


            $out[] = array("About to request the following via POST to $encoder_url",$data);

            $result = json_post($encoder_url,$data);
            $out[] = array("Result from encoder:",$result);

            if($result == "{\"message\":\"The transcoder is not accepting jobs right now. Please try again later.\"}") {
                $out[] = array("Transcoder is busy, try again later");
                $f3->set('transcoder_full',1);
                break;
            }

            // if encoder has begun...
            $file = array(
                'version_id'=>$version['version_id'],
                'quality'=>$quality,
                'complete'=>0,
                'is_master'=>0,
                'filename'=>$dst_filename,
                'path'=>$dst_path,
                'source_path'=>$src_path,
                'width'=>$encoder_output_dimensions['width'],
                'height'=>$encoder_output_dimensions['height'],
                'filesize'=>'-1'
            );

            $out[] = $file_obj->add($file);
        }

        // Last but not least, add a reference to the master file in the `files` db
        
        $file = array(
            'version_id'=>$version['version_id'],
            'quality'=>parse_video_codec($src_attributes['codec_name']),
            'complete'=>1,
            'is_master'=>1,
            'filename'=>$src_filename,
            'path'=>$src_path,
            'source_path'=>$src_path,
            'width'=>$src_attributes['width'],
            'height'=>$src_attributes['height'],
            'filesize'=>filesize($src_path)
        );

        $out[] = $file_obj->add($file);
        
        // add a thumbnail
        $thumbnail_url = $dst_folder . substr($dst_filename,0,-4) . "-5.jpg"; // todo it isn't always -0
        $version_obj = new Version;
        $version_obj=$version_obj->populate($version['version_id']);

        if(!@file_exists($version_obj->thumb)) {
            // there is not a valid thumbnail set right now, so let's update while we're at it
            $version_obj->thumb = $thumbnail_url;
            $version_obj->save();
        }

        $version_obj->width=$src_attributes['width'];
        $version_obj->height=$src_attributes['height'];
        $version_obj->transcoded=1;
        $version_obj->save();


        return $out;
    }

}

class File {
    var $file_id;
    var $version_id;
    var $quality;
    var $complete;
    var $is_master;
    var $path;

    function get($file_id) {
        // todo error checking for bad file_id

        $f3 = \Base::instance();

        $files_db=new DB\SQL\Mapper($f3->get('DB'),'files');
        $file=$files_db->load(array('file_id=?',$file_id));

        $out = array(
            "file_id"=>$file->file_id,
            "version_id"=>$file->version_id,
            "quality"=>$file->quality,
            "complete"=>$file->complete,
            "is_master"=>$file->is_master,
            "hidden"=>$file->hidden,
            "filename"=>$file->filename,
            "path"=>$file->path, // todo check full_path still exists, is sanitized
            "width"=>$file->width,
            "height"=>$file->height,
            "filesize"=>$file->filesize,
            "filesize_h"=>format_bytes($file->filesize,0)
        );

        return $out;
    }

    function add($new_file) {
        $f3 = \Base::instance();
        $out="";
        $files_db=new DB\SQL\Mapper($f3->get('DB'),'files');


        $out[] = array("Preparing to add a new file to the database:",$new_file);
        // see if a similar (alternate) entry already exists
        $alt_file=$files_db->find(
            array('version_id=? AND quality=? AND path=?',$new_file['version_id'],$new_file['quality'],$new_file['path']),
            array()
        );

        if($alt_file) {
            $out[] = "Notice: skipping, there is already another file in the database (".$alt_file->file_id.") that matches the same version ID, quality, and path that this one was";
            return false;
        }

        $files_db->reset();
        
        $files_db->version_id = $new_file['version_id'];
        $files_db->quality = $new_file['quality'];
        $files_db->complete = $new_file['complete'];
        $files_db->is_master = $new_file['is_master'];
        $files_db->path = $new_file['path'];
        $files_db->source_path = $new_file['source_path'];
        $files_db->filesize = $new_file['filesize'];
        $files_db->filename = $new_file['filename'];
        $files_db->width = $new_file['width'];
        $files_db->height = $new_file['height'];

        $files_db->save();

        print_r($out);
        return $out;
    }

    function get_all($version_id) {
        // todo error checking for bad version_id
        $f3 = \Base::instance();

        $files_db=new DB\SQL\Mapper($f3->get('DB'),'files');
        $files=$files_db->find(array('version_id=?',$version_id),array(
            'order' => 'filesize DESC'
        ));

        $out = array();
        foreach($files as $file)
        {
            $out[] = array( // todo should this just call get() instead?
                "file_id"=>$file->file_id,
                "version_id"=>$version_id,
                "quality"=>$file->quality,
                "complete"=>$file->complete,
                "hidden"=>$file->hidden,
                "is_master"=>$file->is_master,
                "path"=>$file->path,
                "full_path"=>$f3->get('media_root').'/'.$file->path, // todo check full_path still exists, is sanitized
                "filesize"=>$file->filesize,
                "filesize_h"=>format_bytes($file->filesize,0)
            );
        }

        return $out;
    }

    // finds a file ID based on all this information, and then returns it
    function find($client_short=NULL,$project_short=NULL,$version_id,$quality) {
        $f3 = \Base::instance();
        $files_db=new DB\SQL\Mapper($f3->get('DB'),'files');
        $files=$files_db->load(array('version_id=? AND quality=?',$version_id,$quality));
        $file_id = $files->file_id;
        return $file_id;
    }
    // toddo make this more secure so people can't guess at version numbers

    function render_links($files) {
        $f3 = \Base::instance();

        $out = "";
        $f3->set('files',$files);

        return \Template::instance()->render('file_links.html');
    }

    function download($file) {
        // todo sanitize $file['path']
        $f3 = \Base::instance();
        $web = \Web::instance();

        $file_id = $file['file_id'];

        $push = new Push;
        $push->download(NULL,NULL,$file_id);

        /*
        $path = $file['path'];
        if(file_exists($path)) {
            $throttle = 0;
            $sent = $web->send($path, NULL, $throttle, TRUE);
            if(!$sent) echo "Error";

        }
        */

    }

    function is_in_db($dst_path) {
        $f3 = \Base::instance();

        $files_db=new DB\SQL\Mapper($f3->get('DB'),'files');
        $file=$files_db->load(array('path=?',$dst_path));

        if($dst_path == $file['path']) return true;
        else return false;
    }

    /*function get_file($file_id) {
        return array("file_id"=>"array with info about this file"); // todo
    }*/
}

class cms {

    function home($f3) {
        $view=new View;

        $client_obj = new Client;
        $clients = $client_obj->get_all();
        $f3->set('clients',$clients);
        $out = "";
        foreach($clients as $client)
        {
            $out .= "<p><a href=\"".$client['client_url']."\">".$client['client_full']."</a></p>";
        }
        $f3->set('out',$out);
        echo \Template::instance()->render('home.html');
    }

    function about() {

    }


    function client($f3) {

        $client_obj = new Client;
        $client = $f3->set('client', $client_obj->get());

        // get a list of projects for this client
        $project_obj = new Project;
        $projects = $project_obj->get_all($client['client_id']);
        $client_projects = "";
        foreach ($projects as $project) {
            if($project_obj->is_active($project)) continue; // todo active detection not yet working

            $project['thumb_embed'] = $f3->get('base_url').$f3->get('thumb_url').'/p/'.$project['project_id'];

            $client_projects .= $project_obj->render_project_summary($project);
        }

        // get vendor info
        $vendor = new Vendor;
        $vendor->get($client['vendor_id']);
        $vendor_array = $vendor->dump_vars();

        // render output
        $f3->set('projects',$projects);
        $f3->set('client_projects',$client_projects);
        $f3->set('title',$client['client_full']);
        $f3->set('vendor',$vendor_array);

        set_client_branding($client);

        echo \Template::instance()->render('clients.html');

    }


    function project($f3,$args) {

        global $version;
        global $project;
        global $client;
        global $f3;

        $client_obj = new Client;
        $client = $f3->set('client', $client_obj->get());

        $project_obj = new Project;
        $project = $f3->set('project', $project_obj->get());

        // get a list of projects for this client (used in the header)
        $project_obj = new Project;
        $projects = $project_obj->get_all($client['client_id']);

        $order_by = "alpha";
        if($project['drafts']){
            $f3->set('version_text',"Version ");
            $order_by = "date";
        }

        // find all versions

        $version_obj = new Version;
        $versions = $version_obj->get_all($project['project_id'],$order_by);

        $newest_version = max($versions);


        $version_summaries = "";
        foreach($versions as $version) {
            $version_summaries .= $version_obj->render_version_summary($version, $args['version_name']?$args['version_name']:$newest_version['version_name'],$order_by);
        }

        // display the latest version, unless otherwise sepcified
        $version = $newest_version;

        if(@$args['version_name']) {
            // make sure the version they're asking for is actually for this project
            foreach($versions as $_version) {
                if ($_version['version_name'] == $args['version_name']) {
                    $version = $_version;
                }
            }
        }

        // if this is a project where versions are used as incremental drafts, warn if they are not on the latest
        if($project['drafts']) {
            if ($version != $newest_version) {
                $f3->set('not_latest', TRUE);
                $f3->set('latest_link', $f3->get('site.url') . $args['client'] . '/' . $args['project']);
            }
        }



        // get vendor info
        $vendor = new Vendor;
        $vendor->get($client['vendor_id']);
        $vendor_array = $vendor->dump_vars();
        $f3->set('vendor',$vendor_array);

        // compile download links
        $file_obj = new File;
        $files = $file_obj->get_all($version['version_id']);;
        $project['download_links'] = $file_obj->render_links($files);

        // generate video embed code
        $video_embed = $version_obj->embed_video($version);

        // render output
        $f3->set('version',$version);
        $f3->set('version_summaries',$version_summaries);
        $f3->set('client',$client);
        $f3->set('projects',$projects);
        $f3->set('project',$project);
        $f3->set('title',$client['client_full'] . " | ". $project['project_full']);
        $f3->set('video_embed',$video_embed);

        if($args['edit'] == 'edit') {

            // function print_edit_version {
            // $edit_thumbnail_grid = ...
            // $edit_other_things = ...
            // return template for [all variables];
            // }

            // echo print_edit_version($version);



            /*$thumb = $version['thumb'];
            // get list of thumbnails
            $thumbnail_grid

            $f3->set('edit_thumbnails', $edit_thumbnails);
            // private admin view*/

            // TODO: authenticate as admin

            echo $this->print_edit_version();

            //echo \Template::instance()->render('edit_projects.html');

        }
        else {

            // public view

            set_client_branding($client);

            echo \Template::instance()->render('projects.html');
        }




        return;


    }


    function print_edit_version() {

        global $f3;
        global $version;
        global $project;
        global $client;




        // scrape all existing thumbnails



        $thumbnail_grid = "";
        $thumbnails = $this->crawl_for_thumbnails(); // create loop

        foreach($thumbnails as $thumbnail) {
            $f3->set('thumbnail',$thumbnail);
            $thumbnail_grid .= \Template::instance()->render('edit_thumbnail_grid.html');
        }

        $f3->set('thumbnail_grid',$thumbnail_grid);

        //$edit_other_things = ...

        return \Template::instance()->render('edit_version.html');
    }

    function crawl_for_thumbnails() {

        global $f3;
        global $version;
        global $project;
        global $client;

        $thumbnails = array();

        $f3 = \Base::instance();
        $path = $f3->get('project_root');
        $out = array();

        $db = $f3->get('DB');

        $out[]="Crawling through list of files in this transcodes folder";

        $drafts_dir = $path . "/" . $client['client_full'] . "/" . $project['project_full'] . "/Drafts";
        $transcodes_dir = $drafts_dir . "/Transcodes";
        $out[].="----Scanning $transcodes_dir";
        if (!$files = @scandir($transcodes_dir)) {
            $out[] .= "----Notice: can't find a transcodes folder here, skipping [$transcodes_dir]";
            return;
        }

        // loop through every file to create array of usable thumbnail paths & data
        for ($i = 0; $i < count($files); $i++) {

            if ($files[$i] == "." || $files[$i] == ".." || $files[$i] == "(template)" || $files[$i] == ".sync" || $files[$i] == "CacheClip" || $files[$i] == "Archive" || $files[$i] == "Transcodes" ) {
                continue;
            }

            if(!contains($files[$i],"[720]")) continue; // only bother with low res thumbnails

            if(!contains($files[$i],$version['version_name'])) continue; // filter out unless version name matches

            $extension = strtolower(substr($files[$i],-3));
            if ($extension != "jpg") continue;

            $thumbnails[$i]['filename'] = $filename = $files[$i];
            $thumbnails[$i]['full_path'] = $full_path = $transcodes_dir . '/' . $files[$i];

            preg_match("/(\d+)\.jpg/",$filename,$numbers);
            $thumbnails[$i]['number'] = $numbers;
            $thumbnails[$i]['base64'] = base64_encode(file_get_contents($full_path));

        }

        return($thumbnails);
    }



    function set($f3,$args) {
        global $f3;
        echo "<pre>";
        // todo authenticate as admin

        if($args['edit'] == 'set') {
            if($args['type'] == 'thumb') {


                $version = new Version;
                $db = $version->populate($args['version_id']);
                $_version = $version->get($args['version_id']);

                $client = new Client;
                $project = new Project;

                $_project = $project->get($_version['project_id']);
                $_client = $client->get($_project['client_id']);




                $path = $f3->get('project_root').'/'.$_client['client_full'].'/'.$_project['project_full'].'/Drafts/Transcodes/'.substr($_version['version_master_filename'],0,-4).' [720]-'.$args['val'].'.jpg';



                $db->thumb=$path;
                $db->save();

                $return_url = $f3->get('base_url') .'/'.$_client['client_short'].'/'.$_project['project_short'].'/'.$_version['version_name'];
                header("Location:".$return_url);
/*
                // debugging
                print_r($_client);
                print_r($_version);
                print_r($_project);
                print_r($args);
                echo "<hr />";
                echo $path;
                echo "<br />";
                echo $return_url;
*/

            }
        }

    }


    function dl($f3,$args) {
        /* echo "<b>";
         if($args['dl'] == 'dl') echo "Downloading"; else echo "Previewing";
         echo "</b> client: <b>".$args['client']."</b>, project: <b>".$args['project']."</b>";
         if($args['version']) echo ", version <b>".$args['version']."</b>";
         else echo ", latest version";
         if($args['quality']) echo ", quality <b>".$args['quality']."</b>";
         else echo ", automatic quality";*/

        $file_obj = new File();
        $file_id = $file_obj->find($args['client'],$args['project'],$args['version'],$args['quality']);

        $file = $file_obj->get($file_id);

        $file_obj->download($file);


    }

    function bounce() {
        #header("Location:http://allenellis.com");
    }

    function vendorlist() {
        echo "<pre>";
        $out = array();
        $vendor = new Vendor;
        $vendor->get(3);
        $vars = $vendor->dump_vars();
        $out[] = array("Variables are",$vars);
        print_r($out);


    }

}
