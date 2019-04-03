<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of videoCreator
 *
 * @author NHaider
 */
class videoCreator {
    //put your code here
    /* @var $pictureProvider PictureProvider */
    private $pictureProvider;
    /* @var $startDate DateTime */
    private $startDate;
    /* @var $endDate DateTime */
    private $endDate;

    private $pictures = array();
    private $maxPictures;
    private $picturePrefix ='';
    private $createdVideoFile = '';
    
   function __construct($pictureProvider)
   {
       global $config_videoMaxLength;
       $this->pictureProvider = $pictureProvider;
       $this->maxPictures = $config_videoMaxLength * 25;
   }
      
   function setDate($date){
       $this->startDate = clone $date;
       $this->endDate = clone $date;
       
   }
   
   function createDayVideo(){
       $this->picturePrefix = 'day';
       $this->selectPictures();  
       $this->createVideo();
       return $this->createdVideoFile;
   }
   
   function createWeekVideo(){
       $this->picturePrefix = 'week';
       $week = new DateInterval('P7D');        
       $this->startDate->sub($week); 
       $this->selectPictures();
       $this->createVideo();
       return $this->createdVideoFile;
   }
  
   function createMonthVideo(){
       $this->picturePrefix = 'month';
       $month = new DateInterval('P30D');        
       $this->startDate->sub($month); 
       $this->selectPictures();
       $this->createVideo();
       return $this->createdVideoFile;
   }   
   
   function createCompleteVideo(){
       $this->picturePrefix = 'all';
       $this->pictures = $this->pictureProvider->getAllPictures();
       $this->reducePicutres();     
       $this->createVideo();
       return $this->createdVideoFile;
   }
   
   private function createVideo(){
       global $config_pathToFFmpeg;
       global $config_videoDir;       
       if($this->copyPictures()){
         $outputFile = $_SERVER['DOCUMENT_ROOT'].$config_videoDir.'/video_'.$this->picturePrefix.'.mp4';    
         $command = $config_pathToFFmpeg.' -f image2 -r 25 -i "'.$_SERVER['DOCUMENT_ROOT'].$config_videoDir.'/'.$this->picturePrefix.'_%05d.jpg" -vcodec libx264 -b 5000k -tune stillimage "'.$outputFile.'"'; 
         DebugMessage("Create Video: ".$command); 
         exec($command);
         $this->createdVideoFile = $config_videoDir.'/video_'.$this->picturePrefix.'.mp4';
         $this->cleanup();
       }
   }   
   
   private function copyPictures(){
       global $config_videoDir;
       $i = 0;
       foreach ($this->pictures as $p){
        $fpn = sprintf("%05d",$i);
        $sourceFile = $_SERVER['DOCUMENT_ROOT'].''.$p->name;
        $destFile = $_SERVER['DOCUMENT_ROOT'].$config_videoDir.'/'.$this->picturePrefix.'_'.$fpn.'.jpg';
        copy($sourceFile,$destFile);
        DebugMessage('copy: '.$sourceFile.' > '.$destFile);
        $i++;
       }
       if($i > 0){
          return true;
       }
       return false;
   }
   
   private function cleanup(){
       global $config_videoDir;       
       $i=0;
       foreach ($this->pictures as $p){
        $fpn = sprintf("%05d",$i);
        $delteFile = $_SERVER['DOCUMENT_ROOT'].$config_videoDir.'/'.$this->picturePrefix.'_'.$fpn.'.jpg';
        unlink($delteFile);
        $i++;
       }
   }

   private function selectPictures(){ 
    $select = $this->pictureProvider->getAllPictures();
    //Sart to day Start
    $this->startDate->setTime(0, 0, 0);
    $this->endDate->setTime(23,59,59);
    DebugMessage("Start ".$this->startDate->format('Y-m-d H:i:s').' End '.$this->endDate->format('Y-m-d H:i:s'));
    //end to day end
    /* @var $p Picture */
    foreach ($select as $p) {
        if ($p->dateTime >= $this->startDate && $p->dateTime <= $this->endDate){ 
            array_push($this->pictures,$p);
        }
    }   
    DebugMessage ('selected Pictures = '.count($this->pictures)); 
    $this->reducePicutres();
  }
   
  private function reducePicutres(){
    if($this->maxPictures >0 && count($this->pictures) > $this->maxPictures){
        $ratio = count($this->pictures) / $this->maxPictures;  //reduce pictures to fit defined length
        DebugMessage('More pictures found than defined in max Pictures '.$this->maxPictures.' / '.count($this->pictures).' ratio '.$ratio);
        $tempArray = array();
        for($i=0;$i<$this->maxPictures;$i++){
            $newIndex = floor($i*$ratio);
            array_push($tempArray,$this->pictures[$newIndex]);
            DebugMessage(' -> add Index '.$newIndex);
        }
        $this->pictures = $tempArray;
    }      
  }
  
}
