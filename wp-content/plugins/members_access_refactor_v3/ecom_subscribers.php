<?php
/*
THIS CAN BE DELETED AND THE FUNCTION PUT SOMEWHERE ELSE
function wpsc_check_ecom_subscribers(){
//default capabilities
	$array_capabilities = array('posts-base','posts-premium','forum-base');
	$userdata = get_userdata($value);
	foreach($array_capabilities as $capabilities){
		if(array_key_exists($capabilities, $userdata->wp_capabilities)){
			$subscriber[] = $userdata;
		}
	}	
}
*/


//function displays the users in the rows to be deleted
/*
function wpsc_display_ecom_subscribers() {
}
	
*/
	



/**
 * A function for making time periods readable
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     2.0.1
 * @link        http://aidanlister.com/2004/04/making-time-periods-readable/
 * @param       int     number of seconds elapsed
 * @param       string  which time periods to display
 * @param       bool    whether to show zero time periods
 */
 /// this function needs to be deleted and mktime used or at least use one function or the other!
function vl_wpscsm_time_duration($seconds, $use = null, $zeros = false) {
  $periods = array (
      'years'     => 31556926,
      'Months'    => 2629743,
      'weeks'     => 604800,
      'days'      => 86400,
      'hours'     => 3600,
      'minutes'   => 60,
      'seconds'   => 1
      );
  $seconds = (float) $seconds;
  $segments = array();
  foreach ($periods as $period => $value) {
      if ($use && strpos($use, $period[0]) === false) {
          continue;
      }
      $count = floor($seconds / $value);
      if ($count == 0 && !$zeros) {
          continue;
      }
      $segments[strtolower($period)] = $count;
      $seconds = $seconds % $value;
  }
  $string = array();
  foreach ($segments as $key => $value) {
      $segment_name = substr($key, 0, -1);
      $segment = $value . ' ' . $segment_name;
      if ($value != 1) {
          $segment .= 's';
      }
      $string[] = $segment;
  }
  return implode(', ', $string);
}

?>
