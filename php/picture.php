<?php

class Picture
{
  public $name;
  public $dateTime;
  public $show=true;
  public $formatedDateTime;
  public $timestamp;
  public $hour;
  public $minute;
  public $webPath;
  public $protocol;

  function __construct($name,$protocol)
  {
    $this->name = $name;
    $this->protocol = $protocol;
    $this->parseName();
  }

  function parseName(){
    global $config_pictureNamePattern;
    global $config_pictureDir;
    $serverName = $_SERVER["SERVER_NAME"];

    $config_pictureNamePattern = str_replace('%','',$config_pictureNamePattern);
    //clean string name
    $filename = basename($this->name);
    $this->name = $config_pictureDir.'/'.$filename;
    $this->webPath = $this->protocol.$serverName.$this->name;
    $res = preg_replace("/[^0-9]/", "", $filename);
    $this->dateTime = date_create_from_format($config_pictureNamePattern,$res);
    if($this->dateTime == false){ //timepattern and filename give an invalid datetime 
        //display filename and pattern in debug
        DebugMessage('Invalid DateTime for filename: '.$filename.' Datetime pattern: '.$config_pictureNamePattern);   
        $this->show = false;
        return;
    }
    $this->timestamp = date_timestamp_get($this->dateTime);
    $this->formatedDateTime = date_format($this->dateTime, 'Y-m-d H:i:s');
    $this->hour = date_Format($this->dateTime,'H');
    $this->minute = date_format($this->dateTime,'i');

    //for depbuging
    //echo "Picture : ".$this->name." Date: ".$this->formatedDateTime.'<br>';
  }

}


 ?>
