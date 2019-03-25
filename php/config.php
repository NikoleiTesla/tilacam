<?php

$config_pictureDir = "/tilacam/pictures";
$config_dayStartHour = 8;
$config_dayendHour = 22;
// ignore days of the week numeric ISO-8601 1 Monday 7 Sunday 
$config_ignoreDays = [6,7]; //ignore Saturday and Sunday
$config_videoDir ="/tilacam/video";
$config_cachRenewInterval = 30000; //cach erneuern alle x ms (5 minuten)
//image pattern (php date format not complemtly implemented) $y=year 2 digits, %Y=year 4 digits, m=month 2 digits, d=day 2 digits,H=hour 2 digits,i=minute 2 digits
//all not numeric char are removed
$config_pictureNamePattern = '%d%m%Y%H%i';

//show informations for debuging reasons default false;
$config_debug = false;

 ?>
