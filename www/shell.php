<?php
Class Shell
{
  private $commandfile = "/home/pi/thermostat.sh";
  private $command;
  private $pid;
  private $outputfile = "/dev/null";

  public function isRunning()
  {
    try
    {
      $result = shell_exec(sprintf('ps %d', $this->pid));
      if(count(preg_split("/\n/", $result)) > 2)
      {
        return true;
      }
    }

    catch(Exception $e)
    {
    }

    return false;
  }

  public function getPid()
  {
    return $this->pid;
  }

  Function RunThermostat($scheduleid)
  {
    $this->command = $this->commandfile ." ". $scheduleid;
    $this->pid = shell_exec($this->command ." > ". $this->outputfile  ." 2>&1 &");
  }

  Function convertScheduleToCron($schedule)
  {
    $dow = null;
    if ($schedule['dayofweek'] == "1111111")
    {
      $dow = "*";
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
          $days[] = "1";
        }

        if ($day == 1 && $i == 1)
        {
          $days[] = "2";
        }
      
        if ($day == 1 && $i == 2)
        {
          $days[] = "3";
        }
	      
        if ($day == 1 && $i == 3)
        {
          $days[] = "4";
        }
	      
        if ($day == 1 && $i == 4)
        {
          $days[] = "5";
        }
	      
        if ($day == 1 && $i == 5)
        {
          $days[] = "6";
        }
	      
        if ($day == 1 && $i == 6)
        {
          $days[] = "0";
        }
        
        $i++;
      }

      $dow = implode(",", $days);
    }
    
    $hour = substr($schedule['timestart'], 0, 2);
    $minute = substr($schedule['timestart'], 3, 2);

    #$cron = "@reboot command\n";
    $cron = "$minute $hour * * $dow ". $this->commandfile ." ". $schedule['id'] ."\n";
    return $cron;
  }

  Function getCron()
  {
  	exec('crontab -l', $output, $return);
  	return implode("\n", $output);
  }

  Function commitToCron($cron)
  {
  	file_put_contents("/tmp/cron", $cron);
  	exec("crontab /tmp/cron", $output, $return);
    
    if($return != 0)
    {
      echo 'Cron Commit Unsuccessful' . implode("  ", $output);
    }
  }
}

?>
