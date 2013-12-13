<?php 
require "db.php";
$database = new database();
require "header.php";
?>
<div class="container">
  <h1>Schedules</h1>
  <p>Display all schedules here, active ones should be greyed out. Schedules should be toggleable on/off, have a button to add new schedules at the bottom.</p>
  <?php
  $schedules = $database->getSchedules();
  
  if ($schedules)
  {
    print "<table class='table table-striped table-hover'>";
    print "<tr><th>Name</th><th>Days</th><th>Time Start</th><th>Time End</th><th>Enabled</th></tr>";
    
    foreach ($schedules as $schedule)
    {
      print "<tr><td>". $schedule['friendlyname'] ."</td><td>";
      
      if ($schedule['dayofweek'] == "1111111")
      {
        print "All";
      }

      elseif ($schedule['dayofweek'] == "0000000")
      {
        print "None";
      }

      else
      {
        $dow = str_split($schedule['dayofweek']);
        $i = 0;
        $days = array();

        foreach ($dow as $day)
        {
          if ($day == 1 && $i == 0)
          {
            $days[] = "Mon";
          }

          if ($day == 1 && $i == 1)
          {
            $days[] = "Tue";
          }
          
          if ($day == 1 && $i == 2)
          {
            $days[] = "Wed";
          }
          
          if ($day == 1 && $i == 3)
          {
            $days[] = "Thur";
          }
          
          if ($day == 1 && $i == 4)
          {
            $days[] = "Fri";
          }
          
          if ($day == 1 && $i == 5)
          {
            $days[] = "Sat";
          }
          
          if ($day == 1 && $i == 6)
          {
            $days[] = "Sun";
          }

          $i++;
        }

        print implode(", ", $days);
      }
      
      print "</td><td>". date("H:i",strtotime($schedule['timestart'])) ."</td>";
      print "<td>". date("H:i",strtotime($schedule['timeend'])) ."</td>";
      print "<td>". $schedule['enabled'] ."</td></tr>";
    }
    
    print "</table>";
  }

  else
  {
    print "<p>No schedules have been defined.</p>";
  }
  ?>
</div> <!-- /container -->  
<?php require "footer.php"; ?>