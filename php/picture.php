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

  function __construct($name)
  {
    $this->name = $name;
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
    $this->webPath = $serverName.$this->name;
    $res = preg_replace("/[^0-9]/", "", $filename);
    $this->dateTime = date_create_from_format($config_pictureNamePattern,$res);
    $this->timestamp = date_timestamp_get($this->dateTime);
    $this->formatedDateTime = date_format($this->dateTime, 'Y-m-d H:i:s');
    $this->hour = date_Format($this->dateTime,'H');
    $this->minute = date_format($this->dateTime,'i');

    //for depbuging
    //echo "Picture : ".$this->name." Date: ".$this->formatedDateTime.'<br>';
  }

}


 ?>
