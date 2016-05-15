<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="assets/ico/favicon.ico">

    <title><?php echo $title; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo $site['url']; ?>ui/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?php echo $site['url']; ?>ui/css/style.css" rel="stylesheet">
    <link href="<?php echo $site['url']; ?>ui/css/font-awesome.min.css" rel="stylesheet">


    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="<?php echo $site['url']; ?>ui/js/modernizr.js"></script>
</head>

<body>  <!-- Fixed navbar -->
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="http://allenellis.com">ALLEN ELLIS</a>
        </div>
        <div class="navbar-collapse collapse navbar-right">
            <ul class="nav navbar-nav">
                <li><a href="<?php echo $client['client_url']; ?>">HOME</a></li>
                <li><a href="<?php echo $site['url']; ?>about">ABOUT</a></li>
                <li><a href="<?php echo $site['url']; ?>contact">CONTACT</a></li>
                <li class="dropdown active">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">PROJECTS <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <?php foreach (($projects?:array()) as $project=>$val): ?>
                            <li><a href="<?php echo $val['project_url']; ?>"><?php echo $val['project_full']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>
