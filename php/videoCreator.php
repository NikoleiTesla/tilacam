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
    private $pictureProvider;
    private $startDate;
    private $endDate;
    
   function __construct($pictureProvider)
   {
       $this->pictureProvider = $pictureProvider;
   }   
    
   function setStartDate($date){
       $this->startDate = $date;
   }
   
   function createDayVideo(){
       $this->endDate = $this->startDate;
   }
   
   function createWeekVideo(){
       $week = new DateInterval('P7D');        
       $this->endDate = $this->startDate;
       $this->endDate->sub($week);       
   }
  
   function createMonthVideo(){
       $week = new DateInterval('P30D');        
       $this->endDate = $this->startDate;
       $this->endDate->sub($week);       
   }   
   
   
   
}
