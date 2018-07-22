<?php
$title="Youtube Channel Video Extract";
$youtube_api='Youtube-API-Key';
$result_count=50; // Maximum 50 results from a channel!

/**
* Functions for script!
* No need to change anything bellow!
*/

 date_default_timezone_set('Africa/Lagos');
function print_a($array){
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}
function covtime($youtube_time)
{
  $start = new DateTime('@0'); // Unix epoch
  $start->add(new DateInterval($youtube_time));
  return $start->format('H:i:s');

}
function timeinsec($str_time)
{
  //  $str_time = "23:12:95";

  $str_time     = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);

  sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);

  $time_seconds = $hours * 3600 + $minutes * 60 + $seconds;
  return ($time_seconds);
}
