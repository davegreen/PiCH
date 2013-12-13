<?php 
require "db.php";
$database = new database();
require "header.php";
?>
<div class="container">
  <h1>Temperature Log</h1>
  <p>Display some temp logs.</p>
  <?php
  $time = 30;

  if (isset($_POST['time']))
  {
    $time = $_POST['time'];
  }

  print "<table class='table table-striped table-hover'>";
  print "<tr><td><form class='form-inline' method='post' action='". $_SERVER['PHP_SELF']. "'>";
  print "<div class='input-prepend input-append'><span class='add-on'>View logs for the past</span>";
  print "<input type='number' class='input-mini' name='time' min='30' max='300' step='5' value='". $time ."'>";
  print "<span class='add-on'>Minutes</span></div></td><td>";
  print "<input type='submit' class='btn btn-success btn-block' value='Go' /></td></tr>";
  print "</table></form>";
  $templog = $database->getTempsLog($time * 60);
  if ($templog)
  {
    print "<table class='table table-striped table-hover'>";
    print "<tr><th>Sensor</th><th>Temperature</th><th>Time</th></tr>";
    
    foreach ($templog as $log)
    {
      print "<tr><td>". $log['friendlyname'] ."</td>";
      print "<td>". $log['temperature'] ."&deg;C</td>";
      print "<td>". date("H:i",strtotime($log['timestamp'])) ."</td></tr>";
    }

    print "</table>";
  }

  else
  {
    print "<p>No logs.</p>";
  }
  ?>
</div> <!-- /container -->    
<?php require "footer.php"; ?>