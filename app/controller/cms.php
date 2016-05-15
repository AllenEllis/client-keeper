<?php
/**
 * Created by PhpStorm.
 * User: Allen
 * Date: 5/14/2016
 * Time: 8:43 PM
 */


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
            "client_full"=> $clients->client_full
        );

        return $out;
    }

}

class Project {
    var $project_id;
    var $client_id;
    var $project_short;
    var $project_full;

    function get($projectID=NULL) {
        $f3 = \Base::instance();

        $project_short = $f3->get('PARAMS.project');
        $projects=new DB\SQL\Mapper($f3->get('DB'),'projects');
        $projects->load(array('project_short=?',$project_short));

        $out = array(
            "project_id"=>$projects->project_id,
            "client_id"=>$projects->client_id,
            "project_short"=>$project_short,
            "project_full"=> $projects->project_full
        );

        return $out;
    }


    function get_all($client_id) {
        // todo error checking for bad client_id
        $f3 = \Base::instance();

        $project_short = $f3->get('PARAMS.project');
        $projects_db=new DB\SQL\Mapper($f3->get('DB'),'projects');
        $projects=$projects_db->find(array('client_id=?',$client_id));

        $out = array();
        foreach($projects as $project)
        {
            $out[] = array(
                "project_id"=>$project->project_id,
                "client_id"=>$project->client_id,
                "project_short"=>$project->project_short,
                "project_full"=>$project->project_full,
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

    function get($version_id=NULL) {
       /* $f3 = \Base::instance();
        if($version_id){

            $versions=new DB\SQL\Mapper($f3->get('DB'),'versions');
            $versions->load(array('version_id=?',$version_id));

            $out = array(
                "version_id"=>$versions->version_id,
                "project_id"=>$versions->project_id,
                "version"=>$versions->version,
                "timestamp"=>$versions->timestamp,
            );

            print_r($out);
            echo "I just ran";

            return $out;
        }
        else
        {
            $version = $f3->get('PARAMS.version');
        }*/
    }

    function get_all($project_id) {
        // todo error checking for bad project_id
        $f3 = \Base::instance();

        $versions_db=new DB\SQL\Mapper($f3->get('DB'),'versions');
        $versions=$versions_db->find(array('project_id=?',$project_id));

        $out = array();
        foreach($versions as $version)
        {
            $out[] = array(
                "version_id"=>$version->version_id,
                "project_id"=>$version->project_id,
                "version"=>$version->version,
                "timestamp"=>$version->timestamp,
                "datetime"=>$version->timestamp, // todo, add timestamp calculation here
                "thumb"=>$version->thumb
            );
        }

        return $out;

    }

    function render_version_summary($version) {
        $f3 = \Base::instance();
        $f3->set('version',$version);

        $file_obj = new File;
        $files = $file_obj->get_all($version['version_id']);

        $out = "";
        $f3->set('files',$files);

        return \Template::instance()->render('version_summary.html');
    }


}

class File {
    var $file_id;
    var $version_id;
    var $quality;
    var $complete;
    var $is_master;
    var $path;

    function get_all($version_id) {
        // todo error checking for bad version_id
        $f3 = \Base::instance();

        $files_db=new DB\SQL\Mapper($f3->get('DB'),'files');
        $files=$files_db->find(array('version_id=?',$version_id));

        $out = array();
        foreach($files as $file)
        {
            $out[] = array(
                "file_id"=>$file->file_id,
                "version_id"=>$version_id,
                "quality"=>$file->quality,
                "complete"=>$file->complete,
                "is_master"=>$file->is_master,
                "path"=>$file->path
            );
        }

        return $out;
    }

    /*function get_file($file_id) {
        return array("file_id"=>"array with info about this file"); // todo
    }*/
}

class cms {

    function home() {
        $view=new View;
        echo $view->render('home.html');
    }

    function about() {
        echo "About me";
    }


    function client($f3) {

        $client_obj = new Client;
        $client = $f3->set('client', $client_obj->get());

        // get a list of projects for this client
        $project_obj = new Project;
        $projects = $project_obj->get_all($client['client_id']);
        $client_projects = "";
        foreach ($projects as $project) {
            $client_projects .= $project_obj->render_project_summary($project);
        }

        // render output
        $f3->set('client_projects',$client_projects);
        $f3->set('title',$client['client_full']);

        echo \Template::instance()->render('clients.html');

    }


    function project($f3,$args) {

        $client_obj = new Client;
        $client = $f3->set('client', $client_obj->get());

        $project_obj = new Project;
        $project = $f3->set('project', $project_obj->get());

        // find all versions
        
        $version_obj = new Version;
        $versions = $version_obj->get_all($project['project_id']);

        $projects_versions = "";
        foreach($versions as $version) {
            $version_summaries .= $version_obj->render_version_summary($version);
        }

        // render output
        $f3->set('version_summaries',$version_summaries);
        $f3->set('client',$client);
        $f3->set('project',$project);
        $f3->set('title',$client['client_full'] . " / ". $project['project_full']);


        echo \Template::instance()->render('projects.html');

        return;


    }

    function dl($f3,$args) {
        echo "<b>";
        if($args['dl'] == 'dl') echo "Downloading"; else echo "Previewing";
        echo "</b> client: <b>".$args['client']."</b>, project: <b>".$args['project']."</b>";
        if($args['version']) echo ", version <b>".$args['version']."</b>";
        else echo ", latest version";
        if($args['quality']) echo ", quality <b>".$args['quality']."</b>";
        else echo ", automatic quality";
    }


}