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
       $this->selectPictures();       
   }
   
   function createWeekVideo(){
       $week = new DateInterval('P7D');        
       $this->startDate->sub($week); 
       $this->selectPictures();
   }
  
   function createMonthVideo(){
       $month = new DateInterval('P30D');        
       $this->startDate->sub($month); 
       $this->selectPictures();
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
            //echo "put to selected pics ".$p->dateTime;
            array_push($this->pictures,$p);
        }
    }
    DebugMessage ('selected Pictures = '.count($this->pictures));    
  }
   
}
