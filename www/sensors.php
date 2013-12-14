<?php 
require "db.php";
$database = new database();
require "header.php";
?>
<div class="container">
  <h1>Sensors</h1>
  <p>Scanning for lifeforms captain! Display the sensors here and allow them to be named.</p>
  <?php
  
  if ((isset($_POST['sensorid'])) && !(isset($_POST['sensorname'])))
  {
    $database->changeSensorState($_POST['sensorid']);
  }

  if ((isset($_POST['sensorid'])) && (isset($_POST['sensorname'])))
  {
    $database->updateSensorName($_POST['sensorid'], $_POST['sensorname']);
  }

  if ((isset($_POST['sensorid'])) && (isset($_POST['offset'])))
  {
    $database->updateSensorOffset($_POST['sensorid'], $_POST['offset']);
  }
  
  $sensors = $database->getSensors();

  if ($sensors)
  {
    print "<table class='table table-hover table-condensed table-responsive'>";

    foreach ($sensors as $sensor)
    {
	  
	  
	  if ($sensor['enabled'])
	  {
	    print "<tr class='success'>";
		print "<td><form method='post' action='". $_SERVER['PHP_SELF']. "'>";
        print "<input type='hidden' name='sensorid' value='". $sensor['uid'] ."'>";
        print "<button class='btn btn-danger btn-block' type='submit'>Disable</button></form></td>";
	  }
	  
	  else
	  {
	    print "<tr class='danger'>";
		print "<td><form method='post' action='". $_SERVER['PHP_SELF']. "'>";
        print "<input type='hidden' name='sensorid' value='". $sensor['uid'] ."'>";
        print "<button class='btn btn-success btn-block' type='submit'>Enable</button></form></div></td>";
	  }
	  
      print "<form method='post' action='". $_SERVER['PHP_SELF']. "'><td><button class='btn btn-success' type='submit'>Update</button></td>";
      print "<td>Sensor: ".$sensor['uid']."</td><input type='hidden' name='sensorid' value='". $sensor['uid'] ."'>";
      print "<td>Name: <input type='text' name='sensorname' value='". $sensor['friendlyname'] ."'></div></td>";
      print "<td>Temp. Offset: <input type='number' name='offset' value='". $sensor['offset'] ."'></div></td></form></tr>";
    }
	
	print "</table>";
  }

  else
  {
    print "<p>No sensors defined or enabled.</p>";
  }
  ?>
</div> <!-- /container --> 
<?php require "footer.php"; ?>
