<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <?php $page = substr(ucfirst(strtolower(basename($_SERVER['PHP_SELF']))),0,strlen(basename($_SERVER['PHP_SELF']))-4); ?>
    <title><?php print $page; ?> - PiCentralHeating</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dave Green (david.green@tookitaway.co.uk)">

    <!-- Le styles -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <?php 
          $status = $database->checkStatus(); 
          if ($status['status'] == true)
          {
            print "<a class='brand'>PiCH: ON (" . date("H:i",strtotime($status['timestamp'])) . ")</a>";
          }

          else
          {
            print "<a class='brand'>PiCH: OFF (" . date("H:i",strtotime($status['timestamp'])) . ")</a>";
          }
          ?>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li <?php if($page == "Status"){print "class='active'";} ?>><a href="status.php">Status</a></li>
              <li <?php if($page == "Scheduling"){print "class='active'";} ?>><a href="scheduling.php">Scheduling</a></li>
              <li <?php if($page == "Schedules"){print "class='active'";} ?>><a href="schedules.php">Schedules</a></li>
              <li <?php if($page == "Rules"){print "class='active'";} ?>><a href="rules.php">Rules</a></li>
              <li <?php if($page == "Sensors"){print "class='active'";} ?>><a href="sensors.php">Sensors</a></li>
              <li <?php if($page == "Log"){print "class='active'";} ?>><a href="log.php">Log</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>