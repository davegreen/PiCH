<?php 
require "db.php";
$database = new database();
require "header.php";

if (isset($_POST['status']))
{
  if (!$_POST['status'])
  {
    $database->changeStatus($_POST['status']);
  }
  
  elseif (isset($_POST['sensor']) && isset($_POST['targettemp']) && isset($_POST['time']))
  {
    if (!$database->addInstantSchedule($_POST['sensor'], $_POST['targettemp'], $_POST['time']))
	{
	  print "<p>Cannot add schedule, adding this schedule would overlap with another schedule</p>";
	}
  }
}

print "<div class='container'>";

if ($status['status'] == true)
{
  $activeschedule = $database->getCurrentSchedule();
  
  if ($activeschedule)
  {
    print "<table class='table table-striped table-hover'>";
    print "<tr><th>Current Schedule</th><td>";
    print "<form class='form-inline' method='post' action='". $_SERVER['PHP_SELF']. "'>";
    print "<input type='hidden' name='status' value='0'>". $activeschedule['friendlyname'] ."</td>";
    print "<td><button class='btn btn-warning' type='submit'>Turn Off</button></form></td></tr>";
    print "<tr><th>Started</th><td colspan='2'>". date("H:i",strtotime($activeschedule['pretimestart'])) ."</td></tr>";
    print "<tr><th>End At</th><td colspan='2'>". date("H:i",strtotime($activeschedule['timeend'])) ."</td></tr>";
    print "<tr><th>Target Temp</th><td colspan='2'>". $activeschedule['targettemp'] ."&deg;C</td></tr>";
    print "</table>";
  }

  else
  {
    print "<form class='form-inline' method='post' action='". $_SERVER['PHP_SELF']. "'>";
    print "<input type='hidden' name='status' value='0'>". $activeschedule['friendlyname'];
    print "<button class='btn btn-warning' type='submit'>Turn Off</button></form>";
  }
}

else
{
  $sensors = $database->getCurrentSensorData();

  if ($sensors)
  {
    print "Target a temperature for a number of minutes";
    print "<form role='form' method='post' action='". $_SERVER['PHP_SELF']. "'>";
    print "<div class='form-group'><label class='control-label'>Sensor</label><div class='col-xs-2'><select class='form-control' name='sensor'>";
    
    foreach ($sensors as $sensor)
    {
      print "<option value=". $sensor['uid'] .">". $sensor['friendlyname'] ."</option>";
    }
    
    print "</select></div></div><div class='form-group'><label class='control-label'>Temp (&deg;C)</label>";
    print "<div class='col-xs-2'><input class='form-control' placeholder='.col-xs-2' type='number' name='targettemp' min='5' max='30' step='0.1' value='15'></div></div>";
    print "<div class='form-group'><label class='control-label'>Time (Mins)</label>";
    print "<div class='col-xs-2'><input type='number' class='form-control' placeholder='.col-xs-2' name='time' min='30' max='300' step='5' value='30'></div></div>";
    print "<input type='hidden' name='status' value='1'><input type='submit' class='btn btn-success' value='Turn On' /></form>";
  }
}
print "<h2>Sensors</h2>";
print "<table class='table table-striped table-hover'>";
$currenttemps = $database->getCurrentSensorData();

if ($currenttemps)
{
  foreach ($currenttemps as $temp)
  {
    print "<tr><td>".$temp['friendlyname']."</td><td>".$temp['temperature']."&deg;C</td></tr>";
  }
}

else
{
  print "<tr><td>No sensors defined or enabled.</td><td>N/A</td></tr>";
}

print "</table>Last Updated: " .date("H:i",strtotime($temp['timestamp']));
?>
</div> <!-- /container -->
<?php require "footer.php"; ?>