<?php
require_once(__DIR__."/php/config.php");
require_once(__DIR__."/php/pictureProvider.php");
require_once(__DIR__."/php/videoCreator.php");

$actionAvailableDays = "getAvailableDays";
$actionAllPictures = "getAllPictures";
$actionFirstPicture = "getFirstPicture";
$actionPictureForDay ="getPicturesForDay";
$actionCreateVideo = "createVideo";

$pp = new PictureProvider();
//always get all pictures (could be optimized with a cache)
$allPictures = $pp->getAllPictures();
// debug
DebugMessage("Picture count ".count($allPictures));
DebugMessage("Days count ".count($pp->getAvailableDays()));
DebugMessage("First Picture to display ".$pp->getFirstPicture()->webPath);
DebugMessage('<img src="http://'.$pp->getFirstPicture()->webPath.'">');
DebugMessage($_REQUEST);

//initialization in global scope to avoid errors
$action="";
$day="";

if(array_key_exists('action',$_REQUEST))
  $action = strip_tags($_REQUEST['action']);
if(array_key_exists('day',$_REQUEST))
  $day = strip_tags($_REQUEST['day']);
if(array_key_exists('mode',$_REQUEST))
  $mode = strip_tags($_REQUEST['mode']);

if($action==$actionAllPictures){
  jsonOutput($allPictures);
} else if ($action==$actionFirstPicture){
  jsonOutput($pp->getFirstPicture());
} else if($action==$actionAvailableDays){
  jsonOutput($pp->getAvailableDays());
} else if($action==$actionPictureForDay){
  jsonOutput($pp->getPicturesForDay($day));
} else if($action==$actionCreateVideo){
    $videoPP = new PictureProvider();
    $vc = new videoCreator($videoPP);
    $enddate = new DateTime($day);
    $vc->setDate($enddate);
    $videofile = '';
    if ($mode == 'day') {
        $videofile = $vc->createDayVideo();
    }
    if ($mode == 'week') {
        $videofile = $vc->createWeekVideo();
    }
    if ($mode == 'month') {
        $videofile = $vc->createMonthVideo();
    }
    if ($mode == 'all') {
        $videofile = $vc->createCompleteVideo();
    }    
    echo $videofile;
} else {
  DebugMessage('Action: '.$action.' is not defined');
}

/*
foreach($allPictures as $picture){
    echo "Picture : ".$picture->name." Date: ".$picture->formatedDateTime.'<br>';

}

echo "<br>";

foreach($days as $key => $val){
  DebugMessage("Day: ".$key." Bilder: ".$val);

}
*/

function jsonOutput($object){
  $result = json_encode($object);
  DebugMessage("Json encode: ".$result);
  header('Content-type: application/json');
  echo $result;
}

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
