<?php
/*
 * Copyright 2009 Don Benjamin
 * Licensed under the Apache License, Version 2.0 (the "License"); 
 * you may not use this file except in compliance with the License. 
 * You may obtain a copy of the License at 
 *
 * 	http://www.apache.org/licenses/LICENSE-2.0 
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, 
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. 
 * See the License for the specific language governing permissions and 
 * limitations under the License.
 */

/** This file contains all the code to provide search fields for searching
 * properties defined with the greatrealestate plugin.
 *
 * It is designed to work with the greatrealestate property management plugin.
 *
 * 	http://www.rogertheriault.com/agents/plugins/great-real-estate-plugin/
 */
require_once(dirname(__FILE__).'/../extra_search_fields.php');

class GreatRealEstateJoiner extends BaseJoiner {
	function GreatRealEstateJoiner($name=null){
		GreatRealEstateJoiner::__construct($name);
	}
	function __construct($name){
		$this->name = $name;
	}
	function sql_restrict($name,$index,$value,$comparison){
		if($this->name) $name=$this->name;
		global $wpdb;
		$table = $wpdb->gre_listings;
		return " AND ( ".$comparison->addSQLWhere("$table.$name",$value).") ";
	}
	function sql_join($join){
		global $wpdb;
		$table = $wpdb->gre_listings;
		if(!strpos($join,$wpdb->gre_listings)) $join.=" JOIN $wpdb->gre_listings ON $wpdb->gre_listings.pageid=$wpdb->posts.id";
		return $join;
	}
	function getAllOptions($fieldName){
		if($this->name) $fieldName=$this->name;
		global $wpdb;
		$q = mysql_query($sql = "SELECT DISTINCT l.$fieldName FROM $wpdb->gre_listings l JOIN $wpdb->posts p ON l.pageid=p.id AND p.post_status='publish'");
		if($e = mysql_error()){
			die("<h1>$sql</h1>".$e);
		}
		$options = array();
		while($r = mysql_fetch_row($q))
			$options[$r[0]] = $r[0];
		return $options;
	}
	function process_where($where){
		global $wpdb;
		$cleared = preg_replace("/AND ?\(?$wpdb->posts.post_type ?= ?'(post|page)' ?\)?/","",$where);
		$cleared = preg_replace("/$wpdb->posts.ID = '\d+'/","1",$cleared);
		return $cleared;
	}
	function getSuggestedFields(){
		return array('listPrice'=>'List Price','city','county');
	}
}



add_filter('custom_search_get_classes','add_real_estate_search_fields');
function add_real_estate_search_fields($classes){
	if(function_exists('greatrealestate_init'))
		$classes['joiner']['GreatRealEstateJoiner']='Great Real Estate';
	return $classes;
}

?>
