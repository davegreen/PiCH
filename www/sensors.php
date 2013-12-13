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
    print "<table class='table table-striped table-hover'>";

    foreach ($sensors as $sensor)
    {
      print "<tr><td>".$sensor['uid']."</td>";
      print "<form method='post' action='". $_SERVER['PHP_SELF']. "'>";
      print "<input type='hidden' name='sensorid' value='". $sensor['uid'] ."'>";
      print "<td><div class='input-prepend input-append'><span class='add-on'>Name</span>";
      print "<input type='text' name='sensorname' value='". $sensor['friendlyname'] ."'></div></td>";
      print "<td><div class='input-prepend input-append'><span class='add-on'>Offset Value</span>";
      print "<input type='number' name='offset' value='". $sensor['offset'] ."'></div></td>";
      print "<td><button class='btn btn-success' type='submit'>Update</button></td></form>";      

      if ($sensor['enabled'] == 0)
      {
        print "<td><form method='post' action='". $_SERVER['PHP_SELF']. "'>";
        print "<input type='hidden' name='sensorid' value='". $sensor['uid'] ."'>";
        print "<button class='btn btn-success btn-block' type='submit'>Enable</button></form></div></td></tr>";
      }

      else
      {
        print "<td><form method='post' action='". $_SERVER['PHP_SELF']. "'>";
        print "<input type='hidden' name='sensorid' value='". $sensor['uid'] ."'>";
        print "<button class='btn btn-warning btn-block' type='submit'>Disable</button></form></td></div></tr>";
      }
    }
  }

  else
  {
    print "<p>No sensors defined or enabled.</p>";
  }
  ?>
  </table>
</div> <!-- /container --> 
<?php require "footer.php"; ?>
