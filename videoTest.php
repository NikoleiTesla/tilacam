<?php
require_once("./php/config.php");
require_once("./php/pictureProvider.php");
require_once './php/videoCreator.php';

$pp = new PictureProvider();
$vc = new videoCreator($pp);
$enddate = new DateTime('2017-10-02');
$vc->setDate($enddate);
$vc->createDayVideo();
$vc->createWeekVideo();
$vc->createMonthVideo();

function DebugMessage($message){
  Global $config_debug;
  if (!$config_debug)
    return;
  
  if(is_array($message)){
    echo "<pre>";
    var_dump($message);
    echo("</pre>");
  } else {
    echo " -debug: ".$message.'<br>';
  }
}
?>


