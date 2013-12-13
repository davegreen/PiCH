<?php 
require "db.php";
$database = new database();
require "header.php";
?>
<div class="container">
  <h1>Rules</h1>
  <p>Display all rules here and whether they are attached to an active schedule, have a button to add new schedules at the bottom.</p>
  <?php
  $rules = $database->getRules();
  if ($rules)
  {
    print "<table class='table table-striped table-hover'>";
    print "<tr><th>Schedule</th><th>Sensor</th><th>Target Temp</th></tr>";
    foreach ($rules as $rule)
    {
      print "<tr><td>". $rule['friendlyname'] ."</td>";
      print "<td>". $rule['sensorname'] ."</td>";
      print "<td>". $rule['targettemp'] ."&deg;C</td></tr>";
    }

    print "</table>";
  }

  else
  {
    print "<p>No rules have been defined.</p>";
  }
  ?>
</div> <!-- /container -->    
<?php require "footer.php"; ?>