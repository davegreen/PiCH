<?php 
require "db.php";
require "shell.php";
$shell = new shell();
$database = new database();
require "header.php";
?>
<div class="container">
  <h1>Scheduling</h1>
  <?php
  if (isset($_POST['name']))
  {
    $days = "0000000";
    
    if (isset($_POST['mon']))
    {
      $days[0] = "1";
    }

    if (isset($_POST['tue']))
    {
      $days[1] = "1";
    }
    
    if (isset($_POST['wed']))
    {
      $days[2] = "1";
    }
    
    if (isset($_POST['thu']))
    {
      $days[3] = "1";
    }
    
    if (isset($_POST['fri']))
    {
      $days[4] = "1";
    }
    
    if (isset($_POST['sat']))
    {
      $days[5] = "1";
    }
    
    if (isset($_POST['sun']))
    {
      $days[6] = "1";
    }
    
    if (!$database->addNewSchedule($_POST['name'], $days, $_POST['timestart'], $_POST['timeend']))
    {
      print "<p>Cannot add schedule, make sure schedules have a unique name and do not overlap</p>";
    }
  }

  elseif (isset($_POST['schedule']))
  {
    $rule = $database->addNewRule($_POST['schedule'], $_POST['sensor'], $_POST['targettemp']);
    if (!$rule)
    {
      print "<p>The rule was not added. This may be because only one rule is currently allowed per schedule.</p>";
    }

    else
    {
      print "<p>Rule added.</p>";
      $activeschedule = $database->getCurrentSchedule();
      if ($activeschedule)
      {
        $shell->RunThermostat($activeschedule['id']);
      }
    }
  }

  elseif (isset($_POST['drule']))
  {
    $database->deleteRule($_POST['drule']);
  }

  elseif (isset($_POST['scheduleid']))
  {
    $database->changeScheduleState($_POST['scheduleid']);
  }

  elseif (isset($_POST['delscheduleid']))
  {
    $database->removeSchedule($_POST['delscheduleid']);
  }

  #$cron = explode("\n", $shell->getCron());
  $cron = array();
  $schedules = $database->getSchedules();

  if ($schedules)
  {
    print "<table class='table table-striped table-hover'>";
    foreach ($schedules as $schedule)
    {
      if ($schedule['enabled'])
      {
        $cron[] = $shell->convertScheduleToCron($schedule);
      }

      print "<tr><th>Name</th><th>Days</th><th>Time Start</th><th>Time End</th><th colspan='2'>Status</th></tr>";
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

      if ($schedule['enabled'] == 0)
      {
        print "<td><form method='post' action='". $_SERVER['PHP_SELF']. "'>";
        print "<input type='hidden' name='scheduleid' value=". $schedule['id'] .">";
        print "<button class='btn btn-success btn-block' type='submit'>Enable</button></form></td>";
      }

      else
      {
        print "<td><form method='post' action='". $_SERVER['PHP_SELF']. "'>";
        print "<input type='hidden' name='scheduleid' value=". $schedule['id'] .">";
        print "<button class='btn btn-warning btn-block' type='submit'>Disable</button></form></td>";
      }

      print "<td><form method='post' action='". $_SERVER['PHP_SELF']. "'>";
      print "<input type='hidden' name='delscheduleid' value=". $schedule['id'] .">";
      print "<button class='btn btn-danger btn-block' type='submit'>Remove Schedule</button></form></td></tr>";

      $rules = $database->getScheduleRules($schedule['id']);

      if ($rules)
      {
        print "<tr><th></th><th>Sensor</th><th>Target Temp</th><th colspan='3'></th></tr>";
      
        foreach ($rules as $rule)
        {
          print "<tr><td></td><td>". $rule['friendlyname'] ."</td>";
          print "<td>". $rule['targettemp'] ."&deg;C</td><td></td>";
          print "<td colspan='2'><form method='post' action='". $_SERVER['PHP_SELF']. "'>";
          print "<input type='hidden' name='drule' value=". $rule['id'] .">";
          print "<button class='btn btn-danger btn-block' type='submit'>Remove Rule</button></form></td></tr>";
        }
      
        print "<tr><td></td><td></td><td></td><td></td><td></td></tr>";
      }
    }

    print "</table>";
    $uniques = array_unique($cron);
    print implode("\n", $uniques);
    $shell->commitToCron(implode("\n", $uniques) . "\n");
  }

  else
  {
    print "<p>No schedules have been defined.</p>";
  }
  
  ?>
  <h2>Add New Schedule</h2>
  <form action="scheduling.php" method="post">
  <table class='table table-striped table-hover'>
  <tr><th>Name</th><th>Days</th><th>Start - End</th><th></th></tr>
  <tr><td><input type="text" maxlength="50" name="name" required /></td>
    <td><label class="checkbox inline"><input type="checkbox" name="mon" value="mon" />Mon</label>
    <label class="checkbox inline"><input type="checkbox" name="tue" value="tue" />Tue</label>
    <label class="checkbox inline"><input type="checkbox" name="wed" value="wed" />Wed</label><br />
    <label class="checkbox inline"><input type="checkbox" name="thu" value="thu" />Thu</label>
    <label class="checkbox inline"><input type="checkbox" name="fri" value="fri" />Fri</label>
    <label class="checkbox inline"><input type="checkbox" name="sat" value="sat" />Sat</label>
    <label class="checkbox inline"><input type="checkbox" name="sun" value="sun" />Sun</label></td>
    <td><div class='input-prepend input-append'><input class='input-mini' type="time" name="timestart" required />
      <span class="add-on">/</span><input class='input-mini' type="time" name="timeend" required /></div></td>
    <td><input type="submit" class="btn btn-primary btn-block" value="Add" /></td></tr>
  </table></form>  
  <?php
  $schedules = $database->getSchedules();
  $sensors = $database->getSensors();
  if ($schedules && $sensors)
  {
    ?>
    <h2>Add New Rule</h2>
    <p>Currently only one schedule per rule is supported.</p>
    <form action="scheduling.php" method="post">
    <table class='table table-striped table-hover'>
    <tr><th>Schedule</th><th>Sensor</th><th>Target Temp</th><th></th></tr>
    <tr><td><select name="schedule">
      <?php
      foreach ($schedules as $schedule)
      {
        print "<option value=". $schedule['id'] .">". $schedule['friendlyname'] ."</option>";
      }
      ?>
      </select></td><td>
      <select name="sensor">
      <?php
      foreach ($sensors as $sensor)
      {
        print "<option value=". $sensor['uid'] .">". $sensor['friendlyname'] ."</option>";
      }
      ?>
      </select></td>
      <td><div class='input-append'>
        <input class='input-mini' type="number" name="targettemp" min="5" max="30" step="0.1" value="15">
        <span class="add-on">&deg;C</span></td>
      <td><input type="submit" class="btn btn-primary btn-block" value="Add" /></td></tr>
    </table></form>  
  <?php
  }
  ?>  
</div> <!-- /container -->  
<?php require "footer.php"; ?>
