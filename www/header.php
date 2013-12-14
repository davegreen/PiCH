<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <?php $page = substr(ucfirst(strtolower(basename($_SERVER['PHP_SELF']))),0,strlen(basename($_SERVER['PHP_SELF']))-4); ?>
    <title><?php print $page; ?> - PiCentralHeating</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dave Green (david.green@tookitaway.co.uk)">
    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <nav class="navbar navbar-default" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
        <?php 
          $status = $database->checkStatus(); 
          if ($status['status'] == true)
          {
            print "<a class='navbar-brand'>PiCH: ON (" . date("H:i",strtotime($status['timestamp'])) . ")</a>";
          }

          else
          {
            print "<a class='navbar-brand'>PiCH: OFF (" . date("H:i",strtotime($status['timestamp'])) . ")</a>";
          }
          ?>
    </div>
	<!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
          <li <?php if($page == "Status"){print "class='active'";} ?>><a href="status.php">Status</a></li>
          <li <?php if($page == "Scheduling"){print "class='active'";} ?>><a href="scheduling.php">Scheduling</a></li>
          <li <?php if($page == "Schedules"){print "class='active'";} ?>><a href="schedules.php">Schedules</a></li>
          <li <?php if($page == "Rules"){print "class='active'";} ?>><a href="rules.php">Rules</a></li>
          <li <?php if($page == "Sensors"){print "class='active'";} ?>><a href="sensors.php">Sensors</a></li>
          <li <?php if($page == "Log"){print "class='active'";} ?>><a href="log.php">Log</a></li>
        </li>
      </ul>
	  <ul class="nav navbar-nav navbar-right">
        <li><a href="#">
		<?php
		if ($status['status'] == true)
        {
		  print "Heating: ON";
		}
		else
		{
		  print "Heating: OFF";
		}
		?>
		</a></li>
	  </ul>
    </div><!-- /.navbar-collapse -->
    </nav>