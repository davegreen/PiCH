<?php
Class Database
{

  Function connect()
  {
    $host = "localhost";
    $user = "pich";
    $pass = "centralheating";
    $db   = "pich";
    $link = null;

    try
    {
      $link = new PDO("mysql:host=$host;dbname=$db", $user, $pass);  
    }
    catch(PDOException $e)
    {
      echo $e->getMessage();
    }

    return $link;
  }

  Function checkStatus()
  {
    $link = $this->connect();
    $query = $link->query("SELECT * FROM status ORDER BY status.timestamp DESC LIMIT 1;");
    return $query->fetch();
  }

  Function changeStatus($state)
  {
    $link = $this->connect();
    $query = $link->prepare("INSERT INTO status (status) VALUES (?);");
    $query->execute(array($state));
  }

  Function getSchedules()
  {
  	$link = $this->connect();
  	$query = $link->query("SELECT * FROM schedules;");
    $num = $query->rowCount();

    if ($num == 0)
    {
      return false;
    }

    else
    {
      return $query->fetchAll();
    }
  }

  Function getCurrentSchedule()
  {
    $time = time() - date("Z");
    $date = getdate($time);
    $sqltime = date("H:i:s", $time);
    $dow = null;

    switch ($date['wday'])
    {
      case 1:
        $dow = '1%';
        break;
      case 2:
        $dow = '_1_____';
        break;
      case 3:
        $dow = '__1____';
        break;
      case 4:
        $dow = '___1___';
        break;
      case 5:
        $dow = '____1__';
        break;
      case 6:
        $dow = '_____1_';
        break;
      case 0:
        $dow = '%1';
        break;
    }

    $link = $this->connect();
    $query = $link->prepare("SELECT schedules.id, schedules.friendlyname, schedules.pretimestart, schedules.timestart, schedules.timeend, AVG(rules.targettemp) AS 'targettemp' FROM schedules INNER JOIN rules ON rules.schedule = schedules.id WHERE schedules.enabled = 1 AND schedules.dayofweek LIKE ? AND schedules.pretimestart < ? AND schedules.timeend > ? GROUP BY schedules.id ORDER BY schedules.pretimestart ASC;");
    $query->execute(array($dow, $sqltime, $sqltime));
    $num = $query->rowCount();

    if ($num == 0)
    {
      return false;
    }

    else
    {
      return $query->fetch();
    }
  }

  Function addInstantSchedule($sensorid, $temp, $length)
  {
    $starttime = time() - date("Z");
    $date = getdate($starttime);
    $endtime = $starttime + ($length * 60);
    $name = ("InstantSchedule-" .date("l-H:i", $starttime). "-". $length ."mins");
    $dow = "0000000";
    
    switch ($date['wday'])
    {
      case 1:
        $dow = '1000000';
        break;
      case 2:
        $dow = '0100000';
        break;
      case 3:
        $dow = '0010000';
        break;
      case 4:
        $dow = '0001000';
        break;
      case 5:
        $dow = '0000100';
        break;
      case 6:
        $dow = '0000010';
        break;
      case 0:
        $dow = '0000001';
        break;
    }
    
    if (!$this->checkScheduleOverlap($dow, date("H:i", $timestart), date("H:i", $timeend)))
    {
      $link = $this->connect();
      $query = $link->prepare("INSERT INTO schedules (friendlyname, dayofweek, pretimestart, timestart, timeend, enabled) VALUES (?, ?, ?, ?, ?, ?);");
      $query->execute(array($name, $dow, date("H:i", $starttime), date("H:i", $starttime), date("H:i", $endtime), "1"));
      $scheduleid = $link->lastInsertId();
      $query = $link->prepare("INSERT INTO rules (schedule, sensor, targettemp) VALUES (?, ?, ?);");
      $query->execute(array($scheduleid, $sensorid, $temp));
      
      #FIXME: ONLY FOR TESTING, REMOVE ONCE THE SCHEDULER GETS IN ON THE ACTION.
      #$this->changeStatus("1");
      return true;
    }

    else
    {
      return false;
    }
  }

  Function removeSchedule($sid)
  {
    $link = $this->connect();
    $query = $link->prepare("DELETE FROM schedules WHERE schedules.id = ?;");
    $query->execute(array($sid));
  }

  Function getRules()
  {
  	$link = $this->connect();
  	$query = $link->query("SELECT sensors.friendlyname AS 'sensorname', rules.sensor, rules.targettemp, schedules.friendlyname FROM rules INNER JOIN schedules ON rules.schedule = schedules.id INNER JOIN sensors ON sensors.uid = rules.sensor WHERE sensors.enabled = 1 ORDER BY schedules.id ASC;");
    $num = $query->rowCount();

    if ($num == 0)
    {
      return false;
    }

    else
    {
      return $query->fetchAll();
    }
  }

  Function addNewRule($scheduleid, $sensorid, $targettemp)
  {
    $link = $this->connect();
    $query = $link->prepare("INSERT INTO rules (schedule, sensor, targettemp) VALUES (?, ?, ?);");
    $query->execute(array($scheduleid, $sensorid, $targettemp));
    $num = $query->rowCount();

    if ($num == 0)
    {
      return false;
    }

    else
    {
      return true;
    }
  }

  Function deleteRule($ruleid)
  {
    $link = $this->connect();
    $query = $link->prepare("DELETE FROM rules WHERE rules.id = ?;");
    $query->execute(array($ruleid));
  }

  Function getScheduleRules($sid)
  {
    $link = $this->connect();
    $query = $link->prepare("SELECT * FROM rules INNER JOIN sensors ON sensors.uid = rules.sensor WHERE sensors.enabled = 1 AND rules.schedule = ?;");
    $query->execute(array($sid));
    $num = $query->rowCount();

    if ($num == 0)
    {
      return false;
    }

    else
    {
      return $query->fetchAll();
    }
  }

  Function addNewSchedule($name, $days, $timestart, $timeend)
  {
    if (!$this->checkScheduleOverlap($days, $timestart, $timeend))
    {
      $link = $this->connect();
      $query = $link->prepare("INSERT INTO schedules (friendlyname, dayofweek, pretimestart, timestart, timeend) VALUES (?, ?, ?, ?, ?);");
      $query->execute(array($name, $days, $timestart, $timestart, $timeend));
      return true;
    }
    
    else
    {
      return false;
    }
  }

  Function changeScheduleState($sid)
  {
    $link = $this->connect();
    $query = $link->prepare("UPDATE schedules SET enabled = !enabled WHERE id = ?;");
    $query->execute(array($sid));
  }

  Function getSensors()
  {
    $link = $this->connect();
    $query = $link->query("SELECT * FROM sensors;");
    $num = $query->rowCount();

    if ($num == 0)
    {
      return false;
    }

    else
    {
      return $query->fetchAll();
    }
  }

  Function changeSensorState($uid)
  {
    $link = $this->connect();
    $query = $link->prepare("UPDATE sensors SET enabled = !enabled WHERE uid = ?");
    $query->execute(array($uid));
  }

  Function updateSensorName($uid, $name)
  {
    if ($name)
    {
      $link = $this->connect();
      $query = $link->prepare("UPDATE sensors SET friendlyname = ? WHERE uid = ?");
      $query->execute(array($name, $uid)); 
    }
  }

  Function updateSensorOffset($uid, $offset)
  {
    if ($offset)
    {
      $link = $this->connect();
      $query = $link->prepare("UPDATE sensors SET offset = ? WHERE uid = ?");
      $query->execute(array($offset, $uid)); 
    }
  }

  Function getCurrentSensorData()
  {
    $link = $this->connect();
    $query = $link->query("SELECT T.uid, T.friendlyname, T.temperature, T.timestamp FROM (SELECT sensors.uid, sensors.friendlyname, temperature.temperature, temperature.timestamp FROM temperature INNER JOIN sensors ON sensors.uid = temperature.sensor WHERE sensors.enabled = 1 ORDER BY temperature.timestamp DESC) AS T GROUP BY T.friendlyname ORDER BY T.temperature DESC;");
    $num = $query->rowCount();

    if ($num == 0)
    {
      return false;
    }

    else
    {
      return $query->fetchAll();
    }
  }

  Function getTempsLog($time)
  {
    $starttime = time() - date("Z") - $time;
    $link = $this->connect();
    $query = $link->prepare("SELECT * FROM temperature INNER JOIN sensors ON sensors.uid = temperature.sensor WHERE timestamp > ? ORDER BY timestamp DESC;");
    $query->execute(array(date('Y-m-d H:i:s', $starttime))); 
    $num = $query->rowCount();

    if ($num == 0)
    {
      return false;
    }

    else
    {
      return $query->fetchAll();
    }
  }

  Function checkScheduleOverlap($days, $starttime, $endtime)
  {
    $link = $this->connect();
    $query = $link->query("SELECT * FROM schedules;");
    $num = $query->rowCount();

    if ($num == 0)
    {
      return false;
    }

    else
    {
      $result = $query->fetchAll();
      foreach ($result as $res)
      {
        $i = 0;
        $dow = str_split($res['dayofweek']);
        foreach (str_split($days) as $day)
        {
          if ($day == $dow[$i] && $day == 1)
          {
            if (substr($starttime, 0, 2) >= substr($res['timestart'], 0, 2) && substr($starttime, 0, 2) <= substr($res['timeend'], 0, 2))
            {
              if (substr($starttime, 3, 2) >= substr($res['timestart'], 3, 2) && substr($starttime, 3, 2) <= substr($res['timeend'], 3, 2))
              {
                return true;
              }
            }

            if (substr($endtime, 0, 2) >= substr($res['timestart'], 0, 2) && substr($endtime, 0, 2) <= substr($res['timeend'], 0, 2))
            {
              if (substr($endtime, 0, 2) >= substr($res['timestart'], 0, 2) && substr($endtime, 0, 2) <= substr($res['timeend'], 0, 2))
              {
                return true;
              }
            }
          }

          $i++;
        }
      }
    }

    return false;
  }
}
