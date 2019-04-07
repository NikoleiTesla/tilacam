<?php
require_once(__DIR__.'/config.php');
require_once(__DIR__.'/picture.php');
/**
 *
 */
class PictureProvider
{
  public $pictures = array();
  public $days = array();
  public $dayDateFormat = 'Y-m-d';

  function __construct()
  {
    // code...
  }

  public function getAllPictures(){
    $protocol = $this->getHttpProtocol();
    $pics = new DirectoryIterator($this->getPictureDir());  //faster and bettern than glob 
    //$pics = glob($this->getPictureDir().'/*.jpg');
    $picturesToSort = array();
    
    //if(count($pics) === 0){
    //  throw new Exception("Fehler im Bilder Ordner ".$this->getPictureDir()." wurden keine Bider gefunden!");
    // }
    foreach ($pics as $p) {
      $picture = new Picture($p,$protocol);
      if($this->IsValidTime($picture) && $this->isValidDay($picture)){
         $picturesToSort[$picture->timestamp] = $picture;
      }
    }
    //Sort Pictures by acutal timestamp not filename
    ksort($picturesToSort);
    foreach ($picturesToSort as $key => $value) {
        array_push($this->pictures,$value);
        $aDay = date_format($value->dateTime,  $this->dayDateFormat);        
        if (!array_key_exists($aDay, $this->days)) {
                $this->days[$aDay] = 0;
        }

        $this->days[$aDay]+=1;        
    }
      
     return $this->pictures;
  }

  public function getPicturesForDay($day){
    $dayPictures = array();
    foreach($this->pictures as $picture){
      $pictureDay = date_format($picture->dateTime,$this->dayDateFormat);
      if($pictureDay == $day){
        array_push($dayPictures,$picture);
      }
    }
    return $dayPictures;
  }

  public function getFirstPicture(){
    end($this->days);
    $day = key($this->days);
    $dayPictures = $this->getPicturesForDay($day);
    reset($this->days);
    return $dayPictures[0];
  }

  public function getAvailableDays(){
      return $this->days;
  }

  public function setPicture($pictures){
    $this->pictures = $pictures;
  }

  private function isValidDay($picture){
      global $config_ignoreDays;
      if (empty($config_ignoreDays)) {
            return true;
        }
      $dow = date("N",$picture->timestamp);
      if (in_array($dow, $config_ignoreDays)) {
            return false;
        }

        return true;
  }
  
  private function IsValidTime($picture){
      global $config_dayStartHour;
      global $config_dayendHour;
      if($picture->hour >= $config_dayStartHour && $picture->hour <= $config_dayendHour){
        return true;
      }
      return false;

  }

  function getPictureDir(){
    Global $config_pictureDir;

    $instalDir = $_SERVER['DOCUMENT_ROOT'];
    $pictureDir = $instalDir."".$config_pictureDir;
    return $pictureDir;
  }
  
  function getHttpProtocol(){
    if(array_key_exists("HTTPS", $_SERVER) && $_SERVER["HTTPS"] == "on"){
        $protocol = "https://";
    } else {
        $protocol = "http://";
    } 
    return $protocol;
  }

}
