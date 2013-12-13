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
  print "<h1>Heating: ON</h1>";
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
    print "<h1>Heating: OFF</h1>";
    print "<table class='table table-striped table-hover'>";
    print "<tr><td>Target a temperature for a number of minutes</td>";
    print "<td><form class='form-inline' method='post' action='". $_SERVER['PHP_SELF']. "'>";
    print "<div class='input-prepend input-append'><span class='add-on'>Sensor</span><select name='sensor' class='input-medium'>";
    
    foreach ($sensors as $sensor)
    {
      print "<option value=". $sensor['uid'] .">". $sensor['friendlyname'] ."</option>";
    }
    
    print "</select></div></td><td><div class='input-prepend input-append'><span class='add-on'>Temp</span>";
    print "<input class='input-mini' type='number' name='targettemp' min='5' max='30' step='0.1' value='15'>";
    print "<span class='add-on'>&deg;C</span></div></td><td>";
    print "<div class='input-prepend input-append'><span class='add-on'>Time</span>";
    print "<input type='number' class='input-mini' name='time' min='30' max='300' step='5' value='30'>";
    print "<span class='add-on'>Minutes</span></td><td><input type='hidden' name='status' value='1'>";
    print "<input type='submit' class='btn btn-success' value='Turn On' /></td></tr>";
    print "</table></form>";
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